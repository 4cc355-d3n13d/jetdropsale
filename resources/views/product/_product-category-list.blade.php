@extends('layouts.app')

@section('breadcrumbs', Breadcrumbs::render('category', $category))

@include('product._product-category')




