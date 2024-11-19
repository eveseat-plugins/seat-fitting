@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::doctrine.page_title'))
@section('page_header', trans('fitting::doctrine.page_title'))


@section('left')
    <div class="card card-primary card-solid">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::doctrine.page_title')}}</h3>
            @can('fitting.create')
                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-xs btn-tool" id="newDoctrine" data-toggle="modal"
                            data-toggle="tooltip" data-target="#addDoctrine" data-placement="top"
                            title="{{trans('fitting::doctrine.add_doctrine_btn')}}">
                        <span class="fa fa-plus-square"></span>
                    </button>
                </div>
            @endcan
        </div>
        <div class="card-body pb-0">
            <div class="form-row">
                <div class="input-group">
                    <select id="doctrineSpinner" class="form-control mr-3">
                        <option value="0">{{trans('fitting::doctrine.select_doctrine_option')}}</option>
                        @foreach ($doctrine_list as $doctrine)
                            <option value="{{ $doctrine['id'] }}" @if($doctrine_id == $doctrine['id']) selected @endif>{{ $doctrine['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-btn">
                        @can('fitting.create')
                            @if (!empty($doctrine_list))
                                <button type="button" id="editDoctrine" class="btn btn-warning btn-sm"
                                        disabled="disabled" data-id="" data-toggle="modal" data-target="#addDoctrine"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{trans('fitting::doctrine.edit_doctrine_btn')}}" inactive>
                                    <span class="fas fa-edit text-white"></span>
                                </button>
                                <button type="button" id="deleteDoctrine" class="btn btn-danger btn-sm"
                                        disabled="disabled" data-id="" data-toggle="tooltip" data-placement="top"
                                        title="{{trans('fitting::doctrine.delete_doctrine_btn')}}">
                                    <span class="fa fa-trash text-white"></span>
                                </button>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body px-2">
            <table id='fitlist' class="table table-hover" style="vertical-align: top">
                <thead>
                <tr>
                    <th></th>
                    <th>{{trans('fitting::fitting.col_ship_type')}}</th>
                    <th>{{trans('fitting::fitting.col_fit_name')}}</th>
                    <th class="pull-right">{{trans('fitting::fitting.col_options')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        @include('fitting::includes.maintainer')
    </div>

    @include('fitting::includes.eft-export', ['includeFooter' => false])

    @include('fitting::includes.edit-fit-modal')
    @include('fitting::includes.delete-fit-modal')
@endsection
@section('center')
    @include('fitting::includes.display-fit')
@endsection
@section('right')
    @include('fitting::includes.display-skills')
    @include('fitting::includes.doctrine-add')
    @include('fitting::includes.doctrine-confirm-delete')
@endsection

@push('javascript')
    <script src="{{ asset('web/js/fitting.js') }}"></script>
    <script src="{{ asset('web/js/fitting-jquery.js') }}"></script>
    <script type="application/javascript">
        let fitListTable = $('#fitlist').DataTable();

        $('#doctrineSpinner').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            placeholder: {
                value: 0,
                text: "Choose Doctrine...."
            },
        });

        $('#deleteDoctrine').on('click', function () {
            $('#doctrineConfirmModal').modal('show');
            $('#fitSelection').val($(this).data('id'));
        });

        $('#deleteDoctrineConfirm').on('click', function () {
            let id = $('#doctrineSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/fitting/deldoctrinebyid/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#fitlist .fitid[data-id="' + id + '"]').remove();
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });

        $('#deleteConfirm').on('click', function () {
            const id = $('#fitSelection').val();
            $('#fitlist .fitid[data-id="' + id + '"]').remove();
            $.ajax({
                headers: function () {
                },
                url: "/fitting/delfittingbyid/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#fitlist .fitid[data-id="' + id + '"]').remove();
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });

        function changeDoctrine(id) {
            if (id > 0) {
                $('button#editDoctrine').prop('disabled', false);
                $('button#deleteDoctrine').prop('disabled', false);

                $.ajax({
                    headers: function () {
                    },
                    url: "/fitting/getdoctrinebyid/" + id,
                    type: "GET",
                    dataType: 'json',
                    timeout: 10000
                }).done(function (result) {
                    if (result) {
                        fitListTable.destroy();
                        $('#fitlist').find("tbody").empty();
                        for (var fitting in result) {
                            row = "<tr><td><img src='https://images.evetech.net/types/" + result[fitting].shipImg + "/icon?size=32' height='24' /></td>";
                            row = row + "<td>" + result[fitting].shipType + "</td>";
                            row = row + "<td>" + result[fitting].name + "</td>";
                            row = row + "<td><button type='button' id='viewfit' class='btn btn-xs btn-success pull-right' data-id='" + result[fitting].id + "' data-toggle='tooltip' data-placement='top' title='View Fitting'>";
                            row = row + "<span class='fa fa-eye text-white'></span></button></td></tr>";
                            $('#fitlist').find("tbody").append(row);
                        }
                        fitListTable = $('#fitlist').DataTable();
                    }
                });
            } else {
                $('button#editDoctrine').prop('disabled', true);
                $('button#deleteDoctrine').prop('disabled', true);
            }
        }

        $('#newDoctrine').on('click', function () {
            $.ajax({
                headers: function () {
                },
                url: "/fitting/fittinglist",
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listoffits').empty();
                $.each(result, function (key, value) {
                    $('#listoffits').append($("<option></option>").attr("value", value.id).text(value.fitname + " -- " + value.shiptype));
                });
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });
        });

        $('#editDoctrine').on('click', function () {
            const id = $('#doctrineSpinner').find(":selected").val();

            $.ajax({
                headers: function () {
                },
                url: "/fitting/getdoctrineedit/" + id,
                type: "GET",
                datatype: 'json',
                timeout: 10000
            }).done(function (result) {
                $('#listoffits').empty();
                $.each(result[1], function (key, value) {
                    $('#listoffits').append($("<option></option>").attr("value", value.id).text(value.fitname + " -- " + value.shiptype));
                });
                $('#selectedFits').empty();
                $.each(result[0], function (key, value) {
                    $('#selectedFits').append($("<option></option>").attr("value", value.id).text(value.fitname + " -- " + value.shiptype));
                });
                $('#doctrineid').val(result[2]);
                $('#doctrinename').val(result[3]);
            }).fail(function (xmlHttpRequest, textStatus, errorThrown) {
            });

        });

        $('#addFits').on('click', function () {
            $("#listoffits option:selected").each(function () {
                $('#listoffits option[value="' + $(this).val() + '"]').remove();
                $('#selectedFits').append($("<option></option>").attr("value", $(this).val()).text($(this).text()));
            });
        });

        $('#removeFits').on('click', function () {
            $("#selectedFits option:selected").each(function () {
                $('#selectedFits option[value="' + $(this).val() + '"]').remove();
                $('#listoffits').append($("<option></option>").attr("value", $(this).val()).text($(this).text()));
            });
        });

        $('#addDocForm').submit(function (event) {
            $('#selectedFits').find("option").each(function () {
                $(this).prop('selected', true);
            });
        });

        let initialId = $('#doctrineSpinner').val();
        if (initialId) {
            changeDoctrine(initialId);
        }
        $('#doctrineSpinner').change(function () {
            const id = $('#doctrineSpinner').find(":selected").val();
            changeDoctrine(id);
        });
    </script>
@endpush