<?php

namespace App\Http\Controllers;

use App\Models\DynamicMenu;
use Illuminate\Http\Request;
use App\Traits\Authorizable;

use Auth;
use DataTables;
use DB;
use URL;
use Image;
use Validator;
use Storage;

class DynamicMenuController extends Controller
{
    // use Authorizable;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            DB::statement(DB::raw('set @rownum=0'));
            $dynamic_menu = DynamicMenu::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','icon','judul','status','created_by'])->get();
            return Datatables::of($dynamic_menu)
                ->addIndexColumn()
                ->editColumn('icon', function ($row) {
                    return $row->icon ? '<img style="width: 150px; height: 150px;"  src="' . $row->icon . '" alt="">' : '<span class="badge badge-secondary badge-pill">Foto tidak terlampir</span>';
                })
                ->editColumn('status',function($row){
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
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('dynamic_menu.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('dynamic_menu.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action','icon'])
                ->make(true);
        }

        return view('dynamic_menus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dynamic_menus.create');
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
            'icon' => 'required|mimes:svg,png|max:1024',
            'status' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
            '*.mimes' => 'Type File Harus svg atau png',
            '*.max' => 'Size File Tidak Boleh Lebih Dari 1Mb'
        ];
        $this->validate($request, $rules, $messages);
        $image = $request->file('icon');
        // dd($image->getClientMimeType(),$image->getClientOriginalExtension());
        $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
        // upload image to gcs
        $googleContent = 'menu' . '/' . $filename;
        $disk = Storage::disk('gcs');
        $disk->put($googleContent, (string) $image);
        $fileUrl = $disk->url(env('GOOGLE_CLOUD_STORAGE_BUCKET') . '/' . $googleContent);

        $dynamic_menu = new DynamicMenu();
        $dynamic_menu->icon = $fileUrl;
        $dynamic_menu->judul = $request->judul;
        $dynamic_menu->status = $request->status;
        $dynamic_menu->created_by = Auth::user()->uuid;
        $dynamic_menu->save();

        toastr()->success('New Menu Added', 'Success');
        return redirect()->route('dynamic_menu.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dynamic_Menu  $dynamic_Menu
     * @return \Illuminate\Http\Response
     */
    public function show(Dynamic_Menu $dynamic_Menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dynamic_Menu  $dynamic_Menu
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $dynamic_menu = DynamicMenu::uuid($uuid);
        return view('dynamic_menus.edit',compact('dynamic_menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dynamic_Menu  $dynamic_Menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $dynamic_menu = DynamicMenu::uuid($uuid);
        if ($request->hasfile('icon')) {
            $rules = [
                'icon' => 'required|mimes:svg,png|max:1024',
                'status' => 'required'
            ];
            $messages = [
                '*.required' => 'Data Harus Di isi',
                '*.mimes' => 'Type File Harus svg atau png',
                '*.max' => 'Size File Tidak Boleh Lebih Dari 1Mb'
            ];
            $this->validate($request, $rules, $messages);
            $image = $request->file('icon');
            $filename = md5(uniqid(mt_rand(), true)) . '.' . $image->getClientOriginalExtension();
            // upload image to gcs
            $googleContent = 'menu' . '/' . $filename;
            $disk = Storage::disk('gcs');
            $disk->put($googleContent, (string) $image);
            $fileUrl = $disk->url(env('GOOGLE_CLOUD_STORAGE_BUCKET') . '/' . $googleContent);
            $dynamic_menu->icon = $fileUrl;
        }
        $dynamic_menu->judul = $request->judul;
        $dynamic_menu->status = $request->status;
        $dynamic_menu->edited_by = Auth::user()->uuid;
        $dynamic_menu->save();
        toastr()->success('Menu Edited', 'Success');
        return redirect()->route('dynamic_menu.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dynamic_Menu  $dynamic_Menu
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $dynamic_menu = DynamicMenu::uuid($uuid);
        $dynamic_menu->delete();
        toastr()->success('Menu Deleted', 'Success');
        return redirect()->route('dynamic_menu.index');
    }
}
