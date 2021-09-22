<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\DynamicMenu;
use App\Models\Website;


class ContentController extends Controller
{
    /**
     * Get Banner for Tata Cara
     *
     * @return json
     */
    public function getBanner()
    {
        $banner = Banner::select('id', 'uuid', 'photo')->where('status',1)->get();
        return response()->json([
            'success' => true,
            'data' => $banner,
        ], 200);
    }
    /**
     * Get Website Content
     *
     * @return json
     */
    public function getWebsiteContent()
    {
        $website = Website::select('id','uuid','title','slug','photo','description')->get();
        return response()->json([
            'success' => true,
            'data' => $website,
        ],200);
    }
    /**
     * Get Detail Website Content
     *
     * @param [type] $id
     * @return json
     */
    public function getWebsiteContentDetail($id)
    {
        $website = Website::select('id','uuid','title','slug','photo','description')->where('uuid',$id)->first();
        return response()->json([
            'success' => true,
            'data' => $website,
        ],200);
    }
}
