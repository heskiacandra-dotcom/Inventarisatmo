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

    // Tambahkan ini
    public function itemLendings()
    {
        return $this->hasMany(ItemLending::class, 'origin_location_id');
    }
    
    // Tambahkan ini
    public function roomBookings()
    {
        return $this->hasMany(RoomBooking::class, 'commodity_location_id');
    }
}