@extends('layouts.admin')

@section('title', 'Tambah Santri - Admin Panel')
@section('page-title', 'Tambah Santri')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-form.css') }}">
@endpush

@section('content')
<div class="form-wrapper">
    <div class="form-header">
        <a href="{{ route('admin.santri.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2><i class="bi bi-person-plus"></i> Tambah Santri Baru</h2>
    </div>

    <div class="form-container">
        <form action="{{ route('admin.santri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="nisa">NISA <span class="required">*</span></label>
                <input type="text" name="nisa" id="nisa" class="form-control @error('nisa') is-invalid @enderror" value="{{ old('nisa') }}" required placeholder="Nomor Induk Santri Alharus">
                <small class="form-text">Contoh: 20250123</small>
                @error('nisa')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required placeholder="Masukkan nama santri">
                @error('nama')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
    <div class="form-group">
        <label for="nis">NIS</label>
        <input type="text" name="nis" id="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis') }}" placeholder="Nomor Induk Sekolah">
        @error('nis')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="kelas">Kelas <span class="required">*</span></label>
        <select name="kelas" id="kelas" class="form-control @error('kelas') is-invalid @enderror" required>
            <option value="">Pilih Kelas</option>
            <option value="MDA 1" {{ old('kelas') == 'MDA 1' ? 'selected' : '' }}>MDA 1</option>
            <option value="MDA 2" {{ old('kelas') == 'MDA 2' ? 'selected' : '' }}>MDA 2</option>
            <option value="MDA 3" {{ old('kelas') == 'MDA 3' ? 'selected' : '' }}>MDA 3</option>
            <option value="MDA 4" {{ old('kelas') == 'MDA 4' ? 'selected' : '' }}>MDA 4</option>
        </select>
        @error('kelas')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}">
                    @error('tanggal_lahir')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Masukkan alamat santri">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nama_orang_tua">Nama Orang Tua</label>
                    <input type="text" name="nama_orang_tua" id="nama_orang_tua" class="form-control @error('nama_orang_tua') is-invalid @enderror" value="{{ old('nama_orang_tua') }}" placeholder="Nama ayah/ibu">
                    @error('nama_orang_tua')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_wali">Nama Wali</label>
                    <input type="text" name="nama_wali" id="nama_wali" class="form-control @error('nama_wali') is-invalid @enderror" value="{{ old('nama_wali') }}" placeholder="Nama wali santri">
                    @error('nama_wali')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="telepon_wali">Telepon Wali</label>
                    <input type="text" name="telepon_wali" id="telepon_wali" class="form-control @error('telepon_wali') is-invalid @enderror" value="{{ old('telepon_wali') }}" placeholder="Nomor telepon wali">
                    @error('telepon_wali')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ old('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="keluar" {{ old('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="foto">Foto</label>
                <input type="file" name="foto" id="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/*">
                <small class="form-text">Format: JPG, PNG, JPEG, WEBP. Maks: 2MB</small>
                @error('foto')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('admin.santri.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection