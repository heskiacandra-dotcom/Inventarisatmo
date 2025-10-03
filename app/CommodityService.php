<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommodityService extends Model
{
    protected $casts = [
        'tanggal_diservis' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected $fillable = [
        'commodity_id', 'tanggal_diservis', 'kerusakan', 'perbaikan', 'tanggal_selesai', 'status'
    ];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
    
}
