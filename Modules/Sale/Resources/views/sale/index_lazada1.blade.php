@extends('backEnd.master')

@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">Lazada {{__('sale.Sales')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="QA_section QA_section_heading_custom check_box_table">
                <div class="QA_table ">
                    <!-- table-responsive -->
                    <div class="">
                        <table class="table Crm_table_active3">
                            <thead>
                            <tr>
                                <th scope="col">{{__('sale.Sl')}}</th>
                                <th scope="col">Date</th>
                                <th scope="col">Order Number</th>
                                <th scope="col">Akun</th>
                                <th scope="col">Item Count</th>
                                <th scope="col">Price</th>
                                <th scope="col" width="5%">Status</th>
                                <th scope="col">{{__('common.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dataOrders as $key => $item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d/m/Y H:mm:s') }}</td>
                                    <td>{{ $item['order_number'] }}</td>
                                    <td>{{ $item['nama_akun'] }}</td>
                                    <td>{{ $item['items_count'] }}</td>
                                    <td>{{ $item['price'] }}</td>
                                    <td>
                                        @if ($item['statuses'][0] == 'INFO_ST_DOMESTIC_RETURN_WITH_LAST_MILE_3PL')
                                            Returned                                        
                                        @else
                                            {{ $item['statuses'][0] }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown CRM_dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false"> {{__('common.select')}}
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right"
                                                 aria-labelledby="dropdownMenu2">
                                                 <a href="javascript:void(0)" onclick="getDetails('{{ $item['order_number'] }}','{{ $item['token'] }}')"
                                                    class="dropdown-item" type="button">{{__('sale.Order Details')}}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade admin-query" id="shipping_details">
        <div class="modal-dialog modal_800px modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('sale.Shipping Info')}}</h4>
                    <button type="button" class="close " data-dismiss="modal">
                        <i class="ti-close "></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="">
                        <div class="row shipping_info">
                            <div class="col-12">
                                <h6>{{__('sale.Shipping Name')}} : <span class="view_shipping_name"></span></h6>
                                <h6>{{__('sale.Shipping Reference No')}} : <span class="view_shipping_ref"></span></h6>
                                <h6>{{__('sale.Date')}} : <span class="view_date"></span></h6>
                                <h6>{{__('sale.Received By')}} : <span class="view_received_by"></span></h6>
                                <h6>{{__('sale.Received Date')}} : <span class="view_received_date"></span></h6>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <div id="getDetails">
    </div>
    @include('backEnd.partials.delete_modal')
    @include('backEnd.partials.approve_modal')
@endsection
@push('scripts')
    <script>
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            setTimeout(function(){ window.location.reload(); }, 15000);
        }
        function modal_close() {
            $('#sale_info_modal').remove();
            $('.modal-backdrop').remove();
            // window.location.reload();
        }
       /* $(document).on('click', '.show_due', function () {
            alert(true);

        })*/

        function saleInfo(id) {
            let input = '<input type="hidden" name="id" value="' + id + '" "> ';
            $('#delivery_info').append(input);
        }

        function shippingInfo(id) {
            $.ajax({
                method: 'POST',
                url: '{{route('sale.shipping_info')}}',
                data: {
                    id: id,
                    _token: "{{csrf_token()}}",
                },
                success: function (result) {
                    $('.view_shipping_name').text(result.shipping_name);
                    $('.view_shipping_ref').text(result.shipping_ref);
                    $('.view_date').text(result.date);
                    $('.view_received_by').text(result.received_by);
                    $('.view_received_date').text(result.received_date);
                }
            })
        }

        function getDetails(f_order_number,f_token){
            $.post('{{ route('get_sale_details_lazada') }}', {_token:'{{ csrf_token() }}', ordernumber:f_order_number, token:f_token}, function(data){
                $('#getDetails').html(data);
                $('#sale_info_modal').modal('show');
                $('select').niceSelect();
            });
        }
    </script>
@endpush
