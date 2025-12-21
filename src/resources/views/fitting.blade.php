@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::fitting.page_title'))
@section('page_header', trans('fitting::fitting.page_title'))

@section('left')
    <div class="card card-primary card-solid">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::fitting.list_title')}}</h3>
            @can('fitting.create')
                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-xs btn-tool" id="addFitting" data-toggle="tooltip"
                            data-placement="top" title="{{trans('fitting::fitting.add_new_fitting_tooltip')}}">
                        <span class="fa fa-plus-square"></span>
                    </button>
                </div>
            @endcan
        </div>
        <div class="card-body px-2">
            <!-- Search Filters -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-rocket"></i></span>
                        </div>
                        <input type="text" id="searchShip" class="form-control" placeholder="Search by ship type...">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-file-signature"></i></span>
                        </div>
                        <input type="text" id="searchFitName" class="form-control" placeholder="Search by fit name...">
                    </div>
                </div>
            </div>
            
            <table id='fitlist' class="table table-hover table-sm" style="vertical-align: top; width: 100%;">
                <thead>
                <tr>
                    <th style="width: 40px;"></th>
                    <th>{{trans('fitting::fitting.col_ship_type')}}</th>
                    <th>{{trans('fitting::fitting.col_fit_name')}}</th>
                    <th class="text-right" style="width: 100px;">{{trans('fitting::fitting.col_options')}}</th>
                </tr>
                </thead>
                <tbody>
                @if (count($fitlist) > 0)
                    @foreach($fitlist as $fit)
                        <tr class="fitid" data-id="{{ $fit['id'] }}">
                            <td><img src='https://images.evetech.net/types/{{$fit['typeID']}}/icon?size=32'
                                     height='24' alt="{{trans('fitting::fitting.fitting_icon_alt')}}"/>
                            </td>
                            <td>{{ $fit['shiptype'] }}</td>
                            <td>{{ $fit['fitname'] }}</td>
                            <td class="no-hover text-right" style="min-width:80px">
                                <button type="button" id="viewfit" class="btn btn-xs btn-success"
                                        data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top"
                                        title="{{trans('fitting::fitting.view_fitting_tooltip')}}">
                                    <span class="fa fa-eye text-white"></span>
                                </button>
                                @can('fitting.create')
                                    <button type="button" id="editfit" class="btn btn-xs btn-warning"
                                            data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top"
                                            title="{{trans('fitting::fitting.edit_fitting_tooltip')}}">
                                        <span class="fas fa-edit text-white"></span>
                                    </button>
                                    <button type="button" id="deletefit" class="btn btn-xs btn-danger"
                                            data-id="{{ $fit['id'] }}" data-toggle="tooltip" data-placement="top"
                                            title="{{trans('fitting::fitting.delete_fitting_tooltip')}}">
                                        <span class="fa fa-trash text-white"></span>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        @include('fitting::includes.maintainer')
    </div>

    @include('fitting::includes.eft-export', ['includeFooter' => true])
    @include('fitting::includes.edit-fit-modal')
    @include('fitting::includes.delete-fit-modal')

@endsection
@section('center')
    @include('fitting::includes.display-fit')
@endsection
@section('right')
    @include('fitting::includes.display-skills')
@endsection

@push('javascript')
    <script src="{{ asset('web/js/fitting.js') }}"></script>
    <script src="{{ asset('web/js/fitting-jquery.js') }}"></script>
    <script type="application/javascript">
        $('#exportLinks').hide();

        // Initialize DataTable with search functionality
        var fittingTable = $('#fitlist').DataTable({
            "paging": true,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "order": [[1, "asc"]], // Sort by ship type by default
            "columnDefs": [
                {
                    "targets": 0, // Icon column
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 3, // Options column
                    "orderable": false,
                    "searchable": false
                }
            ],
            "language": {
                "search": "Search all:",
                "lengthMenu": "Show _MENU_ fittings",
                "info": "Showing _START_ to _END_ of _TOTAL_ fittings",
                "infoEmpty": "No fittings available",
                "infoFiltered": "(filtered from _MAX_ total fittings)",
                "zeroRecords": "No matching fittings found",
                "emptyTable": "No fittings available"
            },
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        });

        // Custom search for ship type column (column index 1)
        $('#searchShip').on('keyup change', function() {
            fittingTable.column(1).search(this.value).draw();
        });

        // Custom search for fit name column (column index 2)
        $('#searchFitName').on('keyup change', function() {
            fittingTable.column(2).search(this.value).draw();
        });

        // Click handlers for fitting actions
        $('#fitlist tbody')
            .on('click', '#deletefit', function () {
                $('#fitConfirmModal').modal('show');
                $('#fitSelection').val($(this).closest('tr').data('id'));
            })
            .on('click', '#viewfit', function () {
                var fitId = $(this).data('id');
                $.ajax({
                    headers: function () {},
                    url: "/fitting/getfittingcostbyid/" + fitId,
                    type: "GET",
                    dataType: 'json',
                    timeout: 10000
                }).done(function (result) {
                    if (result && !result.error) {
                        let total = result.total.toLocaleString();
                        let volume = result.volume.toLocaleString();
                        let ship = result.ship.toLocaleString();
                        $('#current_appraisal').html(`${ship}/${total} (ISK) - ${volume} m3`);
                    } else if (result && result.error) {
                        $('#current_appraisal').html(`<span class="text-warning">${result.error}</span>`);
                    }
                }).fail(function(xhr, status, error) {
                    console.error('Price fetch failed:', error);
                    $('#current_appraisal').html('<span class="text-danger">Failed to fetch pricing</span>');
                });
            });

        $('#deleteConfirm').on('click', function () {
            const id = $('#fitSelection').val();
            
            $.ajax({
                headers: function () {},
                url: "/fitting/delfittingbyid/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                // Remove the row from DataTable
                fittingTable.row($('#fitlist .fitid[data-id="' + id + '"]')).remove().draw();
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
                console.error('Delete failed:', errorThrown);
            });
        });
    </script>
@endpush
