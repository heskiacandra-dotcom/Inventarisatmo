<?php

namespace App;
use App\Commodity;

use Illuminate\Database\Eloquent\Model;

class CommodityStockOpname extends Model
{
    protected $table = 'commodity_stock_opname';
    
    protected $fillable = [
        'commodity_id',
        'system_quantity',
        'physical_quantity',
        'condition',
        'stock_opname_at',
        'updated_by' // Ditambahkan
];

// Tambahkan relasi ke user
public function updatedBy()
{
    return $this->belongsTo(User::class, 'updated_by');
}

    protected $casts = [
        'stock_opname_at' => 'datetime',
    ];
    protected $dates = ['stock_opname_at'];

    protected static function booted()
{
    static::saved(function ($stockOpname) {
        $stockOpname->commodity->updateCondition();
    });
}
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
}