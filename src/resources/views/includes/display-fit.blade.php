<div class="card card-primary card-solid" id="fitting-box">
    <div class="card-header"><h3 class="card-title" id='middle-header'></h3></div>
    <input type="hidden" id="fittingId" value=""/>
    <div class="card-body">
        <div id="fitting-window">
            <table class="table table-condensed table-striped" id="lowSlots">
                <thead>
                <tr>
                    <th>{{trans('fitting::fitting.fit_low_slot_title')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <table class="table table-condensed table-striped" id="midSlots">
                <thead>
                <tr>
                    <th>{{trans('fitting::fitting.fit_mid_slot_title')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <table class="table table-condensed table-striped" id="highSlots">
                <thead>
                <tr>
                    <th>{{trans('fitting::fitting.fit_high_slot_title')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <table class="table table-condensed table-striped" id="rigs">
                <thead>
                <tr>
                    <th>{{trans('fitting::fitting.fit_rigs_title')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
                <table class="table table-condensed table-striped" id="subSlots">
                    <thead>
                    <tr>
                        <th>{{trans('fitting::fitting.fit_subsystems_title')}}</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </table>
            <table id="drones" class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th class="col-md-10">{{trans('fitting::fitting.fit_drone_bay_title')}}</th>
                    <th class="col-md-2">{{trans('fitting::fitting.fit_drone_bay_number')}}</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>