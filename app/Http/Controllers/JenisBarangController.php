<?php

namespace App\Http\Controllers;

use App\JenisBarang;
use App\Http\Requests\StoreJenisBarangRequest;
use App\Http\Requests\UpdateJenisBarangRequest;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    public function index()
    {
        $jenisBarang = JenisBarang::all();
        return view('jenis-barang.index', compact('jenisBarang'))
            ->with('title', 'Jenis Barang')
            ->with('page_heading', 'Daftar Jenis Barang');
    }

    public function create()
    {
        return view('jenis-barang.create')
            ->with('title', 'Tambah Jenis Barang')
            ->with('page_heading', 'Tambah Jenis Barang');
    }

    //Perbarui method store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:5|unique:jenis_barang,kode',
            'name' => 'required|string|max:50',
        ]);

        JenisBarang::create($validated);
        return redirect()->route('jenis-barang.index')
            ->with('success', 'Jenis barang berhasil ditambahkan');
    }

    public function edit(JenisBarang $jenisBarang)
    {
        return view('jenis-barang.edit', compact('jenisBarang'))
            ->with('title', 'Edit Jenis Barang')
            ->with('page_heading', 'Edit Jenis Barang');
    }

   // Perbarui method update
   public function update(Request $request, JenisBarang $jenisBarang)
   {
       $validated = $request->validate([
           'kode' => 'required|string|max:5|unique:jenis_barang,kode,'.$jenisBarang->id,
           'name' => 'required|string|max:50',
       ]);

       $jenisBarang->update($validated);
       return redirect()->route('jenis-barang.index')
           ->with('success', 'Jenis barang berhasil diubah');
   }

    public function destroy(JenisBarang $jenisBarang)
    {
        $jenisBarang->delete();
        return redirect()->route('jenis-barang.index')->with('success', 'Jenis barang berhasil dihapus');
    }
}
