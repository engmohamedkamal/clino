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

    $movements = Cash::query()
        ->when($q, fn($qq) => $qq->where('service', 'like', "%{$q}%"))
        ->when($request->filled('day'), fn($qq) => $qq->whereDate('created_at', $request->day))
        ->with('creator')
        ->latest()
        ->paginate(5)
        ->appends($request->query());

    // totals
    $totalIn  = (float) Cash::sum('cash');
    $totalOut = (float) Cash::sum('cash_out');
    $net      = $totalIn - $totalOut;

    // ✅ edit mode
    $editMovement = null;
    if ($request->filled('edit')) {
        $editMovement = Cash::with('creator')->findOrFail($request->query('edit'));
    }

    return view('dashboard.cash.index', compact('movements', 'totalIn', 'totalOut', 'net', 'editMovement'));
}


    public function create()
    {
        return view('dashboard.cash.create');
    }

    public function store(CashRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {

            $cash = (float) ($data['cash'] ?? 0);
            $cashOut = (float) ($data['cash_out'] ?? 0);

            // الرصيد الحالي
            $currentBalance = (float) Cash::sum('cash') - (float) Cash::sum('cash_out');

            // ✅ منع إدخال الاتنين مع بعض (اختياري بس أنضف)
            if ($cash > 0 && $cashOut > 0) {
                return back()->withErrors('اختار عملية واحدة: إيداع (cash) أو صرف (cash_out) فقط.');
            }

            // ✅ لازم يدخل رقم في واحد منهم
            if ($cash <= 0 && $cashOut <= 0) {
                return back()->withErrors('لازم تدخل قيمة للإيداع (cash) أو الصرف (cash_out).');
            }

            // ✅ صرف: ممنوع يتخطى الرصيد
            if ($cashOut > 0) {
                if ($cashOut > $currentBalance) {
                    return back()->withErrors('المبلغ المصروف أكبر من الرصيد الحالي. العملية مرفوضة.');
                }

                Cash::create([
                    'cash' => 0,
                    'cash_out' => $cashOut,
                    'service' => $data['service'] ?? 'Expense',
                    'created_by' => Auth::id(),
                ]);

                return redirect()
                    ->route('cash.index')
                    ->with('success', 'تم تسجيل المصروف وخصمه من الرصيد.');
            }

            // ✅ إيداع: ينفع أي عدد مرات
            Cash::create([
                'cash' => $cash,
                'cash_out' => 0,
                'service' => $data['service'] ?? 'Cash In',
                'created_by' => Auth::id(),
            ]);

            return redirect()
                ->route('cash.index')
                ->with('success', 'تم إضافة مبلغ إلى الرصيد بنجاح.');
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

    // لو كان Cash In
    if ((float)$cashMovement->cash > 0) {
        $newCash = (float)($data['cash'] ?? 0);

        if ($newCash <= 0) {
            return back()->withErrors('لازم تدخل قيمة Cash صحيحة للإيداع.');
        }

        $cashMovement->update([
            'service'  => $service,
            'cash'     => $newCash,
            'cash_out' => 0,
        ]);
    } else {
        // لو كان Cash Out
        $newOut = (float)($data['cash_out'] ?? 0);

        if ($newOut <= 0) {
            return back()->withErrors('لازم تدخل قيمة Cash Out صحيحة.');
        }

        // ✅ تحقق الرصيد بعد التعديل (عشان مايبقاش بالسالب)
        $totalIn  = (float) Cash::sum('cash');
        $totalOutWithoutThis = (float) Cash::where('id', '!=', $cashMovement->id)->sum('cash_out');
        $balanceBeforeThis   = $totalIn - $totalOutWithoutThis;

        if ($newOut > $balanceBeforeThis) {
            return back()->withErrors('المبلغ المصروف أكبر من الرصيد الحالي. العملية مرفوضة.');
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
                'Cash movement deleted and balance updated successfully.'
            );
        });
    }

    /* ================= Bulk Destroy ================= */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || count($ids) === 0) {
            return back()->withErrors('Select items first to delete.');
        }
        Cash::whereIn('id', $ids)->delete();
        return back()->with('success', 'Selected items deleted successfully.');
    }

public function printCash(Request $request)
{
    $q   = $request->get('q');
    $day = $request->get('day'); // YYYY-MM-DD

    $movements = Cash::query()
        ->when($q, fn($qq) => $qq->where('service', 'like', "%{$q}%"))
        ->when($request->filled('day'), fn($qq) => $qq->whereDate('created_at', $day))
        ->with('creator')
        ->latest()
        ->get(); // ✅ للطباعة نجيب الكل مش paginate

    // ✅ إجمالي (بدّل cash لو اسم العمود مختلف)
    $total = (float) $movements->sum('cash');

    // ✅ بيانات التقرير
    $reportId = 'HC-' . now()->format('Ymd-His');
    $generatedAt = now();
$totalCashIn  = (float) $movements->sum('cash');
$totalCashOut = (float) $movements->sum('cash_out');
$netCash      = $totalCashIn - $totalCashOut;
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
