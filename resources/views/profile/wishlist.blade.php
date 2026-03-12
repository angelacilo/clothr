@extends('profile.layout')

@section('profile_content')
<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 25px;">My Wishlist</h2>

@if($wishlistItems->count() > 0)
    <div class="products__grid">
        @foreach($wishlistItems as $item)
            @php $product = $item->product; @endphp
            @if($product)
                <div class="product-card">
                    <a href="{{ route('product', $product->id) }}" class="product-card__img-box">
                        <img src="{{ $product->images[0] ?? '/placeholder.png' }}" class="product-card__img">
                    </a>
                    <div style="padding-top: 15px;">
                        <h3 style="font-size: 14px; font-weight: 700; color: #000; margin-bottom: 5px;">{{ $product->name }}</h3>
                        <div class="price" style="font-size: 15px; font-weight: 800; color: #000;">₱{{ number_format($product->price, 2) }}</div>
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button onclick="addToCartGlobal({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, '{{ $product->images[0] ?? '' }}')" class="btn-black" style="flex: 1; padding: 10px; border-radius: 6px; font-size: 12px; font-weight: 700;">Add to Bag</button>
                            <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn-outline" style="padding: 10px; border-radius: 6px; color: #ef4444; border-color: #fecaca;"><i data-lucide="trash-2" size="16"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@else
    <div style="text-align: center; padding: 60px 0; border: 1px dashed var(--border-color); border-radius: 12px; background: #fafafa;">
        <i data-lucide="heart" size="48" style="color: var(--border-color); margin-bottom: 20px;"></i>
        <p style="color: var(--text-muted); font-size: 16px;">Your wishlist is empty.</p>
        <a href="{{ route('shop') }}" class="btn-black" style="display: inline-block; margin-top: 20px; padding: 12px 30px; border-radius: 8px;">Start Shopping</a>
    </div>
@endif
@endsection
