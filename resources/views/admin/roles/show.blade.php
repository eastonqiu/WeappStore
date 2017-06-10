@extends('adminlte::layouts.app')

@section('htmlheader_title')
    {{ trans('adminlte_lang::message.home') }}
@endsection

@section('main-content')
<div class="container">
    @include('admin.roles.show_fields')
</div>
@endsection
