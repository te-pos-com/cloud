<?php

namespace App\Http\Controllers;

use App\ProductKategori;
use Illuminate\Http\Request;
use Validator;

class ProductKategoriController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $productkategori = ProductKategori::orderBy("id", "desc")->get();
        return view('backend.accounting.general_settings.product_kategori.list', compact('productkategori'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.product_kategori.create');
        } else {
            return view('backend.accounting.general_settings.product_kategori.modal.create');
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
            'kategori_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('roduct_kategori.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $productkategori            = new ProductKategori();
        $productkategori->kategori_name = $request->input('kategori_name');

        $productkategori->save();

        if (!$request->ajax()) {
            return redirect()->route('product_kategori.index')->with('success', _lang('Saved sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved sucessfully'), 'data' => $productkategori]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $productkategori = ProductKategori::find($id);
        if (!$request->ajax()) {
            return view('backend.accounting.general_settings.product_kategori.edit', compact('productkategori', 'id'));
        } else {
            return view('backend.accounting.general_settings.product_kategori.modal.edit', compact('productkategori', 'id'));
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
            'kategori_name' => 'required|max:191',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('product_kategori.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $productkategori             = ProductKategori::find($id);
        $productkategori->kategori_name  = $request->input('kategori_name');

        $productkategori->save();

        if (!$request->ajax()) {
            return redirect()->route('roduct_kategori.index')->with('success', _lang('Updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated sucessfully'), 'data' => $productkategori]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $productkategori = ProductKategori::find($id);
        if (!empty($productkategori->item->item_name)){
            return redirect('product_kategori')->with('error', _lang('Data sudah digunakan!'));
        }
        else{
            $productkategori->delete();
            return redirect('product_kategori')->with('success', _lang('Berhsil Dihapus'));
        }        
    }
}
