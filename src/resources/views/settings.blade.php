@extends('web::layouts.app')

@section('title', trans('fitting::settings.settings_title'))
@section('page_header', trans('fitting::settings.settings_title'))

@push('head')
    <link rel="stylesheet" href="{{ asset('web/css/fitting.css') }}"/>
@endpush


@section('content')
    <div class="card card-default">
        <div class="card-header">
            <h3 class="card-title">{{ trans('fitting::settings.settings_title') }}</h3>
        </div>
        <div class="card-body">

            <form action="{{ route("fitting.saveSettings") }}" method="POST">
                @csrf

                <div class="form-group row">
                    <label class="col-sm-3 form-label"
                           for="admin_price_provider">{{ trans('fitting::settings.price_provider_label') }}</label>
                    <div class="col-sm-9">
                        @include("pricescore::utils.instance_selector",["id"=>"admin_price_provider","name"=>"admin_price_provider","instance_id"=>setting("fitting.admin_price_provider", true)])
                        <p class="form-text text-muted mb-0">
                            {{ trans('fitting::settings.price_provider_description') }}
                        </p>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="show_about_footer"
                           class="col-sm-3 col-form-label">{{ trans('fitting::settings.show_about_footer') }}</label>
                    <div class="col-sm-9">
                        <div class="form-check">
                            @if(setting('fitting.show_about_footer', true) == 1)
                                <input type="checkbox" name="show_about_footer" class="form-check-input"
                                       id="show_about_footer" value="1" checked/>
                            @else
                                <input type="checkbox" name="show_about_footer" class="form-check-input"
                                       id="show_about_footer" value="1"/>
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{trans('fitting::global.save_btn')}}</button>
            </form>

        </div>
    </div>
@endsection


