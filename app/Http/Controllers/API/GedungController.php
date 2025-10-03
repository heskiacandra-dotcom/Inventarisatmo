<?php

namespace App\Http\Controllers\API;

use App\Gedung;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class GedungController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Gedung $gedung)
    {
        return response()->json([
            'code' => Response::HTTP_OK,
            'message' => 'success',
            'data' => $gedung,
        ], Response::HTTP_OK);
    }
}
