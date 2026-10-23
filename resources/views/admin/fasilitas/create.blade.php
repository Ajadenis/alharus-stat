@extends('layouts.admin')

@section('title', 'Tambah Fasilitas - Admin Panel')
@section('page-title', 'Tambah Fasilitas')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <a href="{{ route('admin.fasilitas.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2><i class="bi bi-plus-circle"></i> Tambah Fasilitas</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.fasilitas.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama">Nama Fasilitas <span class="required">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama fasilitas">
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori') }}" placeholder="Contoh: Ibadah, Akademik, Olahraga">
                    @error('kategori')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="icon">Icon</label>
                    <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="Contoh: bi-building, bi-book">
                    <small class="form-text">Gunakan icon Bootstrap: <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></small>
                    @error('icon')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="jumlah">Jumlah / Kapasitas</label>
                    <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" value="{{ old('jumlah') }}" placeholder="Contoh: 100, 200, 1">
                    @error('jumlah')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                    <class="form-text">Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
                    @error('foto')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Tulis deskripsi fasilitas...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span>Aktif</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection