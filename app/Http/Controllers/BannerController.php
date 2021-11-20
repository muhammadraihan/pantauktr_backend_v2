<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Traits\Authorizable;

use Auth;
use DataTables;
use DB;
use URL;
use Image;
use Validator;
use Storage;

class BannerController extends Controller
{
    use Authorizable;
    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $banner = Banner::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'photo', 'url', 'status', 'created_by'
            ])->get();
            return Datatables::of($banner)
                ->addIndexColumn()
                ->editColumn('photo', function ($row) {
                    return $row->photo ? '<img style="width: 150px; height: 150px;"  src="' . $row->photo . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == '1') {
                        return 'Aktif';
                    } else {
                        return 'Tidak Aktif';
                    }
                })
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('banner.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('banner.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action', 'photo'])
                ->make(true);
        }

        return view('banners.index');
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
            'url' => 'required',
            'status' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
            '*.mimes' => 'Type File Harus jpeg, jpg dan png',
            '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
        ];
        $this->validate($request, $rules, $messages);
        $image = $request->file('photo');
        $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
        
        $resizeImage = Image::make($image);
        $resizeImage->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
        })->encode();
        
        $googleContent = 'banner' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $resizeImage);
        $fileUrl = $disk->url($googleContent);
        $banner = new Banner();
        $banner->photo = $fileUrl;
        $banner->url = $request->url;
        if ($request->has('status')) {
            if ($request->status == 1) {
                Banner::where('status', 1)->update(['status' => 0]);
                $banner->status = $request->status;
            } else {
                $banner->status = $request->status;
            }
        }
        $banner->created_by = Auth::user()->uuid;
        $banner->save();

        toastr()->success('New Banner Added', 'Success');
        return redirect()->route('banner.index');
    }

    public function show(Banner $banner)
    {
        //
    }

    public function edit($uuid)
    {
        $banner = Banner::uuid($uuid);
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, $uuid)
    {
        $rules = [
            'url' => 'required',
            'status' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
        ];
        $this->validate($request, $rules, $messages);

        $banner = Banner::uuid($uuid);
        $banner->url = $request->url;
        $banner->status = $request->status;
        if ($request->hasfile('photo')) {
            $rules = [
                'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
                'url' => 'required',
                'status' => 'required'
            ];
            $messages = [
                '*.required' => 'Data Harus Di isi',
                '*.mimes' => 'Type File Harus jpeg, jpg dan png',
                '*.max' => 'Size File Tidak Boleh Lebih Dari 5Mb'
            ];
            $this->validate($request, $rules, $messages);
            $image = $request->file('photo');
            $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
            $resizeImage = Image::make($image);
            $resizeImage->resize(800, 800, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            $googleContent = 'banner' . '/' . $filename;
            $disk = Storage::disk('gcs');
            $disk->put($googleContent, (string) $resizeImage);
            $fileUrl = $disk->url($googleContent);
            $banner->photo = $fileUrl;
        }
        if ($request->has('status')) {
            Banner::where('status', 1)->update(['status' => 0]);
            $banner->status = $request->status;
        }
        $banner->edited_by = Auth::user()->uuid;
        $banner->save();
        toastr()->success('Banner Edited', 'Success');
        return redirect()->route('banner.index');
    }

    public function destroy($uuid)
    {
        $banner = Banner::uuid($uuid);
        $banner->delete();
        toastr()->success('Banner Deleted', 'Success');
        return redirect()->route('banner.index');
    }
}
