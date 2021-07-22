@extends('backEnd.master')
@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="box_header common_table_header"> 
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">Lazada {{__('sale.Sales')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-2" style="margin-bottom: 15px;">
            <div class="primary_datepicker_input">
                <div class="no-gutters input-right-icon">
                    <div class="col">
                        <div class="">
                            <input placeholder="Date"
                                   class="primary_input_field primary-input date form-control"
                                   id="saleDate" type="text" name="saleDate"
                                   value="{{date('m/d/Y')}}" autocomplete="off"
                                   onchange="getLazadaList()">
                        </div>
                    </div>
                    <button class="" type="button">
                        <i class="ti-calendar" id="start-date-icon"></i>
                    </button>
                </div>
            </div>
        </div>  
    </div>
    <div class="row">
        <!-- Start Sms Details -->
        <div class="col-lg-12 student-details">
            <ul class="nav nav-tabs tab_column border-0" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0)" onclick="getLazadaList('unpaid')" role="tab" 
                        data-toggle="tab">Belum Dibayar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('pending')" role="tab"
                       data-toggle="tab">Untuk Dikemas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('ready_to_ship')" role="tab"
                       data-toggle="tab">Atur Pengiriman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('shipped')" role="tab"
                       data-toggle="tab">Dalam Pengiriman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('delivered')" role="tab"
                       data-toggle="tab">Diterima</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('canceled')" role="tab"
                       data-toggle="tab">Dibatalkan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('returned')" role="tab"
                       data-toggle="tab">Dikembalikan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('failed')" role="tab"
                       data-toggle="tab">Gagal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('lost_by_3pl')" role="tab"
                       data-toggle="tab">Hilang / Rusak</a>
                </li>
            </ul>
            <div class="tab-content" style="text-align: center !important;">
                <div role="tabpanel" class="tab-pane fade show active" id="saleList">
                    <div class="white-box mt-2">
                        <div class="row">
                            <div class="col-12 select_sms_services">
                                <div class="QA_section QA_section_heading_custom check_box_table mt-50">
                                    <div class="QA_table ">
                                        <table class="table Crm_table_active3">
                                            <thead>
                                            <tr>
                                                <th scope="col">{{__('sale.Sl')}}</th>
                                                <th scope="col">Date</th>
                                                <th scope="col">Order Number</th>
                                                <th scope="col">Akun</th>
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
@push("scripts")
    <script type="text/javascript">
        let statusState = ''
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

        function getLazadaList(status = null) {
            if (status) {
                statusState = status;
            }
            $('.tab-content').empty();
            $('.tab-content').append('<img src="{{ asset('public/backEnd/img/spinner.gif') }}" style="margin-top: 90px;"/>');
            let theDate = $('#saleDate').val();
            var pieces = theDate.split('/');
            var saleDate = pieces[2] + '-' + pieces[0] + '-' + pieces[1];

            $.ajax({
                method: 'POST',
                url: '{{route('sale.lazada_list')}}',
                data: {
                    status: status,
                    saleDate: saleDate,
                    _token: "{{csrf_token()}}",
                },
                success: function (result) {
                    $('.tab-content').empty();
                    $('.tab-content').append(result);
                }
            })
        }
    </script>
@endpush
