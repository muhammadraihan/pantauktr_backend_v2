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
    /**
     * Get Banner for Tata Cara
     *
     * @return json
     */
    public function getBanner(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $banner = Banner::select('id', 'uuid', 'photo')->where('status', 1)->first();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get active banner', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
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
    public function getWebsiteContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $website = Website::select('id', 'uuid', 'title', 'slug', 'photo', 'description')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get website content list', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $website,
        ], 200);
    }
    /**
     * Get Detail Website Content
     *
     * @param [type] $id
     * @return json
     */
    public function getWebsiteContentDetail(Request $request, $id)
    {
        $pelapor = Helper::pelapor();
        try {
            $website = Website::select('id', 'uuid', 'title', 'slug', 'photo', 'description')->where('uuid', $id)->first();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get website content detail', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $website,
        ], 200);
    }

    public function getInstagramContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $instagram = Instagram::select('id', 'uuid', 'photo', 'caption')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get instagram post', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $instagram,
        ], 200);
    }

    public function getStaticPageContent(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $static_page = StaticPage::select('id', 'uuid', 'menu_name', 'url')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get static page post', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => $static_page,
        ], 200);
    }

}
