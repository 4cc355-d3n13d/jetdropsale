@extends('layouts.app')

@section('content')
    <div id="productView" >
        @include('components._content-menu')
        @include('product._product-item')
        @include('components._content')
        @include('components._related')
    </div>
@endsection
