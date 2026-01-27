<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use App\Http\Requests\CashRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashController extends Controller
{
  public function index(Request $request)
{
    $q = $request->query('q');
    $user = auth()->user();

    // ================== allowed created_by IDs ==================
    $allowedCreators = [$user->id];

    if ($user->role === 'doctor') {
        // doctor: نفسه + السكرتارية اللي doctor_id = doctor.id
        $secretaryIds = \App\Models\User::query()
            ->where('role', 'secretary')
            ->where('doctor_id', $user->id)
            ->pluck('id')
            ->toArray();

        $allowedCreators = array_values(array_unique(array_merge($allowedCreators, $secretaryIds)));
    }

    if ($user->role === 'secretary') {
        // secretary: نفسه + الدكتور بتاعه (doctor_id)
        if (!empty($user->doctor_id)) {
            $allowedCreators[] = (int) $user->doctor_id;
            $allowedCreators = array_values(array_unique($allowedCreators));
        }
    }

    // (اختياري) لو admin يشوف الكل:
    // if ($user->role === 'admin') { $allowedCreators = null; }

    // ================== query ==================
 $movements = Cash::query()
  ->whereIn('created_by', $allowedCreators)
  ->when($q, fn ($qq) => $qq->where('service', 'like', "%{$q}%"))
  ->when($request->filled('day'), fn ($qq) => $qq->whereDate('created_at', $request->day))
  ->with('creator')
  ->latest()
  ->paginate(10)
  ->appends($request->query());


    // ✅ لو عايز totals لنفس الفلترة (مش totals على كل السيستم)
    $totalIn = (float) \App\Models\Cash::query()
        ->when($allowedCreators, fn($qq) => $qq->whereIn('created_by', $allowedCreators))
        ->sum('cash');

    $totalOut = (float) \App\Models\Cash::query()
        ->when($allowedCreators, fn($qq) => $qq->whereIn('created_by', $allowedCreators))
        ->sum('cash_out');

    $net = $totalIn - $totalOut;

    return view('dashboard.cash.index', compact('movements', 'totalIn', 'totalOut', 'net'));
}


    public function create()
    {
        return view('dashboard.cash.create');
    }

    public function store(CashRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {

            $cash    = (float) ($data['cash'] ?? 0);
            $cashOut = (float) ($data['cash_out'] ?? 0);

            // Current balance
            $currentBalance = (float) Cash::sum('cash') - (float) Cash::sum('cash_out');

            // ✅ Prevent entering both at once (cleaner)
            if ($cash > 0 && $cashOut > 0) {
                return back()->withErrors([
                    'error' => 'Please choose only one operation: Cash In (cash) OR Cash Out (cash_out).'
                ])->withInput();
            }

            // ✅ Must enter a value in one of them
            if ($cash <= 0 && $cashOut <= 0) {
                return back()->withErrors([
                    'error' => 'You must enter a value for either Cash In (cash) or Cash Out (cash_out).'
                ])->withInput();
            }

            // ✅ Cash Out: cannot exceed current balance
            if ($cashOut > 0) {
                if ($cashOut > $currentBalance) {
                    return back()->withErrors([
                        'error' => 'The cash out amount is greater than the current balance. Transaction rejected.'
                    ])->withInput();
                }

                Cash::create([
                    'cash'       => 0,
                    'cash_out'   => $cashOut,
                    'service'    => $data['service'] ?? 'Expense',
                    'created_by' => Auth::id(),
                ]);

                return redirect()
                    ->route('cash.index')
                    ->with('success', 'Cash out recorded successfully and deducted from balance.');
            }

            // ✅ Cash In: allowed any number of times
            Cash::create([
                'cash'       => $cash,
                'cash_out'   => 0,
                'service'    => $data['service'] ?? 'Cash In',
                'created_by' => Auth::id(),
            ]);

            return redirect()
                ->route('cash.index')
                ->with('success', 'Cash in added successfully.');
        });
    }

    /* ================= Edit ================= */
    public function edit(Cash $cashMovement)
    {
        return view('dashboard.cash.edit', compact('cashMovement'));
    }

    /* ================= Update ================= */
    public function update(CashRequest $request, Cash $cashMovement)
    {
        $data = $request->validated();
        $service = $data['service'] ?? $cashMovement->service;

        // If it was Cash In
        if ((float) $cashMovement->cash > 0) {
            $newCash = (float) ($data['cash'] ?? 0);

            if ($newCash <= 0) {
                return back()->withErrors([
                    'error' => 'Please enter a valid Cash In value.'
                ])->withInput();
            }

            $cashMovement->update([
                'service'  => $service,
                'cash'     => $newCash,
                'cash_out' => 0,
            ]);
        } else {
            // If it was Cash Out
            $newOut = (float) ($data['cash_out'] ?? 0);

            if ($newOut <= 0) {
                return back()->withErrors([
                    'error' => 'Please enter a valid Cash Out value.'
                ])->withInput();
            }

            // ✅ Check balance after update (to avoid negative)
            $totalIn  = (float) Cash::sum('cash');
            $totalOutWithoutThis = (float) Cash::where('id', '!=', $cashMovement->id)->sum('cash_out');
            $balanceBeforeThis   = $totalIn - $totalOutWithoutThis;

            if ($newOut > $balanceBeforeThis) {
                return back()->withErrors([
                    'error' => 'The cash out amount is greater than the current balance. Transaction rejected.'
                ])->withInput();
            }

            $cashMovement->update([
                'service'  => $service,
                'cash'     => 0,
                'cash_out' => $newOut,
            ]);
        }

        return redirect()
            ->route('cash.index')
            ->with('success', 'Cash movement updated successfully.');
    }

    /* ================= Destroy ================= */
    public function destroy(Cash $Cash)
    {
        return DB::transaction(function () use ($Cash) {
            $Cash->delete();

            return back()->with(
                'success',
                'Cash movement deleted successfully.'
            );
        });
    }

    /* ================= Bulk Destroy ================= */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->withErrors([
                'error' => 'Please select items to delete first.'
            ]);
        }

        Cash::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected items deleted successfully.');
    }

    public function printCash(Request $request)
    {
        $q   = $request->get('q');
        $day = $request->get('day'); // YYYY-MM-DD

        $movements = Cash::query()
            ->when($q, fn ($qq) => $qq->where('service', 'like', "%{$q}%"))
            ->when($request->filled('day'), fn ($qq) => $qq->whereDate('created_at', $day))
            ->with('creator')
            ->latest()
            ->get();

        // Totals
        $totalCashIn  = (float) $movements->sum('cash');
        $totalCashOut = (float) $movements->sum('cash_out');
        $netCash      = $totalCashIn - $totalCashOut;

        // Keep if your print view still uses $total
        $total = $totalCashIn;

        // Report meta
        $reportId    = 'HC-' . now()->format('Ymd-His');
        $generatedAt = now();

        return view('dashboard.cash.print', compact(
            'movements',
            'total',
            'reportId',
            'generatedAt',
            'q',
            'day',
            'totalCashIn',
            'totalCashOut',
            'netCash'
        ));
    }
}
