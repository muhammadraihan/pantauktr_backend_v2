<?php

namespace App\Helper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Helper
{
  /**
   * Helper for greeting message
   * based on hour time
   * @return string
   */
  public static function greeting()
  {
    $carbon = Carbon::now('Asia/Jakarta');
    $hour = $carbon->format('H');
    if ($hour < 12) {
      return 'Selamat Pagi';
    } elseif ($hour < 17) {
      return 'Selamat Siang';
    }
    return 'Selamat Malam';
  }

  public static function pelapor()
  {
    return Auth::guard('pelapors-api')->user();
  }

  public static function GenerateReportNumber($length = 20)
  {
    $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
  }

  public static function notify($id, $title, $message)
  {
    $accesstoken = env('FCM_KEY');

    // $post_data = array(
    //       "id" => $id,
    //       "notif_id" => $message,
    //       "title" => $title,
    // );

    $post_data = [
      'registration_ids' => 'AAAA7vtgV4o:APA91bGc7FLESkwOnW1Mne4tcyZwENKSyQoirOny555Np4TU-F8wpr99KGughY2UNV-INUyspE-g2M9iRwZ1g-82m6oCLEpbU5fEtW80IuqpFIH2W11oLWDjt3fnZP_Xyyt5f6vCW8jS',
      'notification' => [
        'title' => 'Your title here',
        'body' => 'Your message here',
        'image' => 'Your image link here',
      ],
      'data' => [
        'username' => 'Your username here',
        'description' => 'Your message here',
        'media' => 'Your image link here',
        'category' => 'Your category name',
        'link' => 'Category Link'
      ]
    ];
    $dataString = json_encode($post_data);
    // dd($post_data,$dataString);
    $headers = [
      'Authorization: key=' . $accesstoken,
      'Content-Type: application/json',
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

    $rest = curl_exec($ch);
    return $rest;
    // return response()->json(['status_report' => $message,'message'=>$title]);
    curl_close($ch);
    // dd($rest);
    // if ($rest === false) {
    //     // throw new Exception('Curl error: ' . curl_error($crl));
    //     //print_r('Curl error: ' . curl_error($crl));
    //     $result_notif = 'data belum update';
    // } else {

    // }

    //curl_close($crl);
    //print_r($result_noti);die;
    //     $result_notif = $message;
    // return $result_notif;
  }
}
