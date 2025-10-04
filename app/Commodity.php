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
    ];

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

    public function getPenyusutanPerBulanAttribute()
    {
        $harga = $this->price_per_item;
        $umur = $this->umur_manfaat ?? 60; // default 60 bulan
        $residu = $this->nilai_residu ?? 0;

        return ($harga - $residu) / $umur;
    }
}
