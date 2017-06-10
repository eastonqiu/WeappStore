@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="pull-left">Edit permission</h1>
            </div>
        </div>

        @include('common.errors')

        <div class="row">
            {!! Form::model($permission, ['route' => ['permissions.update', $permission->id], 'method' => 'patch']) !!}

            @include('admin.permissions.fields')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
