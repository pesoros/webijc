@extends('backEnd.master')
@section('mainContent')
    @include("backEnd.partials.alertMessage")
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex">
                            <h3 class="mb-0 mr-30">Reverse Order</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        <!-- Prefix  -->
                        <form action="" method="POST" enctype="multipart/form-data"
                              id="saleForm">
                            @method('PUT')
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                            for="">Action 
                                            *</label>
                                        <select class="primary_select mb-15 contact_type" name="action">
                                            <option selected disabled>-- Action --</option>
                                                <option value="instantRefund">instantRefund</option>
                                                <option value="agreeReturn">agreeReturn</option>
                                                <option value="agreeRefund">agreeRefund</option>
                                                <option value="refuseRefund">refuseRefund</option>
                                                <option value="confirmDelivery">confirmDelivery</option>
                                        
                                        </select>
                                        <span class="text-danger">{{$errors->first('customer_id')}}</span>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label"
                                               for="">Comment</label>
                                        <input type="text" class="primary_input_field"
                                               placeholder="Comment" name="comment"
                                               value="">
                                        <span class="text-danger">{{$errors->first('comment')}}</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @include('backEnd.partials.approve_modal')
@endsection
@push("scripts")
    <script type="text/javascript">
        var baseUrl = $('#app_base_url').val();

        function fromSubmit() {
            $('#saleForm').submit();
        }
        function preview(){
            var id = $('#sale_id').val();
            $.post('{{ route('sale.order.preview') }}', {_token:'{{ csrf_token() }}', id:id}, function(data){
                $('.view_modal').html(data);
                $('#PreviewEdit').modal('show');
                {{ Session::forget('previewSale') }};
            });
        }
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

        $(document).ready(function () {


            let delete_selector = $('.delete_product');

            if (delete_selector.length > 1) {
                delete_selector.show();
            } else {
                delete_selector.hide();
            }

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

            $(document).on('click', '.new_delete_product', function () {
                var whichtr = $(this).closest("tr");
                var id = $(this).data('id');
                let quantity = $('.quantity' + id).val();
                whichtr.remove();
                productDiscount();
                totalQuantity();
                SubTotal();
                grandTotal();
                billingInfo();

                $.ajax({
                    method: "POST",
                    url: "{{route('item.delete')}}",
                    data: {
                        id: id,
                        quantity: quantity,
                        _token: "{{csrf_token()}}"
                    },
                    success: function (result) {
                        toastr.success(result);
                    }
                })
            });

        });

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
                    $('.last_sale_price').text('$' + result.total_amount);
                    $('.last_sale_invoice').text(result.invoice_no);
                    if (result.invoice_no)
                        $('.sale_details').show()
                    else
                        $('.sale_details').hide();
                }
            })
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
                let amount = $(this).text();
                total_amount += parseFloat(amount);
            });
            $('.total_sub_total').val(total_amount);
        }

        function grandTotal() {
            let amount = parseFloat($('.total_sub_total').val());
            let discount = parseFloat($('.product_discounts').val());
            let tax = parseFloat($('.product_tax_input').val());
            if (isNaN(tax))
                tax = 0;
            let final_amount = parseFloat((amount + tax) - discount).toFixed(2);
            $('.total_price').val(final_amount);
        }

        $(document).on('input', '.tax', function () {
            let taxParcentage = parseFloat($(this).val());
            let tr = $(this).parent().parent();
            let productPrice =  parseFloat(tr.find('.product_price ').val());
            let discount =  parseFloat(tr.find('.discount ').val());
            let qty =  parseInt(tr.find('.quantity').val());
            let netSubTotal = (( productPrice - discount) * qty) * taxParcentage /100;
            $(this).attr('net-sub-total',netSubTotal)
            productTax(1,2)
        });

        $(document).on('change','.discount_type',function(){
            billingInfo()
        });



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
            let min_price = $(".product_min_price_" + type + id).val();
            if (price < min_price)
                $(".product_price_" + type + id).addClass('red_border');
            else
                $(".product_price_" + type + id).removeClass('red_border');
            productSubTotal(id, type);
            productTax(id, type);
            productDiscount();
            totalQuantity();
            SubTotal();
            grandTotal();
            billingInfo();
        }

        function addQuantity(id, type) {
            let quantity = $('.quantity_' + type + id).val();
            let house = $('.house').val();
            let datatype = $('.quantity_' + type + id).data('type');

            $.ajax({
                method: 'POST',
                url: "{{route('check.quantity')}}",
                data: {
                    id: id,
                    type: datatype,
                    house: house,
                    quantity: quantity
                },
                success: function (result) {
                    if (result.stock){
                        toastr.error(result.msg);
                        $('.quantity_' + type + id).val(result.stock);
                    }
                    else {
                        productSubTotal(id, type);
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
            productSubTotal(id, type);
            totalQuantity();
            productTax(id, type);
            productDiscount();
            SubTotal();
            grandTotal();
            billingInfo();
        }

        function addTax(id, type) {
            productSubTotal(id, type);
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

        function selectComboProduct(el) {
            let url = 'purchase/combo_product_add';
            $.ajax({
                method: 'POST',
                url: "{{url('/')}}" + '/' + url,
                data: {
                    id: el,
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
    </script>
@endpush
