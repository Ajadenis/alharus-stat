@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('title', 'Beranda - MDT Al-Harus')
@section('description', 'Selamat datang di MDT Al-Harus - Portal berita dan kegiatan santri MDT Al-Harus')

@section('content')
    <!-- Section Hero Start -->
    <section class="hero" id="home">
        <main class="content">
            <h1>Dari Santri Untuk Santri</h1>
            <p>Kreatif & Inovatif</p>
            <a href="#main" class="cta-hero">
                Lihat Kegiatan <i class="bi bi-arrow-down"></i>
            </a>
        </main>
    </section>
    <!-- Section Hero End -->

    <!-- Slideshow Start -->
    <div class="slideshow-container">
        <div class="slideshow">
            @php
                $slides = [
                    ['image' => 'kaulinan-barudak/Rerebonan/rebon-7.jpg', 'caption' => 'Kegiatan Rerebonan - Kaulinan Barudak'],
                    ['image' => 'kaulinan-barudak/Sapintrong/sapintrong-4.jpg', 'caption' => 'Kegiatan Sapintrong - Kaulinan Barudak'],
                    ['image' => 'isra-miraj/15.jpg', 'caption' => 'Peringatan Isra Mi\'raj'],
                ];
            @endphp

            @foreach($slides as $slide)
                <div class="mySlides fade">
                    <img 
                        src="{{ asset('images/' . $slide['image']) }}" 
                        alt="{{ $slide['caption'] }}"
                        onerror="this.src='{{ asset('images/default.jpg') }}'"
                    />
                    <div class="slide-caption">{{ $slide['caption'] }}</div>
                </div>
            @endforeach

            <a class="prev" onclick="plusSlides(-1)">❮</a>
            <a class="next" onclick="plusSlides(1)">❯</a>
        </div>
        <div class="dot-container">
            @foreach($slides as $key => $slide)
                <span class="dot" onclick="currentSlide({{ $key + 1 }})"></span>
            @endforeach
        </div>
    </div>
    <!-- Slideshow End -->

    <!-- Section Main Start -->
    <section id="main" class="main">
        <div class="section-header">
            <h2>Berita & Kegiatan Terbaru</h2>
            <a href="{{ route('kegiatan.index') }}" class="view-all">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="kegiatan-grid">
            @forelse($kegiatanTerbaru as $item)
                <div class="kegiatan-card">
                    <div class="card-image">
                        <img 
                            src="{{ asset('images/' . $item->gambar) }}" 
                            alt="{{ $item->judul }}"
                            onerror="this.src='{{ asset('images/default.jpg') }}'"
                        >
                        @if($item->kategori)
                            <span class="card-badge">{{ $item->kategori }}</span>
                        @endif
                    </div>
                    <div class="card-content">
                        <h3>{{ Str::limit($item->judul, 50) }}</h3>
                        <div class="card-meta">
                            <span class="card-date">
                                <i class="bi bi-calendar3"></i> 
                                {{ $item->formatted_tanggal }}
                            </span>
                            <span class="card-views">
                                <i class="bi bi-eye"></i> 
                                {{ number_format($item->views) }}
                            </span>
                        </div>
                        <p>{{ Str::limit($item->ringkasan, 120) }}</p>
                        <a href="{{ route('kegiatan.show', $item->slug) }}" class="card-cta">
                            Baca Selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada kegiatan terbaru.</p>
                    <p class="sub-text">Admin akan segera menambahkan kegiatan.</p>
                </div>
            @endforelse
        </div>
    </section>
    <!-- Section Main End -->
@endsection

@push('slideshow')
    <script src="{{ asset('js/slideshow.js') }}"></script>
@endpush