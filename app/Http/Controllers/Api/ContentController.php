<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\DynamicMenu;


class ContentController extends Controller
{
    public function getBanner(Request $request)
    {
        $banner = Banner::select('id', 'uuid', 'photo')->where('status',1)->get();
        return response()->json([
            'success' => true,
            'data' => $banner,
        ], 200);
    }

    public function getDynamicMenu(Request $request)
    {
        $dynamic_menu = DynamicMenu::select('id','uuid','icon','judul')->where('status',1)->get();
        return response()->json([
            'success' => true,
            'data' => $dynamic_menu,
        ], 200);
    }
}
