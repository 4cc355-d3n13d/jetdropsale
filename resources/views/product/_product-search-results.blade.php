@extends('layouts.app')

@section('title', 'Search: ' . app('request')->input('query') . ' - ')

@section('breadcrumbs', Breadcrumbs::render('catalog.search', app('request')->input('query')))

@include('product._product-list')
