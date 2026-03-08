@extends('admin.layouts.app')

@section('page-title', 'Edit Product')
@section('page-subtitle', $product->name)

@section('content')
    <div id="admin-product-edit" data-product-id="{{ $product->product_id }}"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection