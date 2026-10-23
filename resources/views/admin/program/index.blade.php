@extends('layouts.admin')

@section('title', 'Kelola Program Unggulan - Admin Panel')
@section('page-title', 'Kelola Program Unggulan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="admin-wrapper">
    <div class="admin-header-section">
        <div class="header-left">
            <h2><i class="bi bi-star"></i> Daftar Program Unggulan</h2>
            <p class="subtitle">Kelola semua program unggulan MDT Al-Harus</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.program.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Program
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-section">
        <form action="{{ route('admin.program.index') }}" method="GET" class="filter-form">
            <div class="filter-group search-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari program..." value="{{ request('search') }}">
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
            <a href="{{ route('admin.program.index') }}" class="btn btn-reset">
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
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $key => $item)
                        <tr>
                            <td>{{ $key + $programs->firstItem() }}</td>
                            <td>
                                <div class="table-image">
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                                </div>
                            </td>
                            <td><strong>{{ $item->nama }}</strong></td>
                            <td><span class="badge-mapel">{{ $item->kategori ?? '-' }}</span></td>
                            <td><i class="bi {{ $item->icon ?? 'bi-star' }}"></i></td>
                            <td>
                                <span class="status-badge {{ $item->is_active ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.program.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('admin.program.toggle-status', $item->id) }}" class="btn-action btn-toggle" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $item->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    </a>
                                    <form action="{{ route('admin.program.destroy', $item->id) }}" method="POST" class="delete-form">
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
                            <td colspan="7" class="empty-row">
                                <div class="empty-state-admin">
                                    <i class="bi bi-inbox"></i>
                                    <h4>Tidak Ada Data</h4>
                                    <p>Belum ada program unggulan yang tersedia.</p>
                                    <a href="{{ route('admin.program.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Tambah Program
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
            <div class="pagination-wrapper">
                {{ $programs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection