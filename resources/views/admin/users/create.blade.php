@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
<div class="container">

    <div class="row">
        <div class="col-sm-12">
            <h1 class="pull-left">Create New User</h1>
        </div>
    </div>

    @include('common.errors')

    <div class="row">
        {!! Form::open(['route' => 'users.store']) !!}

            @include('admin.users.fields')

        {!! Form::close() !!}
    </div>
</div>
@endsection
