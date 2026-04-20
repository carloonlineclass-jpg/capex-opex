<div class="row g-3">
<div class="col-md-6"><label class="form-label">Department Name</label><input name="name" class="form-control" value="{{ old('name',$department->name ?? '') }}" required></div>
<div class="col-md-6"><label class="form-label">Code</label><input name="code" class="form-control" value="{{ old('code',$department->code ?? '') }}" required></div>
<div class="col-md-6"><label class="form-label">CAPEX Limit</label><input type="number" name="capex_limit" class="form-control" value="{{ old('capex_limit',$department->capex_limit ?? 0) }}" required></div>
<div class="col-md-6"><label class="form-label">OPEX Limit</label><input type="number" name="opex_limit" class="form-control" value="{{ old('opex_limit',$department->opex_limit ?? 0) }}" required></div>
</div>
