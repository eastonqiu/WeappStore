@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="pull-left">Edit Role</h1>
            </div>
        </div>

        @include('common.errors')

        <div class="row">
            {!! Form::model($role, ['route' => ['roles.update', $role->id], 'method' => 'patch']) !!}

            @include('admin.roles.fields')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
