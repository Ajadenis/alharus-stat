@extends('layouts.admin')

@section('title', 'Kelola Fasilitas - Admin Panel')
@section('page-title', 'Kelola Fasilitas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="admin-wrapper">
    <div class="admin-header-section">
        <div class="header-left">
            <h2><i class="bi bi-building"></i> Daftar Fasilitas</h2>
            <p class="subtitle">Kelola semua fasilitas MDT Al-Harus</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.fasilitas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Fasilitas
            </a>
        </div>
    </div>

    <div class="filter-section">
        <form action="{{ route('admin.fasilitas.index') }}" method="GET" class="filter-form">
            <div class="filter-group search-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari fasilitas..." value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList ?? [] as $kategori)
                        <option value="{{ $kategori }}" {{ request('kategori') == $kategori ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-reset">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </a>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="table-container">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fasilitas as $key => $item)
                        <tr>
                            <td>{{ $key + $fasilitas->firstItem() }}</td>
                            <td>
                                <div class="table-image">
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                                </div>
                            </td>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td><span class="badge-mapel">{{ $item->kategori ?? '-' }}</span></td>
                            <td>{{ $item->jumlah ?? '-' }}</td>
                            <td><i class="bi {{ $item->icon ?? 'bi-building' }}"></i></td>
                            <td>
                                <span class="status-badge {{ $item->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.fasilitas.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('admin.fasilitas.toggle-status', $item->id) }}" class="btn-action btn-toggle" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $item->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    </a>
                                    <form action="{{ route('admin.fasilitas.destroy', $item->id) }}" method="POST" class="delete-form">
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
                            <td colspan="8" class="empty-row">
                                <div class="empty-state-admin">
                                    <i class="bi bi-inbox"></i>
                                    <h4>Tidak Ada Data</h4>
                                    <p>Belum ada fasilitas yang tersedia.</p>
                                    <a href="{{ route('admin.fasilitas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Tambah Fasilitas
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($fasilitas->hasPages())
            <div class="pagination-wrapper">
                {{ $fasilitas->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection