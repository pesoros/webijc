@extends('backEnd.master')
@section('mainContent')
    @if(session()->has('message-success'))
        <div class="alert alert-success mb-25" role="alert">
            {{ session()->get('message-success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @elseif(session()->has('message-danger'))
        <div class="alert alert-danger">
            {{ session()->get('message-danger') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div id="add_payment">
        <section class="admin-visitor-area up_st_admin_visitor">
            <div class="container-fluid p-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="box_header">
                            <div class="main-title d-flex">
                                <h3 class="mb-0 mr-30">{{__("account.Money Transfer Info Update")}} - {{ $payment->tx_id }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="white_box_50px box_shadow_white">
                            <!-- Prefix  -->
                            <form action="{{ route('transfer_showroom.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Date')}} *</label>
                                            <div class="primary_datepicker_input">
                                                <div class="no-gutters input-right-icon">
                                                    <div class="col">
                                                        <div class="">
                                                            <input placeholder="Date" class="primary_input_field primary-input date form-control" id="startDate" type="text" name="date" value="{{ date('m/d/Y', strtotime($payment->date)) }}" autocomplete="off" required>
                                                        </div>
                                                    </div>
                                                    <button class="" type="button">
                                                        <i class="ti-calendar" id="start-date-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment From')}} *</label>
                                            <select class="primary_select mb-15 payment_from" name="voucher_type" id="payment_from_type" onchange="get_accounts()" required>
                                                <option value="1">{{ __('account.Cash Transfer') }}</option>
                                                <option value="2"@if ($payment->document != null) selected @endif>{{ __('account.Bank Transfer') }</option>
                                            </select>
                                            <span class="text-danger">{{$errors->first('voucher_type')}}</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for=""> {{__("account.Narration")}} </label>
                                            <input class="primary_input_field" name="debit_account_narration[]" placeholder="{{__("account.Narration")}}" type="text" value="{{ $payment->narration }}">
                                            <span class="text-danger">{{$errors->first('debit_account_narration')}}</span>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 payment_from_div">
                                        <div class="primary_input mb-15">
                                            <label class="primary_input_label" for="">{{__('account.Payment From Account')}} *</label>
                                            <div class="payment_from_account">
                                                @include('account::transfers.accounts', [
                                                    "update_account_list" => $payment_accounts->where('configuration_group_id', 2),
                                                    "selected_account_id" => $payment->transactions->where('type', 'Dr')->first()->account_id,
                                                ])
                                            </div>
                                            <span class="text-danger">{{$errors->first('payment_from')}}</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="payment_doc" id="payment_doc" @if ($payment->document != null) value="1" @else value="0" @endif>
                                    @if (isset($payment->document))
                                        <div class="col-lg-6 cheque_no_div_edit">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for=""> {{__('account.Cheque Number')}} *</label>
                                                <input class="primary_input_field" name="cheque_no" id="cheque_no_edit" placeholder="Cheque Number" type="text" value="{{ $payment->document->cheque_no }}" required>
                                                <span class="text-danger">{{$errors->first('cheque_no')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 cheque_date_div_edit">
                                            <div class="primary_input mb-15">
                                                <div class="primary_input mb-15">
                                                    <label class="primary_input_label" for="">{{__('account.Cheque Date')}} *</label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input placeholder="Date" class="primary_input_field primary-input date form-control" id="cheque_date_edit" type="text" name="cheque_date" value="{{ date('m/d/Y', strtotime($payment->document->cheque_date)) }}" autocomplete="off" required>
                                                                </div>
                                                            </div>
                                                            <button class="" type="button">
                                                                <i class="ti-calendar" id="start-date-icon"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 bank_name_div_edit">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for="">  {{__('account.Bank Name')}} *</label>
                                                <input class="primary_input_field" name="bank_name" id="bank_name_edit" placeholder="Bank Name" type="text" value="{{ $payment->document->bank_name }}" required>
                                                <span class="text-danger">{{$errors->first('bank_name')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 bank_branch_div_edit">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('account.Bank Branch')}} *</label>
                                                <input class="primary_input_field" name="bank_branch" id="bank_branch_edit" placeholder="Bank Branch" type="text" value="{{ $payment->document->bank_branch }}" required>
                                                <span class="text-danger">{{$errors->first('bank_branch')}}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-lg-6 cheque_no_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for=""> {{__('account.Cheque Number')}} *</label>
                                                <input class="primary_input_field" name="cheque_no" id="cheque_no" placeholder="Cheque Number" type="text" required>
                                                <span class="text-danger">{{$errors->first('cheque_no')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 cheque_date_div">
                                            <div class="primary_input mb-15">
                                                <div class="primary_input mb-15">
                                                    <label class="primary_input_label" for="">{{__('account.Cheque Date')}} *</label>
                                                    <div class="primary_datepicker_input">
                                                        <div class="no-gutters input-right-icon">
                                                            <div class="col">
                                                                <div class="">
                                                                    <input placeholder="Date" class="primary_input_field primary-input date form-control" id="cheque_date" type="text" name="cheque_date" autocomplete="off" required>
                                                                </div>
                                                            </div>
                                                            <button class="" type="button">
                                                                <i class="ti-calendar" id="start-date-icon"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 bank_name_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label"
                                                       for=""> {{__('account.Bank Name')}} *</label>
                                                <input class="primary_input_field" name="bank_name" id="bank_name" placeholder="Bank Name" type="text" required>
                                                <span class="text-danger">{{$errors->first('bank_name')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 bank_branch_div">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Bank Branch')}} *</label>
                                                <input class="primary_input_field" name="bank_branch" id="bank_branch" placeholder="Bank Branch" type="text" required>
                                                <span class="text-danger">{{$errors->first('bank_branch')}}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row form">
                                    @foreach ($payment->transactions->where('type', 'Cr') as $key => $debit_transaction)
                                        <div class="col-lg-6">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for="">{{__('Payment To')}} *</label>
                                                <select class="primary_select mb-15 payment_to" name="debit_account_id[]" id="debit_account_id" required>
                                                    <option>{{ __('common.Select one') }}</option>
                                                    @foreach ($chartAccounts as $key => $chartAccount)
                                                        <option value="{{ $chartAccount->id }}" @if ($debit_transaction->account_id == $chartAccount->id) selected @endif>{{ $chartAccount->name }}</option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger">{{$errors->first('debit_account_id')}}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="primary_input mb-15">
                                                <label class="primary_input_label" for=""> {{__('account.Amount')}} *</label>
                                                <input class="primary_input_field" name="debit_account_amount[]" placeholder="Amount" type="number" value="{{ $debit_transaction->amount }}">
                                                <span class="text-danger">{{$errors->first('debit_account_amount')}}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="submit_btn text-center ">
                                            <button class="primary-btn semi_large2 fix-gr-bg"><i class="ti-check"></i>{{__("common.Save")}}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="primary_input mb-15">
                                        <label class="primary_input_label" for=""> {{__('account.Total Amount')}} </label>
                                        <input class="primary_input_field" name="total_sum" id="total_sum" placeholder="0" type="text" value="0" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push("scripts")
    <script type="text/javascript">
        $(document).ready(function () {
            if ($('#payment_doc').val() == 1) {
                showDivEdit();
            }else {
                hideDiv();
            }
        });
        setInterval(function() {
            sum();
        }, 3000);

        function sum()
        {
            var values = $("input[name='debit_account_amount[]']").map(function(){return $(this).val();}).get();
            var sum = 0;
            for (var i = 0; i < values.length; i++) {
                sum = parseInt(sum) + parseInt(values[i]);
            }
            $('#total_sum').val(sum);
        }

        function get_accounts()
        {
            var account_cat_id = $('#payment_from_type').val();
            $.post('{{ route('get_accounts_for_payment') }}', {_token:'{{ csrf_token() }}', id:account_cat_id , transfer:1}, function(data){
                $('.payment_from_account').html(data);
                $('.payment_from_div').show();
                $('select').niceSelect();
            });
            if (account_cat_id == 1) {
                $("#bank_branch").attr('disabled', true);
                $("#bank_name").attr('disabled', true);
                $("#cheque_date").attr('disabled', true);
                $("#cheque_no").attr('disabled', true);
                if ($('#payment_doc').val() == 1) {
                    hideDivEdit();
                }else {
                    hideDiv();
                }
            }
            else if (account_cat_id == 2) {
                $("#bank_branch").removeAttr("disabled");
                $("#bank_name").removeAttr("disabled");
                $("#cheque_date").removeAttr("disabled");
                $("#cheque_no").removeAttr("disabled");
                if ($('#payment_doc').val() == 1) {
                    showDivEdit();
                }else {
                    showDiv();
                }
            }
        }

        function hideDiv()
        {
            $('.cheque_no_div').hide();
            $('.cheque_date_div').hide();
            $('.bank_name_div').hide();
            $('.bank_branch_div').hide();
        }

        function showDiv()
        {
            $('.cheque_no_div').show();
            $('.cheque_date_div').show();
            $('.bank_name_div').show();
            $('.bank_branch_div').show();
        }

        function showDivEdit()
        {
            $("#bank_branch_edit").removeAttr("disabled");
            $("#bank_name_edit").removeAttr("disabled");
            $("#cheque_date_edit").removeAttr("disabled");
            $("#cheque_no_edit").removeAttr("disabled");
            $('.cheque_no_div_edit').show();
            $('.cheque_date_div_edit').show();
            $('.bank_name_div_edit').show();
            $('.bank_branch_div_edit').show();
        }

        function hideDivEdit()
        {
            $("#bank_branch_edit").attr('disabled', true);
            $("#bank_name_edit").attr('disabled', true);
            $("#cheque_date_edit").attr('disabled', true);
            $("#cheque_no_edit").attr('disabled', true);
            $('.cheque_no_div_edit').hide();
            $('.cheque_date_div_edit').hide();
            $('.bank_name_div_edit').hide();
            $('.bank_branch_div_edit').hide();
        }
    </script>
@endpush
