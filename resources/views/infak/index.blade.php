@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/infak.css') }}">
@endpush

@section('title', 'Infak Santri - MDT Al-Harus')
@section('description', 'Bayar infak santri MDT Al-Harus')

@section('content')
<div class="infak-page">
    <!-- Hero -->
    <section class="infak-hero">
        <img src="{{ asset('images/logo-alharus.png') }}" alt="MDT Al-Harus" class="logo-hero">
        <h1><i class="bi bi-wallet2"></i> Infak Santri</h1>
        <p>Bayar infak santri dengan mudah dan cepat</p>
    </section>

    <div class="infak-wrapper">
        <div class="infak-card">
            <div class="infak-card-body">
                <!-- Search -->
                <div class="search-section">
                    <div class="search-box">
                        <input type="text" id="nisaInput" placeholder="Masukkan NISA Santri..." autofocus>
                        <button id="btnCari" class="btn-search">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="search-hint">
                        <i class="bi bi-info-circle"></i> Masukkan Nomor Induk Santri Alharus (NISA)
                    </div>
                </div>

                <!-- Loading -->
                <div id="loading" class="loading-spinner" style="display:none;">
                    <div class="spinner-border text-success" role="status"></div>
                    <p>Mencari data santri...</p>
                </div>

                <!-- Result -->
                <div id="resultSection" class="result-section">
                    <!-- Santri Card -->
                    <div class="santri-card">
                        <img id="santriFoto" class="santri-avatar" src="{{ asset('images/default-avatar.png') }}" alt="Foto">
                        <div>
                            <h4 class="santri-name"><i class="bi bi-person-circle"></i> <span id="santriNama">-</span></h4>
                            <p class="santri-class"><i class="bi bi-mortarboard"></i> <span id="santriKelas">-</span></p>
                            <p class="santri-wali"><i class="bi bi-person"></i> Wali: <span id="santriWali">-</span></p>
                        </div>
                    </div>

                    <!-- Infak Table -->
                    <div class="infak-table">
                        <div class="table-header">
                            <h6><i class="bi bi-calendar3"></i> Daftar Infak <span id="tahunDisplay" class="tahun-text"></span></h6>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
                                    <th>Bulan</th>
                                    <th>Status</th>
                                    <th style="text-align:center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="infakTableBody"></tbody>
                        </table>
                    </div>

                    <!-- Payment Section -->
                    <div id="paymentSection" class="payment-section">
                        <h5><i class="bi bi-credit-card"></i> Metode Pembayaran</h5>
                        <div class="payment-method">
                            <input type="radio" name="metode" id="metodeTunai" value="tunai">
                            <label for="metodeTunai"><i class="bi bi-cash"></i> Tunai</label>

                            <input type="radio" name="metode" id="metodeTransfer" value="transfer">
                            <label for="metodeTransfer"><i class="bi bi-bank"></i> Transfer</label>

                            <input type="radio" name="metode" id="metodeQRIS" value="qris">
                            <label for="metodeQRIS"><i class="bi bi-qr-code"></i> QRIS</label>
                        </div>

                        <!-- Transfer Info -->
                        <div id="infoTransfer" class="payment-info">
                            <p><strong><i class="bi bi-bank"></i> Transfer ke Rekening:</strong></p>
                            <p class="rekening">Bank BNI: 1234567890 a.n. Yayasan Al-Harus</p>
                            <p style="margin-top:0.5rem; color:#888; font-size:0.85rem;">
                                <i class="bi bi-info-circle"></i> Setelah transfer, upload bukti di bawah ini.
                            </p>
                        </div>

                        <!-- QRIS Info -->
                        <div id="infoQris" class="payment-info">
                            <p><strong><i class="bi bi-qr-code"></i> Scan QRIS:</strong></p>
                            <div class="qris-placeholder">
                                <i class="bi bi-qr-code"></i>
                                <div class="qris-simulator">
                                    <div class="qris-block dark"></div>
                                    <div class="qris-block light"></div>
                                    <div class="qris-block dark"></div>
                                    <div class="qris-block light"></div>
                                    <div class="qris-block dark"></div>
                                    <div class="qris-block light"></div>
                                    <div class="qris-block dark"></div>
                                    <div class="qris-block light"></div>
                                    <div class="qris-block dark"></div>
                                </div>
                                <p>QRIS Code</p>
                                <small>Scan QRIS di atas untuk pembayaran</small>
                            </div>
                            <p style="margin-top:0.5rem; color:#888; font-size:0.85rem;">
                                <i class="bi bi-info-circle"></i> Setelah scan, upload bukti di bawah ini.
                            </p>
                        </div>

                        <!-- Tunai Info -->
                        <div id="infoTunai" class="payment-info">
                            <p><strong><i class="bi bi-cash"></i> Pembayaran Tunai</strong></p>
                            <p>Bayar langsung ke bendahara madrasah.</p>
                            <p style="color:#888; font-size:0.85rem;">
                                <i class="bi bi-info-circle"></i> Setelah bayar, upload bukti atau minta konfirmasi admin.
                            </p>
                        </div>

                        <!-- Upload Bukti -->
                        <div class="file-upload">
                            <label><i class="bi bi-upload"></i> Upload Bukti Pembayaran</label>
                            <input type="file" id="buktiUpload" accept="image/*">
                            <small>Format: JPG, PNG. Maks: 2MB</small>
                        </div>

                        <div class="file-upload">
                            <label><i class="bi bi-person"></i> Nama Pengirim (Opsional)</label>
                            <input type="text" id="namaPengirim" placeholder="Nama wali yang membayar">
                        </div>
                    </div>

                    <!-- Total & Bayar -->
                    <div id="totalSection" class="total-section">
                        <h4>Total: <span id="totalBayar">Rp 0</span></h4>
                        <button id="btnBayar" class="btn-bayar-sekarang">
                            <i class="bi bi-send"></i> Kirim Bukti & Konfirmasi
                        </button>
                        <span class="text-muted">* Pembayaran akan dikonfirmasi oleh admin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nisaInput = document.getElementById('nisaInput');
        const btnCari = document.getElementById('btnCari');
        const loading = document.getElementById('loading');
        const resultSection = document.getElementById('resultSection');

        let currentSantriId = null;
        let currentNominal = 50000;
        let selectedBulans = [];

        nisaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchSantri();
        });

        btnCari.addEventListener('click', searchSantri);

        // Event metode pembayaran
        document.querySelectorAll('input[name="metode"]').forEach(el => {
            el.addEventListener('change', function() {
                document.querySelectorAll('.payment-info').forEach(info => info.classList.remove('show'));
                const target = document.getElementById('info' + this.value.charAt(0).toUpperCase() + this.value.slice(1));
                if (target) target.classList.add('show');
            });
        });

        function searchSantri() {
            const nisa = nisaInput.value.trim();
            if (!nisa) {
                Swal.fire('Peringatan', 'Masukkan NISA santri!', 'warning');
                return;
            }

            loading.style.display = 'block';
            resultSection.style.display = 'none';

            fetch('{{ route("infak.public.cari") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ nisa: nisa })
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.status === 'success') {
                    currentSantriId = data.data.santri.id;
                    currentNominal = data.data.nominal || 50000;
                    displaySantri(data.data.santri);
                    renderInfakTable(data.data.infak);
                    resultSection.style.display = 'block';
                } else {
                    Swal.fire('Error', data.message || 'Santri tidak ditemukan!', 'error');
                }
            })
            .catch(() => {
                loading.style.display = 'none';
                Swal.fire('Error', 'Terjadi kesalahan!', 'error');
            });
        }

        function displaySantri(santri) {
    document.getElementById('santriNama').textContent = santri.nama;
    document.getElementById('santriKelas').textContent = santri.kelas || '-';
    document.getElementById('santriWali').textContent = santri.nama_wali || '-';
    document.getElementById('santriFoto').src = santri.foto || '{{ asset("images/default-avatar.png") }}';
    document.getElementById('tahunDisplay').textContent = '| Tahun ' + new Date().getFullYear();
}

        function renderInfakTable(infakData) {
            window.currentInfakData = infakData;
            const tbody = document.getElementById('infakTableBody');
            let html = '';

            infakData.forEach((item, index) => {
                const isLunas = item.status === 'lunas';
                const isPending = item.status === 'pending';
                const statusClass = isLunas ? 'status-lunas' : (isPending ? 'status-pending' : 'status-belum');
                const statusText = isLunas ? '✅ Lunas' : (isPending ? '⏳ Pending' : '❌ Belum');

                html += `
                    <tr>
                        <td>
                            <input type="checkbox" class="infak-checkbox" data-index="${index}" 
                                ${isLunas || isPending ? 'disabled' : ''}>
                        </td>
                        <td>${item.bulan}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td style="text-align:center;">
                            ${!isLunas && !isPending ? 
                                `<button class="btn-bayar" onclick="bayarBulan(${index})">
                                    <i class="bi bi-cash"></i> Bayar
                                </button>` : 
                                `<span class="text-muted">-</span>`
                            }
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

            // Select All
            document.getElementById('selectAll').addEventListener('change', function() {
                document.querySelectorAll('.infak-checkbox:not(:disabled)').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateTotal();
            });

            document.querySelectorAll('.infak-checkbox').forEach(cb => {
                cb.addEventListener('change', updateTotal);
            });

            updateTotal();
        }

        function updateTotal() {
            const checkboxes = document.querySelectorAll('.infak-checkbox:checked');
            const total = checkboxes.length * currentNominal;
            selectedBulans = Array.from(checkboxes).map(cb => {
                const index = cb.dataset.index;
                return window.currentInfakData[index]?.bulan_angka;
            });
            const totalSection = document.getElementById('totalSection');
            const paymentSection = document.getElementById('paymentSection');

            if (checkboxes.length > 0) {
                totalSection.style.display = 'block';
                paymentSection.style.display = 'block';
                document.getElementById('totalBayar').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            } else {
                totalSection.style.display = 'none';
                paymentSection.style.display = 'none';
            }
        }

        window.bayarBulan = function(index) {
            const row = document.querySelectorAll('#infakTableBody tr')[index];
            const checkbox = row.querySelector('.infak-checkbox');
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = !checkbox.checked;
                updateTotal();
            }
        };

        // Proses Bayar
        document.getElementById('btnBayar').addEventListener('click', function() {
            const metode = document.querySelector('input[name="metode"]:checked');
            const bukti = document.getElementById('buktiUpload');

            if (!metode) {
                Swal.fire('Peringatan', 'Pilih metode pembayaran!', 'warning');
                return;
            }

            if (selectedBulans.length === 0) {
                Swal.fire('Peringatan', 'Pilih bulan yang mau dibayar!', 'warning');
                return;
            }

            if (!bukti.files || bukti.files.length === 0) {
                Swal.fire('Peringatan', 'Upload bukti pembayaran!', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('santri_id', currentSantriId);
            formData.append('bulan', JSON.stringify(selectedBulans));
            formData.append('tahun', new Date().getFullYear());
            formData.append('metode', metode.value);
            formData.append('jumlah', selectedBulans.length * currentNominal);
            formData.append('bukti', bukti.files[0]);
            formData.append('nama_pengirim', document.getElementById('namaPengirim').value || '');

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengirim...';

            fetch('{{ route("infak.public.upload") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-send"></i> Kirim Bukti & Konfirmasi';

                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 4000,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Gagal mengirim bukti!', 'error');
                }
            })
            .catch(error => {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-send"></i> Kirim Bukti & Konfirmasi';
                Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
            });
        });
    });
</script>
@endsection