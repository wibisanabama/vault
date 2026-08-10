<?php

namespace App\Http\Controllers;

use App\Models\Helm;
use App\Models\Loker;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tarif;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['pelanggan', 'helm', 'loker', 'user', 'pembayaran']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', function ($qp) use ($search) {
                      $qp->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('helm', function ($qh) use ($search) {
                      $qh->where('merk', 'like', "%{$search}%");
                  });
            });
        }

        $transaksi = $query->latest()->paginate(10)->withQueryString();

        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $lokerTersedia = Loker::where('status', 'tersedia')->count();
        $activeTarif = Tarif::where('is_active', true)->first() ?? (object)['harga_per_jam' => 2000];

        return view('transaksi.create', compact('lokerTersedia', 'activeTarif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp'          => 'required|string|max:15',
            'alamat'         => 'nullable|string',
            'merk_helm'      => 'required|string|max:50',
            'warna_helm'     => 'required|string|max:30',
            'deskripsi_helm' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Find available locker
            $loker = Loker::where('status', 'tersedia')->lockForUpdate()->first();

            if (! $loker) {
                return back()->withInput()->with('error', 'Maaf, semua loker sedang terisi!');
            }

            // Find or Create Pelanggan
            $pelanggan = Pelanggan::firstOrCreate(
                ['no_hp' => $request->no_hp],
                [
                    'nama'   => $request->nama_pelanggan,
                    'alamat' => $request->alamat,
                ]
            );

            // Create Helm record
            $helm = Helm::create([
                'pelanggan_id' => $pelanggan->id,
                'merk'         => $request->merk_helm,
                'warna'        => $request->warna_helm,
                'deskripsi'    => $request->deskripsi_helm,
            ]);

            // Generate Kode Transaksi VLT-YYYYMMDD-XXX
            $todayStr = Carbon::today()->format('Ymd');
            $countToday = Transaksi::whereDate('tgl_titip', Carbon::today())->count();
            $kodeTransaksi = 'VLT-' . $todayStr . '-' . str_pad($countToday + 1, 3, '0', STR_PAD_LEFT);

            // Create Transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'pelanggan_id'   => $pelanggan->id,
                'helm_id'        => $helm->id,
                'loker_id'       => $loker->id,
                'user_id'        => auth()->id(),
                'tgl_titip'      => Carbon::now(),
                'status'         => 'titip',
            ]);

            // Update Locker Status
            $loker->update(['status' => 'terisi']);

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Penitipan helm berhasil dicatat. Struk bukti titipan siap dicetak!');
        });
    }

    public function show($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'helm', 'loker', 'user', 'pembayaran'])->findOrFail($id);

        $activeTarif = Tarif::where('is_active', true)->first();
        $rate = $activeTarif ? $activeTarif->harga_per_jam : 2000;

        $tglTitip = Carbon::parse($transaksi->tgl_titip);
        $tglAmbil = $transaksi->tgl_ambil ? Carbon::parse($transaksi->tgl_ambil) : Carbon::now();

        $durasiMenit = max(1, $tglTitip->diffInMinutes($tglAmbil));
        $durasiJam = (int) ceil($durasiMenit / 60);
        $estimasiBiaya = $durasiJam * $rate;

        return view('transaksi.show', compact('transaksi', 'durasiJam', 'durasiMenit', 'rate', 'estimasiBiaya'));
    }

    public function showAmbilForm($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'helm', 'loker'])->findOrFail($id);

        if ($transaksi->status !== 'titip') {
            return redirect()->route('transaksi.show', $id)
                ->with('error', 'Transaksi ini sudah selesai atau dibatalkan.');
        }

        $activeTarif = Tarif::where('is_active', true)->first();
        $rate = $activeTarif ? $activeTarif->harga_per_jam : 2000;

        $tglTitip = Carbon::parse($transaksi->tgl_titip);
        $tglAmbil = Carbon::now();

        $durasiMenit = max(1, $tglTitip->diffInMinutes($tglAmbil));
        $durasiJam = (int) ceil($durasiMenit / 60);
        $totalBiaya = $durasiJam * $rate;

        return view('transaksi.ambil', compact('transaksi', 'durasiJam', 'durasiMenit', 'rate', 'totalBiaya'));
    }

    public function prosesAmbil(Request $request, $id)
    {
        $request->validate([
            'metode_bayar' => 'required|in:tunai,ewallet',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $transaksi = Transaksi::with('loker')->lockForUpdate()->findOrFail($id);

            if ($transaksi->status !== 'titip') {
                return redirect()->route('transaksi.show', $id)
                    ->with('error', 'Transaksi ini tidak dalam status titip.');
            }

            $activeTarif = Tarif::where('is_active', true)->first();
            $rate = $activeTarif ? $activeTarif->harga_per_jam : 2000;

            $tglTitip = Carbon::parse($transaksi->tgl_titip);
            $tglAmbil = Carbon::now();

            $durasiMenit = max(1, $tglTitip->diffInMinutes($tglAmbil));
            $durasiJam = (int) ceil($durasiMenit / 60);
            $totalBiaya = $durasiJam * $rate;

            // Update Transaksi
            $transaksi->update([
                'tgl_ambil' => $tglAmbil,
                'status'    => 'ambil',
                'tarif'     => $totalBiaya,
            ]);

            // Create Pembayaran
            Pembayaran::create([
                'transaksi_id' => $transaksi->id,
                'jumlah'       => $totalBiaya,
                'metode_bayar' => $request->metode_bayar,
                'tgl_bayar'    => $tglAmbil,
                'status'       => 'lunas',
            ]);

            // Free the locker
            if ($transaksi->loker) {
                $transaksi->loker->update(['status' => 'tersedia']);
            }

            return redirect()->route('transaksi.show', $transaksi->id)
                ->with('success', 'Pengambilan helm & pembayaran berhasil diproses!');
        });
    }

    public function struk($id)
    {
        $transaksi = Transaksi::with(['pelanggan', 'helm', 'loker', 'user', 'pembayaran'])->findOrFail($id);
        return view('transaksi.struk', compact('transaksi'));
    }
}
