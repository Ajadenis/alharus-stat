@extends('layouts.admin')

@section('title', 'Kelola Santri - Admin Panel')
@section('page-title', 'Kelola Santri')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="admin-wrapper">
    <div class="admin-header-section">
        <div class="header-left">
            <h2><i class="bi bi-mortarboard"></i> Daftar Santri</h2>
            <p class="subtitle">Kelola semua santri MDT Al-Harus (MDA Kelas 1-4)</p>
        </div>
        <div class="header-right">
            <a href="{{ route('admin.santri.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Santri
            </a>
        </div>
    </div>

    <div class="filter-section">
        <form action="{{ route('admin.santri.index') }}" method="GET" class="filter-form">
            <div class="filter-group search-group">
                <i class="bi bi-search"></i>
                <input type="text" name="search" placeholder="Cari santri (Nama/NISA/NIS)..." value="{{ request('search') }}">
            </div>
            <div class="filter-group">
    <select name="kelas">
        <option value="">Semua Kelas</option>
        @foreach(['MDA 1', 'MDA 2', 'MDA 3', 'MDA 4'] as $kelas)
            <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                {{ $kelas }}
            </option>
        @endforeach
    </select>
</div>
            <div class="filter-group">
                <select name="jenis_kelamin">
                    <option value="">Semua Jenis Kelamin</option>
                    <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div class="filter-group">
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
            <button type="submit" class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('admin.santri.index') }}" class="btn btn-reset">
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
                        <th>NISA</th>
                        <th>Nama</th>
                        <th>Kelas</th>
                        <th>Jenis Kelamin</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santri as $key => $item)
                        <tr>
                            <td>{{ $key + $santri->firstItem() }}</td>
                            <td>
                                <div class="table-image">
                                    <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}">
                                </div>
                            </td>
                            <td><strong>{{ $item->nisa ?? '-' }}</strong></td>
                            <td>{{ $item->nama }}</td>
                            <td><span class="badge-mapel">{{ $item->kelas }}</span></td>
                            <td>{{ $item->jenis_kelamin }}</td>
                            <td>
                                <span class="status-badge {{ $item->status == 'aktif' ? 'active' : 'inactive' }}">
                                    <span class="status-dot"></span>
                                    {{ ucfirst($item->status ?? 'aktif') }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.santri.edit', $item->id) }}" class="btn-action btn-edit" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('admin.santri.toggle-status', $item->id) }}" class="btn-action btn-toggle" title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi {{ $item->is_active ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                    </a>
                                    <form action="{{ route('admin.santri.destroy', $item->id) }}" method="POST" class="delete-form">
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
                                    <p>Belum ada santri yang tersedia.</p>
                                    <a href="{{ route('admin.santri.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle"></i> Tambah Santri
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($santri->hasPages())
            <div class="pagination-wrapper">
                {{ $santri->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection