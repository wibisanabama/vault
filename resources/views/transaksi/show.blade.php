@extends('layouts.app', ['title' => 'Detail Transaksi ' . $transaksi->kode_transaksi])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <!-- Top Navigation -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold text-dark m-0">Detail Transaksi {{ $transaksi->kode_transaksi }}</h4>
                <small class="text-muted">Informasi lengkap penitipan dan pembayaran</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('transaksi.index') }}" class="btn btn-light border rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('transaksi.struk', $transaksi->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-printer me-1"></i> Cetak Struk
                </a>
                @if($transaksi->status === 'titip')
                    <a href="{{ route('transaksi.ambil', $transaksi->id) }}" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Proses Ambil Helm
                    </a>
                @endif
            </div>
        </div>

        <!-- Status Card Banner -->
        <div class="tremor-card mb-4 border-0 text-white {{ $transaksi->status === 'titip' ? 'bg-warning text-dark' : ($transaksi->status === 'ambil' ? 'bg-success' : 'bg-danger') }}">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi {{ $transaksi->status === 'titip' ? 'bi-clock-history' : 'bi-check-circle-fill' }} fs-1"></i>
                    <div>
                        <div class="fw-bold fs-5">
                            @if($transaksi->status === 'titip')
                                Helm Sedang Dititip di {{ $transaksi->loker->nomor_loker }}
                            @else
                                Transaksi Selesai & Helm Sudah Diambil
                            @endif
                        </div>
                        <div class="fs-7 opacity-75">
                            @if($transaksi->status === 'titip')
                                Durasi berjalan: {{ $durasiJam }} jam (Estimasi biaya: Rp {{ number_format($estimasiBiaya, 0, ',', '.') }})
                            @else
                                Selesai pada: {{ $transaksi->tgl_ambil->format('d F Y H:i') }} WIB
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-7 text-uppercase font-monospace opacity-75">Kode Transaksi</div>
                    <div class="fw-extrabold fs-4 font-monospace">{{ $transaksi->kode_transaksi }}</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Transaksi & Pelanggan -->
            <div class="col-12 col-md-6">
                <!-- Info Pelanggan Card -->
                <div class="tremor-card mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-fill text-primary me-2"></i> Data Pelanggan</h6>
                    <table class="table table-borderless table-sm m-0">
                        <tr>
                            <td class="text-muted" style="width: 35%;">Nama</td>
                            <td class="fw-bold text-dark">{{ $transaksi->pelanggan->nama }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No WhatsApp / HP</td>
                            <td class="fw-semibold text-dark"><i class="bi bi-whatsapp text-success me-1"></i> {{ $transaksi->pelanggan->no_hp }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat / Catatan</td>
                            <td class="text-dark">{{ $transaksi->pelanggan->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Info Helm Card -->
                <div class="tremor-card">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-shield-fill text-primary me-2"></i> Detail Helm</h6>
                    <table class="table table-borderless table-sm m-0">
                        <tr>
                            <td class="text-muted" style="width: 35%;">Merk Helm</td>
                            <td class="fw-bold text-dark">{{ $transaksi->helm->merk }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Warna</td>
                            <td class="fw-semibold text-dark">{{ $transaksi->helm->warna }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ciri Khusus / Deskripsi</td>
                            <td class="text-dark">{{ $transaksi->helm->deskripsi ?? 'Tidak ada catatan khusus' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Right Column: Loker, Waktu & Pembayaran -->
            <div class="col-12 col-md-6">
                <!-- Info Penitipan Card -->
                <div class="tremor-card mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-box-seam-fill text-primary me-2"></i> Informasi Loker & Operasional</h6>
                    <table class="table table-borderless table-sm m-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Nomor Loker</td>
                            <td><span class="badge bg-primary px-3 py-1 fs-6 font-monospace">{{ $transaksi->loker->nomor_loker }}</span> ({{ $transaksi->loker->lokasi }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Titip</td>
                            <td class="fw-semibold text-dark">{{ $transaksi->tgl_titip->format('d/m/Y H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Waktu Ambil</td>
                            <td class="fw-semibold text-dark">
                                @if($transaksi->tgl_ambil)
                                    {{ $transaksi->tgl_ambil->format('d/m/Y H:i') }} WIB
                                @else
                                    <span class="badge bg-light text-dark border">Belum Diambil</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Petugas Penerima</td>
                            <td class="text-dark"><i class="bi bi-person-badge me-1"></i> {{ $transaksi->user->name }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Info Pembayaran Card -->
                <div class="tremor-card">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-cash-stack text-primary me-2"></i> Perhitungan & Pembayaran</h6>
                    <table class="table table-borderless table-sm m-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Tarif Dasar</td>
                            <td class="fw-semibold">Rp {{ number_format($rate, 0, ',', '.') }} / jam</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi Total</td>
                            <td class="fw-semibold">{{ $durasiJam }} Jam ({{ $durasiMenit }} Menit)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-bold text-dark pt-2">Total Biaya</td>
                            <td class="fw-bold text-success fs-5 pt-2">
                                Rp {{ number_format($transaksi->tarif ?? $estimasiBiaya, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($transaksi->pembayaran)
                            <tr>
                                <td class="text-muted">Metode Bayar</td>
                                <td>
                                    <span class="badge bg-info text-dark text-uppercase px-2 py-1">
                                        <i class="bi bi-credit-card me-1"></i> {{ $transaksi->pembayaran->metode_bayar }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status Pembayaran</td>
                                <td>
                                    <span class="tremor-badge tremor-badge-success">
                                        <i class="bi bi-check-circle-fill"></i> LUNAS
                                    </span>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
