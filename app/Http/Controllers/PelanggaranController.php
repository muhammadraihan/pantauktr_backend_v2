<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Authorizable;
use App\Models\Pelanggaran;

use Auth;
use DataTables;
use Image;
use Storage;
use URL;

class PelanggaranController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Pelanggaran::latest()->get();

            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    return $row->image ? '<img style="width: 150px; height: 150px;"  src="' . $row->image . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('created_by', function ($row) {
                    return $row->userCreate->name ?? null;
                })
                ->editColumn('edited_by', function ($row) {
                    if ($row->edited_by != null) {
                        return $row->userEdit->name ?? null;
                    } else {
                        return null;
                    }
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('pelanggaran.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('pelanggaran.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action', 'image'])
                ->make(true);
        }

        return view('pelanggaran.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pelanggaran.create');
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
            'name' => 'required|min:2',
            'keterangan' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png|max:5000',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong',
            '*.mimes' => 'Type File Harus jpeg, jpg dan png',
            '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
        ];

        $this->validate($request, $rules, $messages);

        $image = $request->file('image');
        $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
        // resizing image to upload
        $resizeImage = Image::make($image);
        $resizeImage->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
        })->encode();
        // upload resized image to gcs
        $googleContent = 'reference' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $resizeImage);
        $fileUrl = $disk->url($googleContent);

        $pelanggaran = new Pelanggaran();
        $pelanggaran->name = $request->name;
        $pelanggaran->keterangan = $request->keterangan;
        $pelanggaran->image = $fileUrl;
        $pelanggaran->created_by = Auth::user()->uuid;

        $pelanggaran->save();


        toastr()->success('New Pelanggaran Added', 'Success');
        return redirect()->route('pelanggaran.index');
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
        $pelanggaran = Pelanggaran::uuid($id);
        return view('pelanggaran.edit', compact('pelanggaran'));
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
        $rules = [
            'name' => 'required|min:2',
            'keterangan' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png|max:5000',
        ];

        $messages = [
            '*.required' => 'Field tidak boleh kosong',
            '*.mimes' => 'Type File Harus jpeg, jpg dan png',
            '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
        ];

        $this->validate($request, $rules, $messages);
        // Saving data
        $pelanggaran = Pelanggaran::uuid($id);
        $pelanggaran->name = $request->name;
        $pelanggaran->keterangan = $request->keterangan;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
            // resizing image to upload
            $resizeImage = Image::make($image);
            $resizeImage->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            // upload resized image to gcs
            $googleContent = 'reference' . '/' . $filename;
            $disk = Storage::disk('gcs');
            $disk->put($googleContent, (string) $resizeImage);
            $fileUrl = $disk->url($googleContent);
            $pelanggaran->image = $fileUrl;
        }
        $pelanggaran->edited_by = Auth::user()->uuid;
        $pelanggaran->save();

        toastr()->success('Pelanggaran Edited', 'Success');
        return redirect()->route('pelanggaran.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pelanggaran = Pelanggaran::uuid($id);
        $pelanggaran->delete();
        toastr()->success('Pelanggaran Deleted', 'Success');
        return redirect()->route('pelanggaran.index');
    }
}
