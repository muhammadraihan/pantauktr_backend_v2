<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Website;
use App\Models\Instagram;
use App\Models\StaticPage;

use Exception;
use Helper;
use Log;


class ContentController extends Controller
{
    public function getBanner(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $banner = Banner::select('id', 'uuid', 'photo', 'url')->where('status', 1)->first();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $banner,
        ]);
    }
    public function getWebsiteContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $website = Website::select('uuid', 'title', 'slug', 'photo', 'description')->latest()->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $website,
        ]);
    }

    public function getWebsiteContentDetail(Request $request, $id)
    {
        $pelapor = Helper::pelapor();
        try {
            $website = Website::select('uuid', 'title', 'slug', 'photo', 'description')->where('uuid', $id)->first();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $website,
        ]);
    }

    public function getInstagramContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $instagram = Instagram::select('uuid', 'photo', 'caption')->latest()->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $instagram,
        ]);
    }

    public function getStaticPageContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $static_page = StaticPage::select('menu_name', 'url')->get();
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $static_page,
        ]);
    }
}
