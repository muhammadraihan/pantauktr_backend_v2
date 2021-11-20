<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FcmRegistrationToken;
use Carbon\Carbon;

use DB;
use Exception;
use Helper;
use Log;


class PushNotificationController extends Controller
{
    public function SaveToken(Request $request)
    {
        $pelapor = Helper::pelapor();
        DB::beginTransaction();
        try {
            $newFcmToken = new FcmRegistrationToken;
            $newFcmToken->pelapor_id = $pelapor->uuid;
            $newFcmToken->token = $request->fcm_token;
            $newFcmToken->revoked = false;
            $newFcmToken->expires_at = Carbon::now()->addMonths(2);
            $newFcmToken->save();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        DB::commit();
        return response()->json([
            "success" => true,
            "message" => 'FCM token saved',
        ]);
    }

    public function RevokeToken(Request $request)
    {
        $pelapor = Helper::pelapor();
        $fcm_token = $request->fcm_token;
        DB::beginTransaction();
        try {
            FcmRegistrationToken::where('token', $fcm_token)
                ->where('revoked', 0)
                ->update(['revoked' => 1]);
        } catch (Exception $e) {
            DB::rollback();response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        DB::commit();
        return response()->json([
            "success" => true,
            "message" => 'FCM token revoked',
        ]);
    }
}
