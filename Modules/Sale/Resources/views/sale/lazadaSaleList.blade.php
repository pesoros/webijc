@extends('backEnd.master_list')
@section('mainContent')
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
@endsection