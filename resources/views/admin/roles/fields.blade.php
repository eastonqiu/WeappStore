{!! Form::hidden('id', null) !!}

<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Display_name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('display_name', 'Display_name:') !!}
    {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('permissions', 'Permissions:') !!}
    @foreach($allPermissions as $perm)
        <div>
        {!! Form::checkbox('perms[]', $perm['id'], in_array($perm['id'], empty($perms)? [] : $perms)) !!}
        {!! Form::label('display_name', $perm['display_name']) !!}
        </div>
    @endforeach
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('roles.index') !!}" class="btn btn-default">Cancel</a>
</div>
