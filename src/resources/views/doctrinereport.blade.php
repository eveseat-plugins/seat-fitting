@extends('web::layouts.grids.12')

@section('title', trans('fitting::doctrine.report_page_title'))
@section('page_header', trans('fitting::doctrine.report_page_title'))


@section('full')
    <div class="card card-primary card-solid">
        <div class="card-header">
            <h3 class="card-title">{{trans('fitting::doctrine.report_page_title')}}</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <label for="alliances">{{trans('fitting::doctrine.report_alliance_label')}}:</label>
                        <select id="alliances" class="form-control" multiple>
                            @foreach ($alliances as $alliance)
                                <option value="{{ $alliance->alliance_id }}">{{ $alliance->name }}
                                    [{{ $alliance->ticker }}]
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block">{!! trans('fitting::doctrine.report_alliance_note') !!}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <label for="corporations">{{trans('fitting::doctrine.report_corporation_label')}}:</label>
                        <select id="corporations" class="form-control" multiple>
                            @foreach ($corps as $corp)
                                <option value="{{ $corp->corporation_id }}">{{ $corp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="form-group">
                        <label for="doctrines">{{trans('fitting::doctrine.report_doctrine_label')}}:</label>
                        <select id="doctrines" class="form-control">
                            @foreach ($doctrines as $doctrine)
                                <option value="{{ $doctrine->id }}">{{ $doctrine->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="button" id="runreport" class="btn btn-info btn-flat">
                        <span class="fa fa-sync"></span>
                        {{trans('fitting::doctrine.report_run_btn')}}
                    </button>
                </div>
            </div>
        </div>
        @include('fitting::includes.maintainer')
    </div>

    <div class="card card-primary card-solid" id="reportbox">
        <div class="card-header bg-danger d-none" id="missing_warn">
            <h3 class="card-title">{{trans('fitting::doctrine.report_box_alert_multiple_fitting_names')}}</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow: auto">
                <table id="report" class="table table-condensed table-striped no-footer">
                    <thead>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('javascript')
    <script type="application/javascript">
        const button = $('#runreport');
        let table;
        const report = $('#report');

        $(document).ready(function () {
            $('#reportbox').hide();

            $('#alliances').select2({
                sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
                multiple: true,
                placeholder: "Select alliances"
            });
            $('#corporations').select2({
                sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
                multiple: true,
                placeholder: "Select corporations"
            });
            $('#doctrines').select2({sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),});

        });

        button.on('click', function () {
            const allianceids = $('#alliances').select2('data').map((e)=>parseInt(e.id));
            const corpids = $('#corporations').select2('data').map((e)=>parseInt(e.id));
            const doctrineid = parseInt($('#doctrines').find(":selected").val());

            button.prop("disabled", true);
            button.html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>' + '{{trans('fitting::doctrine.report_loading')}}'
            );

            //
            // hide pane while loading data
            //
            $('#reportbox').hide();

            $('#missing_warn').addClass('d-none');
            button.removeClass("bg-danger")

            //
            // in case datatable has already been set, ensure data are cleared from cache and destroy the instance
            //
            if (table) {
                table.clear();
                table.destroy();
                report.find("thead, tbody").empty();
            }

            report.find("thead, tbody").empty();

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{csrf_token()}}'
                },
                url: "/fitting/runReport",
                type: "POST",
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    alliances: allianceids,
                    corporations: corpids,
                    doctrine: doctrineid,
                }),
                timeout: 60000,
            }).done(function (result) {

                try {

                    if (Object.keys(result.fittings).length !== (Object.keys(result.totals).length - 1)) {
                        $('#missing_warn').removeClass('d-none');
                    }

                    let header = "";

                    for (let fit in result.fittings) {
                        header += "<th style='text-align: center'>" + result.fittings[fit] + "</th>";
                    }

                    header += "</tr>";

                    report.find("thead").append("<tr><th>{{trans('fitting::doctrine.report_character_header')}}</th>" + header);

                    let body = "<tr><td><label>{{trans('fitting::doctrine.report_hull_header')}}</label></td>";

                    for (let total in result.totals) {
                        if (result.totals[total].ship === null) {
                            result.totals[total].ship = 0;
                        }

                        if (result.totals[total].fit === null) {
                            result.totals[total].fit = 0;
                        }

                        if (total !== "chars") {
                            body = body + "<td style='text-align: center; width: 10em;'>" + result.totals[total].ship + "  /  " + result.totals[total].fit + "<br/>";
                            body = body + Math.round((result.totals[total].ship / result.totals['chars']) * 100) + "%  /  " + Math.round((result.totals[total].fit / result.totals['chars']) * 100) + "%</td>";
                        }

                    }

                    report.find("tbody").prepend(body);

                    for (let char in result.chars) {
                        body = "<tr><td style='position: sticky;'>" + char + "</td>";

                        for (let ships in result.chars[char]) {
                            if (result.chars[char][ships].ship === true) {
                                body += "<td style='text-align: center; width: 10em; min-width: 95px;'><span class='badge badge-success'>HULL</span> / ";
                            } else {
                                body += "<td style='text-align: center; width: 10em; min-width: 95px;'><span class='badge badge-danger'>HULL</span> / ";
                            }

                            if (result.chars[char][ships].fit === true) {
                                body += "<span class='badge badge-success'>{{trans('fitting::doctrine.report_fit_badge')}}</span></td>";
                            } else {
                                body += "<span class='badge badge-danger'>{{trans('fitting::doctrine.report_fit_badge')}}</span></td>";
                            }
                        }

                        body += "</tr>";

                        report.find("tbody").append(body);
                    }

                    //
                    // show report content
                    //
                    $('#reportbox').show();

                    // table = report.DataTable({
                    //     scrollX: true,
                    //     // scrollY: "300px",
                    //     scrollCollapse: true,
                    //     paging: false,
                    //     fixedColumns: true
                    // });

                    button.html(
                        `<span class="fa fa-sync"></span>
                        Run Report
                    </button>`
                    );
                    button.prop("disabled", false);

                } catch (error) {
                    button.html(
                        `<span class="fa fa-sync"></span>
                        {{trans('fitting::doctrine.report_run_btn_last_failed')}}
                        </button>`
                    );
                    button.addClass("bg-danger")
                    button.prop("disabled", false);
                }
            })
                .fail(function () {
                    button.html(
                        `<span class="fa fa-sync"></span>
                        {{trans('fitting::doctrine.report_run_btn_last_timeout')}}
                    </button>`
                    );
                    button.addClass("bg-danger")
                    button.prop("disabled", false);
                });
        });
    </script>
@endpush

