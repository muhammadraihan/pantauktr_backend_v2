<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Laporan;
use App\Models\Pelanggaran;
use App\Models\BentukPelanggaran;
use App\Models\Kawasan;
use App\Models\User;

use Auth;
use DB;

class ChartController extends Controller
{
    use Authorizable;

    public function index(Request $request, User $uuid)
    {
        $year = DB::table('laporans')
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y') tahun"))
            ->groupBy('tahun')
            ->get();
        $month = DB::table('laporans')
            ->select(DB::raw("DATE_FORMAT(created_at, '%m') bulan"))
            ->groupBy('bulan')
            ->get();

        if ($request->user()->hasRole('pemda')) {
            $laporan_pelanggaran = DB::table('laporans')
                ->join('pelanggarans', 'pelanggarans.uuid', '=', 'laporans.jenis_pelanggaran')
                ->select('laporans.jenis_pelanggaran', 'pelanggarans.name')
                ->where('laporans.kota', 'like', $users->city->city_name)
                ->get();

            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();
            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_bentuk_pelanggaran = DB::table('laporans')
                ->join('bentuk_pelanggarans', 'bentuk_pelanggarans.uuid', '=', 'laporans.bentuk_pelanggaran')
                ->select('laporans.bentuk_pelanggaran', 'bentuk_pelanggarans.bentuk_pelanggaran')
                ->where('laporans.kota', 'like', $users->city->city_name)
                ->get();

            $lapBentukPelanggaran = $laporan_bentuk_pelanggaran->groupBy('name');
            $arrBentukPelanggaran = array();
            foreach ($lapBentukPelanggaran as $key => $value) {
                $arrBentukPelanggaran[$key] = count($value);
            }

            $laporan_kawasan = DB::table('laporans')
                ->join('kawasans', 'kawasans.uuid', '=', 'laporans.kawasan')
                ->select('laporans.kawasan', 'kawasans.kawasan')
                ->where('laporans.kota', 'like', $users->city->city_name)
                ->get();

            $lapKawasan = $laporan_kawasan->groupBy('name');
            $arrKawasan = array();
            foreach ($lapKawasan as $key => $value) {
                $arrKawasan[$key] = count($value);
            }
        } else {
            $laporan_pelanggaran = DB::table('laporans')
                ->join('pelanggarans', 'pelanggarans.uuid', '=', 'laporans.jenis_pelanggaran')
                ->select('laporans.jenis_pelanggaran', 'pelanggarans.name')
                ->get();

            $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
            $arrPelanggaran = array();

            foreach ($lapPelanggaran as $key => $value) {
                $arrPelanggaran[$key] = count($value);
            }

            $laporan_bentuk_pelanggaran = DB::table('laporans')
                ->join('bentuk_pelanggarans', 'bentuk_pelanggarans.uuid', '=', 'laporans.bentuk_pelanggaran')
                ->select('laporans.bentuk_pelanggaran', 'bentuk_pelanggarans.bentuk_pelanggaran')
                ->get();

            $lapBentukPelanggaran = $laporan_bentuk_pelanggaran->groupBy('bentuk_pelanggaran');
            $arrBentukPelanggaran = array();
            foreach ($lapBentukPelanggaran as $key => $value) {
                $arrBentukPelanggaran[$key] = count($value);
            }

            $laporan_kawasan = DB::table('laporans')
                ->join('kawasans', 'kawasans.uuid', '=', 'laporans.kawasan')
                ->select('laporans.kawasan', 'kawasans.kawasan')
                ->get();

            $lapKawasan = $laporan_kawasan->groupBy('kawasan');
            $arrKawasan = array();
            foreach ($lapKawasan as $key => $value) {
                $arrKawasan[$key] = count($value);
            }
        }
        return view('chart.index', compact(
            'year',
            'month',

            'laporan_pelanggaran',
            'lapPelanggaran',
            'arrPelanggaran',

            'laporan_bentuk_pelanggaran',
            'lapBentukPelanggaran',
            'arrBentukPelanggaran',

            'laporan_kawasan',
            'lapKawasan',
            'arrKawasan',
        ));
    }

    public function filter(Request $request, User $uuid)
    {
        $users = Auth::user($uuid);
        if (request()->ajax()) {
            if ($request->user()->hasRole('pemda')) {
                $laporan_pelanggaran = DB::table('laporans')
                    ->join('pelanggarans', 'pelanggarans.uuid', '=', 'laporans.jenis_pelanggaran')
                    ->select('laporans.jenis_pelanggaran', 'pelanggarans.name')
                    ->whereYear('laporans.created_at', $request['tahun'])
                    ->whereMonth('laporans.created_at', $request['bulan'])
                    ->where('laporans.kota', 'like', $users->city->city_name)
                    ->get();

                $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
                $arrPelanggaran = array();
                foreach ($lapPelanggaran as $key => $value) {
                    $arrPelanggaran[$key] = count($value);
                }

                $laporan_bentuk_pelanggaran = DB::table('laporans')
                    ->join('bentuk_pelanggarans', 'bentuk_pelanggarans.uuid', '=', 'laporans.bentuk_pelanggaran')
                    ->select('laporans.bentuk_pelanggaran', 'bentuk_pelanggarans.bentuk_pelanggaran')
                    ->whereYear('laporans.created_at', $request['tahun'])
                    ->whereMonth('laporans.created_at', $request['bulan'])
                    ->where('laporans.kota', 'like', $users->city->city_name)
                    ->get();

                $lapBentukPelanggaran = $laporan_bentuk_pelanggaran->groupBy('bentuk_pelanggaran');
                $arrBentukPelanggaran = array();
                foreach ($lapBentukPelanggaran as $key => $value) {
                    $arrBentukPelanggaran[$key] = count($value);
                }

                $laporan_kawasan = DB::table('laporans')
                    ->join('kawasans', 'kawasans.uuid', '=', 'laporans.kawasan')
                    ->select('laporans.kawasan', 'kawasans.kawasan')
                    ->whereYear('laporans.created_at', $request['tahun'])
                    ->whereMonth('laporans.created_at', $request['bulan'])
                    ->where('laporans.kota', 'like', $users->city->city_name)
                    ->get();

                $lapKawasan = $laporan_kawasan->groupBy('kawasan');
                $arrKawasan = array();
                foreach ($lapKawasan as $key => $value) {
                    $arrKawasan[$key] = count($value);
                }
            } else {
                $laporan_pelanggaran = DB::table('laporans')
                    ->join('pelanggarans', 'pelanggarans.uuid', '=', 'laporans.jenis_pelanggaran')
                    ->select('laporans.jenis_pelanggaran', 'pelanggarans.name')
                    ->whereYear('laporans.created_at', $request['tahun'])
                    ->whereMonth('laporans.created_at', $request['bulan'])
                    ->get();
                $lapPelanggaran = $laporan_pelanggaran->groupBy('name');
                $arrPelanggaran = array();
                foreach ($lapPelanggaran as $key => $value) {
                    $arrPelanggaran[$key] = count($value);
                }

                $laporan_bentuk_pelanggaran = DB::table('laporans')
                ->join('bentuk_pelanggarans', 'bentuk_pelanggarans.uuid', '=', 'laporans.bentuk_pelanggaran')
                ->select('laporans.bentuk_pelanggaran', 'bentuk_pelanggarans.bentuk_pelanggaran')
                ->whereYear('laporans.created_at', $request['tahun'])
                ->whereMonth('laporans.created_at', $request['bulan'])
                ->get();

                $lapBentukPelanggaran = $laporan_bentuk_pelanggaran->groupBy('bentuk_pelanggaran');
                $arrBentukPelanggaran = array();
                foreach ($lapBentukPelanggaran as $key => $value) {
                    $arrBentukPelanggaran[$key] = count($value);
                }

                $laporan_kawasan = DB::table('laporans')
                    ->join('kawasans', 'kawasans.uuid', '=', 'laporans.kawasan')
                    ->select('laporans.kawasan', 'kawasans.kawasan')
                    ->whereYear('laporans.created_at', $request['tahun'])
                    ->whereMonth('laporans.created_at', $request['bulan'])
                    ->get();

                $lapKawasan = $laporan_kawasan->groupBy('kawasan');
                $arrKawasan = array();
                foreach ($lapKawasan as $key => $value) {
                    $arrKawasan[$key] = count($value);
                }
            }
        }
        return response()->json([$arrPelanggaran,$arrBentukPelanggaran,$arrKawasan]);
    }
}
