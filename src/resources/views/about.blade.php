@extends('web::layouts.grids.4-4-4')

@section('title', trans('fitting::about.page_title'))
@section('page_header', trans('fitting::about.page_title'))

@section('left')

    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::about.functionality_title')}}</h3>
        </div>
        <div class="card-body">
            <p>
                {{trans('fitting::about.functionality_body')}}
            </p>
        </div>
        @include('fitting::includes.maintainer')
    </div>
@stop

@section('center')

    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::about.thanks_title')}}</h3>
        </div>
        <div class="card-body">
            <div class="box-body">
                <p>
                    {!! trans('fitting::about.thanks_intro') !!}
                </p>

                <p>
                <table class="table table-borderless">
                    <tr>
                        <td>Seat-Fitting</td>
                        <td>
                            <a href="https://evewho.com/character/96057938"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!}
                                Crypta Electrica</a></td>
                    </tr>

                    <tr>
                        <td>Seat</td>
                        <td>
                            <a href="https://evewho.com/corporation/98482334"> {!! img('corporations', 'logo', 98482334, 64, ['class' => 'img-circle eve-icon small-icon']) !!}
                                eveseat.net</a></td>
                    </tr>
                </table>
                </p>

                <p>{!! trans('fitting::about.thanks_footer') !!}</p>
            </div>
        </div>
        @include('fitting::includes.maintainer')
    </div>

@stop
@section('right')

    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::about.info_title')}}</h3>
        </div>
        <div class="card-body">
            <legend>{{trans('fitting::about.info_body_legend')}}</legend>
            <p>{!! trans('fitting::about.info_body_text') !!}</p>
        </div>

        @include('fitting::includes.maintainer')
    </div>

@stop