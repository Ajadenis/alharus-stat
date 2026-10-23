@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush
@section('title', 'Admin - Daftar Kegiatan')

@section('content')
<div class="admin-container">
    <div class="page-header">
        <h1>Daftar Kegiatan</h1>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kegiatan
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="admin-filter">
        <form action="{{ route('admin.kegiatan.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Cari kegiatan..." value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <select name="kategori">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kategori)
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
                <i class="bi bi-search"></i> Filter
            </button>
            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-reset">Reset</a>
        </form>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Kegiatan -->
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Gambar</th>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Views</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kegiatan as $key => $item)
                    <tr>
                        <td>{{ $key + $kegiatan->firstItem() }}</td>
                        <td>
                            <img src="{{ asset('images/' . $item->gambar) }}" alt="{{ $item->judul }}" class="table-img">
                        </td>
                        <td>{{ Str::limit($item->judul, 30) }}</td>
                        <td><span class="badge">{{ $item->kategori ?? '-' }}</span></td>
                        <td>{{ $item->formatted_tanggal }}</td>
                        <td>{{ $item->views }}</td>
                        <td>
                            <span class="status-badge {{ $item->is_active ? 'active' : 'inactive' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.kegiatan.edit', $item->id) }}" class="btn-edit" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('admin.kegiatan.toggle-status', $item->id) }}" class="btn-toggle" title="Toggle Status">
                                    <i class="bi {{ $item->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                </a>
                                <form action="{{ route('admin.kegiatan.destroy', $item->id) }}" method="POST" class="delete-form" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data kegiatan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $kegiatan->links() }}
    </div>
</div>
@endsection