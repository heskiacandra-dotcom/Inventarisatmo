<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoomBooking extends Model
{
    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(CommodityLocation::class, 'commodity_location_id');
    }
}