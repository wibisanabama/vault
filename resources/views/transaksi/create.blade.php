@extends('layouts.app', ['title' => 'Form Titip Helm Baru'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="tremor-card mb-4">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark m-0"><i class="bi bi-box-arrow-in-down text-primary me-2"></i> Input Penitipan Helm</h5>
                    <small class="text-muted">Isi data pelanggan dan helm untuk melakukan penitipan baru</small>
                </div>
                <a href="{{ route('transaksi.index') }}" class="btn btn-light border btn-sm rounded-pill">
                    Kembali
                </a>
            </div>

            <!-- Lockers Alert Banner -->
            @if($lokerTersedia > 0)
                <div class="alert alert-info rounded-3 border-0 d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <span>Loker tersedia: <strong>{{ $lokerTersedia }} unit</strong> (sistem akan memilihkan loker secara otomatis).</span>
                    </div>
                    <span class="badge bg-primary px-3 py-2">Tarif: Rp {{ number_format($activeTarif->harga_per_jam, 0, ',', '.') }} / jam</span>
                </div>
            @else
                <div class="alert alert-danger rounded-3 border-0 mb-4">
                    <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                    <strong>Peringatan!</strong> Semua loker saat ini sedang terisi penuh. Tidak dapat menambah penitipan baru.
                </div>
            @endif

            <form action="{{ route('transaksi.store') }}" method="POST">
                @csrf

                <!-- Section 1: Data Pelanggan -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase fs-7 mb-3" style="letter-spacing: 0.05em; color: #64748b;">1. Data Pelanggan</h6>
                    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="nama_pelanggan" class="form-label fw-semibold fs-7">Nama Pelanggan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control rounded-3 @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}" placeholder="Contoh: Budi Santoso" required>
                            @error('nama_pelanggan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="no_hp" class="form-label fw-semibold fs-7">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control rounded-3 @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" required>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="alamat" class="form-label fw-semibold fs-7">Alamat / Catatan Identitas (Opsional)</label>
                            <input type="text" name="alamat" id="alamat" class="form-control rounded-3" value="{{ old('alamat') }}" placeholder="Contoh: Jl. Merdeka No. 10 atau No Plat Kendaraan">
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="border-color: #e2e8f0;">

                <!-- Section 2: Data Helm -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark text-uppercase fs-7 mb-3" style="letter-spacing: 0.05em; color: #64748b;">2. Identitas Helm</h6>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="merk_helm" class="form-label fw-semibold fs-7">Merk / Brand Helm <span class="text-danger">*</span></label>
                            <input type="text" name="merk_helm" id="merk_helm" class="form-control rounded-3 @error('merk_helm') is-invalid @enderror" value="{{ old('merk_helm') }}" placeholder="Contoh: KYT, NHK, Shoei, AGV" required>
                            @error('merk_helm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="warna_helm" class="form-label fw-semibold fs-7">Warna Helm <span class="text-danger">*</span></label>
                            <input type="text" name="warna_helm" id="warna_helm" class="form-control rounded-3 @error('warna_helm') is-invalid @enderror" value="{{ old('warna_helm') }}" placeholder="Contoh: Hitam Doff, Red Bull, Putih" required>
                            @error('warna_helm')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="deskripsi_helm" class="form-label fw-semibold fs-7">Deskripsi Ciri Khusus / Keterangan tambahan (Opsional)</label>
                            <textarea name="deskripsi_helm" id="deskripsi_helm" rows="2" class="form-control rounded-3" placeholder="Contoh: Helm Full-Face, stiker visor jernih, ada lecet samping kanan">{{ old('deskripsi_helm') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-3 d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route('transaksi.index') }}" class="btn btn-light border rounded-pill px-4">Batal</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold" @if($lokerTersedia === 0) disabled @endif>
                        <i class="bi bi-save me-1"></i> Simpan & Cetak Struk Titip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
