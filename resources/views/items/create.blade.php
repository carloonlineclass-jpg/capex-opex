@extends('layouts.admin', ['title' => ($type ?? request('type', 'CAPEX')) === 'OPEX' ? 'Add OPEX Item' : 'Add CAPEX Item'])
@section('content')
<div class="module-head"><div><h2 class="module-title">{{ ($type ?? request('type', 'CAPEX')) === 'OPEX' ? 'Add OPEX Item' : 'Add CAPEX Item' }}</h2><div class="module-note">Create a new inventory record</div></div></div>
<div class="form-shell"><form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">@csrf @include('items.form', ['item' => $item ?? null])<div class="mt-4 d-flex gap-2"><button class="btn-primaryx">Save Item</button><a href="{{ route('items.index', ['type' => $type ?? request('type')]) }}" class="btn btn-light small-btn" style="border:1px solid #c7cbd4">Cancel</a></div></form></div>
@endsection