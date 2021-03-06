@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <div class="container">

        <h1 class="pull-left">Users</h1>
        <a class="btn btn-primary pull-right" style="margin-top: 25px" href="{!! route('users.create') !!}">Add New</a>

        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>

        @if($users->isEmpty())
            <div class="well text-center">No Users found.</div>
        @else
            @include('admin.users.table')
        @endif

        {{ $users->links() }}

    </div>
@endsection
