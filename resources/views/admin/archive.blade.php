@extends('layouts.admin')

@section('title', 'Archive')
@section('subtitle', 'Archived products & categories')

@section('content')
<div class="archive-container" style="height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; max-width: 400px;">
        <div style="width: 80px; height: 80px; background-color: #f3f4f6; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
            <i data-lucide="archive" style="width: 40px; height: 40px; color: var(--text-light);"></i>
        </div>
        <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 12px; color: var(--text-dark);">No Archived Items</h2>
        <p style="font-size: 14px; color: var(--text-medium); line-height: 1.6;">
            Archived products and categories will appear here. Archive items to keep your active catalog clean.
        </p>
    </div>
</div>
@endsection
