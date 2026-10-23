@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')
@section('page-title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- ======================================== -->
    <!-- WELCOME SECTION -->
    <!-- ======================================== -->
    <div class="welcome-section">
        <div class="welcome-text">
            <h2>Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
            <p>Berikut adalah ringkasan data MDT Al-Harus</p>
        </div>
        <div class="welcome-date">
            <i class="bi bi-calendar3"></i>
            {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    {{-- ======================================== --}}
{{-- STATISTIK PENDAPATAN INFAK --}}
{{-- ======================================== --}}
<div class="stats-section-title">
    <div class="section-header-with-filter">
        <h3><i class="bi bi-cash-coin"></i> Statistik Pendapatan Infak</h3>
        <form method="GET" action="{{ route('admin.dashboard') }}" class="year-filter">
            <label for="infak_year">Tahun:</label>
            <select name="infak_year" id="infak_year" onchange="this.form.submit()">
                @foreach($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ $selectedYear == $tahun ? 'selected' : '' }}>
                        {{ $tahun }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- 4 CARD STATISTIK INFAK --}}
<div class="stats-grid-infak">
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="bi bi-cash-stack"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($totalInfakTahunIni, 0, ',', '.') }}</h3>
            <p>Total Infak Tahun {{ $selectedYear }}</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="bi bi-calendar-month"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($totalInfakBulanIni, 0, ',', '.') }}</h3>
            <p>Infak Bulan Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon yellow">
            <i class="bi bi-calendar-day"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($totalInfakHariIni, 0, ',', '.') }}</h3>
            <p>Infak Hari Ini</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="bi bi-graph-up"></i>
        </div>
        <div class="stat-info">
            <h3>Rp {{ number_format($rataRataInfakBulanan, 0, ',', '.') }}</h3>
            <p>Rata-rata per Transaksi</p>
        </div>
    </div>
</div>

{{-- GRAFIK INFAK DENGAN DROPDOWN --}}
<div class="dashboard-grid">
    {{-- Grafik Infak --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="bi bi-bar-chart-line"></i> Grafik Pendapatan Infak</h3>
            <div class="chart-dropdown-wrapper">
                <select id="chartType" class="chart-dropdown" onchange="changeChart(this.value)">
                    <option value="bulan">Per Bulan ({{ $selectedYear }})</option>
                    <option value="tahun">Per Tahun</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            {{-- GRAFIK PER BULAN --}}
            <div id="chartBulan" class="chart-view active">
                @php
                    $maxInfakBulanan = max(array_column($infakPerBulan, 'total')) ?: 1;
                @endphp
                <div class="chart-container">
                    @foreach($infakPerBulan as $data)
                        @php
                            $height = ($data['total'] / $maxInfakBulanan) * 100;
                        @endphp
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar-value">
                                @if($data['total'] > 0)
                                    Rp {{ number_format($data['total'], 0, ',', '.') }}
                                @endif
                            </div>
                            <div class="chart-bar" style="height: {{ $height }}%"></div>
                            <div class="chart-bar-label">{{ $data['bulan'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-summary">
                    <span><strong>Total {{ $selectedYear }}:</strong> Rp {{ number_format($totalBulanan, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- GRAFIK PER TAHUN --}}
            <div id="chartTahun" class="chart-view">
                @php
                    $maxInfakTahunan = max(array_column($infakPerTahun, 'total')) ?: 1;
                @endphp
                <div class="chart-container">
                    @foreach($infakPerTahun as $data)
                        @php
                            $height = ($data['total'] / $maxInfakTahunan) * 100;
                        @endphp
                        <div class="chart-bar-wrapper">
                            <div class="chart-bar-value">
                                @if($data['total'] > 0)
                                    Rp {{ number_format($data['total'], 0, ',', '.') }}
                                @endif
                            </div>
                            <div class="chart-bar" style="height: {{ $height }}%"></div>
                            <div class="chart-bar-label">{{ $data['tahun'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="chart-summary">
                    <span><strong>Total 5 Tahun:</strong> Rp {{ number_format($totalTahunan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Infak Terbaru --}}
    <div class="dashboard-card">
        <div class="card-header">
            <h3><i class="bi bi-clock-history"></i> Infak Terbaru</h3>
            <a href="{{ route('admin.infak.index') }}" class="view-all">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="card-body">
            @forelse($infakTerbaru ?? [] as $item)
                <div class="recent-item">
                    <div class="recent-icon green">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="recent-info">
                        <h4>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</h4>
                        <div class="recent-meta">
                            <span class="recent-date">
                                <i class="bi bi-calendar3"></i>
                                {{ $item->tanggal_bayar ? $item->tanggal_bayar->format('d M Y') : ($item->bulan . '/' . $item->tahun) }}
                            </span>
                            <span class="recent-source">
                                <i class="bi bi-person"></i>
                                {{ $item->sumber ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state-admin">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada data infak</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

    <!-- ======================================== -->
    <!-- STATISTIK KEGIATAN -->
    <!-- ======================================== -->
    <div class="stats-section-title">
        <h3><i class="bi bi-calendar-event"></i> Statistik Kegiatan</h3>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-newspaper"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalKegiatan }}</h3>
                <p>Total Kegiatan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $kegiatanAktif }}</h3>
                <p>Kegiatan Aktif</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $kegiatanNonaktif }}</h3>
                <p>Kegiatan Nonaktif</p>
            </div>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- KEGIATAN TERBARU & QUICK ACTIONS -->
    <!-- ======================================== -->
    <div class="dashboard-grid">
        <!-- Kegiatan Terbaru -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-clock-history"></i> Kegiatan Terbaru</h3>
                <a href="{{ route('admin.kegiatan.index') }}" class="view-all">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @forelse($kegiatanTerbaru ?? [] as $item)
                    <div class="recent-item">
                        <div class="recent-image">
                            <img src="{{ asset('images/' . $item->gambar) }}" alt="{{ $item->judul }}">
                        </div>
                        <div class="recent-info">
                            <h4>{{ Str::limit($item->judul, 40) }}</h4>
                            <div class="recent-meta">
                                <span class="recent-date">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $item->tanggal ? $item->tanggal->format('d M Y') : '-' }}
                                </span>
                                <span class="recent-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-admin">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada kegiatan terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-lightning"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <!-- KEGIATAN -->
                    <a href="{{ route('admin.kegiatan.create') }}" class="quick-action">
                        <i class="bi bi-calendar-plus"></i>
                        <div>
                            <h4>Tambah Kegiatan</h4>
                            <p>Tambahkan kegiatan baru</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.kegiatan.index') }}" class="quick-action">
                        <i class="bi bi-calendar-check"></i>
                        <div>
                            <h4>Kelola Kegiatan</h4>
                            <p>Lihat dan edit semua kegiatan</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- INFORMASI TAMBAHAN -->
    <!-- ======================================== -->
    <div class="info-grid">
        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-tags"></i>
            </div>
            <div class="info-content">
                <h4>Kategori Kegiatan</h4>
                <p>{{ $totalKategori ?? 0 }} Kategori</p>
                <div class="kategori-tags">
                    @foreach($kategoriList ?? [] as $kategori)
                        <span class="tag">{{ $kategori }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-calendar-range"></i>
            </div>
            <div class="info-content">
                <h4>Kegiatan Tahun Ini</h4>
                <p>{{ $kegiatanTahunIni ?? 0 }} Kegiatan</p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, ($kegiatanTahunIni ?? 0) * 10) }}%"></div>
                </div>
            </div>
        </div>
    </div>


    <!-- ======================================== -->
    <!-- STATISTIK GURU -->
    <!-- ======================================== -->
    <div class="stats-section-title">
        <h3><i class="bi bi-people"></i> Statistik Guru</h3>
    </div>
    <div class="stats-grid stats-grid-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalGuru ?? 0 }}</h3>
                <p>Total Guru</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $guruAktif ?? 0 }}</h3>
                <p>Guru Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-person-x"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $guruNonaktif ?? 0 }}</h3>
                <p>Guru Nonaktif</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Guru Terbaru -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-people"></i> Guru Terbaru</h3>
                <a href="{{ route('admin.guru.index') }}" class="view-all">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @forelse($guruTerbaru ?? [] as $item)
                    <div class="recent-item">
                        <div class="recent-image">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        </div>
                        <div class="recent-info">
                            <h4>{{ Str::limit($item->nama, 40) }}</h4>
                            <div class="recent-meta">
                                <span class="recent-date">
                                    <i class="bi bi-briefcase"></i>
                                    {{ $item->jabatan_list }}
                                </span>
                                <span class="recent-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-admin">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada guru terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions Guru -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-lightning"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <!-- GURU -->
                    <a href="{{ route('admin.guru.create') }}" class="quick-action">
                        <i class="bi bi-person-plus"></i>
                        <div>
                            <h4>Tambah Guru</h4>
                            <p>Tambahkan guru baru</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.guru.index') }}" class="quick-action">
                        <i class="bi bi-people"></i>
                        <div>
                            <h4>Kelola Guru</h4>
                            <p>Lihat dan edit semua guru</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================== -->
    <!-- STATISTIK ISMA -->
    <!-- ======================================== -->
    <div class="stats-section-title">
        <h3><i class="bi bi-person-badge"></i> Statistik ISMA</h3>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalIsma ?? 0 }}</h3>
                <p>Total ISMA</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $ismaAktif ?? 0 }}</h3>
                <p>ISMA Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-person-x"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $ismaNonaktif ?? 0 }}</h3>
                <p>ISMA Nonaktif</p>
            </div>
        </div>
    </div>

        <!-- ISMA Terbaru -->
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-person-badge"></i> ISMA Terbaru</h3>
                <a href="{{ route('admin.isma.index') }}" class="view-all">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @forelse($ismaTerbaru ?? [] as $item)
                    <div class="recent-item">
                        <div class="recent-image">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        </div>
                        <div class="recent-info">
                            <h4>{{ Str::limit($item->nama, 40) }}</h4>
                            <div class="recent-meta">
                                <span class="recent-date">
                                    <i class="bi bi-briefcase"></i>
                                    {{ $item->jabatan_list }}
                                </span>
                                <span class="recent-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-admin">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada anggota ISMA terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions Isma -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-lightning"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <!-- ISMA -->
                    <a href="{{ route('admin.isma.create') }}" class="quick-action">
                        <i class="bi bi-person-badge"></i>
                        <div>
                            <h4>Tambah ISMA</h4>
                            <p>Tambahkan anggota baru</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.isma.index') }}" class="quick-action">
                        <i class="bi bi-person-badge-fill"></i>
                        <div>
                            <h4>Kelola ISMA</h4>
                            <p>Lihat dan edit semua anggota</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- ======================================== -->
    <!-- STATISTIK SANTRI -->
    <!-- ======================================== -->
    <div class="stats-section-title">
        <h3><i class="bi bi-mortarboard"></i> Statistik Santri</h3>
    </div>
    <div class="stats-grid stats-grid-3">
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalSantri ?? 0 }}</h3>
                <p>Total Santri</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="bi bi-person-check"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $santriAktif ?? 0 }}</h3>
                <p>Santri Aktif</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="bi bi-person-x"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $santriNonaktif ?? 0 }}</h3>
                <p>Santri Nonaktif</p>
            </div>
        </div>
    </div>
        <!-- Santri Terbaru -->
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-mortarboard"></i> Santri Terbaru</h3>
                <a href="{{ route('admin.santri.index') }}" class="view-all">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                @forelse($santriTerbaru ?? [] as $item)
                    <div class="recent-item">
                        <div class="recent-image">
                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                        </div>
                        <div class="recent-info">
                            <h4>{{ Str::limit($item->nama, 40) }}</h4>
                            <div class="recent-meta">
                                <span class="recent-date">
                                    <i class="bi bi-book"></i>
                                    {{ $item->kelas }}
                                </span>
                                <span class="recent-status {{ $item->is_active ? 'active' : 'inactive' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-admin">
                        <i class="bi bi-inbox"></i>
                        <p>Belum ada santri terbaru</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        <!-- Quick Actions SANTRI -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="bi bi-lightning"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <!-- SANTRI -->
                    <a href="{{ route('admin.santri.create') }}" class="quick-action">
                        <i class="bi bi-mortarboard"></i>
                        <div>
                            <h4>Tambah Santri</h4>
                            <p>Tambahkan santri baru</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.santri.index') }}" class="quick-action">
                        <i class="bi bi-mortarboard-fill"></i>
                        <div>
                            <h4>Kelola Santri</h4>
                            <p>Lihat dan edit semua santri</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>






















</div>

@push('scripts')
<script>
    // ========================================
    // TOGGLE CHART (Bulan / Tahun)
    // ========================================
    function changeChart(type) {
        const chartBulan = document.getElementById('chartBulan');
        const chartTahun = document.getElementById('chartTahun');

        if (type === 'bulan') {
            chartBulan.classList.add('active');
            chartTahun.classList.remove('active');
        } else {
            chartBulan.classList.remove('active');
            chartTahun.classList.add('active');
        }
    }
</script>
@endpush
@endsection

