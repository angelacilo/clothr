@extends('shop.layouts.app')

@section('title', 'Order Confirmed')

@section('content')
    <div id="shop-order-success-root" data-order-id="{{ $orderId ?? '' }}"></div>
@endsection
