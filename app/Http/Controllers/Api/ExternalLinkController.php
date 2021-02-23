<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\External_link;

class ExternalLinkController extends Controller
{
    public function listLink(Request $request)
    {
        $blog = External_link::select('uuid','title','description','link')->get();
        return response()->json([
            'success' => true,
            'data' => $blog,
        ],200);
    }

    public function getOneBlog(Request $request,$id)
    {
        $blog = External_link::select('uuid','title','description','link')->where('uuid',$id)->first();
        return response()->json([
            'success' => true,
            'data' => $blog,
        ],200);
    }
}
