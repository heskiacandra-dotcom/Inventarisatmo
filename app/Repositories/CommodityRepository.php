<?php

namespace App\Repositories;

use App\Commodity;

class CommodityRepository
{
    public function __construct(
        private Commodity $model
    ) {}

    
    // Tambahkan method baru ini
    public function getConditionName(int $condition): string
    {
        return match ($condition) {
            1 => 'Baik',
            2 => 'Rusak',
            3 => 'Belum di SO',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Count commodities based on different conditions.
     */
    public function countCommodityCondition()
    {
        return $this->model->selectRaw('`condition`, COUNT(`condition`) AS count')
            ->groupBy('condition')
            ->get();
    }

    /**
     * Count commodities for each date of purchase.
     */
    public function countCommodityEachDate()
    {
        return $this->model->selectRaw('COUNT(`date_of_purchase`) AS count, date_of_purchase')
            ->whereNotNull('date_of_purchase')
            ->where('date_of_purchase', '!=', '')
            ->groupBy('date_of_purchase')
            ->orderBy('date_of_purchase')
            ->get();
    }

    /**
     * Count the number of commodities grouped by material.
     */
    public function countCommodityByMaterial()
    {
        return $this->model->selectRaw('COUNT(`material`) AS count, material')
            ->whereNotNull('material')
            ->where('material', '!=', '')
            ->where('material', '!=', '-')
            ->groupBy('material')
            ->orderByDesc('count')
            ->get();
    }

    /**
     * Count the number of commodities grouped by brand.
     */
    public function countCommodityByBrand()
    {
        return $this->model->selectRaw('COUNT(`brand`) AS count, brand')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->where('brand', '!=', '-')
            ->groupBy('brand')
            ->orderByDesc('count')
            ->take(8)
            ->get();
    }
}
