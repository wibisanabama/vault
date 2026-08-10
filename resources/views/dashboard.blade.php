@extends('layouts.app', ['title' => 'Dashboard Overview'])

@section('content')
<div class="row g-3 mb-4">
    <!-- Stat 1: Loker Tersedia -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <div class="tremor-card-header">
                <span class="tremor-card-title">Kapasitas Loker</span>
                <span class="tremor-badge tremor-badge-success">
                    <i class="bi bi-box-seam"></i> {{ $lokerTersedia }} Kosong
                </span>
            </div>
            <div class="tremor-card-value">{{ $lokerTersedia }} <span class="fs-6 text-muted font-normal">/ {{ $totalLoker }}</span></div>
            <div class="tremor-card-sub">Loker Siap Terisi</div>
        </div>
    </div>

    <!-- Stat 2: Sedang Dititip -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <div class="tremor-card-header">
                <span class="tremor-card-title">Helm Dititip</span>
                <span class="tremor-badge tremor-badge-warning">
                    <i class="bi bi-clock-history"></i> Aktif
                </span>
            </div>
            <div class="tremor-card-value">{{ $sedangDititip }}</div>
            <div class="tremor-card-sub">Helm di dalam loker saat ini</div>
        </div>
    </div>

    <!-- Stat 3: Pendapatan Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <div class="tremor-card-header">
                <span class="tremor-card-title">Pendapatan Hari Ini</span>
                <span class="tremor-badge tremor-badge-info">
                    <i class="bi bi-calendar-event"></i> Hari Ini
                </span>
            </div>
            <div class="tremor-card-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</div>
            <div class="tremor-card-sub">{{ $transaksiHariIni }} total transaksi hari ini</div>
        </div>
    </div>

    <!-- Stat 4: Pendapatan Bulan Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="tremor-card">
            <div class="tremor-card-header">
                <span class="tremor-card-title">Pendapatan Bulan Ini</span>
                <span class="tremor-badge tremor-badge-success">
                    <i class="bi bi-graph-up-arrow"></i> Bulanan
                </span>
            </div>
            <div class="tremor-card-value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
            <div class="tremor-card-sub">Bulan {{ date('F Y') }}</div>
        </div>
    </div>
</div>

<!-- Quick Action & Charts Row -->
<div class="row g-4 mb-4">
    <!-- Chart Pendapatan 7 Hari -->
    <div class="col-12 col-lg-8">
        <div class="tremor-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <h6 class="fw-bold text-dark m-0">Tren Pendapatan 7 Hari Terakhir</h6>
                    <small class="text-muted">Grafik total penerimaan transaksi harian</small>
                </div>
            </div>
            <div style="height: 260px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="col-12 col-lg-4">
        <div class="tremor-card h-100 d-flex flex-column justify-content-between">
            <div>
                <h6 class="fw-bold text-dark mb-3">Aksi Cepat Operasional</h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary py-3 rounded-3 fw-semibold text-start d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-plus-circle-fill me-2 fs-5"></i> Titip Helm Baru</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>

                    <a href="{{ route('transaksi.index', ['status' => 'titip']) }}" class="btn btn-light border py-3 rounded-3 fw-semibold text-start d-flex align-items-center justify-content-between text-dark">
                        <span><i class="bi bi-box-arrow-up-right me-2 text-primary fs-5"></i> Proses Ambil Helm</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>

                    <a href="{{ route('loker.index') }}" class="btn btn-light border py-3 rounded-3 fw-semibold text-start d-flex align-items-center justify-content-between text-dark">
                        <span><i class="bi bi-grid-3x3-gap-fill me-2 text-warning fs-5"></i> Cek Grid Loker</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top text-center text-muted" style="font-size: 0.8rem;">
                <i class="bi bi-shield-check text-success me-1"></i> Vault Security Operating System v1.0
            </div>
        </div>
    </div>
</div>

<!-- Tabel Transaksi Terbaru -->
<div class="tremor-table-container">
    <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
            <h6 class="fw-bold text-dark m-0">Transaksi Terbaru</h6>
            <small class="text-muted">5 aktivitas penitipan helm terkini</small>
        </div>
        <a href="{{ route('transaksi.index') }}" class="btn btn-link btn-sm text-decoration-none fw-semibold">
            Lihat Semua Transaksi <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="table-responsive">
        <table class="tremor-table">
            <thead>
                <tr>
                    <th>Kode Struk</th>
                    <th>Pelanggan</th>
                    <th>Helm</th>
                    <th>Loker</th>
                    <th>Waktu Titip</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksiTerbaru as $t)
                    <tr>
                        <td class="fw-bold text-primary">{{ $t->kode_transaksi }}</td>
                        <td>
                            <div class="fw-semibold">{{ $t->pelanggan->nama }}</div>
                            <div class="text-muted fs-7">{{ $t->pelanggan->no_hp }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $t->helm->merk }}</div>
                            <div class="text-muted fs-7">Warna: {{ $t->helm->warna }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary px-2 py-1 fs-7 fw-bold">{{ $t->loker->nomor_loker }}</span>
                        </td>
                        <td>{{ $t->tgl_titip->format('d/m/Y H:i') }}</td>
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
                            @if($t->status === 'titip')
                                <a href="{{ route('transaksi.ambil', $t->id) }}" class="btn btn-success btn-sm rounded-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Ambil
                                </a>
                            @else
                                <a href="{{ route('transaksi.show', $t->id) }}" class="btn btn-light border btn-sm rounded-2">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartDataPendapatan),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2,
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
