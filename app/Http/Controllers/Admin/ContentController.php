<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ContentImport;
use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ContentController

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = Content::with('type')->get();
        // return response()->json($contents);
        return view('admin.smscontent.index', compact('contents'))->with('no', 1);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $contenttypes = ContentType::all()->pluck('name', 'id');
        return view('admin.smscontent.create', compact('contenttypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $content = new Content;
        $content->message = $request->message;
        $content->eng_message = $request->eng_message;
        $content->content_type = $request->content_type;
        $content->user_id = Auth::id();
        $content->save();

        return redirect()->route('admin.contents.index')->with('success', 'sms content Saved Successful!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function show(Content $content)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
        $contenttypes = ContentType::all()->pluck('name', 'id');
        return view('admin.smscontent.edit', compact('content', 'contenttypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
        $content = Content::findOrFail($content->id);
        $content->message = $request->message;
        $content->eng_message = $request->eng_message;
        $content->content_type = $request->content_type;
        $content->user_id = Auth::id();
        $content->length = $request->priority;
        $content->update();

        return redirect()->route('admin.contents.index')->with('success', 'sms content Saved Successful!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Content  $content
     * @return \Illuminate\Http\Response
     */
    public function destroy(Content $content)
    {

        $content->delete();
        return redirect()->route('admin.contents.index')->with('success', 'sms content delete Successful!');
    }


    public function import()
    {
        Excel::import(new ContentImport, request()->file('file'));

        return back();
    }
}
