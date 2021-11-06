<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Laporan;
use App\Models\TindakLanjut;
use App\Models\Pelanggaran;
use App\Models\Kawasan;
use App\Models\Kota;
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
                'id', 'uuid', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'keterangan', 'photo', 'lat', 'lng', 'nama_lokasi', 'kawasan', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_at'
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
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('l\\, j F Y H:i:s');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('tindaklanjut.index', $row->uuid) . '"><i class="fal fa-edit"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['photo', 'action'])
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
                'id', 'uuid', 'jenis_pelanggaran', 'bentuk_pelanggaran', 'keterangan', 'photo', 'lat', 'lng', 'nama_lokasi', 'kawasan', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'propinsi', 'negara', 'place_id', 'created_by', 'created_at'
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
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat('l\\, j F Y H:i:s');
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('tindaklanjut.index', $row->uuid) . '"><i class="fal fa-edit"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['photo', 'action'])
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
        // Saving data
        $tindaklanjut = new TindakLanjut();
        $tindaklanjut->laporan_id = $request->laporan_id;
        $tindaklanjut->keterangan = $request->keterangan;
        $tindaklanjut->status = $request->status;
        $tindaklanjut->updated_by = Auth::user()->uuid;

        $tindaklanjut->save();
        $this->sendNotifToAndroid($tindaklanjut);

        toastr()->success('Tindak Lanjut Updated', 'Success');
        return redirect()->route('laporan.index');
    }

    public function sendNotifToAndroid($messages)
    {
        //token from env
        $token = "AAAA7vtgV4o:APA91bGc7FLESkwOnW1Mne4tcyZwENKSyQoirOny555Np4TU-F8wpr99KGughY2UNV-INUyspE-g2M9iRwZ1g-82m6oCLEpbU5fEtW80IuqpFIH2W11oLWDjt3fnZP_Xyyt5f6vCW8jS";
        //token from device

        // $token = Laporan::uuid($messages->laporan_id)->pluck('token_device');
        $from = "d0IckHe9o1l3aPayOh553P:APA91bGyzUPnsqCjQ6xDTBW_ZUG1TQ-JL3wu053nmgWlT3le1vEen_s6Ty8R9kiUd2M2Vr8RwNLgCAcu3G8-xbrVG5VxQoMosxPdAogSaIPZ7k0xX-fnDgjTrnIsFJcnKIf5_qJ5TGWR";

        $msg = collect(array(
            'body'  => $messages->status,
            'title' => "Status Laporan",
            'receiver' => 'erw',
            'icon'  => "https://image.flaticon.com/icons/png/512/270/270014.png",/*Default Icon*/
            'sound' => 'mySound'/*Default sound*/
        ))->toJson();

        $fields = (object)array(

            'to'        => $from,
            'notification'  => array(
                "data" => $msg,
            )
        );

        $headers = array(
            'Authorization: key=' . $token,
            'Content-Type: application/json'
        );
        //#Send Reponse To FireBase Server 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        return $result;
        curl_close($ch);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
