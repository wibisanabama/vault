<?php

namespace App\Http\Controllers;

use App\Models\Loker;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalLoker = Loker::count();
        $lokerTersedia = Loker::where('status', 'tersedia')->count();
        $lokerTerisi = Loker::where('status', 'terisi')->count();

        $transaksiHariIni = Transaksi::whereDate('tgl_titip', $today)->count();
        $sedangDititip = Transaksi::where('status', 'titip')->count();

        $pendapatanHariIni = Pembayaran::whereDate('tgl_bayar', $today)
            ->where('status', 'lunas')
            ->sum('jumlah');

        $pendapatanBulanIni = Pembayaran::whereBetween('tgl_bayar', [$startOfMonth, Carbon::now()])
            ->where('status', 'lunas')
            ->sum('jumlah');

        $transaksiTerbaru = Transaksi::with(['pelanggan', 'helm', 'loker', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // 7 Days Chart Data
        $chartLabels = [];
        $chartDataPendapatan = [];
        $chartDataTitip = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');

            $chartDataPendapatan[] = Pembayaran::whereDate('tgl_bayar', $date)
                ->where('status', 'lunas')
                ->sum('jumlah');

            $chartDataTitip[] = Transaksi::whereDate('tgl_titip', $date)->count();
        }

        return view('dashboard', compact(
            'totalLoker',
            'lokerTersedia',
            'lokerTerisi',
            'transaksiHariIni',
            'sedangDititip',
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'transaksiTerbaru',
            'chartLabels',
            'chartDataPendapatan',
            'chartDataTitip'
        ));
    }
}
