@extends('layouts.master',['tmenu_part1' => ($tmenu_part1 ?? ''), 'tmenu_part2' => ($tmenu_part2 ?? ''), 'tmenu_part3' => ($tmenu_part3 ?? '')] )
@section('title') Products @endsection

@section('content')
@php $editing = $product->exists; @endphp
<div class="card card3 custom-font-small" style="border-radius:5px;">
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

        <form method="POST" action="{{ $editing ? route('product.update', $product->id) : route('product.store') }}">
            @csrf
            <div style="padding:5px; background-color:rgb(13, 114, 158); color:white;">Product Detail</div>
            <div class="m-2">
                <div class="row">
                    <div class="col-md-3" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>SKU</b> :</label><span style="padding:0 5px; color:red;">*</span>
                        <input type="text" name="sku" maxlength="50" class="form-control form-control-sm" value="{{ old('sku', $product->sku) }}" required>
                    </div>
                    <div class="col-md-6" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Name</b> :</label><span style="padding:0 5px; color:red;">*</span>
                        <input type="text" name="name" maxlength="150" class="form-control form-control-sm" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="col-md-3" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Category</b> :</label>
                        <input type="text" name="category" maxlength="50" class="form-control form-control-sm" list="product-categories"
                               value="{{ old('category', $product->category) }}" placeholder="e.g. Coffee Beans">
                        <datalist id="product-categories">
                            @foreach($categories as $category)
                                <option value="{{ $category }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Unit</b> :</label><span style="padding:0 5px; color:red;">*</span>
                        <input type="text" name="unit" maxlength="20" class="form-control form-control-sm" value="{{ old('unit', $product->unit) }}"
                               placeholder="kg, bottle, box, unit" required>
                    </div>
                    <div class="col-md-3" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Unit Price ({{ config('ife.currency', 'RM') }})</b> :</label><span style="padding:0 5px; color:red;">*</span>
                        <input type="number" step="0.01" min="0" name="unit_price" class="form-control form-control-sm"
                               value="{{ old('unit_price', $product->unit_price) }}" required>
                    </div>
                    <div class="col-md-3 d-flex align-items-end" style="padding-bottom:14px;">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (offered on orders and recommended)</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="padding-bottom:10px;">
                        <label class="custom-font-xsmall"><b>Description</b> :</label>
                        <textarea name="description" rows="3" maxlength="2000" class="form-control form-control-sm">{{ old('description', $product->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <div class="custom_float">
                    <a href="{{ route('product.index') }}" class="btn btn-light custom-button-shadow" style="width:100px">@lang('translation.back')</a>
                    <button type="submit" class="btn btn-primary custom-button-shadow" style="width:100px">@lang('translation.save')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
