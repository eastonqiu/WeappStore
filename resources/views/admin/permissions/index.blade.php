@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
    <div class="container">

        <h1 class="pull-left">permissions</h1>
        <a class="btn btn-primary pull-right" style="margin-top: 25px" href="{!! route('permissions.create') !!}">Add New</a>

        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>

        @if($permissions->isEmpty())
            <div class="well text-center">No permissions found.</div>
        @else
            @include('admin.permissions.table')
        @endif

        {{ $permissions->links() }}

    </div>
@endsection
