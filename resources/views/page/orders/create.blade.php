@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') Record Order @endsection

@section('content')
@php
    $currency = config('ife.currency', 'RM');
    $oldLines = old('lines', [['product_id' => '', 'quantity' => '']]);
@endphp

<div class="card card3 custom-font-small">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3">
            <div style="padding:5px; background-color:rgb(13, 114, 158); color:white;">Outlet</div>
            <div class="m-2">
                <b>{{ $lead->business_name ?: $lead->name }}</b>
                @if($lead->business_name) <span class="text-muted">({{ $lead->name }})</span> @endif
                @if($lead->customer_id) &middot; Customer ID {{ $lead->customer_id }} @endif
            </div>
        </div>

        @if($products->isEmpty())
            <div class="alert alert-warning">There are no active products in the catalogue yet. An Admin can add them under Admin &rsaquo; Products.</div>
        @else
        <form method="POST" action="{{ route('lead.orders.store', $lead->id) }}" id="order-form">
            @csrf
            <div class="row mb-2">
                <div class="col-md-3">
                    <label class="custom-font-xsmall"><b>Order date</b> :</label>
                    <input type="date" name="order_date" class="form-control form-control-sm" max="{{ now()->toDateString() }}"
                           value="{{ old('order_date', now()->toDateString()) }}">
                </div>
            </div>

            <div style="padding:5px; background-color:rgb(13, 114, 158); color:white;">Products</div>
            <div class="m-2" style="overflow-x:auto;">
                <table class="table table-sm custom-font-small mb-1" id="lines-table">
                    <thead class="table-dark">
                        <tr>
                            <td style="min-width:260px;">Product</td>
                            <td style="width:120px;">Quantity</td>
                            <td style="width:140px; text-align:right;">Unit price ({{ $currency }})</td>
                            <td style="width:140px; text-align:right;">Line total ({{ $currency }})</td>
                            <td style="width:40px;"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oldLines as $i => $line)
                            <tr class="order-line">
                                <td>
                                    <select name="lines[{{ $i }}][product_id]" class="form-select form-select-sm line-product">
                                        <option value="">-- Select product --</option>
                                        @foreach($products->groupBy(fn ($p) => $p->category ?: 'Other') as $category => $items)
                                            <optgroup label="{{ $category }}">
                                                @foreach($items as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}" data-unit="{{ $product->unit }}"
                                                        {{ (string) ($line['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }} ({{ $product->sku }})
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.01" min="0.01" name="lines[{{ $i }}][quantity]" class="form-control line-qty"
                                               value="{{ $line['quantity'] ?? '' }}">
                                        <span class="input-group-text line-unit"></span>
                                    </div>
                                </td>
                                <td class="line-price" style="text-align:right;"></td>
                                <td class="line-total" style="text-align:right;"></td>
                                <td><button type="button" class="btn btn-sm btn-link text-danger p-0 remove-line" title="Remove"><i class="fas fa-times"></i></button></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;"><b>Total</b></td>
                            <td style="text-align:right;"><b id="order-total">0.00</b></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-line">+ Add product</button>
                <div class="text-muted custom-font-xsmall mt-1">Prices come from the catalogue when the order is saved.</div>
            </div>

            <div class="row mt-2">
                <div class="col-md-12">
                    <label class="custom-font-xsmall"><b>Remark</b> :</label>
                    <textarea name="remark" rows="2" maxlength="1000" class="form-control form-control-sm">{{ old('remark') }}</textarea>
                </div>
            </div>

            <div class="form-actions mt-3">
                <div class="custom_float">
                    <a href="{{ route('lead.view', $lead->id) }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                    <button type="submit" class="btn btn-primary custom-button-shadow" style="width:120px">Save order</button>
                </div>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
    (function () {
        var nextIndex = {{ count($oldLines) }};
        var $body     = $('#lines-table tbody');
        var template  = $body.find('tr.order-line').first().clone();

        function money(value) {
            return (Math.round(value * 100) / 100).toFixed(2);
        }

        function recalc() {
            var total = 0;
            $body.find('tr.order-line').each(function () {
                var $row   = $(this);
                var $opt   = $row.find('.line-product option:selected');
                var price  = parseFloat($opt.data('price')) || 0;
                var qty    = parseFloat($row.find('.line-qty').val()) || 0;
                var hasSel = $opt.val() !== '';
                $row.find('.line-unit').text(hasSel ? ($opt.data('unit') || '') : '');
                $row.find('.line-price').text(hasSel ? money(price) : '');
                $row.find('.line-total').text(hasSel && qty > 0 ? money(price * qty) : '');
                if (hasSel && qty > 0) total += price * qty;
            });
            $('#order-total').text(money(total));
        }

        $('#add-line').on('click', function () {
            var $row = template.clone();
            $row.find('select').attr('name', 'lines[' + nextIndex + '][product_id]').val('');
            $row.find('input').attr('name', 'lines[' + nextIndex + '][quantity]').val('');
            nextIndex++;
            $body.append($row);
            recalc();
        });

        $body.on('click', '.remove-line', function () {
            if ($body.find('tr.order-line').length > 1) {
                $(this).closest('tr').remove();
            } else {
                $(this).closest('tr').find('select, input').val('');
            }
            recalc();
        });

        $body.on('change input', '.line-product, .line-qty', recalc);
        recalc();
    })();
</script>
@endsection
