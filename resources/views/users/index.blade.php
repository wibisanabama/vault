@extends('layouts.app', ['title' => 'Kelola Petugas & Admin'])

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark m-0">Kelola Akun Petugas & Admin</h4>
        <small class="text-muted">Manajemen hak akses pengguna sistem Vault</small>
    </div>
    <button class="btn btn-primary rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
        <i class="bi bi-person-plus-fill me-1"></i> Tambah User Baru
    </button>
</div>

<!-- Users Table Card -->
<div class="tremor-table-container">
    <div class="table-responsive">
        <table class="tremor-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama User</th>
                    <th>Email Login</th>
                    <th>Role Akses</th>
                    <th>Total Transaksi Dilayani</th>
                    <th>Terdaftar Pada</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $u)
                    <tr>
                        <td>{{ $users->firstItem() + $index }}</td>
                        <td class="fw-bold text-dark">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                                <span class="badge bg-info text-dark ms-1">Anda</span>
                            @endif
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="tremor-badge tremor-badge-danger"><i class="bi bi-shield-lock-fill"></i> Admin</span>
                            @else
                                <span class="tremor-badge tremor-badge-info"><i class="bi bi-person-badge-fill"></i> Petugas</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-pill px-3 py-1">{{ $u->transaksi_count }} Transaksi</span>
                        </td>
                        <td>{{ $u->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <button class="btn btn-light border btn-sm rounded-2" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $u->id }}" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                    <button type="button" class="btn btn-light border text-danger btn-sm rounded-2" data-bs-toggle="modal" data-bs-target="#modalHapusUser{{ $u->id }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Modal Edit User -->
                            <div class="modal fade text-start" id="modalEditUser{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 border-0 shadow">
                                        <div class="modal-header border-bottom">
                                            <h5 class="modal-title fw-bold text-dark">Edit Data User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('users.update', $u->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control rounded-3" value="{{ $u->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Email Login</label>
                                                    <input type="email" name="email" class="form-control rounded-3" value="{{ $u->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Role / Peran</label>
                                                    <select name="role" class="form-select rounded-3">
                                                        <option value="petugas" {{ $u->role === 'petugas' ? 'selected' : '' }}>Petugas</option>
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold fs-7">Password Baru (Kosongkan jika tidak diubah)</label>
                                                    <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 8 karakter">
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

                            <!-- Modal Hapus User -->
                            @if($u->id !== auth()->id())
                                <div class="modal fade text-start" id="modalHapusUser{{ $u->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">
                                            <div class="modal-header border-bottom">
                                                <h5 class="modal-title fw-bold text-dark">Konfirmasi Hapus User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <div class="text-danger mb-3">
                                                    <i class="bi bi-exclamation-triangle-fill display-4"></i>
                                                </div>
                                                <h6 class="fw-bold text-dark">Apakah Anda yakin ingin menghapus akun <strong>{{ $u->name }}</strong>?</h6>
                                                <p class="text-muted fs-7 mb-0">Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                            <div class="modal-footer border-top justify-content-center">
                                                <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Ya, Hapus Akun</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top">
            {{ $users->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="modalTambahUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Ahmad Subagyo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3" placeholder="user@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Role / Peran <span class="text-danger">*</span></label>
                        <select name="role" class="form-select rounded-3">
                            <option value="petugas" selected>Petugas</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-7">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Minimal 8 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Tambah User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
