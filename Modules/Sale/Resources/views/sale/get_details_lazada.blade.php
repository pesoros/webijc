
<div class="modal fade admin-query" id="sale_info_modal">
    <div class="modal-dialog modal_1000px modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __("purchase.Sales Detail's") }} - Order Number : {{ @$sale[0]['order_id'] }}</h4>
                <button type="button" class="close" onclick="modal_close()" data-dismiss="modal">
                    <i class="ti-close "></i>
                </button>
            </div>
            @php
                $setting = app('general_setting');
            @endphp
            <div class="modal-body">
                <div id="printableArea">
                    <div class="row mt-10">
                        <div class="col-12">
                            <div class="QA_section QA_section_modal QA_section_heading_custom">
                                <div class="QA_table ">
                                    <!-- table-responsive -->
                                    <div class="">
                                        <table class="table Crm_table_active3">
                                            <tr class="m-0 p-2">
                                                <th scope="col">No</th>
                                                <th scope="col">Lazada SKU</th>
                                                <th scope="col"  width="39%">{{__('product.Name')}}</th>
                                                <th scope="col">{{__('sale.Price')}}</th>
                                                <th scope="col">Url</th>
                                            </tr>
                                            @foreach($sale as $key=> $item)
                                                    <tr>
                                                        <td>{{$key++}}</td>
                                                        <td>{{$item['shop_sku']}}</td>
                                                        <td>{{$item['name']}}</td>
                                                        <td class="text-right">{{single_price($item['item_price'])}}</td>
                                                        <td><a href="{{$item['product_detail_url']}}" target="_blank">Page</a></td>
                                                    </tr>
                                            @endforeach
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="row text-center mt-20">
                    <div class="col-md-12 col-12">
                        <a href="javascript:void(0)" class="primary-btn fix-gr-bg mr-2"
                           onclick="printDiv('printableArea')">{{__('pos.Print')}}</a>
                        <a href="javascript:void(0)" onclick="modal_close()"
                           class="primary-btn fix-gr-bg mr-2"  data-dismiss="modal">{{__('common.Close')}}</a>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>
