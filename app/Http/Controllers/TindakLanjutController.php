<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Pelapor;
use App\Models\TindakLanjut;
use App\Notifications\LaporanProcessNotification;
use Auth;
use Illuminate\Http\Request;

class TindakLanjutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
                $status_laporan = 'Diterima.';
                break;
            case 1:
                $status_laporan = 'Diproses.';
                break;
            case 2:
                $status_laporan = 'Selesai.';
                break;
            default:
                $status_laporan =  'Diterima.';
                break;
        }
        // send push notification
        $details = [
            'nomor_laporan' => $nomor_laporan,
            'status' => $status_laporan,
            'keterangan' => $request->keterangan,
        ];
        $pelapor->notify(new LaporanProcessNotification($details));

        toastr()->success('Laporan diproses', 'Success');
        return redirect()->route('laporan.index');
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
        $laporan = Laporan::uuid($id);
        $tindak_lanjut = TindakLanjut::where('laporan_id', $laporan->uuid)->latest()->first();
        return view('tindak_lanjut.create', compact('laporan', 'tindak_lanjut'));
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
        //
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
