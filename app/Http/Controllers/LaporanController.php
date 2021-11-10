<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Laporan;
use App\Models\TindakLanjut;
use App\Models\Pelanggaran;
use App\Models\Kawasan;
use App\Models\Kota;
use App\Models\Pelapor;
use App\Notifications\LaporanProcessNotification;
use Carbon\Carbon;

use Auth;
use DataTables;
use DB;
use Helper;

class LaporanController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roles = $user->getRoleNames();
        $user_city = $user->city_id ? Helper::GetOperatorCityName($user->city->city_name) : '';

        $city = Kota::all()->pluck('city_name', 'city_name');
        $pelanggaran = Pelanggaran::all()->pluck('name', 'uuid');
        $kawasan = Kawasan::all()->pluck('kawasan', 'uuid');

        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $laporans = Laporan::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'nomor_laporan', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'keterangan', 'photo', 'lat', 'lng', 'nama_lokasi', 'kawasan', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'status', 'created_at'
            ])->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                return $query->where(function ($q) use ($user, $user_city) {
                    return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                        ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                        ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                });
            });
            return Datatables::of($laporans)
                ->editColumn('jenis_pelanggaran', function ($row) {
                    return $row->pelanggaran->name ?? null;
                })
                ->editColumn('bentuk_pelanggaran', function ($row) {
                    return $row->BentukPelanggaran->bentuk_pelanggaran ?? null;
                })
                ->editColumn('kawasan', function ($row) {
                    return $row->Kawasan->kawasan ?? null;
                })
                ->editColumn('photo', function ($row) {
                    return $row->photo ? '<img style="width: 150px; height: 150px;"  src="' . $row->photo . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('status', function ($row) {
                    switch ($row->status) {
                        case 0:
                            return '<span class="badge badge-secondary">Diterima</span>';
                            break;
                        case 1:
                            return '<span class="badge badge-info">Ditindak lanjuti</span>';
                            break;
                        case 2:
                            return '<span class="badge badge-success">Selesai</span>';
                            break;
                        default:
                            return '<span class="badge badge-secondary">Diterima</span>';
                            break;
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('l\\, j F Y H:i:s');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('tindaklanjut.index', $row->uuid) . '"><i class="fal fa-edit"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['photo', 'status', 'action'])
                ->make();
        }

        return view('laporan.index', compact('city', 'pelanggaran', 'kawasan'));
    }

    public function filter(Request $request)
    {
        if (request()->ajax()) {
            $user = Auth::user();
            $roles = $user->getRoleNames();
            $user_city = $user->city_id ? Helper::GetOperatorCityName($user->city->city_name) : '';
            $request_city = Helper::GetOperatorCityName($request->get('city'));

            DB::statement(DB::raw('set @rownum=0'));

            $laporan = Laporan::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'nomor_laporan', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'keterangan', 'photo', 'lat', 'lng', 'nama_lokasi', 'kawasan', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'status', 'created_at'
            ])->when($roles[0] == "pemda", function ($query) use ($user, $user_city) {
                return $query->where(function ($q) use ($user, $user_city) {
                    return $q->where('kota', 'like', '%' . $user->city->city_name  . '%')
                        ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'City' . '%')
                        ->orWhere('kota', 'like', '%' . $user_city . ' ' . 'Regency' . '%');
                });
            })->when($request->get('tahun'), function ($query) use ($request) {
                return $query->whereYear('created_at', $request->get('tahun'));
            })->when($request->get('bulan'), function ($query) use ($request) {
                return $query->whereMonth('created_at', $request->get('bulan'));
            })->when($request->get('city'), function ($query) use ($request, $request_city) {
                return $query->where(function ($q) use ($request, $request_city) {
                    return $q->where('kota', 'like', '%' . $request->get('city') . '%')
                        ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'City' . '%')
                        ->orWhere('kota', 'like', '%' . $request_city . ' ' . 'Regency' . '%');
                });
            })->when($request->get('pelanggaran'), function ($query) use ($request) {
                return $query->where('jenis_pelanggaran', $request->get('pelanggaran'));
            })->when($request->get('bentuk'), function ($query) use ($request) {
                return $query->where('bentuk_pelanggaran', $request->get('bentuk'));
            })->when($request->get('kawasan'), function ($query) use ($request) {
                return $query->where('kawasan', $request->get('kawasan'));
            });

            return Datatables::of($laporan)
                ->editColumn('jenis_pelanggaran', function ($row) {
                    return $row->pelanggaran->name ?? null;
                })
                ->editColumn('bentuk_pelanggaran', function ($row) {
                    return $row->BentukPelanggaran->bentuk_pelanggaran ?? null;
                })
                ->editColumn('kawasan', function ($row) {
                    return $row->Kawasan->kawasan ?? null;
                })
                ->editColumn('photo', function ($row) {
                    return $row->photo ? '<img style="width: 150px; height: 150px;"  src="' . $row->photo . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('status', function ($row) {
                    switch ($row->status) {
                        case 0:
                            return '<span class="badge badge-secondary">Diterima</span>';
                            break;
                        case 1:
                            return '<span class="badge badge-info">Ditindak lanjuti</span>';
                            break;
                        case 2:
                            return '<span class="badge badge-success">Selesai</span>';
                            break;
                        default:
                            return '<span class="badge badge-secondary">Diterima</span>';
                            break;
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('l\\, j F Y H:i:s');
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('tindaklanjut.index', $row->uuid) . '"><i class="fal fa-edit"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['photo', 'status', 'action'])
                ->make();
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function tindaklanjut($id)
    {
        $idlaporan = Laporan::uuid($id);
        return view('laporan.tindak_lanjut', compact('idlaporan'));
    }

    public function storetindaklanjut(Request $request)
    {
        $rules = [
            'keterangan' => 'required|min:2',
            'status' => 'required'
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong !',
        ];

        $this->validate($request, $rules, $messages);
        $laporan = Laporan::uuid($request->laporan_id);
        $laporan->status = $request->status;
        $laporan->save();
        // get pelapor data
        $pelapor =  Pelapor::where('uuid', $laporan->created_by)->first();
        // Saving data
        $tindaklanjut = new TindakLanjut();
        $tindaklanjut->laporan_id = $request->laporan_id;
        $tindaklanjut->keterangan = $request->keterangan;
        $tindaklanjut->status = $request->status;
        $tindaklanjut->updated_by = Auth::user()->uuid;
        $tindaklanjut->save();

        $nomor_laporan = $laporan->nomor_laporan;
        switch ((int)$laporan->status) {
            case 0:
                $status_laporan = 'Telah diterima.';
                break;
            case 1:
                $status_laporan = 'Sedang ditindak lanjuti oleh pihak terkait.';
                break;
            case 2:
                $status_laporan = 'Telah selesai.';
                break;
            default:
                $status_laporan =  'Telah diterima';
                break;
        }
        // push notification
        $details = [
            'nomor_laporan' => $nomor_laporan,
            'status' => $status_laporan
        ];
        $pelapor->notify(new LaporanProcessNotification($details));

        toastr()->success('Laporan di tindak lanjuti', 'Success');
        return redirect()->route('laporan.index');
    }
}
