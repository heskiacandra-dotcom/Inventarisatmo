<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommodityLocation extends Model
{
    protected $guarded = [];

    // Relasi ke Gedung
    public function gedung()
    {
        return $this->belongsTo(Gedung::class);
    }

    // Relasi ke Commodity
    public function commodities()
    {
        return $this->hasMany(Commodity::class);
    }
}