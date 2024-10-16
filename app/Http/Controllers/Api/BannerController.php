<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
class BannerController extends Controller
{
    public function getbannerlist()
    {
        $bannerlist = Banner::all();
        return response()->json($bannerlist);
    }   
}
