<?php

namespace App\Http\Controllers;

use App\Models\Loker;
use Illuminate\Http\Request;

class LokerController extends Controller
{
    public function index(Request $request)
    {
        $query = Loker::with(['transaksi' => function ($q) {
            $q->where('status', 'titip')->latest();
        }]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', $request->lokasi);
        }

        $loker = $query->orderBy('nomor_loker')->get();
        $lokasiList = Loker::distinct()->pluck('lokasi');

        return view('loker.index', compact('loker', 'lokasiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_loker' => 'required|string|max:10|unique:loker,nomor_loker',
            'lokasi'      => 'required|string|max:50',
        ]);

        Loker::create([
            'nomor_loker' => strtoupper($request->nomor_loker),
            'lokasi'      => $request->lokasi,
            'status'      => 'tersedia',
        ]);

        return redirect()->route('loker.index')->with('success', 'Loker baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $loker = Loker::findOrFail($id);

        $request->validate([
            'nomor_loker' => 'required|string|max:10|unique:loker,nomor_loker,' . $id,
            'lokasi'      => 'required|string|max:50',
        ]);

        $loker->update([
            'nomor_loker' => strtoupper($request->nomor_loker),
            'lokasi'      => $request->lokasi,
        ]);

        return redirect()->route('loker.index')->with('success', 'Data loker berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $loker = Loker::findOrFail($id);

        if ($loker->status === 'terisi') {
            return back()->with('error', 'Loker yang sedang terisi tidak dapat dihapus.');
        }

        $loker->delete();

        return redirect()->route('loker.index')->with('success', 'Loker berhasil dihapus.');
    }
}
