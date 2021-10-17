@php
                                                                    $subtotal = $sale->items->sum('price') * $sale->items->sum('quantity');
                                                                    $total_due = 0;
                                                                    $this_due = 0;
                                                                    $tax = 0;
                                                                    $discountProductTotal = 0;
                                                                    $subTotalAmount = 0;
                                                                    foreach ($sale->items as $product) {

                                                                        $prductDiscount = $product->price * $product->discount / 100;

                                                                        $tax +=(($product->price - $prductDiscount) * $product->quantity ) * $product->tax / 100;

                                                                        if ($product->discount > 0) {
                                                                            $discountProductTotal += $prductDiscount * $product->quantity;
                                                                        }
                                                                        $subTotalAmount += $product->price * $product->quantity;
                                                                    }
                                                                    $this_due = $sale->payable_amount - $sale->payments->sum('amount') - $sale->payments->sum('advance_amount') - $sale->payments->sum('return_amount');
                                                                    $discount = $sale->total_discount;
                                                                    $vat = (($sale->amount - $discount) * $sale->total_tax) / 100;
                                                                @endphp
                                                                @php
                                                                    $paid =0;
                                                                @endphp
<div id="printablePos" class="invoice_part_iner">
    <table class="invoice_table invoice_info_table">
        <h3 style="text-align: center; color: black">{{app('general_setting')->company_name}}</h3>
        <h5 style="text-align: center; color: black">{{app('general_setting')->country_name}}</h5>
        <p style="text-align: center;color: black">{{__('retailer.Phone')}}
            :{{app('general_setting')->phone}}
            , {{__('retailer.Email')}}
            : {{app('general_setting')->email}}</p>
        <tbody>
        <tr>
            <td><h6>{{$sale->invoice_no}}</h6></td>
            <td style="text-align: right;">
                <h6>{{__('sale.Invoice No')}}</h6></td>

        </tr>
        <tr>
            <td><h6>{{__('sale.Date')}}</h6></td>
            <td style="text-align: right;">
                <h6>{{$sale->date}}</h6>
            </td>
        </tr>
        @php
            $name = ($sale->customer_id != null) ? $sale->customer->name : $sale->agentuser->name;
            $mobile = ($sale->customer_id != null) ? $sale->customer->mobile : $sale->agentuser->agent->phone;
            $email = ($sale->customer_id != null) ? $sale->customer->email : $sale->agentuser->email;
        @endphp
        <tr>
            <td><h6>{{__('sale.Customer')}}</h6></td>
            <td style="text-align: right;"><h6>{{$name}}</h6>
            </td>
        </tr>
        {{--<tr>
            <td>
                <h6>{{__('sale.Customer')}} {{__('retailer.Phone')}}</h6>
            </td>
            <td style="text-align: right;"><h6>{{$mobile}}</h6></td>
        </tr>--}}
        </tbody>
    </table>
    <div class="table_title">
        <h3 style="font-size: 14px; text-transform: uppercase; text-align: center; color: black">{{ __('common.Invoice') }}</h3>
    </div>
    <table class="invoice_table pdf_table_1"
           style="margin-bottom: 0 !important;color: black">
        <thead>
        <tr>
            <th scope="col">{{__('common.No')}}</th>
            <th scope="col">{{__('common.Name')}}</th>
            <th scope="col">{{__('quotation.Qty')}}</th>
            <th scope="col" class="text-right">{{__('sale.Price')}}</th>
            <th scope="col" class="text-right">{{__('sale.Discount')}} (%)</th>
            <th scope="col" class="text-right">{{__('sale.Amount')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($sale->items as $key => $item)
            <tr>
                <td>{{$key+1}}</td>
                @php
                    $product =$item->productable->product ? $item->productable->product->product_name : $item->productable->name;
                @endphp
                <td>{{$product}}</td>
                <td>{{$item->quantity}}</td>
                <td class="text-right">{{$item->price}}</td>
                <td class="text-right">{{$item->discount}}</td>
                <td class="text-right">{{ single_price($item->sub_total)}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <hr>
    <table class="invoice_table dashed_table"
           style="margin-bottom: 10px;color: black">
        <tbody>
        <tr>
            <th class="w_70">{{__('sale.SubTotal')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($subTotalAmount)}}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('quotation.Product wise Total Discount')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($discountProductTotal)}}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Product Tax')}}:</th>
            <td style="text-align: right;">
                <span>{{single_price($tax)}}</span></td>
        </tr>

        <tr>
            <th class="w_70">{{__('sale.Grand Total')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($subTotalAmount - $discountProductTotal + $tax) }}</span></td>
        </tr>

        <tr>
            <th class="w_70">{{__('sale.Discount')}}:</th>
            <td style="text-align: right;">
                @if ($sale->total_discount > 0)
                    <span>-{{ single_price($sale->total_discount) }}</span></td>
                @else
                    <span>{{ single_price(0)}}</span></td>
                @endif
        </tr>
        <tr>
            <th class="w_70">{{__('quotation.Other Tax')}} ({{ $sale->total_tax }}%):</th>
            <td style="text-align: right;">
                <span>{{ single_price($vat) }}</span></td>
        </tr>
        @if($sale->shipping_charge > 0)
            <tr>
                <th class="w_70">{{__('purchase.Shipping Charge')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->shipping_charge) }}</span></td>
            </tr>
        @endif
        @if($sale->other_charge > 0)
            <tr>
                <th class="w_70">{{__('purchase.Other Charge')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->other_charge) }}</span></td>
            </tr>
        @endif
        <tr>
            <th class="w_70">{{__('sale.Total Amount')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount) }}</span></td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Paid Amount')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payments->sum('amount'))}}</span>
            </td>
        </tr>
        <tr>
            <th class="w_70">{{__('sale.Due')}}:</th>
            <td style="text-align: right;">
                <span>{{ single_price($sale->payable_amount - $sale->payments->sum('amount'))}}</span>
            </td>
        </tr>
        @if ($sale->payable_amount - ($sale->payments->sum('amount') + $sale->payments->sum('advance_amount')) < 0)
            <tr>
                <th class="w_70">{{__('purchase.Advance Amount')}}:</th>
                <td style="text-align: right;">
                    <span>{{ single_price($sale->payments->sum('amount') + $sale->payments->sum('advance_amount') - $sale->payable_amount)}}</span>
                </td>
            </tr>
        @endif
        </tbody>
    </table>
    <div style="text-align: center;">
        {!! DNS1D::getBarcodeSVG($sale->id, 'C39') !!}
    </div>
    <p style="text-align: center;"> {{app('general_setting')->terms_conditions}}</p>
    <p>Remarks:</p>
    <p style="text-align: center;">{{app('general_setting')->remarks_title}}</p>
    <span class="dashed-underline"></span>
    <p style="text-align: center;">{{app('general_setting')->remarks_body}}</p>
</div>