@extends('layouts.admin')

@section('title', 'Edit Program - Admin Panel')
@section('page-title', 'Edit Program Unggulan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <a href="{{ route('admin.program.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2><i class="bi bi-pencil-square"></i> Edit Program Unggulan</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.program.update', $program->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama">Nama Program <span class="required">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $program->nama) }}" required placeholder="Masukkan nama program">
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <input type="text" name="kategori" id="kategori" class="form-control @error('kategori') is-invalid @enderror" value="{{ old('kategori', $program->kategori) }}" placeholder="Contoh: Keagamaan, Bahasa, Keterampilan">
                    @error('kategori')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="icon">Icon</label>
                    <input type="text" name="icon" id="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $program->icon) }}" placeholder="Contoh: bi-book, bi-mic, bi-star">
                    <small class="form-text">Gunakan icon Bootstrap: <a href="https://icons.getbootstrap.com/" target="_blank">icons.getbootstrap.com</a></small>
                    @error('icon')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi <span class="required">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="5" class="form-control @error('deskripsi') is-invalid @enderror" required placeholder="Tulis deskripsi program...">{{ old('deskripsi', $program->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="foto">Foto</label>
                @if($program->foto)
                    <div class="current-image">
                        <img src="{{ $program->foto_url }}" alt="Foto saat ini">
                        <div>
                            <p class="image-name"><strong>Foto saat ini:</strong> {{ $program->foto }}</p>
                            <class="form-text">Upload foto baru untuk mengganti</small>
                        </div>
                    </div>
                @endif
                <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                <small class="form-text">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
                @error('foto')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                    <span>Aktif</span>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update
                </button>
                <a href="{{ route('admin.program.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection