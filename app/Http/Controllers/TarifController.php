<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifList = Tarif::latest()->get();
        return view('tarif.index', compact('tarifList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:50',
            'harga_per_jam' => 'required|numeric|min:0',
        ]);

        // If marked active or if no active rate exists, activate it
        $isFirst = Tarif::count() === 0;

        Tarif::create([
            'nama'          => $request->nama,
            'harga_per_jam' => $request->harga_per_jam,
            'is_active'     => $isFirst || $request->boolean('is_active'),
        ]);

        if ($request->boolean('is_active')) {
            Tarif::where('id', '!=', Tarif::latest()->first()->id)->update(['is_active' => false]);
        }

        return redirect()->route('tarif.index')->with('success', 'Tarif baru berhasil ditambahkan.');
    }

    public function setActive($id)
    {
        Tarif::query()->update(['is_active' => false]);
        Tarif::where('id', $id)->update(['is_active' => true]);

        return redirect()->route('tarif.index')->with('success', 'Tarif aktif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tarif = Tarif::findOrFail($id);

        if ($tarif->is_active) {
            return back()->with('error', 'Tarif yang sedang aktif tidak dapat dihapus.');
        }

        $tarif->delete();

        return redirect()->route('tarif.index')->with('success', 'Tarif berhasil dihapus.');
    }
}
