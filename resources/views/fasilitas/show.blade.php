@extends('layouts.app')

@section('title', $fasilitas->nama . ' - MDT Al-Harus')
@section('description', Str::limit($fasilitas->deskripsi ?? 'Fasilitas MDT Al-Harus', 150))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fasilitas.css') }}">
<link rel="stylesheet" href="{{ asset('css/madrasah.css') }}">
@endpush

@section('content')
    <div class="fasilitas-detail-wrapper">
        <div class="fasilitas-detail-container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="{{ route('home') }}">Beranda</a>
                <span class="separator">/</span>
                <a href="{{ route('fasilitas.index') }}">Fasilitas</a>
                <span class="separator">/</span>
                <span class="current">{{ $fasilitas->nama }}</span>
            </nav>

            <!-- Detail Content -->
            <article class="fasilitas-detail">
                <div class="fasilitas-detail-image">
                    <img src="{{ $fasilitas->foto_url }}" alt="{{ $fasilitas->nama }}">
                    @if($fasilitas->kategori)
                        <span class="fasilitas-detail-badge">{{ $fasilitas->kategori }}</span>
                    @endif
                </div>

                <div class="fasilitas-detail-body">
                    <div class="fasilitas-detail-icon">
                        <i class="bi {{ $fasilitas->icon ?? 'bi-building' }}"></i>
                    </div>
                    <h1>{{ $fasilitas->nama }}</h1>
                    @if($fasilitas->jumlah)
                        <div class="fasilitas-detail-jumlah">
                            <i class="bi bi-people"></i> Kapasitas: {{ $fasilitas->jumlah }}
                        </div>
                    @endif
                    <div class="fasilitas-detail-description">
                        <p>{{ $fasilitas->deskripsi ?? 'Tidak ada deskripsi untuk fasilitas ini.' }}</p>
                    </div>
                    <a href="{{ route('fasilitas.index') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Fasilitas
                    </a>
                </div>
            </article>
        </div>
    </div>
@endsection