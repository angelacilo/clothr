@extends('layouts.admin')

@section('title', 'Archive')
@section('subtitle', 'Archived products & categories')

@section('content')
<div class="archive-container">
    @if($archived->isEmpty())
        <div style="height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
            <div style="text-align: center; max-width: 400px;">
                <div style="width: 80px; height: 80px; background-color: #f3f4f6; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i data-lucide="archive" style="width: 40px; height: 40px; color: var(--text-light);"></i>
                </div>
                <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--text-dark);">No Archived Items</h2>
                <p style="font-size: 14px; color: var(--text-medium); line-height: 1.6;">
                    Archived products will appear here.
                </p>
            </div>
        </div>
    @else
        <div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 24px;">
            @foreach($archived as $product)
                <div class="card" style="padding: 0; overflow: hidden; opacity: 0.7;">
                    <img src="{{ $product->images[0] ?? '/placeholder.png' }}" style="width: 100%; height: 200px; object-fit: cover; filter: grayscale(1);">
                    <div style="padding: 16px;">
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 8px;">{{ $product->name }}</h4>
                        <p style="font-size: 12px; color: var(--text-medium); margin-bottom: 12px;">Archived</p>
                        <form action="{{ route('admin.products.update', $product->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="isArchived" value="0">
                            <button class="btn btn-outline" style="width: 100%; font-size: 12px; padding: 6px;">Restore</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
