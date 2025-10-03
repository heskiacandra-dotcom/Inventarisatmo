<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Gedung extends Model
{
    // Tentukan nama tabel secara eksplisit
    protected $table = 'gedung';

    protected $guarded = [];

    public function commodityLocations()
    {
        return $this->hasMany(CommodityLocation::class);
    }
}
