@extends('layouts.shop')

@section('title', $page->title)

@section('extra_css')
    .info-page { max-width: 800px; margin: 0 auto; padding: 80px 24px; }
    .info-header { margin-bottom: 60px; text-align: center; }
    .info-title { font-size: 48px; font-weight: 800; margin-bottom: 16px; letter-spacing: -0.02em; }
    .info-content { line-height: 1.8; color: var(--text-secondary); font-size: 16px; }
    .info-content h2 { color: var(--text-primary); font-size: 24px; font-weight: 700; margin: 40px 0 20px; }
    .info-content h3 { color: var(--text-primary); font-size: 18px; font-weight: 700; margin: 30px 0 15px; }
    .info-content p { margin-bottom: 20px; }
    .info-content ul { margin-bottom: 20px; padding-left: 20px; list-style: disc; }
    .info-content li { margin-bottom: 10px; }
    .info-content .faq-item { margin-bottom: 40px; border-bottom: 1px solid var(--border-color); padding-bottom: 30px; }
    .info-content .faq-item:last-child { border-bottom: none; }
    .info-content .faq-q { font-weight: 800; color: var(--text-primary); margin-bottom: 12px; font-size: 18px; }
    .info-content .faq-a { color: var(--text-secondary); }
    .info-content .contact-card { background: var(--bg-secondary); padding: 40px; border-radius: 16px; margin: 40px 0; border: 1px solid var(--border-color); }
    .info-content .contact-card h3 { margin-top: 0; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); }
    .info-content .contact-card p { margin-bottom: 25px; font-size: 18px; color: var(--text-primary); font-weight: 600; }
    .info-content .contact-card p:last-child { margin-bottom: 0; }

    @media (max-width: 768px) {
        .info-page { padding: 40px 24px; }
        .info-title { font-size: 32px; }
    }
@endsection

@section('content')
<div class="info-page">
    <div class="info-header">
        <h1 class="info-title">{{ $page->title }}</h1>
    </div>
    <div class="info-content">
        {!! $page->body !!}
    </div>
</div>
@endsection
