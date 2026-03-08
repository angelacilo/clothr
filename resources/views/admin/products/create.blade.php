@extends('admin.layouts.app')

@section('page-title', 'Create Product')
@section('page-subtitle', 'Add a new product')

@section('content')
    <div id="admin-product-create"></div>
@endsection

@section('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
@endsection