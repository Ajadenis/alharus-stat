@extends('layouts.admin')

@section('title', 'Tambah Anggota ISMA - Admin Panel')
@section('page-title', 'Tambah Anggota ISMA')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <a href="{{ route('admin.isma.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2><i class="bi bi-person-plus"></i> Tambah Anggota ISMA</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.isma.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nama">Nama <span class="required">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama anggota">
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- ======================================== --}}
            {{-- DROPDOWN JABATAN - AMBIL DARI CONFIG     --}}
            {{-- ======================================== --}}
            <div class="form-group">
                <label for="jabatan">Jabatan <span class="required">*</span></label>
                <select name="jabatan[]" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" multiple required>
                    @php
                        $jabatanList = config('isma_jabatan.list', []);
                    @endphp
                    
                    @if(count($jabatanList) > 0)
                        @foreach($jabatanList as $jabatan)
                            <option value="{{ $jabatan }}" {{ in_array($jabatan, old('jabatan', [])) ? 'selected' : '' }}>
                                {{ $jabatan }}
                            </option>
                        @endforeach
                    @else
                        <option value="">-- Tidak ada data jabatan --</option>
                    @endif
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
                <label for="foto">Foto</label>
                <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                <small class="form-text">Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
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
                <a href="{{ route('admin.isma.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection