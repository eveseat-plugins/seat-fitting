@extends('web::layouts.app')

@section('title', trans('fitting::settings.settings_title'))
@section('page_header', trans('fitting::settings.settings_title'))


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
                        @include("pricescore::utils.instance_selector",["id"=>"admin_price_provider","name"=>"price_source","instance_id"=>setting("fitting.admin_price_provider", true)])
                        <p class="form-text text-muted mb-0">
                            {{ trans('fitting::settings.price_provider_description') }}
                        </p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">{{trans('fitting::global.save_btn')}}</button>
            </form>

        </div>

        @include('fitting::includes.maintainer')
    </div>
@endsection


