@extends('layouts.app', ['title' => 'Kelola Tarif Penitipan'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Kelola Tarif Layanan</h4>
        <small class="text-muted">Atur skema harga penitipan helm per jam yang berlaku di sistem</small>
    </div>
    <button class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahTarif">
        <i class="bi bi-plus-lg me-1"></i> Buat Skema Tarif Baru
    </button>
</div>

<div class="row g-4">
    <!-- Left Column: Active Tarif Banner -->
    <div class="col-12 col-md-5">
        @php
            $active = $tarifList->where('is_active', true)->first();
        @endphp
        <div class="tremor-card bg-primary text-white border-0">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-7">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> TARIF AKTIF SEKARANG
                </span>
                <i class="bi bi-cash-coin fs-1 opacity-50"></i>
            </div>
            @if($active)
                <h5 class="fw-bold mb-1">{{ $active->nama }}</h5>
                <div class="display-5 fw-extrabold my-2 font-monospace">
                    Rp {{ number_format($active->harga_per_jam, 0, ',', '.') }}
                    <span class="fs-6 font-normal text-white-50">/ jam</span>
                </div>
                <p class="fs-7 text-white-50 m-0">
                    Tarif ini otomatis digunakan untuk menghitung biaya semua transaksi baru yang berlangsung.
                </p>
            @else
                <div class="alert alert-warning text-dark m-0">
                    Belum ada tarif aktif terpilih. Silakan aktifkan salah satu tarif.
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Tariff Table -->
    <div class="col-12 col-md-7">
        <div class="tremor-table-container">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold text-dark m-0">Daftar Skema Tarif</h6>
            </div>
            <table class="tremor-table">
                <thead>
                    <tr>
                        <th>Nama Skema</th>
                        <th>Harga / Jam</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifList as $t)
                        <tr>
                            <td class="fw-bold text-dark">{{ $t->nama }}</td>
                            <td class="fw-bold text-success font-monospace fs-6">
                                Rp {{ number_format($t->harga_per_jam, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($t->is_active)
                                    <span class="tremor-badge tremor-badge-success"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                                @else
                                    <span class="tremor-badge tremor-badge-info">Non-aktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if(!$t->is_active)
                                        <form action="{{ route('tarif.set-active', $t->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-2 fw-semibold px-2">
                                                Aktifkan Tarif
                                            </button>
                                        </form>

                                        <form action="{{ route('tarif.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus tarif {{ $t->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light border text-danger btn-sm rounded-2">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-light border btn-sm rounded-2" disabled>Tarif Utama</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data tarif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Tarif -->
<div class="modal fade" id="modalTambahTarif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Tambah Skema Tarif Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tarif.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nama Skema Tarif <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Tarif Regular / Weekend" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Harga Per Jam (Rp) <span class="text-danger">*</span></label>
                        <input type="number" step="500" min="0" name="harga_per_jam" class="form-control rounded-3" placeholder="Contoh: 3000" required>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label fw-semibold fs-7" for="is_active">Langsung aktifkan tarif ini sekarang</label>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Tarif</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
