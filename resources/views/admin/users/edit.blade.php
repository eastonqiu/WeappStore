@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="pull-left">Edit User</h1>
            </div>
        </div>

        @include('common.errors')

        <div class="row">
            {!! Form::model($user, ['route' => ['users.update', $user->id], 'method' => 'patch']) !!}

            @include('admin.users.fields')

            {!! Form::close() !!}
        </div>
    </div>
@endsection
