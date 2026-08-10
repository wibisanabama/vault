@extends('layouts.app', ['title' => 'Status & Management Loker'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Status Loker Penitipan</h4>
        <small class="text-muted">Pantau ketersediaan unit loker secara real-time</small>
    </div>
    @if(auth()->user()->isAdmin())
        <button class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahLoker">
            <i class="bi bi-plus-lg me-1"></i> Tambah Loker Baru
        </button>
    @endif
</div>

<!-- Filter Bar -->
<div class="tremor-card mb-4 p-3">
    <form action="{{ route('loker.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-12 col-md-6 d-flex gap-1">
            <a href="{{ route('loker.index') }}" class="btn btn-sm rounded-pill {{ !request('status') ? 'btn-primary' : 'btn-light border' }}">
                Semua Unit
            </a>
            <a href="{{ route('loker.index', ['status' => 'tersedia']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'tersedia' ? 'btn-success' : 'btn-light border' }}">
                <i class="bi bi-check-circle me-1"></i> Kosong (Tersedia)
            </a>
            <a href="{{ route('loker.index', ['status' => 'terisi']) }}" class="btn btn-sm rounded-pill {{ request('status') === 'terisi' ? 'btn-danger' : 'btn-light border' }}">
                <i class="bi bi-box-seam me-1"></i> Terisi
            </a>
        </div>

        <div class="col-12 col-md-6 text-md-end">
            <span class="fs-7 text-muted me-2">Lokasi Zone:</span>
            @foreach($lokasiList as $lokasi)
                <a href="{{ route('loker.index', ['lokasi' => $lokasi]) }}" class="btn btn-sm rounded-pill {{ request('lokasi') === $lokasi ? 'btn-secondary' : 'btn-light border' }}">
                    {{ $lokasi }}
                </a>
            @endforeach
        </div>
    </form>
</div>

<!-- Locker Grid View -->
<div class="row g-3">
    @forelse($loker as $l)
        @php
            $activeTransaksi = $l->transaksi->first();
        @endphp
        <div class="col-6 col-sm-4 col-md-3 col-xl-2">
            <div class="locker-card {{ $l->status }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge {{ $l->status === 'tersedia' ? 'bg-success' : 'bg-danger' }} rounded-pill px-2 py-1 fs-7">
                        {{ strtoupper($l->status) }}
                    </span>
                    @if(auth()->user()->isAdmin())
                        <div class="dropdown">
                            <button class="btn btn-link btn-sm p-0 text-muted" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                <li>
                                    <button class="dropdown-item fs-7" data-bs-toggle="modal" data-bs-target="#modalEditLoker{{ $l->id }}">Edit Loker</button>
                                </li>
                                @if($l->status === 'tersedia')
                                    <li>
                                        <form action="{{ route('loker.destroy', $l->id) }}" method="POST" onsubmit="return confirm('Hapus loker {{ $l->nomor_loker }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger fs-7">Hapus</button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="locker-number mb-1">{{ $l->nomor_loker }}</div>
                <div class="fs-7 text-muted mb-2">{{ $l->lokasi }}</div>

                @if($l->status === 'terisi' && $activeTransaksi)
                    <div class="pt-2 border-top text-start" style="font-size: 0.75rem;">
                        <div class="fw-bold text-truncate">{{ $activeTransaksi->pelanggan->nama }}</div>
                        <div class="text-muted text-truncate">{{ $activeTransaksi->helm->merk }}</div>
                        <a href="{{ route('transaksi.show', $activeTransaksi->id) }}" class="btn btn-outline-danger btn-sm w-100 mt-2 py-1 fs-7 rounded-2">
                            Lihat Struk
                        </a>
                    </div>
                @else
                    <div class="pt-2 border-top">
                        <a href="{{ route('transaksi.create') }}" class="btn btn-outline-success btn-sm w-100 py-1 fs-7 rounded-2">
                            + Isi Loker
                        </a>
                    </div>
                @endif
            </div>

            <!-- Modal Edit Loker -->
            <div class="modal fade text-start" id="modalEditLoker{{ $l->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content rounded-4 border-0 shadow">
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-bold text-dark">Edit Loker {{ $l->nomor_loker }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('loker.update', $l->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7">Nomor Loker</label>
                                    <input type="text" name="nomor_loker" class="form-control rounded-3" value="{{ $l->nomor_loker }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold fs-7">Lokasi / Zona</label>
                                    <input type="text" name="lokasi" class="form-control rounded-3" value="{{ $l->lokasi }}" required>
                                </div>
                            </div>
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5 text-muted">
            Belum ada unit loker terdaftar.
        </div>
    @endforelse
</div>

<!-- Modal Tambah Loker -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="modalTambahLoker" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Tambah Loker Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('loker.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nomor Loker <span class="text-danger">*</span></label>
                        <input type="text" name="nomor_loker" class="form-control rounded-3" placeholder="Contoh: L-021" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Lokasi / Zona <span class="text-danger">*</span></label>
                        <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Zona A (Utama)" value="Zona A (Utama)" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Tambah Loker</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
