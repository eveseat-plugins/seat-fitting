@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::fitting.page_title'))
@section('page_header', trans('fitting::fitting.page_title'))

@push('head')
    <link rel="stylesheet" href="{{ asset('web/css/fitting.css') }}"/>
@endpush

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
            <table id='fitlist' class="table table-hover" style="vertical-align: top">
                <thead>
                <tr>
                    <th></th>
                    <th>{{trans('fitting::fitting.col_ship_type')}}</th>
                    <th>{{trans('fitting::fitting.col_fit_name')}}</th>
                    <th class="pull-right">{{trans('fitting::fitting.col_options')}}</th>
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
                            <td class="no-hover pull-right" style="min-width:80px">
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
        @if(setting("fitting.show_about_footer", true))
            <div class="card-footer text-muted">
                {!! trans('fitting::about.maintained_by', ['route' => route('cryptafitting::about'), 'img' => img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon'])]) !!}
                <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
            </div>
        @endif
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

        $('#fitlist')
            .on('click', '#deletefit', function () {
                $('#fitConfirmModal').modal('show');
                $('#fitSelection').val($(this).data('id'));
            }).on('click', '#viewfit', function () {
            $.ajax({
                headers: function () {
                },
                url: "/fitting/getfittingcostbyid/" + $(this).data('id'),
                type: "GET",
                dataType: 'json',
                timeout: 10000

            }).done(function (result) {
                if (result) {
                    let total = result.total.toLocaleString();
                    let volume = result.volume.toLocaleString();

                    $('#current_appraisal').html(total + " (ISK)" + " - " + volume + "m3");
                }
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
    </script>
@endpush

