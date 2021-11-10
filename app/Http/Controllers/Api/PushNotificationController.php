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
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error saving push notification registration token', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'origin' => env('APP_URL'),
                'error' => $e->getMessage(),
            ]);
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
            $revoke_fcm_token = FcmRegistrationToken::where('token', $fcm_token)->first();
            $revoke_fcm_token->revoked = 1;
            $revoke_fcm_token->save();
        } catch (Exception $e) {
            DB::rollback();
            // log message to local an slack
            Log::stack(['stack', 'slack'])->error('Error revoking push notification registration token', [
                'user' => $pelapor->email,
                'agent' => $request->header('User-Agent'),
                'origin' => env('APP_URL'),
                'error' => $e->getMessage(),
            ]);
            return response()->json([
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
