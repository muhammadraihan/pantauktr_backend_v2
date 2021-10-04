<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\Http\Request;
use App\Traits\Authorizable;
use Auth;
use DataTables;
use DB;
use URL;

class StaticPageController extends Controller
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
            $static = StaticPage::select([DB::raw('@rownum  := @rownum  + 1 AS rownum'),
            'id','uuid','menu_name','url','created_by'])->get();
            return Datatables::of($static)
                ->addIndexColumn()
                ->editColumn('created_by', function ($row) {
                    return $row->users->name;
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a class="btn btn-success btn-sm btn-icon waves-effect waves-themed" href="' . route('static_page.edit', $row->uuid) . '"><i class="fal fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm btn-icon waves-effect waves-themed delete-btn" data-url="' . URL::route('static_page.destroy', $row->uuid) . '" data-id="' . $row->uuid . '" data-token="' . csrf_token() . '" data-toggle="modal" data-target="#modal-delete"><i class="fal fa-trash-alt"></i></a>';
                })
                ->removeColumn('id')
                ->removeColumn('uuid')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('static_pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('static_pages.create');
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
            'menu_name' => 'required',
            'url' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
        ];
        $this->validate($request, $rules, $messages);
        
        $static_page = new StaticPage();
        $static_page->menu_name = $request->menu_name;
        $static_page->url = $request->url;
        $static_page->created_by = Auth::user()->uuid;
        $static_page->save();

        toastr()->success('New Static Page Added', 'Success');
        return redirect()->route('static_page.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StaticPage  $staticPage
     * @return \Illuminate\Http\Response
     */
    public function show(StaticPage $staticPage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StaticPage  $staticPage
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        $static_page = StaticPage::uuid($uuid);
        return view('static_pages.edit',compact('static_page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StaticPage  $staticPage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        $rules = [
            'menu_name' => 'required',
            'url' => 'required'
        ];
        $messages = [
            '*.required' => 'Data Harus Di isi',
        ];
        $this->validate($request, $rules, $messages);
        
        $static_page = StaticPage::uuid($uuid);
        $static_page->menu_name = $request->menu_name;
        $static_page->url = $request->url;
        $static_page->edited_by = Auth::user()->uuid;
        $static_page->save();

        toastr()->success('Static Page Edited', 'Success');
        return redirect()->route('static_page.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StaticPage  $staticPage
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        $static_page = StaticPage::uuid($uuid);
        $static_page->delete();
        toastr()->success('Static Page Deleted', 'Success');
        return redirect()->route('static_page.index');
    }
}
