<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Acquisition;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;

class AcquisitionController extends Controller
{
    public function index()
    {
        $acquisitions = Acquisition::with(['supplier','item'])->latest()->paginate(10);
        return view('acquisitions.index', compact('acquisitions'));
    }

    public function create()
    {
        return view('acquisitions.create', [
            'items' => Item::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required','exists:suppliers,id'],
            'item_id' => ['required','exists:items,id'],
            'quantity' => ['required','integer','min:1'],
            'unit_cost' => ['required','numeric','min:0'],
            'acquisition_date' => ['required','date'],
            'remarks' => ['nullable','string'],
        ]);

        $acquisition = Acquisition::create($data);
        $acquisition->item->increment('quantity', $acquisition->quantity);

        return redirect()->route('acquisitions.index')->with('success', 'Acquisition recorded successfully.');
    }

    public function edit(Acquisition $acquisition)
    {
        return view('acquisitions.edit', [
            'acquisition' => $acquisition,
            'items' => Item::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Acquisition $acquisition)
    {
        $data = $request->validate([
            'supplier_id' => ['required','exists:suppliers,id'],
            'item_id' => ['required','exists:items,id'],
            'quantity' => ['required','integer','min:1'],
            'unit_cost' => ['required','numeric','min:0'],
            'acquisition_date' => ['required','date'],
            'remarks' => ['nullable','string'],
        ]);

        $acquisition->update($data);
        return redirect()->route('acquisitions.index')->with('success', 'Acquisition updated successfully.');
    }

    public function destroy(Acquisition $acquisition)
    {
        $acquisition->delete();
        return redirect()->route('acquisitions.index')->with('success', 'Acquisition deleted successfully.');
    }
}
