
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
                                <th scope="col">{{__('common.Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dataOrders as $key => $item)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item['created_at'])->format('d F Y H:mm:s') }}</td>
                                    <td>
                                        {{ $item['order_number'] }}
                                        <br>
                                        @if ($item['statuses'][0] == 'INFO_ST_DOMESTIC_RETURN_WITH_LAST_MILE_3PL')
                                            Returned                                        
                                        @else
                                            {{ $item['statuses'][0] }}
                                        @endif
                                    </td>
                                    <td>{{ $item['nama_akun'] }}</td>
                                    <td>{{ single_price($item['price']) }}</td>
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

<script>
    $(document).ready(function () {
        // CRM TABLE 3
        if ($('.Crm_table_active3').length) {
            pdfMake.fonts = {
                DejaVuSans: {
                    normal: 'DejaVuSans.ttf',
                    bold: 'DejaVuSans-Bold.ttf',
                    italics: 'DejaVuSans-Oblique.ttf',
                    bolditalics: 'DejaVuSans-BoldOblique.ttf'
                }
            };
            $('.Crm_table_active3').DataTable({
                bLengthChange: true,
                "bDestroy": true,
                language: {
                    search: "<i class='ti-search'></i>",
                    searchPlaceholder: 'Quick Search',
                    paginate: {
                        next: "<i class='ti-arrow-right'></i>",
                        previous: "<i class='ti-arrow-left'></i>"
                    }
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: 'Copy',
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i>',
                        titleAttr: 'Excel',
                        title: $("#logo_title").val(),
                        margin: [10, 10, 10, 0],
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        },

                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text"></i>',
                        titleAttr: 'CSV',
                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        title: $("#logo_title").val(),
                        titleAttr: 'PDF',
                        charSet: 'utf-8',

                        exportOptions: {
                            columns: ':visible',
                            columns: ':not(:last-child)',
                        },
                        orientation: 'landscape',
                        pageSize: 'A4',
                        margin: [0, 0, 0, 0],
                        alignment: 'center',
                        header: true,
                        customize: function (doc) {

                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            doc.content.splice(1, 0, {
                                margin: [0, 0, 0, 0],
                                alignment: 'center',
                                image: 'data:image/png;base64,' + $("#logo_img").val(),
                            });
                            doc.defaultStyle = {
                                font: 'DejaVuSans'
                            }
                        }

                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i>',
                        titleAttr: 'Print',
                        alignment: 'center',
                        title: $("#logo_title").val(),
                        exportOptions: {
                            columns: ':not(:last-child)',
                        }
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i>',
                        postfixButtons: ['colvisRestore']
                    }
                ],
                columnDefs: [{
                    visible: false
                }],
                responsive: true,
            });
        }

        $('label select').niceSelect();
        $(function () {
            $('label .nice-select').addClass('dataTable_select');
        });
        $('label .nice-select').on('click', function () {
            $(this).toggleClass('open_selectlist');
        })

    });
</script>