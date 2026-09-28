{{--
    Outlet activity on the lead view: visit history (IFE reports linked to
    this outlet), order history, and - for Admins / Managers - a read-only
    copy of the rep's "Recommended" tab.
    Params: $customerDetail (with visits.createdBy, orders.lines.product,
            orders.createdBy loaded), $recommendation (array|null)
--}}
@php
    $currency  = config('ife.currency', 'RM');
    $canOrder  = Auth::guard('web')->user()->can('record_order');
    $canCancel = Auth::guard('web')->user()->can('manage_order');
@endphp

<style>
    .oa-head { padding:5px; background-color:rgb(13, 114, 158); color:white; }
    .oa-table { width:100%; border-collapse:collapse; box-shadow:1px 1px 3px 1px rgb(183, 183, 183); }
    .oa-table td { border-bottom:1px solid #acacac; vertical-align:top; }
    .oa-pill { display:inline-block; padding:0 10px; border-radius:25px; color:white; font-size:0.75rem; }
    .oa-gap { background:#0b7a3b; }
    .oa-topup { background:#b36b00; }
    .oa-cancelled { background:#8e8e93; }
    .oa-why { background:#f3f6ff; border-left:3px solid #556ee6; padding:8px 12px; white-space:pre-line; }
</style>

{{-- Visit history --}}
<div class="mt-3">
    <div class="oa-head">Visit history <span class="custom-font-xsmall">({{ $customerDetail->visits->count() }})</span></div>
    <div class="m-2" style="overflow-x:auto;">
        @if($customerDetail->visits->isEmpty())
            <div class="text-muted custom-font-small py-2">No visits recorded at this outlet yet. Visits filed from the app show up here.</div>
        @else
            <table class="table table-sm custom-font-small oa-table">
                <thead class="table-dark">
                    <tr>
                        <td>Date</td>
                        <td>Visited by</td>
                        <td>Status</td>
                        <td>Summary</td>
                        <td>Next follow-up</td>
                        <td style="width:30px"></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerDetail->visits as $visit)
                        <tr>
                            <td style="white-space:nowrap;">{{ $visit->created_at->format('j M Y, g:i A') }}</td>
                            <td>{{ optional($visit->createdBy)->name }}</td>
                            <td>{{ $visit->status }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($visit->problem_description, 160) }}</td>
                            <td style="white-space:nowrap;">
                                {{ $visit->next_followup_date ? \Illuminate\Support\Carbon::parse($visit->next_followup_date)->format('j M Y') : '' }}
                                @if($visit->next_followup_plan)
                                    <div class="text-muted custom-font-xsmall" style="white-space:normal;">{{ \Illuminate\Support\Str::limit($visit->next_followup_plan, 80) }}</div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ifereport.view', ['id' => $visit->id]) }}" target="_blank" title="Open report">
                                    <i class="mdi mdi-clipboard-outline" style="font-size:20px;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Order history --}}
<div class="mt-3">
    <div class="oa-head d-flex justify-content-between align-items-center">
        <span>Order history <span class="custom-font-xsmall">({{ $customerDetail->orders->count() }})</span></span>
        @if($canOrder)
            <a href="{{ route('lead.orders.create', $customerDetail->id) }}" class="btn btn-sm btn-light py-0">+ Record order</a>
        @endif
    </div>
    <div class="m-2" style="overflow-x:auto;">
        @if($customerDetail->orders->isEmpty())
            <div class="text-muted custom-font-small py-2">No orders recorded for this outlet yet.</div>
        @else
            <table class="table table-sm custom-font-small oa-table">
                <thead class="table-dark">
                    <tr>
                        <td>Order</td>
                        <td>Date</td>
                        <td>Products</td>
                        <td style="text-align:right;">Total ({{ $currency }})</td>
                        <td>Recorded by</td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customerDetail->orders as $order)
                        <tr style="{{ $order->isCancelled() ? 'opacity:0.6;' : '' }}">
                            <td style="white-space:nowrap;">
                                {{ $order->order_no }}
                                @if($order->isCancelled())
                                    <span class="oa-pill oa-cancelled">Cancelled</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">{{ $order->order_date->format('j M Y') }}</td>
                            <td>
                                @foreach($order->lines as $line)
                                    <div>{{ rtrim(rtrim(number_format($line->quantity, 2), '0'), '.') }} {{ optional($line->product)->unit }} &times; {{ optional($line->product)->name }}</div>
                                @endforeach
                                @if($order->remark)
                                    <div class="text-muted custom-font-xsmall">{{ $order->remark }}</div>
                                @endif
                            </td>
                            <td style="text-align:right; white-space:nowrap;">{{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ optional($order->createdBy)->name }}</td>
                            <td style="white-space:nowrap;">
                                @if($canCancel && !$order->isCancelled())
                                    <form method="POST" action="{{ route('lead.orders.cancel', $order->id) }}"
                                          onsubmit="return confirm('Cancel {{ $order->order_no }}? It stays on the history but stops counting as a purchase.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Recommended products (read-only copy for Admins / Managers) --}}
@if($recommendation !== null)
<div class="mt-3 mb-3">
    <div class="oa-head">Suggested Orders <span class="custom-font-xsmall">(what the rep sees on the outlet screen)</span></div>
    <div class="m-2">
        @if($recommendation['status'] !== 'ready')
            <div class="text-muted custom-font-small py-2">
                We need more information to give you suggestions. Order history and outlet details from similar locations help us learn.
            </div>
        @else
            <div class="custom-font-small mb-2">
                Based on the {{ $recommendation['similar_outlets'] }} most similar outlets.
                {{ $recommendation['gap_count'] }} product{{ $recommendation['gap_count'] === 1 ? '' : 's' }} not ordered yet;
                estimated extra value <b>{{ $currency }} {{ number_format($recommendation['estimated_monthly_value'], 2) }}</b> a month.
            </div>
            <div style="overflow-x:auto;">
                <table class="table table-sm custom-font-small oa-table">
                    <thead class="table-dark">
                        <tr>
                            <td>Product</td>
                            <td></td>
                            <td style="text-align:right;">Similar outlets buying</td>
                            <td style="text-align:right;">Suggested / month</td>
                            <td style="text-align:right;">Currently / month</td>
                            <td style="text-align:right;">Value / month ({{ $currency }})</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recommendation['items'] as $item)
                            <tr>
                                <td>{{ $item['name'] }} <span class="text-muted custom-font-xsmall">{{ $item['sku'] }}</span></td>
                                <td>
                                    <span class="oa-pill {{ $item['status'] === 'gap' ? 'oa-gap' : 'oa-topup' }}">
                                        {{ $item['status'] === 'gap' ? 'Not ordered' : 'Top up' }}
                                    </span>
                                </td>
                                <td style="text-align:right;">{{ $item['buyers'] }} of {{ $item['neighbors_used'] }} ({{ (int) round($item['support'] * 100) }}%)</td>
                                <td style="text-align:right;">{{ $item['recommended_qty'] }} {{ $item['unit'] }}</td>
                                <td style="text-align:right;">{{ $item['current_qty'] }} {{ $item['unit'] }}</td>
                                <td style="text-align:right;">{{ number_format($item['est_monthly_value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($recommendation['explanation']['why'])
                <div class="oa-why custom-font-small mt-2">
                    <b>Why these?</b>
                    <div>{{ $recommendation['explanation']['why'] }}</div>
                    @if($recommendation['explanation']['opening_line'])
                        <div class="mt-2"><b>Opening line:</b> "{{ $recommendation['explanation']['opening_line'] }}"</div>
                    @endif
                    <div class="text-muted custom-font-xsmall mt-1">
                        {{ $recommendation['explanation']['source'] === 'bedrock' ? 'Written by Claude (Amazon Bedrock).' : 'Template explanation (AI not configured).' }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endif
