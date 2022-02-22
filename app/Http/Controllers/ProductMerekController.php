<?php

namespace App\Http\Controllers;

use App\ProductMerek;
use Illuminate\Http\Request;
use Validator;

class ProductMerekController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $productmerek = ProductMerek::orderBy("id", "desc")->get();
        return view('backend.accounting.general_settings.product_merek.list', compact('productmerek'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.product_merek.create');
        } else {
            return view('backend.accounting.general_settings.product_merek.modal.create');
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
            'merek_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('roduct_merek.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $productmerek            = new ProductMerek();
        $productmerek->merek_name = $request->input('merek_name');

        $productmerek->save();

        if (!$request->ajax()) {
            return redirect()->route('roduct_merek.index')->with('success', _lang('Saved sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved sucessfully'), 'data' => $productmerek]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $productmerek = ProductMerek::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.product_merek.edit', compact('productmerek', 'id'));
        } else {
            return view('backend.accounting.general_settings.product_merek.modal.edit', compact('productmerek', 'id'));
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
            'merek_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('product_merek.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $productmerek             = ProductMerek::find($id);
        $productmerek->merek_name  = $request->input('merek_name');

        $productmerek->save();

        if (!$request->ajax()) {
            return redirect()->route('roduct_merek.index')->with('success', _lang('Updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated sucessfully'), 'data' => $productmerek]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $productmerek = ProductMerek::find($id);
        if (!empty($productmerek->item->item_name)){
            return redirect('product_merek')->with('error', _lang('Data sudah digunakan!'));
        }
        else{
            $productmerek->delete();
            return redirect('product_merek')->with('success', _lang('Berhsil Dihapus'));
        }
    }
}
