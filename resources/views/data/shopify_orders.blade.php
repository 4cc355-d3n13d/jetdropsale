@extends('layouts.bootstrap')

@section('content')
    @each('data._shopify_order', $data, 'order')
@endsection
