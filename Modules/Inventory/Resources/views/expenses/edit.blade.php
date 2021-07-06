@extends('backEnd.master')
@section('mainContent')
    <div id="add_payment">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{__("account.Update Expense Info")}}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="white_box_50px box_shadow_white">
                            <!-- Prefix  -->
                            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-3">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Date')}} *</label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input placeholder="Date" class="primary_input_field primary-input date form-control" id="startDate" type="text" name="date" value="{{ date('m/d/Y', strtotime($expense->date)) }}" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $account_type = $expense->voucher->transactions->last();
                                        $credit_accounts = $expense->voucher->transactions->where('type', 'Cr');
                                        $dedit_accounts = $expense->voucher->transactions->where('type', 'Dr');
                                    @endphp
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label"
                                                   for=""> {{__("account.Narration")}} </label>
                                            <input class="primary_input_field" name="narration" placeholder="{{__("account.Narration")}}" type="text" value="{{ $expense->voucher->narration }}">
                                            <span class="text-danger">{{$errors->first('narration')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <label class="primary_input_label text-center" for="" id="dynamic_text">Please Enter Your credit details</label>
                                <label class="h1 primary_input_label text-center gradient-color2" for="" id="alert_txt"></label>
                                <hr>
                                <div class="row form">
                                @if ($account_type->type == "Dr")
                                    @foreach ($credit_accounts as $key => $credit_account)
                                        <div class="col-lg-4">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('account.Payment To')}} *</label>
                                                <select class="primary_select mb-15 payment_to" name="sub_account_id[]" id="debit_account_id" required>
                                                    <option>Select one</option>
                                                    @foreach ($accounts as $key => $account)
                                                        <option value="{{ $account->id }}" @if ($credit_account->account_id == $account->id) selected @endif>{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{$errors->first('payment_from')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Amount')}} *</label>
                                                <input class="primary_input_field" name="sub_amount[]" placeholder="Amount" type="number" value="{{ $credit_account->amount }}">
                                                <span class="text-danger">{{$errors->first('sub_amount')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Narration')}} </label>
                                                <input class="primary_input_field" name="sub_narration[]" placeholder="Narration" type="text" value="{{ $credit_account->narration }}">
                                                <span class="text-danger">{{$errors->first('amount')}}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach ($dedit_accounts as $key => $dedit_account)
                                        <div class="col-lg-4">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('account.Payment To')}} *</label>
                                                <select class="primary_select mb-15 payment_to" name="sub_account_id[]" id="debit_account_id" required>
                                                    <option>Select one</option>
                                                    @foreach ($accounts as $key => $account)
                                                        <option value="{{ $account->id }}" @if ($dedit_account->account_id == $account->id) selected @endif>{{ $account->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{$errors->first('payment_from')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Amount')}} *</label>
                                                <input class="primary_input_field" name="sub_amount[]" placeholder="Amount" type="number" value="{{ $dedit_account->amount }}">
                                                <span class="text-danger">{{$errors->first('sub_amount')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Narration')}} </label>
                                                <input class="primary_input_field" name="sub_narration[]" placeholder="Narration" type="text" value="{{ $dedit_account->narration }}">
                                                <span class="text-danger">{{$errors->first('amount')}}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-lg-1">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('common.Action')}} </label>
                                        <button class="primary-btn btn-sm fix-gr-bg" id="add_payment_to_form"><i class="ti-plus"></i>{{__('account.Add')}}</button>
                                    </div>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__('account.Total Amount')}} </label>
                                            <input class="primary_input_field" name="sub_amounts" id="sub_amounts" type="text" value="{{ $expense->voucher->amount }}" readonly>
                                            <span class="text-danger">{{$errors->first('amount')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="submit_btn text-center ">
                                            <button class="primary-btn semi_large2 fix-gr-bg"><i class="ti-check"></i>{{__("common.Update")}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push("scripts")
    <script type="text/javascript">
        setInterval(function() {
            sum_amount();
        }, 3000);

        function sum_amount()
        {
            var sub_amounts = $("input[name='sub_amount[]']").map(function(){return $(this).val();}).get();
            var sum = 0;
            for (var i = 0; i < sub_amounts.length; i++) {
                sum = parseInt(sum) + parseInt(sub_amounts[i]);
            }

            $("#sub_amounts").val(sum);
        }

        function get_dynamic_text(){
            var account_type = $('#account_type').val();
            if (account_type == 'debit') {
                $("#dynamic_text").html("---- Please Enter Your Credit details ----");
            }else {
                $("#dynamic_text").html("---- Please Enter Your Debit details ----");
            }
        }

        $(document).ready(function(){
            var i = 0;
            $(document).on('click', '#add_payment_to_form', function(e){
                i++;
                e.preventDefault();
                $( ".form" ).append('<div class="col-lg-4 row_id_'+i+'">'+
                    '<div class="primary_input mb-15">'+
                        '<select class="primary_select mb-15 payment_to" name="sub_account_id[]" id="payment_to" required>'+
                            '<option>Select one</option>'+
                            '@foreach ($accounts as $key => $chartAccount)'+
                                '<option value="{{ $chartAccount->id }}">{{ $chartAccount->name }}</option>'+
                            '@endforeach'+
                        '</select>'+
                    '</div>'+
                '</div>'+
                '<div class="col-lg-3 row_id_'+i+'">'+
                    '<div class="primary_input mb-15">'+
                        '<input class="primary_input_field" name="sub_amount[]" placeholder="Amount" type="number" value="">'+
                    '</div>'+
                '</div>'+
                '<div class="col-lg-4 row_id_'+i+'">'+
                    '<div class="primary_input mb-15">'+
                        '<input class="primary_input_field" name="sub_narration[]" placeholder="Narration" type="text" value="">'+
                    '</div>'+
                '</div>'+
                '<div class="col-lg-1 row_id_'+i+'">'+
                    '<div class="primary_input mb-15">'+
                        '<button class="primary-btn btn-sm fix-gr-bg delete_payment_to_form" data-status="'+i+'"><i class="ti-trash"></i>Delete</button>'+
                    '</div>'+
                '</div>' );
                $('select').niceSelect();
            });

            $(document).on('click', '.delete_payment_to_form', function(e){
                e.preventDefault();
                var i = $(this).data('status');
                $('.row_id_'+i).remove();
            });
        });

    </script>
@endpush
