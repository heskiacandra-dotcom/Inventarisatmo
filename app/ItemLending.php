<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemLending extends Model
{
    protected $guarded = [];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function originLocation()
    {
        return $this->belongsTo(CommodityLocation::class, 'origin_location_id');
    }

    public function returnLocation()
    {
        return $this->belongsTo(CommodityLocation::class, 'return_location_id');
    }
}