@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('title', 'Admin - Tambah Kegiatan')

@section('content')
<div class="admin-container">
    <div class="page-header">
        <h1>Tambah Kegiatan</h1>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="judul">Judul <span class="required">*</span></label>
                <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                @error('judul')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori') }}" placeholder="Contoh: Keagamaan, Budaya, Acara">
                @error('kategori')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}">
                    @error('tanggal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                    <class="form-text">Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
                    @error('gambar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="ringkasan">Ringkasan <span class="required">*</span></label>
                <textarea name="ringkasan" id="ringkasan" rows="3" class="form-control @error('ringkasan') is-invalid @enderror" required>{{ old('ringkasan') }}</textarea>
                @error('ringkasan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi <span class="required">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="6" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    Aktif
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection