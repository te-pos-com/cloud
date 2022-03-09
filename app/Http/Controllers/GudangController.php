<?php

namespace App\Http\Controllers;

use App\Gudang;
use App\Cabang;
use Illuminate\Http\Request;
use Validator;
use DB;

class GudangController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $gudang = Gudang::orderBy("id", "desc")->get();
        return view('backend.accounting.general_settings.gudang.list', compact('gudang'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.gudang.create');
        } else {
            return view('backend.accounting.general_settings.gudang.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'gudang_name' => 'required|max:191',
            'cabang_id' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('gudang.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        DB::select("ALTER TABLE gudang AUTO_INCREMENT=0");
        $gudang = new Gudang();
        $gudang->gudang_name = $request->input('gudang_name');
        $gudang->cabang_id = $request->input('cabang_id');

        $gudang->save();

        if (!$request->ajax()) {
            return redirect()->route('gudang.index')->with('success', _lang('Saved sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved sucessfully'), 'data' => $gudang]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $gudang = Gudang::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.gudang.edit', compact('gudang', 'id'));
        } else {
            return view('backend.accounting.general_settings.gudang.modal.edit', compact('gudang', 'id'));
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'gudang_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('gudang.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $gudang = Gudang::find($id);
        $gudang->gudang_name  = $request->input('gudang_name');

        $gudang->save();

        if (!$request->ajax()) {
            return redirect()->route('gudang.index')->with('success', _lang('Updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated sucessfully'), 'data' => $gudang]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $gudang = Gudang::find($id);
        $gudang->delete();
        return redirect('gudang')->with('success', _lang('Updated sucessfully'));
    }
}
