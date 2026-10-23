@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kegiatan.css') }}">
@endpush

@section('title', 'Kegiatan - MDT Al-Harus')
@section('description', 'Daftar kegiatan dan acara MDT Al-Harus - Pondok Pesantren')

@section('content')
    <!-- Hero Section -->
    <section class="kegiatan-hero">
        <h1>Kegiatan Kami</h1>
        <p>Berbagai kegiatan dan acara yang diadakan di MDT Al-Harus</p>
    </section>

    <div class="kegiatan-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Form Pencarian -->
            <div class="sidebar-widget search-widget">
                <h3>Cari Kegiatan</h3>
                <form action="{{ route('kegiatan.index') }}" method="GET" class="search-form">
                    <div class="search-box">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Cari kegiatan..." 
                            value="{{ request('search') }}"
                            class="search-input"
                        >
                        <button type="submit" class="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    @if(request('search'))
                        <a href="{{ route('kegiatan.index') }}" class="reset-search">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Filter Kategori -->
            <div class="sidebar-widget kategori-widget">
                <h3>Kategori</h3>
                <ul class="kategori-list">
                    <li>
                        <a href="{{ route('kegiatan.index') }}" class="{{ !request('kategori') ? 'active' : '' }}">
                            Semua
                        </a>
                    </li>
                    @foreach($kategoriList as $kategori)
                        <li>
                            <a href="{{ route('kegiatan.index', ['kategori' => $kategori]) }}" 
                               class="{{ request('kategori') == $kategori ? 'active' : '' }}">
                                {{ $kategori }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Kegiatan Terbaru -->
            <div class="sidebar-widget terbaru-widget">
                <h3>Kegiatan Terbaru</h3>
                <ul class="terbaru-list">
                    @foreach($kegiatanTerbaru as $item)
                        <li>
                            <a href="{{ route('kegiatan.show', $item->slug) }}">
                                <div class="terbaru-item">
                                    <div class="terbaru-thumb">
                                        <img src="{{ asset('images/' . $item->gambar) }}" alt="{{ $item->judul }}">
                                    </div>
                                    <div class="terbaru-info">
                                        <h4>{{ Str::limit($item->judul, 30) }}</h4>
                                        <span class="terbaru-date">
                                            <i class="bi bi-calendar3"></i> 
                                            {{ $item->formatted_tanggal }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Konten Utama -->
        <section class="kegiatan">
            <h2>Daftar Kegiatan</h2>

            @if($data->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Tidak ada kegiatan yang ditemukan.</p>
                    <p class="sub-text">Coba gunakan kata kunci lain atau reset pencarian.</p>
                    <a href="{{ route('kegiatan.index') }}" class="cta">Lihat Semua Kegiatan</a>
                </div>
            @else
                @foreach ($data as $item)
                    <div class="row">
                        <div class="kegiatan-img">
                            <img 
                                src="{{ asset('images/' . $item->gambar) }}" 
                                alt="{{ $item->judul }}"
                                onerror="this.src='{{ asset('images/default-kegiatan.jpg') }}'"
                            >
                        </div>
                        <div class="content">
                            @if($item->kategori)
                                <span class="badge">{{ $item->kategori }}</span>
                            @endif

                            <h3>{{ $item->judul }}</h3>

                            <div class="meta-info">
                                <span class="date">
                                    <i class="bi bi-calendar3"></i> 
                                    {{ $item->formatted_tanggal }}
                                </span>
                                <span class="views">
                                    <i class="bi bi-eye"></i> 
                                    {{ number_format($item->views) }} dilihat
                                </span>
                            </div>

                            <p>{{ Str::limit($item->ringkasan, 200) }}</p>

                            <a href="{{ route('kegiatan.show', $item->slug) }}" class="cta">
                                Selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="pagination-container">
                    {{ $data->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection