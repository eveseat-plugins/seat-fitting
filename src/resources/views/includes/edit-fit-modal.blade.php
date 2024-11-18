<div class="modal fade" tabindex="-1" role="dialog" id="fitEditModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">{{trans('fitting::fitting.edit_fitting_modal_title')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
            </div>
            <form role="form" action="{{ route('cryptafitting::saveFitting') }}" method="post">
                <input type="hidden" id="fitSelection" name="fitSelection" value="0">
                <div class="modal-body">
                    <p>{{trans('fitting::fitting.edit_fitting_body')}}</p>
                    {{ csrf_field() }}
                    <textarea name="eftfitting" id="eftfitting" rows="15" style="width: 100%"></textarea>
                </div>
                <div class="modal-footer">
                    <div class="btn-group pull-right" role="group">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('fitting::global.cancel_btn')}}</button>
                        <input type="submit" class="btn btn-primary" id="savefitting" value="{{trans('fitting::global.submit_btn')}}"/>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->