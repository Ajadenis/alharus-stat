@extends('layouts.app')

@section('title', $program->nama . ' - MDT Al-Harus')
@section('description', Str::limit($program->deskripsi, 150))

@section('content')
    <div class="program-detail-wrapper">
        <div class="program-detail-container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                <span class="separator">/</span>
                <a href="{{ route('program-unggulan.index') }}">Program Unggulan</a>
                <span class="separator">/</span>
                <span class="current">{{ $program->nama }}</span>
            </nav>

            <!-- Detail Content -->
            <article class="program-detail">
                <div class="program-detail-image">
                    <img src="{{ $program->foto_url }}" alt="{{ $program->nama }}">
                    @if($program->kategori)
                        <span class="program-detail-badge">{{ $program->kategori }}</span>
                    @endif
                </div>

                <div class="program-detail-body">
                    <div class="program-detail-icon">
                        <i class="bi {{ $program->icon ?? 'bi-star' }}"></i>
                    </div>
                    <h1>{{ $program->nama }}</h1>
                    <div class="program-detail-description">
                        <p>{{ $program->deskripsi }}</p>
                    </div>
                    <a href="{{ route('program-unggulan.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Program Unggulan
                    </a>
                </div>
            </article>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/unggul.css') }}">
@endpush