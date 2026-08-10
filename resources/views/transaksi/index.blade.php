@extends('layouts.app', ['title' => 'Daftar Transaksi Penitipan'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Daftar Transaksi</h4>
        <small class="text-muted">Kelola seluruh data transaksi penitipan dan pengambilan helm</small>
    </div>
    <a href="{{ route('transaksi.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
        <i class="bi bi-plus-lg me-1"></i> Titip Helm Baru
    </a>
</div>

<!-- Filter & Search Bar Card -->
<div class="tremor-card mb-4 p-3">
    <form action="{{ route('transaksi.index') }}" method="GET" class="row g-2 align-items-center">
        <!-- Status Filter Tabs -->
        <div class="col-12 col-md-6 d-flex gap-1">
            <a href="{{ route('transaksi.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-light border' }}">
                Semua
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'titip']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'titip' ? 'btn-warning text-dark' : 'btn-light border' }}">
                <i class="bi bi-clock"></i> Sedang Dititip
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'ambil']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'ambil' ? 'btn-success' : 'btn-light border' }}">
                <i class="bi bi-check-circle"></i> Selesai
            </a>
        </div>

        <!-- Search Input -->
        <div class="col-12 col-md-6 d-flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-pill" placeholder="Cari Kode Transaksi, Nama, Merk Helm...">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bi bi-search"></i>
            </button>
            @if(request()->hasAny(['status', 'search']))
                <a href="{{ route('transaksi.index') }}" class="btn btn-light border btn-sm rounded-pill px-3" title="Reset Filter">
                    <i class="bi bi-x-circle"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="tremor-table-container">
    <div class="table-responsive">
        <table class="tremor-table">
            <thead>
                <tr>
                    <th>Kode Struk</th>
                    <th>Pelanggan</th>
                    <th>Detail Helm</th>
                    <th>Loker</th>
                    <th>Waktu Titip</th>
                    <th>Waktu Ambil</th>
                    <th>Total Biaya</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                    <tr>
                        <td class="fw-bold text-primary">
                            <a href="{{ route('transaksi.show', $t->id) }}" class="text-decoration-none">{{ $t->kode_transaksi }}</a>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $t->pelanggan->nama }}</div>
                            <div class="text-muted fs-7"><i class="bi bi-whatsapp"></i> {{ $t->pelanggan->no_hp }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $t->helm->merk }} ({{ $t->helm->warna }})</div>
                            <div class="text-muted fs-7">{{ Str::limit($t->helm->deskripsi ?? 'Tanpa deskripsi', 25) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary px-2 py-1 fs-7 fw-bold">{{ $t->loker->nomor_loker }}</span>
                        </td>
                        <td>
                            <div class="fs-7">{{ $t->tgl_titip->format('d/m/Y') }}</div>
                            <div class="fw-bold fs-7">{{ $t->tgl_titip->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            @if($t->tgl_ambil)
                                <div class="fs-7">{{ $t->tgl_ambil->format('d/m/Y') }}</div>
                                <div class="fw-bold fs-7">{{ $t->tgl_ambil->format('H:i') }} WIB</div>
                            @else
                                <span class="text-muted fs-7">-</span>
                            @endif
                        </td>
                        <td>
                            @if($t->tarif)
                                <div class="fw-bold text-success">Rp {{ number_format($t->tarif, 0, ',', '.') }}</div>
                            @else
                                <span class="text-muted fs-7">Belum Selesai</span>
                            @endif
                        </td>
                        <td>
                            @if($t->status === 'titip')
                                <span class="tremor-badge tremor-badge-warning"><i class="bi bi-clock"></i> Dititip</span>
                            @elseif($t->status === 'ambil')
                                <span class="tremor-badge tremor-badge-success"><i class="bi bi-check-circle"></i> Selesai</span>
                            @else
                                <span class="tremor-badge tremor-badge-danger"><i class="bi bi-x-circle"></i> Batal</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('transaksi.show', $t->id) }}" class="btn btn-light border btn-sm rounded-2" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($t->status === 'titip')
                                    <a href="{{ route('transaksi.ambil', $t->id) }}" class="btn btn-success btn-sm rounded-2 fw-semibold px-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Ambil
                                    </a>
                                @endif
                                <a href="{{ route('transaksi.struk', $t->id) }}" target="_blank" class="btn btn-light border btn-sm rounded-2" title="Cetak Struk">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                            Tidak ada data transaksi yang ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transaksi->hasPages())
        <div class="p-3 border-top">
            {{ $transaksi->links() }}
        </div>
    @endif
</div>
@endsection
