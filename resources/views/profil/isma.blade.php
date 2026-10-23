@extends('layouts.app')

@section('title', 'ISMA - MDT Al-Harus')
@section('description', 'Ikatan Santri Madrasah Al-Harus')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/isma.css') }}">
@endpush
@section('content')
    <!-- Hero -->
    <section class="isma-hero">
        <h1>ISMA</h1>
        <p>Ikatan Santri Madrasah Al-Harus</p>
    </section>

    <div class="isma-wrapper">
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
                        <a href="{{ route('profil.guru') }}">
                            <i class="bi bi-people"></i> Guru
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('profil.isma') }}" class="active">
                            <i class="bi bi-person-badge"></i> ISMA
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-widget">
                <h3>Info ISMA</h3>
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="info-text">
                        <strong>Jumlah Anggota</strong>
                        {{ $isma->count() }} Orang
                    </div>
                </div>
            </div>
        </aside>

        <!-- Konten -->
        <section class="isma-content">
            <h2>Daftar Anggota ISMA</h2>

            <div class="isma-grid">
                @forelse($isma as $item)
                    <div class="isma-card">
                        <div class="isma-card-image">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        </div>
                        <div class="isma-card-body">
                            <h3 class="isma-card-name">{{ $item->nama }}</h3>
                            <div class="isma-card-jabatan">
                                <i class="bi bi-briefcase"></i> {{ $item->jabatan_list }}
                            </div>
                            <div class="isma-card-footer">
                                <span class="isma-card-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada data anggota ISMA.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
