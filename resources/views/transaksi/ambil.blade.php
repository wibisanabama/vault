@extends('layouts.app', ['title' => 'Proses Pengambilan Helm'])

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="tremor-card">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-4 border-bottom">
                <div>
                    <h5 class="fw-bold text-dark m-0"><i class="bi bi-box-arrow-up-right text-success me-2"></i> Proses Pengambilan Helm</h5>
                    <small class="text-muted">Hitung biaya akhir dan selesaikan transaksi penitipan</small>
                </div>
                <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-light border btn-sm rounded-pill">
                    <i class="bi bi-arrow-left"></i> Batal
                </a>
            </div>

            <!-- Transaction Summary Banner -->
            <div class="bg-light p-3 rounded-3 border mb-4">
                <div class="row text-center">
                    <div class="col-4 border-end">
                        <div class="text-muted fs-7 text-uppercase">Kode Struk</div>
                        <div class="fw-bold text-primary fs-6 font-monospace">{{ $transaksi->kode_transaksi }}</div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="text-muted fs-7 text-uppercase">Nomor Loker</div>
                        <div class="fw-bold text-dark fs-6">{{ $transaksi->loker->nomor_loker }}</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted fs-7 text-uppercase">Pelanggan</div>
                        <div class="fw-bold text-dark fs-6">{{ $transaksi->pelanggan->nama }}</div>
                    </div>
                </div>
            </div>

            <!-- Detail Breakdown -->
            <div class="mb-4">
                <h6 class="fw-bold text-dark text-uppercase fs-7 mb-3" style="letter-spacing: 0.05em; color: #64748b;">Rincian Durasi & Biaya</h6>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <td class="bg-light text-muted" style="width: 40%;">Waktu Dititipkan</td>
                                <td class="fw-semibold">{{ $transaksi->tgl_titip->format('d/m/Y H:i') }} WIB</td>
                            </tr>
                            <tr>
                                <td class="bg-light text-muted">Waktu Diambil Sekarang</td>
                                <td class="fw-semibold text-primary">{{ now()->format('d/m/Y H:i') }} WIB</td>
                            </tr>
                            <tr>
                                <td class="bg-light text-muted">Durasi Penitipan</td>
                                <td>
                                    <span class="fw-bold">{{ $durasiJam }} Jam</span>
                                    <small class="text-muted">({{ $durasiMenit }} menit)</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-light text-muted">Tarif Per Jam</td>
                                <td>Rp {{ number_format($rate, 0, ',', '.') }} / jam</td>
                            </tr>
                            <tr class="table-success">
                                <td class="fw-bold text-dark">TOTAL BIAYA PENITIPAN</td>
                                <td class="fw-extrabold fs-4 text-success font-monospace">
                                    Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <form action="{{ route('transaksi.proses-ambil', $transaksi->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold text-dark">Pilih Metode Pembayaran <span class="text-danger">*</span></label>

                    <div class="row g-3">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="metode_bayar" id="bayar_tunai" value="tunai" checked>
                            <label class="btn btn-outline-primary p-3 w-100 rounded-3 text-start d-flex align-items-center gap-3" for="bayar_tunai">
                                <i class="bi bi-cash-stack fs-2"></i>
                                <div>
                                    <div class="fw-bold">Tunai / Cash</div>
                                    <div class="fs-7 text-muted">Bayar langsung di kasir</div>
                                </div>
                            </label>
                        </div>

                        <div class="col-6">
                            <input type="radio" class="btn-check" name="metode_bayar" id="bayar_ewallet" value="ewallet">
                            <label class="btn btn-outline-primary p-3 w-100 rounded-3 text-start d-flex align-items-center gap-3" for="bayar_ewallet">
                                <i class="bi bi-qr-code-scan fs-2"></i>
                                <div>
                                    <div class="fw-bold">E-Wallet / QRIS</div>
                                    <div class="fs-7 text-muted">GoPay, OVO, ShopeePay, Dana</div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 pt-2">
                    <button type="submit" class="btn btn-success py-3 rounded-pill fw-bold fs-6">
                        <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Pembayaran & Ambil Helm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
