<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBarang extends Model
{
    // Tentukan nama tabel secara eksplisit
    protected $table = 'jenis_barang';
    protected $fillable = ['name', 'kode'];

    public function kategoriBarang()
    {
        return $this->hasMany(KategoriBarang::class, 'jenis_barang_id');
    }
}