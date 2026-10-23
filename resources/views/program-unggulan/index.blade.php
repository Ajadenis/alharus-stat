@extends('layouts.app')

@section('title', 'Program Unggulan - MDT Al-Harus')
@section('description', 'Program unggulan MDT Al-Harus')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/madrasah.css') }}">
<link rel="stylesheet" href="{{ asset('css/unggul.css') }}">
@endpush
@section('content')
    <!-- Hero -->
    <section class="madrasah-hero">
        <h1>Program Unggulan</h1>
        <p>Program-program andalan MDT Al-Harus untuk mencetak generasi unggul</p>
    </section>

    <div class="program-wrapper">
        <div class="program-grid">
            @forelse($programs as $program)
                <div class="program-card">
                    <div class="program-card-image">
                        <img src="{{ $program->foto_url }}" alt="{{ $program->nama }}">
                        @if($program->kategori)
                            <span class="program-badge">{{ $program->kategori }}</span>
                        @endif
                    </div>
                    <div class="program-card-body">
                        <div class="program-icon">
                            <i class="bi {{ $program->icon ?? 'bi-star' }}"></i>
                        </div>
                        <h3 class="program-card-title">{{ $program->nama }}</h3>
                        <p class="program-card-description">{{ Str::limit($program->deskripsi, 120) }}</p>
                        <a href="{{ route('program-unggulan.show', $program->slug) }}" class="program-card-cta">
                            Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada program unggulan.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection