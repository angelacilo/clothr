@extends('layouts.admin')

@section('title', 'Reviews')
@section('subtitle', 'Customer reviews & ratings')

@section('content')
<div class="reviews-container" style="height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; max-width: 450px;">
        <div style="width: 90px; height: 90px; border: 2px dashed #cbd5e1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 32px;">
            <i data-lucide="star" style="width: 44px; height: 44px; color: #94a3b8;"></i>
        </div>
        <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 16px; color: var(--text-dark);">No Reviews Yet</h2>
        <p style="font-size: 15px; color: var(--text-medium); line-height: 1.6; padding: 0 20px;">
            Customer reviews and ratings will appear here once customers start reviewing your products.
        </p>
    </div>
</div>
@endsection
