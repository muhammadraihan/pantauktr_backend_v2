<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pelapor;
use Auth;
use Carbon\Carbon;
use Helper;

class DashboardController extends Controller
{
  public function index()
  {
    return redirect()->route('backoffice.dashboard');
  }

  public function dashboard()
  {
    $user = Auth::user();
    $roles = $user->getRoleNames();
    $user_city = $user->city_id ? Helper::GetOperatorCityName($user->city->city_name) : '';

    $today = Carbon::now()->format('Y-m-d');
    $this_month = Carbon::now()->format('m');

    $total_laporan = Laporan::when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
      return $query->where(function ($q) use ($user, $user_city) {
        return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
          ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
          ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
      });
    })->count();
    $laporan_proses = Laporan::where('status', 1)
      ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
        return $query->where(function ($q) use ($user, $user_city) {
          return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
        });
      })->count();
    $laporan_selesai = Laporan::where('status', 2)
      ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
        return $query->where(function ($q) use ($user, $user_city) {
          return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
        });
      })->count();
    $laporan_today = Laporan::whereDate('created_at', $today)
      ->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
        return $query->where(function ($q) use ($user, $user_city) {
          return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
            ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
        });
      })->count();
    $total_pengguna = Pelapor::count();
    $pengguna_month = Pelapor::whereMonth('created_at', $this_month)->count();

    return view('backoffice.dashboard', compact('total_laporan', 'laporan_proses', 'laporan_selesai', 'laporan_today', 'total_pengguna', 'pengguna_month'));
  }
}
