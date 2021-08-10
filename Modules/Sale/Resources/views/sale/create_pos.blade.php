@extends('backEnd.master')

@push('styles')
    <style>
       @import url('https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700&display=swap');

       .grid-wrapper {
           --auto-grid-min-size: 10rem;
           display: grid;
           grid-gap: 2rem;
           grid-template-columns: repeat(auto-fill, minmax(var(--auto-grid-min-size), 1fr));
           margin: 0;
           padding: 0;
           box-sizing: border-box;
           font-family: 'Montserrat', sans-serif;

       }

       .grid-wrapper li {
           background-color: rgba(138, 138, 162, 0.17);
           color: #ffffff;
           font-size: 24px;
           list-style-type: none;
           text-align: center;
           text-transform: capitalize;
           font-weight: 600;
           border-radius: 8%;
           height: 205px;
       }

       .main-container {
           margin: 0 auto;
           max-width: 1170px;
           padding: 2rem 1rem;
       }

       .pos-img {
            width: 100%;
            height: 151px;
            object-fit: cover;
            object-position: center;
            border-radius: 10%;
            margin-top: 10px;
        }

        .nametag {
            margin: 10px;
        }

        .nametag p {
            color: black
        }
        .img-overlay {
            position: relative;
            margin: 10px
        }

        .img-overlay-image {
            display: block;
        }

        .overlay {
            position: absolute;
            height: 100%;
            width: 100%;
            opacity: 0;
            transition: .5s ease;
            background-color: rgba(0,0,0,.5);
            border-radius: 15px;
        }

        .img-overlay:hover .overlay {
            opacity: 1;
        }

        .text {
            color: white;
            font-size: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            text-align: center;
            cursor: default;
        }

        *:focus {
            outline: none;
        }
        .txt-search-tm {
            border-bottom:3px solid #219ae2;
            padding-top:10px;
            padding-bottom:5x;
            margin-bottom:20px;
        }
        p.txt-search-title-tm {
            font-size:20px;
            font-weight:400;
            padding-top:7px;
            color:#219ae2;
        }
        .txt-search-tm i.material-icons {
            position:absolute;
            top:10px;
            right:15px;
            font-size:30px;
            color:#219ae2;
        }
        .txt-search-input-container-tm {
            padding-top:5px;
        }

        input.txt-search-input-tm {
            width:100%;
            border:none;
            font-size:20px;
            font-weight:300;
            color:#219ae2;
        }
        input.txt-search-input-tm::-webkit-input-placeholder {
            color: #aaaaaa;
        }

        input.txt-search-input-tm:-moz-placeholder { /* Firefox 18- */
            color: #aaaaaa; 
        }

        input.txt-search-input-tm::-moz-placeholder {  /* Firefox 19+ */
            color: #aaaaaa; 
        }

        input.txt-search-input-tm:-ms-input-placeholder {  
            color: #aaaaaa;
        }
        .txt-search-results-tm {
            width:100%;
        }


        .txt-columns-tm
        {   
            -moz-column-width: 11.5em; /* Firefox */
            -webkit-column-width: 11.5em; /* webkit, Safari, Chrome */
            column-width: 11.5em;
        }
        /*remove standard list and bullet formatting from ul*/
        .txt-columns-tm ul
        {
            margin: 0;
            padding: 0;
            list-style-type: none;
        }
        .txt-columns-tm ul li {
        text-align:left;
        }
        /* correct webkit/chrome uneven margin on the first column*/
        .txt-columns-tm ul li:first-child
        {
            margin-top:0px;
        }
        .grid-wrapper .fade-in-tm {
            animation-name: fadeIn;
            animation-duration: 1s;
            display: block;
        }
        .grid-wrapper .fade-out-tm {
            opacity:0;
            animation-name: fadeOut;
            animation-duration: 1s;
            height:0;
            overflow:hidden;
            display: none;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
        @keyframes fadeOut {
            from {
                opacity: 1;
                height:auto;
                overflow:hidden;
            }

            to {
                opacity: 0;
                
            }
        }
        .ntr {
            width: 80px;
        }
        .ntr input {
            width: 100%;
        }

        .pos-sumprice {
            margin-top: 30px;
        }
        .pricetag {
            float: right;
        }

    </style>
@endpush
@section('mainContent')
    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">{{ __('sale.Add Sale') }}</h3>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="sale_margin_price" class="sale_margin_price"
                       value="{{app('general_setting')->sale_margin_price}}">
                <div class="col-8">
                    <div class="white_box_30px box_shadow_white">
                        <div class="row">
                            <div class="col-md-8 txt-search-input-container-tm">
                            <input type="text" name="searchtext" value="" class="txt-search-input-tm" placeholder="Search a Product..." autocomplete="off"/>  
                            <i class="material-icons">search</i>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <h4 class="text-danger customer_due pb-3"
                                    style="display: none">{{__('sale.Previous Due')}}
                                    :<a target="_blank" href="{{route('due.invoice.list')}}" class="balance_due"></a>
                                </h4>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6 text-right">
                                <h4 class="text-danger customer_invoice pb-3"
                                    style="display: none">{{__('sale.Last Invoice')}}
                                    :<a href="javascript:void(0)" data-toggle="modal" onclick="invoiceDetail()"
                                        class="invoice_link"></a></h4>
                            </div>
                        </div>
                        
                        {{-- gridpos --}}
                        <section>
                            <div class="main-container">
                                <ul class="grid-wrapper">
                                    @foreach ($allProducts as $product)
                                        <li>
                                            <div class="img-overlay" onclick="clickCart('{{$product->product_id}}-{{ $product->product_type }}')">
                                                <img src="https://images.unsplash.com/photo-1511690656952-34342bb7c2f2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80"
                                                    class="pos-img img-overlay-image" alt="2013 Toyota Tacoma" id="itemImg">
                                                    <div class="overlay">
                                                        <div class="text"><i class="fas fa-shopping-cart"></i> Add</i></div>
                                                        </div>
                                            </div>
                                            <div class="nametag">
                                                <p>{{$product->product_name}}</p>
                                            </div>
                                            <div style="float: none; clear: both;"></div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </section>
                        {{-- gridpos end --}}
                    </div>
                </div>
                <div class="col-4">
                    <div class="white_box_30px box_shadow_white">
                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-6">
                                <h4 class="text-danger customer_due pb-3"
                                    style="display: none">{{__('sale.Previous Due')}}
                                    :<a target="_blank" href="{{route('due.invoice.list')}}" class="balance_due"></a>
                                </h4>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6 text-right">
                                <h4 class="text-danger customer_invoice pb-3"
                                    style="display: none">{{__('sale.Last Invoice')}}
                                    :<a href="javascript:void(0)" data-toggle="modal" onclick="invoiceDetail()"
                                        class="invoice_link"></a></h4>
                            </div>
                        </div>
                        
                        {{-- nota --}}
                        <section>
                            <div class="box_header common_table_header ">
                                <div class="main-title d-md-flex">
                                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">Point Of Sales</h3>
                                </div>
                            </div>
                            <div class="QA_section3 QA_section_heading_custom th_padding_l0 ">
                                <div class="QA_table">
                                    <!-- table-responsive -->
                                    <div class="table-responsive">
                                        <table class="table pt-0 shadow_none pb-0 ">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Product</th>
                                                    <th scope="col">Qty</th>
                                                    <th scope="col">Price</th>
                                                </tr>
                                            </thead>
                                           <tbody id="product_details">
                                                
                                           </tbody>
                                        </table>
                                        <table class="table pt-0 shadow_none pb-0 pos-sumprice">
                                            <tbody>
                                                <tr>
                                                    <td scope="col">Total Qty</td>
                                                    <td scope="col" class="pricetag">
                                                        <input class="primary_input_field total_quantity" type="number" value="0" />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td scope="col">Sub Total</td>
                                                    <td scope="col" class="pricetag">
                                                        <input class="primary_input_field total_sub_total" type="number" value="0" />
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                        {{-- nota end --}}


                        <div class="col-12 mt-20">
                            <div class="submit_btn text-center ">
                                <button class="primary-btn fix-gr-bg submit-button" onclick="fromSubmit()"><i
                                        class="ti-check"></i>{{__('common.Save')}}</button>
                                {{-- <button class="primary-btn fix-gr-bg submit-button" onclick="fromSubmitwithPreview()"><i
                                        class="ti-check"></i>{{__('quotation.Draft')}}</button> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" value="{{urlShortener()}}" class="url">

    <div class="view_modal">

    </div>
    <div class="invoice_details w-100">

    </div>
@endsection
@push("scripts")
    <script type="text/javascript">

        var baseUrl = $('#app_base_url').val();
        let credit_limit = 0;
        function productTax(id, type) {
            let product_tax = 0;
            $.each($('.tax'), function (index, value) {
                let amount = $(this).attr('net-sub-total');
                product_tax += parseFloat(amount);
            });
            if (product_tax > 0){
                $('.product_tax').show();
            }else{
                $('.product_tax').hide();
            }
            $('.product_tax_input').val(product_tax);
        }

            function calcutionTaxDiscount(tr){
                let taxParcentage = parseFloat(tr.find('.tax').val());
                let productPrice =  parseFloat(tr.find('.product_price ').val());
                let discount =  parseFloat(tr.find('.discount ').val());
                let qty =  parseInt(tr.find('.quantity').val());
                let discount_price = (productPrice * discount) / 100;
                let netSubTotal = (( productPrice - discount_price) * qty) * taxParcentage /100;
                tr.find('.tax').attr('net-sub-total',netSubTotal)
            }

           function productDiscount() {
                let product_discounts = 0;
                $.each($('.discount'), function (index, value) {
                    let amount = $(this).val();
                    let tr = $(this).parent().parent();
                    let qty =  parseInt(tr.find('.quantity').val());
                    let totalDiscount = amount * qty;
                    product_discounts += parseInt(totalDiscount);
                    calcutionTaxDiscount(tr);
                    productTax(1,2)

                });
                if (product_discounts > 0){
                    $('.product_discounts').show();
                }else{
                    $('.product_discounts').hide();
                }
                $('.product_discounts').val(product_discounts);
            }

        function fromSubmitWithMail()
        {
            var customer_id = $('.contact_type').val();
            if (customer_id != "") {
                $('.submit-button').prop('disabled', true);
                $('.send_mail').val(1);
                $('#saleForm').submit();
            }else {
                toastr.error("please Select Customer First");
            }
        }
        $(document).ready(function () {
            $(document).on('change', '.product_info', function () {
                let id = $(this).val();
                let customer = $('.contact_type').val();

                $.post('{{ route('sale.product_modal_for_select') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    customer: customer,
                }, function (data) {
                    if (data.product_type == "Variable") {
                        $('.view_modal').html(data.html);
                        $('#Item_Details').modal('show');
                    } else {
                        if (data.product_id != 1) {
                            $('#product_details').append(data.product_id);
                            $('select').niceSelect();
                            totalQuantity();
                            productDiscount();
                            totalQuantity();
                            SubTotal();
                            grandTotal();
                            billingInfo();
                            $('.last_price_td').show();
                        }
                    }
                    $('.charging_infos').show();
                });
            });

            $(document).on('click', '.delete_product', function () {
                var whichtr = $(this).closest("tr");
                var id = $(this).data('id');
                whichtr.remove();
                productDiscount();
                totalQuantity();
                SubTotal();
                grandTotal();
                billingInfo();

                $.ajax({
                    url: "{{route('item.session.delete')}}",
                    method: "POST",
                    data: {
                        id: id,
                        _toke: "{{csrf_token()}}"
                    },
                    success: function (result) {
                        toastr.success(result.success);
                    }
                })
            });

            $(document).on('change', '#paying_method', function () {
                let value = $(this).val();

                if (value == 'cheque')
                    $('.cheque').show()
                else
                    $('.cheque').hide()
            })
        });

        function fromSubmit() {
            var customer_id = $('.contact_type').val();
            if (customer_id != "") {
                $('.submit-button').prop('disabled', true);
                $('#saleForm').submit();
            }else {
                toastr.error("please Select Customer First");
            }
        }

        function fromSubmitwithPreview() {
            var customer_id = $('.contact_type').val();
            if (customer_id != "") {
                $('.submit-button').prop('disabled', true);
                $('.preview_status').val(1);
                $('#saleForm').submit();
            }else {
                toastr.error("please Select Customer First");
            }
        }

        function showDiv() {
            if ($('.payment_method').val().split('-')[0] == 'bank') {
                $('.amount_div').show();
                $('.bank_info_div').show();
                $('.amount_payment').removeAttr('disabled');
            } else if ($('.payment_method').val().split('-')[0] == 'cash') {
                $('.amount_div').show();
                $('.bank_info_div').hide();
                $('.amount_payment').removeAttr('disabled');
            } else {
                $('.amount_div').hide();
                $('.bank_info_div').hide();
                $('.amount_payment').attr('disabled', 'disabled');
            }
        }

        function productSubTotal(id, type) {
            let price = $(".product_price_" + type + id).val();
            let quantity = $('.quantity_' + type + id).val();
            let sub_total = price * quantity;
            $('.product_subtotal_' + type + id).text(sub_total);
        }

        function totalQuantity() {
            let total_quantity = 0;
            $.each($('.quantity'), function (index, value) {
                let amount = $(this).val();
                total_quantity += parseInt(amount);
            });

            if (total_quantity > 0 || !isNaN(total_quantity)) {
                $('.total_quantity').text(total_quantity);
                $('.total_quantity').val(total_quantity);
            } else {
                total_quantity = $('.total_quantity').text();
                $('.total_quantity').text(total_quantity);
                $('.total_quantity').val(total_quantity);
            }
        }

        function SubTotal() {
            let total_amount = 0;

            $.each($('.product_subtotal'), function (index, value) {
                let amount = $(this).val(); 
                total_amount += parseFloat(amount);
            });
            $('.total_sub_total').val(total_amount);
        }

        function grandTotal() {
            let amount = parseFloat($('.total_sub_total').val());
            let discount = 0;
            let tax = 0;
            if (isNaN(tax))
                tax = 0;
            let final_amount = parseFloat((amount + tax) - discount).toFixed(2);
            $('.total_price').val(final_amount);
        }

        function billingInfo() {
            //billing_info
            let total_discount = parseFloat($('.total_discount').val());
            let total_tax = parseFloat($('.total_tax').val());
            let shipping_charge = parseFloat($('.shipping_charge').val());
            let other_charge = parseFloat($('.other_charge').val());
            let total_amount = parseFloat($('.total_price').val());
            let calculated_total_tax = 0;
            let calculated_total_discount = 0;

            let discount_type = $('.discount_type').val();

            if (total_discount > 0) {
                $('.total_discount_tr').show();
                if (discount_type == 2) {
                    calculated_total_discount = parseFloat($(".total_price").val()) * total_discount / 100;
                } else {
                    calculated_total_discount = total_discount;
                }
            } else {
                $('.total_discount_tr').hide();
            }

            if (total_tax > 0) {
                calculated_total_tax = ((total_amount - calculated_total_discount) * total_tax) / 100;
            }
            $('.total_discount_amount').val(calculated_total_discount);

            let final_amount = parseFloat(((total_amount + calculated_total_tax + shipping_charge + other_charge)) - calculated_total_discount).toFixed(2);

            $('.total_amount').val(final_amount);
        }

        function priceCalc(id, type) {
            let price = $(".product_price_" + type + id).val();
            let quantity = $('.quantity_' + type + id).val();
            let sub_total = price * quantity;
            $('.product_subtotal_' + type + id).text(sub_total);
            let min_price = $(".product_min_price_" + type + id).val();
            if (price < min_price)
                $(".product_price_" + type + id).addClass('red_border');
            else
                $(".product_price_" + type + id).removeClass('red_border');
            productTax(id, type);
            productDiscount();
            totalQuantity();
            SubTotal();
            grandTotal();
            billingInfo();
        }

        function addQuantity(id, type) {
            let datatype = $('.quantity_' + type + id).data('type');
            let price = $(".product_price_" + type + id).val();
            let quantity = $('.quantity_' + type + id).val();
            let house = $('.house').val();

            $.ajax({
                method: 'POST',
                url: "{{route('check.quantity')}}",
                data: {
                    id: id,
                    type: datatype,
                    house: house,
                    quantity: quantity,
                    _token: "{{csrf_token()}}",
                },
                success: function (result) {
                    if (result.stock){
                        toastr.error(result.msg);
                        $('.quantity_' + type + id).val(result.stock);
                    }
                    else {
                        let sub_total = price * quantity;
                        $('.product_subtotal_' + type + id).text(sub_total);
                        let tr = $('.quantity_' + type + id).parent().parent();
                        calcutionTaxDiscount(tr);
                        totalQuantity();
                        productTax(id, type);
                        productDiscount();
                        SubTotal();
                        grandTotal();
                        billingInfo();
                    }
                }
            })
        }

        function addDiscount(id, type) {
            let price = $('.product_price_' + type + id).val();
            let quantity = $('.quantity_' + type + id).val();
            let sub_total = price * quantity;
            $('.product_subtotal_' + type + id).text(sub_total);
            totalQuantity();
            productTax(id, type);
            productDiscount();
            SubTotal();
            grandTotal();
            billingInfo();
        }

        function addTax(id, type) {
            let price = $('.product_price_' + type + id).val();
            let quantity = $('.quantity_' + type + id).val();
            let sub_total = price * quantity;
            $('.product_subtotal_' + type + id).text(sub_total);
            totalQuantity();
            productTax(id, type);
            productDiscount();
            SubTotal();
            grandTotal();
            billingInfo();
        }

        function selectProduct(el) {
            let url = 'sale/product_add';

            $.ajax({
                method: 'POST',
                url: "{{url('/')}}" + '/' + url,
                data: {
                    id: el,
                    customer: customer,
                    _token: "{{csrf_token()}}",
                },
                success: function (data) {
                    if (data == 1)
                        toastr.error('this item is already added');
                    else {
                        $('#product_details').append(data);
                        $('select').niceSelect();
                        totalQuantity();
                        productDiscount();
                        SubTotal();
                        grandTotal();
                        billingInfo();
                    }

                }
            })
        }
        function saleDetails() {
            let customer_id = $('.contact_type').val();

            $.ajax({
                method: 'POST',
                url: "{{route('customer.details')}}",
                data: {
                    customer_id: customer_id,
                    _token: "{{csrf_token()}}",
                },

                success: function (result) {
                    $('.customer_due').show();
                    $('.balance_due').text(' ' + result.due);
                    if (result.credit_limit)
                        credit_limit = parseFloat(result.credit_limit);
                    if (result.due > credit_limit) {
                        toastr.warning('Your due is greater than your credit limit');
                    }
                    if (result.url) {
                        $('.customer_invoice').show();
                        $('.last_price').show();
                        $('.invoice_link').text(' ' + result.invoice);
                    }
                }
            })
        }

        function deliveryInfo() {
            let value = $('#sale_type').val();
            if (value == '0') {
                $('.delivery_info').show();
            } else
                $('.delivery_info').hide();
        }

        function invoiceDetail() {
            let customer_id = $('.contact_type').val();

            $.ajax({
                method: 'POST',
                url: "{{route('get_sale_details')}}",
                data: {
                    customer_id: customer_id,
                    _token: "{{csrf_token()}}",
                },

                success: function (result) {
                    $('.invoice_details').html(result);
                    $('#sale_info_modal').modal('show');
                }
            })
        }

        function house() {
            let val = $('.house').val();

            $.ajax({
                method: 'POST',
                url: "{{route('check.existence')}}",
                data: {
                    _token: "{{csrf_token()}}",
                    val: val,
                },
                success: function (resutl) {
                    $('.product_info').html(resutl);
                    $('select').niceSelect('update');
                }
            })
        }

        let productInCart = [];

        function clickCart(productId) {
            idSplit = productId.split("-");
            let findPro = productInCart.find((product) => {
                return product.id === idSplit[0];
            });
            if (findPro) {
                for (let index = 0; index < productInCart.length; index++) {
                    if (productInCart[index]['id'] == idSplit[0]) {
                        numberQty = parseInt(productInCart[index]['qty']);
                        numberQty += 1;
                        productInCart[index]['qty'] = numberQty;
                        $('.qty_ntr_'+ idSplit[0]).val(numberQty);
                        let price = numberQty * productInCart[index]['price'];
                        $('.price_ntr_'+ idSplit[0]).val(price);
                        totalQuantity();
                        productDiscount();
                        totalQuantity();
                        SubTotal();
                    }
                }
            } else {
                let datas = [];
                $.post('{{ route('sale.product_modal_for_select_pos') }}', {
                    _token: '{{ csrf_token() }}',
                    id: productId,
                }, function (data) {
                    if (data.product_type == "Variable") {
                        $('.view_modal').html(data.html);
                        $('#Item_Details').modal('show');
                    } else {
                        if (data.product_id != 1) {
                            $('#product_details').append(data.product_id);
                            let price = $('.price_ntr_'+ idSplit[0]).val();
                            datas['id'] =  idSplit[0];
                            datas['qty'] =  1;
                            datas['price'] = price;
                            productInCart.push(datas);
                            // $('select').niceSelect();
                            totalQuantity();
                            productDiscount();
                            totalQuantity();
                            SubTotal();
                            // grandTotal();
                            // billingInfo();
                            // $('.last_price_td').show();

                            $( ".quantity" ).change(function() {
                                let idProd = this.id;
                                let valProd = $(this).val();
                                for (let index = 0; index < productInCart.length; index++) {
                                    if (productInCart[index]['id'] == idProd) {
                                        productInCart[index]['qty'] = valProd;
                                        $('.qty_ntr_'+ idSplit[0]).val(valProd);
                                        let price = valProd * productInCart[index]['price'];
                                        $('.price_ntr_'+ idSplit[0]).val(price);
                                    }
                                }
                            });
                        }
                    }
                    $('.charging_infos').show();
                });   
            }   

            // let id = $(this).val();

             
        }

        $(function(){
            $('.txt-search-input-tm').keyup(function(){
                var searchText = $(this).val().toUpperCase();
                $('.grid-wrapper > li').each(function(){
                    var currentLiText = $( this ).find( '.nametag p' ).text().toUpperCase();
                        showCurrentLi = currentLiText.indexOf(searchText) !== -1;
                        console.log(currentLiText)
                    if(showCurrentLi){
                    $(this).addClass('fade-in-tm').removeClass('fade-out-tm');
                    }else{
                    $(this).addClass('fade-out-tm').removeClass('fade-in-tm');
                    }
                });     
            });
        });
    </script>
@endpush
