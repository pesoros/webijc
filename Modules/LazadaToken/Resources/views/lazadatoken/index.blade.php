@extends('backEnd.master')

@section('mainContent')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="box_header common_table_header">
                <div class="main-title d-md-flex">
                    <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">Lazada Token</h3>
                    <ul class="d-flex">
                        <li><a class="primary-btn radius_30px mr-10 fix-gr-bg" href="{{route("create")}}"><i
                                    class="ti-plus"></i>New Token</a>
                        </li>
                    </ul>
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
                                <th scope="col">Akun</th>
                                <th scope="col">Expires in</th>
                                <th scope="col">{{__('common.Status')}}</th>
                                <th scope="col">{{__('common.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>

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
@endpush
