<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\External_link;

use Exception;
use Helper;
use Log;

class ExternalLinkController extends Controller
{
    public function listLink(Request $request)
    {
        $pelapor = Helper::pelapor();
        try {
            $blog = External_link::select('uuid', 'title', 'description', 'link')->orderByDesc('created_at')->get();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get external link list', [
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
            'data' => $blog,
        ]);
    }

    public function getOneBlog(Request $request, $id)
    {
        $pelapor = Helper::pelapor();
        try {
            $blog = External_link::select('uuid', 'title', 'description', 'link')->where('uuid', $id)->first();
        } catch (Exception $e) {
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error get external link detail', [
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
            'data' => $blog,
        ]);
    }
}
