@extends('layouts.admin')

@section('title', 'Kelola Infak - Admin Panel')
@section('page-title', 'Kelola Infak')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-infak.css') }}">
@endpush

@section('content')
<div class="admin-wrapper">
    <!-- Header -->
    <div class="admin-header-section">
        <div class="header-left">
            <h2><i class="bi bi-cash-stack"></i> Daftar Infak</h2>
            <p class="subtitle">Kelola semua pembayaran infak santri</p>
        </div>
        <div class="header-right">
            <!-- ALL ACCEPT BUTTON -->
            @php
                $pendingCount = $infaks->where('status', 'pending')->count();
            @endphp
            @if($pendingCount > 0)
                <form action="{{ route('admin.infak.all-accept') }}" method="POST" id="allAcceptForm" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-all-accept" onclick="return confirm('Konfirmasi SEMUA pembayaran pending ({{ $pendingCount }} data)?')">
                        <i class="bi bi-check-all"></i> All Accept ({{ $pendingCount }})
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-section">
        <form action="{{ route('admin.infak.index') }}" method="GET" class="filter-form">
            <div class="filter-group search-group">
                <i class="bi bi-search"></i>
                <input type="text" name="nisa" placeholder="Cari NISA..." value="{{ request('nisa') }}">
            </div>
            <div class="filter-group">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="belum_bayar" {{ request('status') == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="lunas" {{ request('status') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('admin.infak.index') }}" class="btn btn-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            {{ session('warning') }}
        </div>
    @endif

    <!-- Table -->
    <div class="table-container">
        <div class="table-header-toolbar">
            <div class="table-title">
                <i class="bi bi-list-ul"></i> Data Infak
                <span class="count">{{ $infaks->total() }}</span>
            </div>
            <div class="table-info">
                Menampilkan {{ $infaks->firstItem() }} - {{ $infaks->lastItem() }} dari {{ $infaks->total() }} data
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="width:80px;">NISA</th>
                        <th>Nama Santri</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Metode</th>
                        <th style="width:60px;">Bukti</th>
                        <th style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infaks as $key => $item)
                        <tr>
                            <td>{{ $key + $infaks->firstItem() }}</td>
                            <td><strong>{{ $item->nisa }}</strong></td>
                            <td>{{ $item->santri->nama ?? '-' }}</td>
                            <td>{{ $item->bulan }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ $item->jumlah_rupiah }}</td>
                            <td>
                                @php
                                    $statusMap = [
                                        'lunas' => ['class' => 'success', 'label' => '✅ Lunas'],
                                        'pending' => ['class' => 'warning', 'label' => '⏳ Pending'],
                                        'belum_bayar' => ['class' => 'danger', 'label' => '❌ Belum'],
                                    ];
                                    $status = $statusMap[$item->status] ?? ['class' => 'danger', 'label' => $item->status];
                                @endphp
                                <span class="infak-badge {{ $status['class'] }}">
                                    <span class="dot"></span>
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($item->metode_pembayaran)
                                    @php
                                        $metodeMap = [
                                            'tunai' => '💰 Tunai',
                                            'transfer' => '🏦 Transfer',
                                            'qris' => '📱 QRIS',
                                        ];
                                    @endphp
                                    {{ $metodeMap[$item->metode_pembayaran] ?? $item->metode_pembayaran }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->bukti_pembayaran)
                                    <a href="{{ asset('images/' . $item->bukti_pembayaran) }}" target="_blank" class="btn-action btn-view" title="Lihat Bukti">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($item->status == 'pending')
                                        {{-- KONFIRMASI --}}
                                        <form action="{{ route('admin.infak.konfirmasi', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-toggle" title="Konfirmasi" onclick="return confirm('Konfirmasi pembayaran ini?')">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        {{-- TOLAK --}}
                                        <form action="{{ route('admin.infak.tolak', $item->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn-action btn-delete" title="Tolak" onclick="return confirm('Tolak pembayaran ini?')">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted" style="font-size:0.7rem;">-</span>
                                    @endif

                                    {{-- HAPUS --}}
                                    <form action="{{ route('admin.infak.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-row">
                                <div class="empty-state-admin">
                                    <i class="bi bi-inbox"></i>
                                    <h4>Tidak Ada Data</h4>
                                    <p>Belum ada data infak.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($infaks->hasPages())
            <div class="pagination-wrapper">
                {{ $infaks->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
