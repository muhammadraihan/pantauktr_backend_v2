<?php

namespace App\Helper;

use App\Models\FcmRegistrationToken;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Auth;

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

  public static function ListOfMonths($oldest_date, $latest_date)
  {
    $latest = Carbon::parse($latest_date)->format('Y-m-d');
    $oldest = Carbon::parse($oldest_date)->format('Y-m-d');
    $period = CarbonPeriod::create($oldest, '1 month', $latest);
    $months = array();
    foreach ($period as $key => $date) {
      $months[] = $date->format('M');
    }

    return $months;
  }

  public static function ListOfYears($oldest_date, $latest_date)
  {
    $latest = Carbon::parse($latest_date)->format('Y-m-d');
    $oldest = Carbon::parse($oldest_date)->format('Y-m-d');
    $period = CarbonPeriod::create($oldest, '1 year', $latest);
    $years = array();
    foreach ($period as $key => $date) {
      $years[] = $date->format('Y');
    }
    return $years;
  }

  public static function GetOperatorCityName($city)
  {
    $explode_city = explode(" ", $city);
    $explode_1 = array_key_exists(1, $explode_city) ? $explode_city[1] : "";
    $explode_2 = array_key_exists(2, $explode_city) ? $explode_city[2] : "";
    $city = $explode_2 ? $explode_1 . " " . $explode_2 : $explode_1;

    return $city;
  }
}
