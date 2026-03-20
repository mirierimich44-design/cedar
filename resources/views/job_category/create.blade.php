<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\App\Http\Controllers\JobCategoryController::class, 'store']), 'method' => 'post', 'id' => 'job_category_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'messages.add' )</h4>
    </div>

    <div class="modal-body">
      <div class="form-group">
        {!! Form::label('name', __( 'job.category' ) . ':*') !!}
          {!! Form::text('name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'job.category' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('short_code', __( 'lang_v1.short_code' ) . ':') !!}
          {!! Form::text('short_code', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.short_code' )]); !!}
      </div>

      <div class="form-group">
        {!! Form::label('description', __( 'job.description' ) . ':') !!}
          {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __( 'job.description' ), 'rows' => 3]); !!}
      </div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
