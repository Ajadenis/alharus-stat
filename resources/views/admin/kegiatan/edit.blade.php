@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('title', 'Admin - Edit Kegiatan')

@section('content')
<div class="admin-container">
    <div class="page-header">
        <h1>Edit Kegiatan</h1>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.kegiatan.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="judul">Judul <span class="required">*</span></label>
                <input type="text" name="judul" id="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $kegiatan->judul) }}" required>
                @error('judul')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="text" name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori', $kegiatan->kategori) }}" placeholder="Contoh: Keagamaan, Budaya, Acara">
                @error('kategori')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $kegiatan->tanggal?->format('Y-m-d')) }}">
                    @error('tanggal')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    @if($kegiatan->gambar)
                        <div class="current-image">
                            <img src="{{ asset('images/' . $kegiatan->gambar) }}" alt="Current Image" width="100">
                            <p class="image-name">Gambar saat ini: {{ basename($kegiatan->gambar) }}</p>
                        </div>
                    @endif
                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*">
                    <class="form-text">Kosongkan jika tidak ingin mengubah gambar. Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
                    @error('gambar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="ringkasan">Ringkasan <span class="required">*</span></label>
                <textarea name="ringkasan" id="ringkasan" rows="3" class="form-control @error('ringkasan') is-invalid @enderror" required>{{ old('ringkasan', $kegiatan->ringkasan) }}</textarea>
                @error('ringkasan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi <span class="required">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="6" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $kegiatan->is_active) ? 'checked' : '' }}>
                    Aktif
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection