<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Helper;

class PushNotificationController extends Controller
{
    public function notification()
    {
        // $notifHelper = Helper::notify();
        $user = User::where('id', 1)->first();
 
        $notification_id = $user->id;
        $title = "Greeting Notification";
        $message = "Have good day!";
        $id = $user->id;
        $type = "basic";
        
        $res = Helper::notify($notification_id, $title, $message, $id,$type);
        
        // return $res;
        if($res == 1){
        
            // success code
            return response()->json([
                'success' => true,
                'messages' => 'Notif terkirim',
                ],200);
        }else{
        
            return response()->json([
                'success' => true,
                'messages' => 'Notif gagal',
                ],400);
        }
    }
}
