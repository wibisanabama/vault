<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'harian'); // 'harian' or 'bulanan'
        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $bulan = $request->get('bulan', Carbon::today()->format('Y-m'));

        $query = Transaksi::with(['pelanggan', 'helm', 'loker', 'user', 'pembayaran']);

        if ($jenis === 'harian') {
            $query->whereDate('tgl_titip', $tanggal);
            $periodeText = 'Hari ' . Carbon::parse($tanggal)->translatedFormat('d F Y');
        } else {
            $date = Carbon::createFromFormat('Y-m', $bulan);
            $query->whereYear('tgl_titip', $date->year)
                  ->whereMonth('tgl_titip', $date->month);
            $periodeText = 'Bulan ' . $date->translatedFormat('F Y');
        }

        $transaksi = $query->latest()->get();

        $totalTransaksi = $transaksi->count();
        $transaksiSelesai = $transaksi->where('status', 'ambil')->count();
        $sedangTitip = $transaksi->where('status', 'titip')->count();

        // Calculate revenue from pembayaran
        $transaksiIds = $transaksi->pluck('id');
        $pembayaran = Pembayaran::whereIn('transaksi_id', $transaksiIds)->where('status', 'lunas')->get();

        $totalPendapatan = $pembayaran->sum('jumlah');
        $pendapatanTunai = $pembayaran->where('metode_bayar', 'tunai')->sum('jumlah');
        $pendapatanEwallet = $pembayaran->where('metode_bayar', 'ewallet')->sum('jumlah');

        return view('laporan.index', compact(
            'jenis',
            'tanggal',
            'bulan',
            'periodeText',
            'transaksi',
            'totalTransaksi',
            'transaksiSelesai',
            'sedangTitip',
            'totalPendapatan',
            'pendapatanTunai',
            'pendapatanEwallet'
        ));
    }
}
