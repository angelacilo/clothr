@extends('shop.layouts.app')

@section('title', 'Product')

@section('content')
    <div id="shop-product-detail-root" data-slug="{{ $slug ?? '' }}"></div>
@endsection
