<?php

namespace App;

use App\CommodityStockOpname;
use App\CommodityLocation;
use App\Gedung;
use App\JenisBarang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commodity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'condition' => 'integer',
        'aktif'     => 'integer',
        'tanggal_pelepasan' => 'date',
    ];

    /**
     * Scope: hanya barang yang masih aktif (belum dilepas)
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', 1);
    }

    /**
     * Lepas / disposal barang — set aktif = 0 dan isi data pelepasan
     */
    public function lepas(string $tanggalPelepasan, string $metodePelepasan, ?string $catatanPelepasan = null): void
    {
        $this->update([
            'aktif'              => 0,
            'tanggal_pelepasan'  => $tanggalPelepasan,
            'metode_pelepasan'   => $metodePelepasan,
            'catatan_pelepasan'  => $catatanPelepasan,
        ]);
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate nomor urut per lokasi
            $model->item_number = $model->generateItemNumber();

            // Relasi
            $commodityLocation = CommodityLocation::find($model->commodity_location_id);
            $gedung = Gedung::find($model->gedung_id);
            $jenisBarang = JenisBarang::find($model->jenis_barang_id);

            // Jika salah satu relasi tidak ditemukan, log dan hentikan pembuatan kode
            if (! $commodityLocation || ! $gedung || ! $jenisBarang) {
                Log::warning('Relasi tidak ditemukan saat membuat Commodity', [
                    'commodity_location_id' => $model->commodity_location_id,
                    'gedung_id' => $model->gedung_id,
                    'jenis_barang_id' => $model->jenis_barang_id,
                ]);
                return;
            }

            // Ekstrak tahun dari date_of_purchase (fleksibel)
            $tahun = null;

            if (!empty($model->date_of_purchase)) {
                if (preg_match('/\b(19|20)\d{2}\b/', $model->date_of_purchase, $matches)) {
                    $tahun = $matches[0];
                }
            }

            if (!$tahun) {
                $tahun = now()->format('Y'); // fallback
            }


            // Format urut 3 digit
            $formattedItemNumber = str_pad($model->item_number, 3, '0', STR_PAD_LEFT);

            // Susun kode barang
            $model->item_code = sprintf(
                '%s.%s.%s.%s.%s.%s',
                $jenisBarang->kode,         // Jenis
                $tahun,                     // Tahun
                1,                          // Teritori default
                $gedung->kode,              // Peruntukan/Gedung
                $commodityLocation->kode,   // Ruang
                $formattedItemNumber        // Urut
            );
        });
    }

    /**
     * Generate nomor urut per lokasi
     */
    private function generateItemNumber()
    {
        $lastNumber = self::where('commodity_location_id', $this->commodity_location_id)
            ->max('item_number');

        return ($lastNumber ? $lastNumber : 0) + 1;
    }

    /**
     * Accessor nomor urut terformat (3 digit)
     */
    public function getFormattedItemNumberAttribute()
    {
        return str_pad($this->item_number, 3, '0', STR_PAD_LEFT);
    }

    // ==== Relasi ====

    public function jenisBarang()
    {
        return $this->belongsTo(JenisBarang::class);
    }

    public function commodity_location()
    {
        return $this->belongsTo(CommodityLocation::class);
    }

    public function gedung()
    {
        return $this->belongsTo(Gedung::class, 'gedung_id', 'id');
    }

    public function commodity_acquisition()
    {
        return $this->belongsTo(CommodityAcquisition::class);
    }

    public function stockOpnames()
    {
        return $this->hasMany(CommodityStockOpname::class);
    }

    public function latestStockOpname()
    {
        return $this->hasOne(CommodityStockOpname::class)->latestOfMany('stock_opname_at');
    }

    public function services()
    {
        return $this->hasMany(CommodityService::class);
    }

    // ==== Helper tambahan ====

    public function indonesian_format_date($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function indonesian_currency($value)
    {
        return number_format($value, 2, ',', '.');
    }

    public function updateCondition()
    {
        $latestStockOpname = $this->stockOpnames()->latest('stock_opname_at')->first();
        $now = now();

        if ($latestStockOpname) {
            $deadline = $latestStockOpname->stock_opname_at->addMonths(3);
            $this->condition = ($now->gt($deadline)) ? 3 : $latestStockOpname->condition;
        } else {
            $deadline = $this->created_at->addMonths(3);
            $this->condition = ($now->gt($deadline)) ? 3 : 3;
        }

        $this->save();
    }

    public function getConditionName()
    {
        return match ($this->condition) {
            1 => 'Baik',
            2 => 'Rusak',
            3 => 'Belum di SO',
            default => null
        };
    }

    public function getHargaPerolehanAttribute()
    {
        return $this->price_per_item > 0 ? (float)$this->price_per_item : ($this->price > 0 ? (float)$this->price : 0);
    }

    public function getTanggalPerolehanParsedAttribute()
    {
        if (!empty($this->tanggal_perolehan)) {
            try {
                return Carbon::parse($this->tanggal_perolehan);
            } catch (\Throwable $e) {}
        }

        if (!empty($this->date_of_purchase)) {
            try {
                if (preg_match('/\b(19|20)\d{2}\b/', (string)$this->date_of_purchase, $matches)) {
                    return Carbon::createFromDate((int)$matches[0], 1, 1);
                }
                return Carbon::parse($this->date_of_purchase);
            } catch (\Throwable $e) {}
        }

        return $this->created_at ? Carbon::parse($this->created_at) : now();
    }

    public function getUmurManfaatBulanAttribute()
    {
        return ($this->umur_manfaat && (int)$this->umur_manfaat > 0) ? (int)$this->umur_manfaat : 60;
    }

    public function getNilaiResiduValueAttribute()
    {
        return $this->nilai_residu ? (float)$this->nilai_residu : 0;
    }

    public function getPenyusutanPerBulanAttribute()
    {
        $harga = $this->harga_perolehan;
        $residu = $this->nilai_residu_value;
        $umur = $this->umur_manfaat_bulan;

        if ($umur <= 0) {
            return 0;
        }

        return max(0, ($harga - $residu) / $umur);
    }

    public function getBulanBerjalanAttribute()
    {
        $perolehan = $this->tanggal_perolehan_parsed;
        $umur = $this->umur_manfaat_bulan;
        $diff = max(0, $perolehan->diffInMonths(now()));

        return min($diff, $umur);
    }

    public function getAkumulasiPenyusutanAttribute()
    {
        $penyusutan = $this->penyusutan_per_bulan * $this->bulan_berjalan;
        $maxPenyusutan = max(0, $this->harga_perolehan - $this->nilai_residu_value);

        return min($penyusutan, $maxPenyusutan);
    }

    public function getNilaiBukuAttribute()
    {
        return max($this->nilai_residu_value, $this->harga_perolehan - $this->akumulasi_penyusutan);
    }

    public function getPersentasePenyusutanAttribute()
    {
        if ($this->harga_perolehan <= 0) {
            return 0;
        }
        return min(100, round(($this->akumulasi_penyusutan / $this->harga_perolehan) * 100, 1));
    }

    public function itemLendings()
    {
        return $this->hasMany(ItemLending::class);
    }
}
