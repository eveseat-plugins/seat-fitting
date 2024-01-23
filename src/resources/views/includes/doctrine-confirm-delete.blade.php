<div class="modal fade" tabindex="-1" role="dialog" id="doctrineConfirmModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">{{trans('fitting::doctrine.delete_doctrine_modal_title')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{trans('fitting::doctrine.delete_doctrine_modal_body')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">{{trans('fitting::global.cancel_btn')}}</button>
                <button type="button" class="btn btn-primary" id="deleteDoctrineConfirm"
                        data-dismiss="modal">{{trans('fitting::global.delete_btn')}}
                    Doctrine
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

