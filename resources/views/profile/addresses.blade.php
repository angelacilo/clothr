@extends('profile.layout')

@section('profile_content')
<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 25px;">Address Book</h2>

<div class="address-grid">
    @foreach($addresses as $addr)
        <div class="address-card" style="{{ $addr->is_default ? 'border-color: #000; background: #f9fafb;' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                <div style="font-weight: 700; font-size: 15px;">{{ $addr->label }}</div>
                @if($addr->is_default)
                    <span style="background: #000; color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">Default</span>
                @endif
            </div>
            <div style="font-size: 14px; color: var(--text-secondary); line-height: 1.6;">
                {{ $addr->first_name }} {{ $addr->last_name }}<br>
                {{ $addr->address_line_1 }}<br>
                {{ $addr->city }}, {{ $addr->zip_code }}<br>
                {{ $addr->country }} ({{ $addr->phone }})
            </div>
            <div style="display: flex; gap: 15px; margin-top: 15px; border-top: 1px solid var(--border-color); padding-top: 15px;">
                @if(!$addr->is_default)
                    <form action="{{ route('profile.addresses.default', $addr->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="color: var(--php-blue); font-size: 13px; font-weight: 700;">Set Default</button>
                    </form>
                @endif
                <form action="{{ route('profile.addresses.delete', $addr->id) }}" method="POST" onsubmit="return confirm('Remove this address?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: #ef4444; font-size: 13px; font-weight: 700;">Remove</button>
                </form>
            </div>
        </div>
    @endforeach
    <div class="address-card" style="border: 2px dashed var(--border-color); display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 150px;">
        <i data-lucide="plus" size="32" style="color: var(--text-muted); margin-bottom: 10px;"></i>
        <span style="font-weight: 600; font-size: 14px; color: var(--text-secondary);">Add New Address</span>
    </div>
</div>
@endsection
