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
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

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

        $pelapor =  Pelapor::where('uuid', $laporan->created_by)->first();

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
        $details = [
            'nomor_laporan' => $nomor_laporan,
            'status' => $status_laporan,
            'keterangan' => $request->keterangan,
        ];
        $pelapor->notify(new LaporanProcessNotification($details));

        toastr()->success('Laporan diproses', 'Success');
        return redirect()->route('laporan.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $laporan = Laporan::uuid($id);
        $tindak_lanjut = TindakLanjut::where('laporan_id', $laporan->uuid)->latest()->first();
        return view('tindak_lanjut.create', compact('laporan', 'tindak_lanjut'));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
