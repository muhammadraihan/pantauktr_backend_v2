<?php

namespace App\Http\Controllers;

use App\Models\Instagram;
use Illuminate\Http\Request;
use App\Traits\Authorizable;

use Auth;
use DataTables;
use DB;
use URL;
use Image;
use Validator;
use Storage;

class InstagramController extends Controller
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
            DB::statement(DB::raw('set @rownum=0'));
            $instagram = Instagram::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'photo', 'caption', 'created_by'
            ])->get();
            return Datatables::of($instagram)
                ->addIndexColumn()
                ->editColumn('photo', function ($row) {
                    return $row->photo ? '<img style="width: 150px; height: 150px;"  src="' . $row->photo . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('instagram.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('instagram.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action', 'photo'])
                ->make(true);
        }

        return view('instagrams.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('instagrams.create');
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
            'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
            'caption' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
            '*.mimes' => 'Type File Harus jpeg, jpg dan png',
            '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
        ];
        $this->validate($request, $rules, $messages);
        $image = $request->file('photo');
        $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
        // resizing image to upload
        $resizeImage = Image::make($image);
        $resizeImage->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
        })->encode();
        // upload resized image to gcs
        $googleContent = 'instagram' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $resizeImage);
        $fileUrl = $disk->url($googleContent);

        $instagram = new Instagram();
        $instagram->photo = $fileUrl;
        $instagram->caption = $request->caption;
        $instagram->created_by = Auth::user()->uuid;
        $instagram->save();

        toastr()->success('New Instagram Content Added', 'Success');
        return redirect()->route('instagram.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Instagram  $instagram
     * @return \Illuminate\Http\Response
     */
    public function show($uuid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Instagram  $instagram
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $instagram = Instagram::uuid($uuid);
        return view('instagrams.edit', compact('instagram'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Instagram  $instagram
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $instagram = Instagram::uuid($uuid);
        if ($request->hasfile('photo')) {
            $rules = [
                'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
                'caption' => 'required'
            ];
            $messages = [
                '*.required' => 'Data Harus Di isi',
                '*.mimes' => 'Type File Harus jpeg, jpg dan png',
                '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
            ];
            $this->validate($request, $rules, $messages);
            $image = $request->file('photo');
            $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
            // resizing image to upload
            $resizeImage = Image::make($image);
            $resizeImage->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            // upload resized image to gcs
            $googleContent = 'instagram' . '/' . $filename;
            $disk = Storage::disk('gcs');
            $disk->put($googleContent, (string) $resizeImage);
            $fileUrl = $disk->url($googleContent);
            $instagram->photo = $fileUrl;
        }
        $instagram->caption = $request->caption;
        $instagram->edited_by = Auth::user()->uuid;
        $instagram->save();
        toastr()->success('Instagram Content Edited', 'Success');
        return redirect()->route('instagram.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Instagram  $instagram
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $instagram = Instagram::uuid($uuid);
        $instagram->delete();
        toastr()->success('Instagram Content Deleted', 'Success');
        return redirect()->route('instagram.index');
    }
}
