@extends('layouts.app', ['title' => 'Laporan Laba / Rugi'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Laporan Pendapatan {{ $periodeText }}</h4>
        <small class="text-muted">Rekapitulasi transaksi dan pendapatan penitipan helm</small>
    </div>
    <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 fw-semibold no-print">
        <i class="bi bi-printer me-1"></i> Cetak Laporan
    </button>
</div>

<!-- Filter Box -->
<div class="tremor-card mb-4 p-3 no-print">
    <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label fw-semibold fs-7">Jenis Laporan</label>
            <select name="jenis" id="jenis_laporan" class="form-select form-select-sm rounded-3" onchange="toggleFilterType(this.value)">
                <option value="harian" {{ $jenis === 'harian' ? 'selected' : '' }}>Laporan Harian</option>
                <option value="bulanan" {{ $jenis === 'bulanan' ? 'selected' : '' }}>Laporan Bulanan</option>
            </select>
        </div>

        <div class="col-12 col-md-4" id="box_harian" style="{{ $jenis === 'bulanan' ? 'display:none;' : '' }}">
            <label class="form-label fw-semibold fs-7">Pilih Tanggal</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control form-control-sm rounded-3">
        </div>

        <div class="col-12 col-md-4" id="box_bulanan" style="{{ $jenis === 'harian' ? 'display:none;' : '' }}">
            <label class="form-label fw-semibold fs-7">Pilih Bulan & Tahun</label>
            <input type="month" name="bulan" value="{{ $bulan }}" class="form-control form-control-sm rounded-3">
        </div>

        <div class="col-12 col-md-2">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-semibold">
                <i class="bi bi-filter"></i> Tampilkan
            </button>
        </div>
    </form>
</div>

<!-- Summary Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <span class="tremor-card-title">Total Transaksi</span>
            <div class="tremor-card-value">{{ $totalTransaksi }}</div>
            <div class="tremor-card-sub">{{ $transaksiSelesai }} selesai, {{ $sedangTitip }} aktif</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <span class="tremor-card-title">TOTAL PENDAPATAN</span>
            <div class="tremor-card-value text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            <div class="tremor-card-sub">Penerimaan Lunas</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <span class="tremor-card-title">Pembayaran Tunai</span>
            <div class="tremor-card-value">Rp {{ number_format($pendapatanTunai, 0, ',', '.') }}</div>
            <div class="tremor-card-sub">Kasir Direct Cash</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <span class="tremor-card-title">Pembayaran E-Wallet</span>
            <div class="tremor-card-value text-primary">Rp {{ number_format($pendapatanEwallet, 0, ',', '.') }}</div>
            <div class="tremor-card-sub">QRIS / Digital Transfer</div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="tremor-table-container">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark m-0">Rincian Transaksi {{ $periodeText }}</h6>
        <span class="badge bg-secondary rounded-pill px-3 py-1">{{ $totalTransaksi }} record</span>
    </div>

    <div class="table-responsive">
        <table class="tremor-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Struk</th>
                    <th>Pelanggan</th>
                    <th>Helm</th>
                    <th>Loker</th>
                    <th>Titip</th>
                    <th>Ambil</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $index => $t)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary">{{ $t->kode_transaksi }}</td>
                        <td>
                            <div class="fw-semibold">{{ $t->pelanggan->nama }}</div>
                            <div class="fs-7 text-muted">{{ $t->pelanggan->no_hp }}</div>
                        </td>
                        <td>{{ $t->helm->merk }} ({{ $t->helm->warna }})</td>
                        <td><span class="badge bg-secondary px-2 py-1 fs-7">{{ $t->loker->nomor_loker }}</span></td>
                        <td>{{ $t->tgl_titip->format('d/m H:i') }}</td>
                        <td>{{ $t->tgl_ambil ? $t->tgl_ambil->format('d/m H:i') : '-' }}</td>
                        <td>
                            @if($t->pembayaran)
                                <span class="badge bg-info text-dark text-uppercase px-2 py-1 fs-7">
                                    {{ $t->pembayaran->metode_bayar }}
                                </span>
                            @else
                                <span class="text-muted fs-7">-</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-success font-monospace">
                            @if($t->tarif)
                                Rp {{ number_format($t->tarif, 0, ',', '.') }}
                            @else
                                <span class="text-muted font-normal fs-7">Dititipkan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            Tidak ada data transaksi pada periode {{ $periodeText }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($totalTransaksi > 0)
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="8" class="text-end text-dark">TOTAL REKAPITULASI PENDAPATAN:</td>
                        <td class="text-end text-success font-monospace fs-5">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFilterType(val) {
        if(val === 'harian') {
            document.getElementById('box_harian').style.display = 'block';
            document.getElementById('box_bulanan').style.display = 'none';
        } else {
            document.getElementById('box_harian').style.display = 'none';
            document.getElementById('box_bulanan').style.display = 'block';
        }
    }
</script>
@endpush
