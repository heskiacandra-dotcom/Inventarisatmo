<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommodityLocationReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }

    public function reported_location()
    {
        return $this->belongsTo(CommodityLocation::class , 'reported_location_id');
    }
}
