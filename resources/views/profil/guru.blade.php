@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/madrasah.css') }}">
    <link rel="stylesheet" href="{{ asset('css/guru.css') }}">
@endpush

@section('title', 'Profil Guru - MDT Al-Harus')
@section('description', 'Daftar guru MDT Al-Harus')

@section('content')
    <!-- Hero -->
    <section class="madrasah-hero">
        <h1>Profil Guru</h1>
        <p>Para pendidik yang berdedikasi di MDT Al-Harus</p>
    </section>

    <div class="madrasah-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h3>Menu Profil</h3>
                <ul class="page-list">
                    <li>
                        <a href="{{ route('profil.madrasah') }}">
                            <i class="bi bi-building"></i> Madrasah
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profil.guru') }}" class="active">
                            <i class="bi bi-people"></i> Guru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profil.isma') }}">
                            <i class="bi bi-person-badge"></i> ISMA
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Info Guru</h3>
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="info-text">
                        <strong>Jumlah Guru</strong>
                        {{ $guru->count() }} Orang
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-book"></i></div>
                    <div class="info-text">
                        <strong>Mata Pelajaran</strong>
                        {{ $guru->pluck('mata_pelajaran')->unique()->count() }} Bidang
                    </div>
                </div>
            </div>
        </aside>

        <!-- Konten -->
        <section class="madrasah-content">
            <h2>Daftar Guru MDT Al-Harus</h2>

            <div class="guru-grid">
                @forelse($guru as $item)
                    <div class="guru-card">
                        <div class="guru-card-image">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        </div>
                        <div class="guru-card-body">
                            <h3 class="guru-card-name">{{ $item->nama }}</h3>
                            <div class="guru-card-jabatan">
                                <i class="bi bi-briefcase"></i>
                                {{ $item->jabatan_list }}
                            </div>
                            <div class="guru-card-footer">
                              <span class="guru-card-mapel">
                                    <i class="bi bi-book"></i> {{ $item->mata_pelajaran }}
                                </span>
                                <span class="guru-card-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada data guru.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection