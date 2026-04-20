<input type="hidden" name="item_type" value="{{ old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX')) }}">
@php($fixedType = old('item_type', $item->item_type ?? $type ?? request('type', 'CAPEX')))
@php($isCapex = $fixedType === 'CAPEX')
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">{{ $isCapex ? 'Asset Tag ID' : 'Item Code' }}</label>
    <input name="item_code" class="form-control" value="{{ old('item_code', $item->item_code ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">{{ $isCapex ? 'Asset Name / Model' : 'Name' }}</label>
    <input name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label class="form-label">Type</label>
    <input class="form-control" value="{{ $fixedType }}" readonly>
  </div>

  <div class="col-md-6">
    <label class="form-label">Category</label>
    <select name="category_id" class="form-select">
      <option value="">Select category</option>
      @foreach($categories as $category)
        <option value="{{ $category->id }}" @selected(old('category_id', $item->category_id ?? '') == $category->id)>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label">Or add new category</label>
    <input name="new_category" class="form-control" value="{{ old('new_category') }}" placeholder="Type new category here">
  </div>

  @if($isCapex)
    <div class="col-md-4">
      <label class="form-label">Date Acquired</label>
      <input type="date" name="acquisition_date" class="form-control" value="{{ old('acquisition_date', optional($item->acquisition_date ?? null)->format('Y-m-d') ?? '') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label">Department</label>
      <input name="assigned_department" class="form-control" value="{{ old('assigned_department', $item->assigned_department ?? '') }}" placeholder="Example: ITSO">
    </div>
    <div class="col-md-4">
      <label class="form-label">Asset Type</label>
      <input name="asset_type_name" class="form-control" value="{{ old('asset_type_name', $item->asset_type_name ?? '') }}" placeholder="Example: COMPUTER EQUIPMENT">
    </div>
    <div class="col-md-6">
      <label class="form-label">Assigned Room / Deployed Room</label>
      <input name="room_assigned" class="form-control" value="{{ old('room_assigned', $item->room_assigned ?? '') }}" placeholder="Example: 719">
    </div>
    <div class="col-md-6">
      <label class="form-label">Brand</label>
      <input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}">
    </div>
    <input type="hidden" name="quantity" value="{{ old('quantity', $item->quantity ?? 1) }}">
    <input type="hidden" name="unit" value="{{ old('unit', $item->unit ?? 'unit') }}">
    <input type="hidden" name="unit_price" value="{{ old('unit_price', $item->unit_price ?? 0) }}">
    <input type="hidden" name="availability_status" value="{{ old('availability_status', $item->availability_status ?? 'Available') }}">
    <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}">
  @else
    <div class="col-md-3"><label class="form-label">Quantity</label><input type="number" name="quantity" class="form-control" value="{{ old('quantity', $item->quantity ?? 0) }}" required></div>
    <div class="col-md-3"><label class="form-label">Unit</label><input name="unit" class="form-control" value="{{ old('unit', $item->unit ?? '') }}" required></div>
    <div class="col-md-3"><label class="form-label">Unit Price</label><input type="number" step="0.01" min="0" name="unit_price" class="form-control" value="{{ old('unit_price', $item->unit_price ?? 0) }}"></div>
    <div class="col-md-3"><label class="form-label">Brand</label><input name="brand" class="form-control" value="{{ old('brand', $item->brand ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Availability</label><select name="availability_status" class="form-select"><option value="Available" @selected(old('availability_status', $item->availability_status ?? 'Available')==='Available')>Available</option><option value="Limited Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Limited Stock')>Limited Stock</option><option value="Out of Stock" @selected(old('availability_status', $item->availability_status ?? '')==='Out of Stock')>Out of Stock</option></select></div>
    <div class="col-md-4"><label class="form-label">Low Stock Threshold</label><input type="number" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $item->low_stock_threshold ?? 0) }}" required></div>
  @endif

  <div class="col-md-6"><label class="form-label">Item Image</label><input type="file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp,image/*"><div class="tiny mt-1">Upload a JPG, PNG, or WEBP file up to 4MB.</div></div>
  <div class="col-md-6"><label class="form-label">Specifications</label><textarea name="specifications" class="form-control" rows="3">{{ old('specifications', $item->specifications ?? '') }}</textarea></div>
  @if(!empty($item?->display_image))
    <div class="col-md-6">
      <label class="form-label d-block">Current Image</label>
      <img src="{{ $item->display_image }}" alt="{{ $item->name ?? 'Item image' }}" style="width:140px;height:140px;object-fit:cover;border-radius:14px;border:1px solid #cfd5de;background:#fff">
      @if(!empty($item?->image_path))
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" name="remove_image" id="remove_image">
        <label class="form-check-label" for="remove_image">Remove current image</label>
      </div>
      @endif
    </div>
  @endif
  <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="4">{{ old('description', $item->description ?? '') }}</textarea></div>
  <div class="col-12 form-check ms-2"><input class="form-check-input" type="checkbox" value="1" name="is_active" id="is_active" @checked(old('is_active', $item->is_active ?? true))><label class="form-check-label" for="is_active">Active item</label></div>
</div>
