@extends('layouts.app')

@section('title', 'Fasilitas - MDT Al-Harus')
@section('description', 'Fasilitas MDT Al-Harus')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/fasilitas.css') }}">
<link rel="stylesheet" href="{{ asset('css/madrasah.css') }}">
@endpush

@section('content')
    <!-- Hero -->
    <section class="madrasah-hero">
        <h1>Fasilitas</h1>
        <p>Fasilitas lengkap untuk menunjang pendidikan dan kenyamanan santri</p>
    </section>

    <div class="fasilitas-wrapper">
        <div class="fasilitas-grid">
            @forelse($fasilitas as $item)
                <div class="fasilitas-card">
                    <div class="fasilitas-card-image">
                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        @if($item->kategori)
                            <span class="fasilitas-badge">{{ $item->kategori }}</span>
                        @endif
                    </div>
                    <div class="fasilitas-card-body">
                        <div class="fasilitas-icon">
                            <i class="bi {{ $item->icon ?? 'bi-building' }}"></i>
                        </div>
                        <h3 class="fasilitas-card-title">{{ $item->nama }}</h3>
                        @if($item->jumlah)
                            <div class="fasilitas-jumlah">
                                <i class="bi bi-people"></i> Kapasitas: {{ $item->jumlah }}
                            </div>
                        @endif
                        <p class="fasilitas-card-description">{{ Str::limit($item->deskripsi ?? 'Tidak ada deskripsi', 100) }}</p>
                        <div class="fasilitas-card-footer">
                            <a href="{{ route('fasilitas.show', $item->slug) }}" class="fasilitas-card-cta">
                                Selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada fasilitas.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection