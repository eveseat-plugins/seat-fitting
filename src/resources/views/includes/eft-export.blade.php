<div class="card card-primary card-solid" id='eftexport'>
    <div class="card-header">
        <h3 class="card-title">{{trans('fitting::fitting.eft_fitting_title')}}</h3>
    </div>
    <div class="card-body">
            <textarea name="showeft" id="showeft" rows="15" style="width: 100%" onclick="this.focus();this.select()"
                      readonly="readonly"></textarea>
        <div id="exportLinks" class="mt-2 list-group"></div>
    </div>
    @if($includeFooter)
        <div class="card-footer">
            {{trans('fitting::fitting.fitting_current_price')}}
            <a id="current_appraisal" class="float-right">
                {{trans('fitting::fitting.fitting_price_empty')}}
            </a>
        </div>
    @endif
</div>