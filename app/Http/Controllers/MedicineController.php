<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $medicalOrders = Medicine::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('type', 'like', "%{$q}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.medicine.view', compact('medicalOrders', 'q'));
    }

    public function create()
    {
        return view('dashboard.medicine.add');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['medicine', 'rumor', 'analysis'])],
        ]);

        Medicine::create($data);

        return redirect()
            ->route('medical-orders.index')
            ->with('success', 'Medical order created successfully.');
    }

    public function edit(Medicine $medicalOrder)
    {
        return view('dashboard.medicine.edit', compact('medicalOrder'));
    }

    public function update(Request $request, Medicine $medicalOrder)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['medicine', 'rumor', 'analysis'])],
        ]);

        $medicalOrder->update($data);

        return redirect()
            ->route('medical-orders.index')
            ->with('success', 'Medical order updated successfully.');
    }
    public function destroy(Medicine $medicalOrder)
    {
        $medicalOrder->delete();

        return redirect()
            ->route('medical-orders.index')
            ->with('success', 'Medical order deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->withErrors('Please select at least one medical order to delete.');
        }

        $ids = array_values(array_filter($ids, fn ($id) => is_numeric($id)));

        if (count($ids) === 0) {
            return back()->withErrors('Invalid selection.');
        }

        Medicine::whereIn('id', $ids)->delete();

        return redirect()
            ->route('medical-orders.index')
            ->with('success', 'Selected medical orders deleted successfully.');
    }
}