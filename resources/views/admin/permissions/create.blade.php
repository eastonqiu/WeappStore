@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
<div class="container">

    <div class="row">
        <div class="col-sm-12">
            <h1 class="pull-left">Create New Permission</h1>
        </div>
    </div>

    @include('common.errors')

    <div class="row">
        {!! Form::open(['route' => 'permissions.store']) !!}

            @include('admin.permissions.fields')

        {!! Form::close() !!}
    </div>
</div>
@endsection
