@extends('backEnd.master')
@section('mainContent')
    @php
        if (Modules\Sale\Entities\Sale::latest()->first()) {
            $aid = Modules\Sale\Entities\Sale::latest()->first()->id + 1;
        }else {
            $aid = 1;
        }
    @endphp
    <div class="row justify-content-center">
        <div class="col-6">
            <div class="box_header common_table_header"> 
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">Lazada {{__('sale.Sales')}}</h3>
                </div>
            </div>
        </div>
        <div class="col-6" style="margin-bottom: 15px;">
            {{-- <div class="primary_datepicker_input">
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
            </div> --}}

            <div class="float-lg-right float-none pos_tab_btn justify-content-end packeddiv" style="display: none">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" onclick="packedChecked()" role="tab"
                           data-toggle="tab">Pindah Packed <span class="countspan diterima"></span></a>
                    </li>
                </ul>
            </div>

            <div class="float-lg-right float-none pos_tab_btn justify-content-end rtsdiv" style="display: none">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" onclick="rtsChecked()" role="tab"
                           data-toggle="tab">Pindah RTS <span class="countspan diterima"></span></a>
                    </li>
                </ul>
            </div>

            <div class="float-lg-right float-none pos_tab_btn justify-content-end picklistdiv">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" onclick="picklistChecked()" role="tab"
                           data-toggle="tab">Pick List <span class="countspan diterima"></span></a>
                    </li>
                </ul>
            </div>

            <div class="float-lg-right float-none pos_tab_btn justify-content-end printdiv">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" onclick="printChecked()" role="tab"
                           data-toggle="tab">Shipping Label <span class="countspan diterima"></span></a>
                    </li>
                </ul>
            </div>
            
            <div class="float-lg-right float-none pos_tab_btn justify-content-end">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0)" id="CheckAll" role="tab"
                           data-toggle="tab">Centang Semua <span class="countspan diterima"></span></a>
                    </li>
                </ul>
            </div>


        </div>  
    </div>
    <div class="row">
        <!-- Start Sms Details -->
        <div class="col-lg-12 student-details">
            <ul class="nav nav-tabs tab_column border-0" role="tablist">
                {{-- <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0)" onclick="getLazadaList('unpaid')" role="tab" 
                        data-toggle="tab">Belum Dibayar <span class="countspan belumbayar"></span></a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0)" onclick="getLazadaList('pending')" role="tab"
                       data-toggle="tab">Order Masuk <span class="countspan ordermasuk"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('packed')" role="tab"
                       data-toggle="tab">Siap Packing <span class="countspan siappacking"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('ready_to_ship')" role="tab"
                       data-toggle="tab">Siap Diambil <span class="countspan siapdiambil"></span></a>
                </li>
                {{-- <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('shipped')" role="tab"
                       data-toggle="tab">Dalam Pengiriman <span class="countspan dalampengiriman"></span></a>
                </li> --}}
                {{-- <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0)" onclick="getLazadaList('delivered')" role="tab"
                       data-toggle="tab">Diterima <span class="countspan diterima"></span></a>
                </li> --}}

            </ul>
            <div class="tab-content" style="text-align: center !important;">  
                
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
        let statusState = 'unpaid'
        getLazadaList('pending')
        function printDiv(divName) {
            var printContents = document.getElementById(divName).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            setTimeout(function(){ window.location.reload(); }, 15000);
        }
        function orderMovement(orderId, orderItemId, shippingType, token) {
            var dataId = $("input[name='combo_id[]']").map(function(){return $(this).val();}).get();
            var dataPrice= $("input[name='combo_price[]']").map(function(){return $(this).val();}).get();
            var dataQty= $("input[name='combo_quantity[]']").map(function(){return $(this).val();}).get();
            var dataTax= $("input[name='combo_tax[]']").map(function(){return $(this).val();}).get();
            var dataDiscount= $("input[name='combo_discount[]']").map(function(){return $(this).val();}).get();
            var data_total_amount = 0;
            for (let index = 0; index < dataPrice.length; index++) {
                data_total_amount = data_total_amount + parseInt(dataPrice[index]);
            }

            console.log(data_total_amount)

            let reqUrl = '';
            if (statusState == 'pending' || statusState == 'unpaid') {
                reqUrl = '{{route('setToPacked')}}';
                dataToSend = {
                    _token: "{{csrf_token()}}",
                    date: "{{date('m/d/Y')}}",
                    customer_id: "customer-2",
                    warehouse_id: "showroom-{{ session()->get('showroom_id') }}",
                    invoice_no: "{{ \Modules\Setup\Entities\IntroPrefix::find(3)->prefix . '-' . date('y') . date('m').Auth::id().$aid }}",
                    ref_no: orderId,
                    product: 1,
                    shipping_name: null,
                    total_discount: 0,
                    discount_type: 1,
                    sale_url: "{{urlShortener()}}",
                    payment_method: [
                        "cash-00"
                    ],
                    amount: [
                        null
                    ],
                    bank_name: [
                        null
                    ],
                    branch: [
                        null
                    ],
                    account_no: [
                        null
                    ],
                    account_owner: [
                        null
                    ],
                    combo_product_id: dataId,
                    combo_product_price: dataPrice,
                    combo_product_quantity: dataQty,
                    product_tax: dataTax,
                    combo_product_discount: dataDiscount,
                    item_amount: data_total_amount,
                    total_quantity: 2,
                    total_tax: "0-0",
                    total_discount_amount: 0,
                    shipping_charge: 0,
                    other_charge: 0,
                    total_amount: data_total_amount,
                    preview_status: 0,
                    notes: null,
                    send_mail: null,
                    customer: 3,
                    type: "customer",
                    serial_no: [null],
                    sale_type: 1,
                    shippingType: shippingType,
                    orderItemId: orderItemId,
                    token: token
                }
            } else if (statusState == 'packed') {
                reqUrl = '{{route('setToRts')}}';
                dataToSend = {
                    _token: "{{csrf_token()}}",
                    shippingType: shippingType,
                    orderItemId: orderItemId,
                    token: token
                }
            }

            $.ajax({
                method: 'POST',
                url: reqUrl,
                data: dataToSend,
                success: function (result) {
                    console.log(result)
                    getLazadaList(null);
                }
            })

        }

        function getDocumentLz(orderItemId, token, doctype) {
            let reqUrl = '{{route('getDocumentLz')}}';
            reqUrl += '?orderItemId=' + orderItemId;
            reqUrl += '&doctype=' + doctype;
            reqUrl += '&token=' + token;

            window.open(reqUrl, '_blank');
            return;
            // $.ajax({
            //     method: 'POST',
            //     url: reqUrl,
            //     data: {
            //         _token: "{{csrf_token()}}",
            //         orderItemId: orderItemId,
            //         doctype: 'shippingLabel',
            //         token: token,
            //     },
            //     success: function (result) {
            //         console.log('rsss1', reqUrl);
            //         console.log('rsss2', result);
            //     }
            // })

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
            orderNumber = "'"+ f_order_number +"'";
            orderToken = "'"+ f_token +"'";
            $.post('{{ route('get_sale_details_lazada') }}', {_token:'{{ csrf_token() }}', ordernumber:f_order_number, token:f_token}, function(data){
                $('#getDetails').html(data);
                $('#sale_info_modal').modal('show');
                $('select').niceSelect();    
                // if (statusState == 'unpaid') {
                //     $( ".order-action-spot" ).hide();
                //     $( ".order-invoice-spot" ).hide();
                //     $( ".order-shipping-spot" ).hide();
                // } else if (statusState == 'pending') {
                //     $( ".order-action-spot" ).show();
                //     $( ".order-invoice-spot" ).hide();
                //     $( ".order-shipping-spot" ).hide();
                // }  if (statusState == 'packed') {
                //     $( ".order-action-spot" ).show();
                //     $( ".order-invoice-spot" ).show();
                //     $( ".order-shipping-spot" ).show();
                // }  if (statusState == 'ready_to_ship') {
                //     $( ".order-action-spot" ).hide();
                //     $( ".order-invoice-spot" ).show();
                //     $( ".order-shipping-spot" ).show();
                // }  if (statusState == 'shipped') {
                //     $( ".order-action-spot" ).hide();
                //     $( ".order-invoice-spot" ).show();
                //     $( ".order-shipping-spot" ).show();
                // }  if (statusState == 'delivered') {
                //     $( ".order-action-spot" ).hide();
                //     $( ".order-invoice-spot" ).show();
                //     $( ".order-shipping-spot" ).show();
                // }  
                
            });
        }

        function getLazadaList(status = null) {
            $('.packeddiv').hide();
            $('.rtsdiv').hide();
            $('.picklistdiv').hide();
            $('.printdiv').hide();
            if (status) {
                statusState = status;
            } else {
                status = statusState;
            }
            $('.tab-content').empty();
            $('.tab-content').prepend('<img src="{{ asset('public/backEnd/img/spinner.gif') }}" style="margin-top: 90px;"/>');
            // let theDate = $('#saleDate').val();
            // var pieces = theDate.split('/');
            // var saleDate = pieces[2] + '-' + pieces[0] + '-' + pieces[1];
            $.ajax({
                method: 'POST',
                url: '{{route('sale.lazada_list')}}',
                data: {
                    status: status,
                    _token: "{{csrf_token()}}",
                },
                success: function (result) {
                    $('.tab-content').empty();
                    $('.tab-content').append(result.body);
                    $('.countspan').text("(" + result.countspan + ")");
                    countsnap()
                }
            })
        }

        function countsnap() {
            if (statusState == 'unpaid') {
                $('.belumbayar').show();
                $('.ordermasuk').hide();
                $('.siappacking').hide();
                $('.siapdiambil').hide();
                $('.dalampengiriman').hide();
                $('.diterima').hide();
            } else if (statusState == 'pending') {
                $('.belumbayar').hide();
                $('.ordermasuk').show();
                $('.siappacking').hide();
                $('.siapdiambil').hide();
                $('.dalampengiriman').hide();
                $('.diterima').hide();
                $('.packeddiv').show();
                $('.rtsdiv').hide();
                $('.picklistdiv').hide();
                $('.printdiv').hide();
            } else if (statusState == 'packed') {
                $('.belumbayar').hide();
                $('.ordermasuk').hide();
                $('.siappacking').show();
                $('.siapdiambil').hide();
                $('.dalampengiriman').hide();
                $('.diterima').hide();
                $('.packeddiv').hide();
                $('.rtsdiv').show();
                $('.picklistdiv').show();
                $('.printdiv').show();
            } else if (statusState == 'ready_to_ship') {
                $('.belumbayar').hide();
                $('.ordermasuk').hide();
                $('.siappacking').hide();
                $('.siapdiambil').show();
                $('.dalampengiriman').hide();
                $('.diterima').hide();
                $('.picklistdiv').show();
                $('.printdiv').show();
            } else if (statusState == 'shipped') {
                $('.belumbayar').hide();
                $('.ordermasuk').hide();
                $('.siappacking').hide();
                $('.siapdiambil').hide();
                $('.dalampengiriman').show();
                $('.diterima').hide();
            } else if (statusState == 'delivered') {
                $('.belumbayar').hide();
                $('.ordermasuk').hide();
                $('.siappacking').hide();
                $('.siapdiambil').hide();
                $('.dalampengiriman').hide();
                $('.diterima').show();
            }
        }
        function printChecked() {
            let checkedVals = [];
            let querystring = "";
            $("input:checkbox[class=editor-active]:checked").each(function () {
                checkedVals.push($(this).val());
            });

            for (let i = 0; i < checkedVals.length; i++) {
                const element = checkedVals[i];
                if (i !== 0) {
                    querystring += ','; 
                }
                querystring += element;
            }
            console.log(querystring)


            let reqUrl = '{{route('getDocumentMultiple')}}';
            reqUrl += '?orderItemId=' + querystring;
            reqUrl += '&doctype=shippingLabel';

            window.open(reqUrl, '_blank');
            return;
        }

        function picklistChecked() {
            let checkedVals = [];
            let querystring = "";
            $("input:checkbox[class=editor-active]:checked").each(function () {
                checkedVals.push($(this).val());
            });

            for (let i = 0; i < checkedVals.length; i++) {
                const element = checkedVals[i];
                if (i !== 0) {
                    querystring += ','; 
                }
                querystring += element;
            }
            console.log(querystring)


            let reqUrl = '{{route('getMultipleOrderItem')}}';
            reqUrl += '?orderItemId=' + querystring;
            reqUrl += '&doctype=shippingLabel';

            $.ajax({
                method: 'GET',
                url: reqUrl,
                success: function (result) {
                    console.log(result)
                    getLazadaList('rts');
                }
            })
            return;
        }

        function rtsChecked() {
            $('.tab-content').empty();
            $('.tab-content').prepend('<img src="{{ asset('public/backEnd/img/spinner.gif') }}" style="margin-top: 90px;"/>');
            
            let checkedVals = [];
            let querystring = "";
            $("input:checkbox[class=editor-active]:checked").each(function () {
                checkedVals.push($(this).val());
            });

            for (let i = 0; i < checkedVals.length; i++) {
                const element = checkedVals[i];
                if (i !== 0) {
                    querystring += ','; 
                }
                querystring += element;
            }
            console.log(querystring)


            let reqUrl = '{{route('rtsMultiple')}}';
            reqUrl += '?orderItemId=' + querystring;

            $.ajax({
                method: 'GET',
                url: reqUrl,
                success: function (result) {
                    console.log(result)
                    getLazadaList('packed');
                }
            })
            return;
        }

        function packedChecked() {
            $('.tab-content').empty();
            $('.tab-content').prepend('<img src="{{ asset('public/backEnd/img/spinner.gif') }}" style="margin-top: 90px;"/>');

            let checkedVals = [];
            let querystring = "";
            $("input:checkbox[class=editor-active]:checked").each(function () {
                checkedVals.push($(this).val());
            });

            console.log('ppp',checkedVals)

            for (let i = 0; i < checkedVals.length; i++) {
                const element = checkedVals[i];
                if (i !== 0) {
                    querystring += ','; 
                }
                querystring += element;
            }
            console.log(querystring)


            let reqUrl = '{{route('packedMultiple')}}';
            reqUrl += '?orderItemId=' + querystring;

            $.ajax({
                method: 'GET',
                url: reqUrl,
                success: function (result) {
                    console.log(result)
                    getLazadaList('pending');
                }
            })
            return;
        }

        let checkaAll = 0

        $("#CheckAll").click(function () {
            if (checkaAll == 0) {
                $('.editor-active').prop('checked', true);  
                checkaAll = 1              
            } else {
                $('.editor-active').prop('checked', false);
                checkaAll = 0
            }
        });
    </script>
@endpush
