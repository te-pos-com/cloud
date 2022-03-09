<?php

namespace App\Http\Controllers;

use App\Cabang;
use App\Gudang;
use Illuminate\Http\Request;
use Validator;
use DB;

class CabangController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $cabang = Cabang::orderBy("id", "desc")->get();
        return view('backend.accounting.general_settings.cabang.list', compact('cabang'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
         
        $count_cabang = Cabang::selectRaw("COUNT(id) as total")->first()->total;
        if (cabang()<=$count_cabang){
            return view('backend.accounting.general_settings.cabang.modal.limit');
        }
        else{
            if (!$request->ajax()) {
                return view('backend.accounting.general_settings.cabang.create');
            } else {
                return view('backend.accounting.general_settings.cabang.modal.create');
            }
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
            'cabang_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('cabang.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        DB::select("ALTER TABLE cabang AUTO_INCREMENT=0");
        $cabang = new Cabang();
        $cabang->cabang_name = $request->input('cabang_name');
        $cabang->cabang_phone = $request->input('cabang_phone');
        $cabang->cabang_email = $request->input('cabang_email');
        $cabang->cabang_alamat = $request->input('cabang_alamat');

        $cabang->save();

        if (!$request->ajax()) {
            return redirect()->route('cabang.index')->with('success', _lang('Saved sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved sucessfully'), 'data' => $cabang]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $cabang = Cabang::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.cabang.edit', compact('cabang', 'id'));
        } else {
            return view('backend.accounting.general_settings.cabang.modal.edit', compact('cabang', 'id'));
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
            'cabang_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('cabang.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $cabang= Cabang::find($id);
        $cabang->cabang_name  = $request->input('cabang_name');
        $cabang->cabang_email  = $request->input('cabang_email');
        $cabang->cabang_phone  = $request->input('cabang_phone');
        $cabang->cabang_alamat  = $request->input('cabang_alamat');

        $cabang->save();

        if (!$request->ajax()) {
            return redirect()->route('cabang.index')->with('success', _lang('Updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated sucessfully'), 'data' => $cabang]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $cabang = Cabang::find($id);
        if (!empty($cabang->gudang->gudang_name)){
            return redirect('cabang')->with('error', _lang('Data sudah digunakan!'));
        } 
        else{
            $cabang->delete();
            return redirect('cabang')->with('success', _lang('Data Berhasil Dihapus'));
        }
    }
}
