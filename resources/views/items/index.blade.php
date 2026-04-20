@extends('layouts.admin', ['title' => $type === 'OPEX' ? 'OPEX Inventory' : 'CAPEX Inventory'])
@section('content')
@php
    $isOpex = $type === 'OPEX';
    $canManage = auth()->user()->canManageInventory();
@endphp
<div class="module-head">
    <div>
        <h2 class="module-title">{{ $isOpex ? 'OPEX Inventory' : 'CAPEX Inventory' }}</h2>
        <div class="module-note">{{ $isOpex ? 'Track supplies and stock levels for request processing' : 'Inventory monitoring only for NU Clark CAPEX assets' }}</div>
    </div>
    @if($canManage)
        <a href="{{ route('items.create', ['type' => $type]) }}" class="btn-primaryx"><i class="bi bi-plus-lg"></i> {{ $isOpex ? 'Add OPEX Item' : 'Add CAPEX Item' }}</a>
    @endif
</div>
<div class="surface p-3">
    <form method="GET" class="search-strip mb-3">
        <input type="hidden" name="type" value="{{ $type }}">
        <i class="bi bi-search text-muted"></i>
        <input class="search-input" name="search" value="{{ $search ?? '' }}" placeholder="{{ $isOpex ? 'Search by item name, code, brand, or specs...' : 'Search by asset name, code, or room...' }}">
        <div class="filter-box"><i class="bi bi-funnel text-muted"></i><select name="stock_filter" onchange="this.form.submit()"><option value="">All</option>@if($isOpex)<option value="available" @selected(($stockFilter ?? '') === 'available')>Available</option><option value="low" @selected(($stockFilter ?? '') === 'low')>Limited Stock</option>@if($canManage)<option value="out" @selected(($stockFilter ?? '') === 'out')>Out of Stock</option>@endif @else<option value="active" @selected(($stockFilter ?? '') === 'active')>Active Only</option>@endif</select></div>
        <button class="btn-primaryx small-btn" type="submit">Apply</button>
    </form>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>{{ $isOpex ? 'Item' : 'Asset' }}</th>
                    <th>Details</th>
                    <th>{{ $isOpex ? 'Category / Brand' : 'Category' }}</th>
                    <th>{{ $isOpex ? 'Price' : 'QR / Code' }}</th>
                    <th>{{ $isOpex ? 'Stock' : 'Assigned Room' }}</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td data-label="{{ $isOpex ? 'Item' : 'Asset' }}">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $item->display_image }}" alt="{{ $item->name }}" style="width:64px;height:64px;object-fit:cover;border-radius:12px;border:1px solid #cfd5de">
                                <div>
                                    <div style="font-weight:700;font-size:12px">{{ $item->name }}</div>
                                    <div class="tiny-2">{{ $item->item_code }}</div>
                                    <div class="tiny-2">Unit: {{ $item->unit }}</div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Details">{{ $item->specifications ?: ($item->description ?: '-') }}</td>
                        <td data-label="{{ $isOpex ? 'Category / Brand' : 'Category' }}">{{ $item->category->name ?? 'Uncategorized' }}<div class="tiny-2">{{ $item->brand ?: 'No brand' }}</div></td>
                        <td data-label="{{ $isOpex ? 'Price' : 'QR / Code' }}">{{ $isOpex ? '₱'.number_format((float) $item->unit_price, 2) : ($item->qr_value ?: $item->item_code) }}</td>
                        <td data-label="{{ $isOpex ? 'Stock' : 'Assigned Room' }}">@if($isOpex)<div style="font-weight:700">{{ $item->quantity }} {{ $item->unit }}</div><div class="tiny-2">Threshold: {{ $item->low_stock_threshold }}</div>@else {{ $item->room_assigned ?: 'Not assigned' }} @endif</td>
                        <td data-label="Status">
                            @if($isOpex)
                                <span class="status {{ $item->isOutOfStock() ? 'maintenance' : ($item->isLimitedStock() ? 'low' : 'available') }}">{{ $item->availability_status }}</span>
                            @else
                                <span class="status {{ $item->is_active ? 'available' : 'maintenance' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                            @endif
                        </td>
<td class="text-end" data-label="Actions">
    <a class="btn-soft small-btn" href="{{ route('items.show', $item) }}">
        <i class="bi bi-eye"></i>
    </a>

    @if(!$isOpex)
        <a class="btn-soft small-btn" target="_blank" href="{{ route('api.items.qr', ['id' => $item->id]) }}">
            <i class="bi bi-qr-code"></i>
        </a>
    @endif

    @if($canManage)
        <a class="btn-soft small-btn" href="{{ route('items.edit', $item) }}">
            <i class="bi bi-pencil"></i>
        </a>

        <form class="d-inline" method="POST" action="{{ route('items.destroy', $item) }}">
            @csrf
            @method('DELETE')
            <button class="btn-soft small-btn" onclick="return confirm('Delete item?')">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    @elseif($isOpex && auth()->user()->isRequestor())
        <a class="btn-approve small-btn" href="{{ route('requisitions.create', ['item_id' => $item->id]) }}">
            <i class="bi bi-cart-plus"></i>
        </a>
    @endif
</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">No {{ strtolower($isOpex ? 'opex items' : 'capex assets') }} available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $items->links() }}
</div>
@endsection
