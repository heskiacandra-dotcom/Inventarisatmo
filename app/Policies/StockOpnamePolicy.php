<?php

namespace App\Policies;

use App\User;

class StockOpnamePolicy
{

    public function viewAny(User $user)
    {
        // Izinkan akses ke halaman utama stock opname
        return $user->hasPermission('lihat stock opname');
    }

    public function viewHistory(User $user)
    {
        // Izinkan akses ke halaman history stock opname
        return $user->hasPermission('history stock opname');
    }

    public function create(User $user)
    {
        // Izinkan pembuatan stock opname baru
        return $user->hasPermission('tambah stock opname');
    }
}