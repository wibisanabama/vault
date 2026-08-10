<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::withCount(['helm', 'transaksi']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
        }

        $pelanggan = $query->latest()->paginate(10)->withQueryString();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'no_hp'  => 'required|string|max:15|unique:pelanggan,no_hp',
            'alamat' => 'nullable|string',
        ]);

        Pelanggan::create($request->only('nama', 'no_hp', 'alamat'));

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:100',
            'no_hp'  => 'required|string|max:15|unique:pelanggan,no_hp,' . $id,
            'alamat' => 'nullable|string',
        ]);

        $pelanggan->update($request->only('nama', 'no_hp', 'alamat'));

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }
}
