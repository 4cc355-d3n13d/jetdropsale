@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('catalog'))

@section('content')

        @include('components.mobile_categories_menu')
        @include('components._menu-categories')
        @include('catalog._catalog-list')
        {{--@include('catalog._catalog-bestsellers')--}}
@endsection
