<div class="row g-3">
<div class="col-md-6"><label class="form-label">Item</label><select name="item_id" class="form-select" required>@foreach($items as $item)<option value="{{ $item->id }}" @selected(old('item_id',$acquisition->item_id ?? '')==$item->id)>{{ $item->name }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Supplier</label><select name="supplier_id" class="form-select" required>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id',$acquisition->supplier_id ?? '')==$supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
<div class="col-md-4"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" value="{{ old('quantity',$acquisition->quantity ?? 1) }}" required></div>
<div class="col-md-4"><label class="form-label">Unit Cost</label><input type="number" step="0.01" name="unit_cost" class="form-control" value="{{ old('unit_cost',$acquisition->unit_cost ?? 0) }}" required></div>
<div class="col-md-4"><label class="form-label">Acquisition Date</label><input type="date" name="acquisition_date" class="form-control" value="{{ old('acquisition_date', isset($acquisition) ? $acquisition->acquisition_date : now()->toDateString()) }}" required></div>
<div class="col-12"><label class="form-label">Remarks</label><textarea name="remarks" class="form-control">{{ old('remarks',$acquisition->remarks ?? '') }}</textarea></div>
</div>
