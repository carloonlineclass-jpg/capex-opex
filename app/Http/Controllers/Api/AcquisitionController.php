<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemAcquisition;
use App\Models\Item;
use Illuminate\Http\Request;

class AcquisitionController extends Controller
{
    public function index() { return ItemAcquisition::with(['supplier','item'])->get(); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);
        $acquisition = ItemAcquisition::create($data);
        Item::findOrFail($data['item_id'])->increment('quantity', $data['quantity']);
        return $acquisition->load(['supplier','item']);
    }

    public function show(string $id) { return ItemAcquisition::with(['supplier','item'])->findOrFail($id); }

    public function update(Request $request, string $id)
    {
        $acquisition = ItemAcquisition::findOrFail($id);
        $acquisition->update($request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]));
        return $acquisition->load(['supplier','item']);
    }

    public function destroy(string $id) { ItemAcquisition::findOrFail($id)->delete(); return response()->json(['message' => 'Acquisition deleted']); }
}
