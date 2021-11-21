<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use App\Traits\Authorizable;

use Auth;
use DataTables;
use DB;
use URL;
use Image;
use Validator;
use Storage;

class WebsiteController extends Controller
{
    use Authorizable;
  
    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $websites = Website::select([
                DB::raw('@rownum  := @rownum  + 1 AS rownum'),
                'id', 'uuid', 'title', 'photo', 'description', 'created_by'
            ])->get();
            return Datatables::of($websites)
                ->addIndexColumn()
                ->editColumn('photo', function ($row) {
                    return $row->photo ? '<img style="width: 150px; height: 150px;"  src="' . $row->photo . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('website.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('website.show', $row->uuid) . '"><i class="fal fa-eye"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('website.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action', 'photo'])
                ->make(true);
        }

        return view('websites.index');
    }

    public function create()
    {
        return view('websites.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'slug' => 'required',
            'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
            'description' => 'required'
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
        $resizeImage->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
        })->encode();
        
        $googleContent = 'website' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $resizeImage);
        $fileUrl = $disk->url($googleContent);

        $websites = new Website();
        $websites->title = $request->title;
        $websites->slug = $request->slug;
        $websites->photo = $fileUrl;
        $websites->description = $request->description;
        $websites->created_by = Auth::user()->uuid;
        $websites->save();

        toastr()->success('New News Added', 'Success');
        return redirect()->route('website.index');
    }

    public function show($uuid)
    {
        $website = Website::uuid($uuid);
        return view('websites.show', compact('website'));
    }

    public function edit($uuid)
    {
        $website = Website::uuid($uuid);
        return view('websites.edit', compact('website'));
    }

    public function update(Request $request, $uuid)
    {
        $website = Website::uuid($uuid);
        if ($request->hasfile('photo')) {
            $rules = [
                'title' => 'required',
                'slug' => 'required',
                'photo' => 'required|mimes:jpeg,jpg,png|max:5000',
                'description' => 'required'
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
            $resizeImage->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
            
            $googleContent = 'website' . '/' . $filename;
            $disk = Storage::disk('gcs');
            $disk->put($googleContent, (string) $resizeImage);
            $fileUrl = $disk->url($googleContent);
            $website->photo = $fileUrl;
        }
        $website->title = $request->title;
        $website->slug = $request->slug;
        $website->description = $request->description;
        $website->edited_by = Auth::user()->uuid;
        $website->save();
        toastr()->success('Berita Edited', 'Success');
        return redirect()->route('website.index');
    }

    public function destroy($uuid)
    {
        $website = Website::uuid($uuid);
        $website->delete();
        toastr()->success('Berita Deleted', 'Success');
        return redirect()->route('website.index');
    }
}
