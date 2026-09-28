@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') Products @endsection

@section('content')
@php $currency = config('ife.currency', 'RM'); @endphp
<div class="card card3 custom-font-small">
    <div class="card-body">
        <form method="GET" action="{{ route('product.index') }}" class="row g-2 align-items-end mb-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or SKU" value="{{ $request->search }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ $request->category === $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="active" class="form-select form-select-sm">
                    <option value="1" {{ $request->get('active', '1') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $request->get('active') === '0' ? 'selected' : '' }}>Inactive</option>
                    <option value="all" {{ $request->get('active') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary custom-button-shadow">@lang('translation.search')</button>
                <a href="{{ route('product.index') }}" class="btn btn-sm btn-light custom-button-shadow">@lang('translation.reset')</a>
                <a href="{{ route('product.create') }}" class="btn btn-sm btn-primary custom-button-shadow ms-auto">@lang('translation.add')</a>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="table table-sm custom-font-small" style="width:100%; border-collapse:collapse; box-shadow:1px 1px 3px 1px rgb(183, 183, 183);">
                <thead class="table-dark">
                    <tr>
                        <td>SKU</td>
                        <td>Name</td>
                        <td>Category</td>
                        <td>Unit</td>
                        <td style="text-align:right;">Unit Price ({{ $currency }})</td>
                        <td>Status</td>
                        <td style="text-align:right;">On orders</td>
                        <td style="width:150px;"></td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="border-bottom:1px solid #acacac;">{{ $product->sku }}</td>
                            <td style="border-bottom:1px solid #acacac;">
                                {{ $product->name }}
                                @if($product->description)
                                    <div class="text-muted custom-font-xsmall">{{ \Illuminate\Support\Str::limit($product->description, 90) }}</div>
                                @endif
                            </td>
                            <td style="border-bottom:1px solid #acacac;">{{ $product->category }}</td>
                            <td style="border-bottom:1px solid #acacac;">{{ $product->unit }}</td>
                            <td style="border-bottom:1px solid #acacac; text-align:right;">{{ number_format($product->unit_price, 2) }}</td>
                            <td style="border-bottom:1px solid #acacac;">{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                            <td style="border-bottom:1px solid #acacac; text-align:right;">{{ $product->order_lines_count }}</td>
                            <td style="border-bottom:1px solid #acacac; white-space:nowrap;">
                                <a class="btn btn-sm btn-primary custom-button-shadow" href="{{ route('product.edit', $product->id) }}">@lang('translation.edit')</a>
                                <form method="POST" action="{{ route('product.delete') }}" class="d-inline"
                                      onsubmit="return confirm('{{ $product->order_lines_count > 0 ? 'This product is on existing orders, so it will be deactivated instead of deleted. Continue?' : 'Delete ' . $product->sku . '?' }}');">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-sm btn-danger custom-button-shadow">@lang('translation.delete')</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted py-3">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-2">{{ $products->links() }}</div>
    </div>
</div>
@endsection
