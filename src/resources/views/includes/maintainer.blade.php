@if(setting("fitting.show_about_footer", true))
    <div class="card-footer text-muted">
        {!! trans('fitting::about.maintained_by', ['route' => route('fitting.about'), 'img' => img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon'])]) !!}
        <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>
@endif