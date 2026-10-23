@extends('layouts.admin')

@section('title', 'Tambah Guru - Admin Panel')
@section('page-title', 'Tambah Guru')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-guru.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <a href="{{ route('admin.guru.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2><i class="bi bi-person-plus"></i> Tambah Guru Baru</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama">Nama <span class="required">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama guru">
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- DROPDOWN JABATAN MULTI-SELECT --}}
            <div class="form-group">
                <label for="jabatan">Jabatan <span class="required">*</span></label>
                <select name="jabatan[]" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" multiple required>
                    @foreach(config('jabatan.list', []) as $jabatan)
                        <option value="{{ $jabatan }}" {{ in_array($jabatan, old('jabatan', [])) ? 'selected' : '' }}>
                            {{ $jabatan }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text">
                    <i class="bi bi-info-circle"></i> 
                    Tekan <strong>Ctrl</strong> (Windows) atau <strong>Cmd</strong> (Mac) untuk pilih lebih dari satu
                </small>
                @error('jabatan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mata_pelajaran">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control @error('mata_pelajaran') is-invalid @enderror" value="{{ old('mata_pelajaran') }}" required placeholder="Contoh: Matematika, Bahasa Arab, dll">
                @error('mata_pelajaran')
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
                <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection