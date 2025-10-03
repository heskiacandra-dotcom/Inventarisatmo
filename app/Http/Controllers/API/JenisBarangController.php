<?php

namespace App\Http\Controllers\API;

use App\CommodityLocation;
use App\Http\Controllers\Controller;
use App\JenisBarang;
use Illuminate\Http\Response;

class JenisBarangController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(JenisBarang $jenisBarang)
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $jenisBarang,
        ], Response::HTTP_OK);
    }
}
