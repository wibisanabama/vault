@extends('layouts.app', ['title' => 'Data Pelanggan'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Data Pelanggan</h4>
        <small class="text-muted">Daftar pelanggan yang pernah menggunakan layanan penitipan helm</small>
    </div>
    <button class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahPelanggan">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah Pelanggan
    </button>
</div>

<!-- Search Bar -->
<div class="tremor-card mb-4 p-3">
    <form action="{{ route('pelanggan.index') }}" method="GET" class="row g-2">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm rounded-start-pill" placeholder="Cari nama atau no WhatsApp...">
                <button type="submit" class="btn btn-primary btn-sm rounded-end-pill px-3">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </div>
        @if(request('search'))
            <div class="col-auto">
                <a href="{{ route('pelanggan.index') }}" class="btn btn-light border btn-sm rounded-pill">Reset</a>
            </div>
        @endif
    </form>
</div>

<!-- Table Card -->
<div class="tremor-table-container">
    <div class="table-responsive">
        <table class="tremor-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pelanggan</th>
                    <th>Nomor WhatsApp / HP</th>
                    <th>Alamat / Catatan</th>
                    <th>Total Titip</th>
                    <th>Tanggal Terdaftar</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggan as $index => $p)
                    <tr>
                        <td>{{ $pelanggan->firstItem() + $index }}</td>
                        <td class="fw-bold text-dark">{{ $p->nama }}</td>
                        <td>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->no_hp) }}" target="_blank" class="text-decoration-none fw-semibold text-success">
                                <i class="bi bi-whatsapp"></i> {{ $p->no_hp }}
                            </a>
                        </td>
                        <td>{{ $p->alamat ?? '-' }}</td>
                        <td>
                            <span class="badge bg-primary rounded-pill px-3 py-1">{{ $p->transaksi_count }}x Penitipan</span>
                        </td>
                        <td>{{ $p->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-light border btn-sm rounded-2" data-bs-toggle="modal" data-bs-target="#modalEditPelanggan{{ $p->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <form action="{{ route('pelanggan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data pelanggan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light border text-danger btn-sm rounded-2" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal Edit Pelanggan -->
                            <div class="modal fade text-start" id="modalEditPelanggan{{ $p->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title fw-bold text-dark">Edit Data Pelanggan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('pelanggan.update', $p->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Nama Pelanggan</label>
                                                    <input type="text" name="nama" class="form-control rounded-3" value="{{ $p->nama }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Nomor WhatsApp / HP</label>
                                                    <input type="text" name="no_hp" class="form-control rounded-3" value="{{ $p->no_hp }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Alamat / Catatan</label>
                                                    <textarea name="alamat" class="form-control rounded-3" rows="2">{{ $p->alamat }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top">
                                                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data pelanggan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pelanggan->hasPages())
        <div class="p-3 border-top">
            {{ $pelanggan->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Pelanggan -->
<div class="modal fade" id="modalTambahPelanggan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Tambah Pelanggan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pelanggan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nama Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <input type="text" name="no_hp" class="form-control rounded-3" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Alamat / Catatan Identitas</label>
                        <textarea name="alamat" class="form-control rounded-3" rows="2" placeholder="Alamat atau nomor plat motor..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Tambah Pelanggan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
