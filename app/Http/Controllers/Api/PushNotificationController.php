<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Firebase;
use App\Libraries\Push;
use App\Models\User;
use Helper;
// use Request;

class PushNotificationController extends Controller
{
    public function notification()
    {

        $token = "AAAA7vtgV4o:APA91bGc7FLESkwOnW1Mne4tcyZwENKSyQoirOny555Np4TU-F8wpr99KGughY2UNV-INUyspE-g2M9iRwZ1g-82m6oCLEpbU5fEtW80IuqpFIH2W11oLWDjt3fnZP_Xyyt5f6vCW8jS";  
        $from = "d0IckHe9o1l3aPayOh553P:APA91bGYaP4JYogz8hEzoRBX0EurBsuTOngNnmdTuQHplwZ6HHLQMw36sIc3EEGiKDreIBcW3FSDd_qU30lfy9RAlcxU9oi60hdueOpLhjSrDufG7xncaED0MHhk2j595--TEfOU8Lt8";
        $msg = array
                (
                'body'  => "Testing Testing",
                'title' => "Hi, From me to you",
                'receiver' => 'erw',
                'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
                'sound' => 'mySound'/*Default sound*/
                );

        $fields = array
                (
                    'to'        => $token,
                    'notification'  => $msg
                );

        $headers = array
                (
                    'Authorization: key=' . $from,
                    'Content-Type: application/json'
                );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        dd($result);
        curl_close( $ch );
    }
}
