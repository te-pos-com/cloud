<?php

namespace App\Http\Controllers;

use App\Item;
use App\Product;
use App\ProductKategori;
use App\ProductMerek;
use App\Stock;
use Illuminate\Http\Request;
use Validator;
use DB;

class ProductController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $items = Item::where("item_type", "product")
            ->orderBy("id", "desc")->get();
        return view('backend.accounting.product.list', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return view('backend.accounting.product.create');
        } else {
            return view('backend.accounting.product.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    //public function cekbarcodedouble($barcode,){
        //$item = Item::where("barcode", $barcode)
        //        ->where("barcode", $barcode);      
        //return $item;
    //} 
     
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'item_name'     => 'required',
            'inisial'       => 'required',
            'barcode'       => 'required',
            'product_cost'  => 'required|numeric',
            'product_price' => 'required|numeric',
            'product_unit'  => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('products.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }


		DB::select("ALTER TABLE items AUTO_INCREMENT=0");
		DB::select("ALTER TABLE products AUTO_INCREMENT=0");
        //Create Item
        $item            = new Item();
        $item->item_name = $request->input('item_name');
        $item->item_type = 'product';
        $item->barcode       = $request->input('barcode');
        $item->inisial       = $request->input('inisial');
        $item->id_kategori   = $request->input('id_kategori');
        $item->id_merek      = $request->input('id_merek');
        $item->tipe          = $request->input('tipe');
        $item->minimal_stok  = $request->input('minimal_stok');
        $item->minimal_jual  = $request->input('minimal_jual');

        $company_id = company_id();

        if ($request->hasFile('gambar')) {
            $image           = $request->file('gambar');
            $name            = 'product_gambar' . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/product');
            $image->move($destinationPath, $name);
            $item->gambar = $name;
        }
        else{
            $item->gambar = "default.jpg";
        }
        


        
        $item->save();

        //Create Product
        $product                = new Product();
        $product->item_id       = $item->id;
        $product->supplier_id   = $request->input('supplier_id');
        $product->product_cost  = $request->input('product_cost');
        $product->product_price = $request->input('product_price');
        $product->product_unit  = $request->input('product_unit');
        $product->description   = $request->input('description');
        $product->save();

        //Create Stock Row
        $stock             = new Stock();
        $stock->product_id = $item->id;
        $stock->quantity   = 0;
        $stock->save();

        if (!$request->ajax()) {
            return redirect()->route('products.create')->with('success', _lang('Information has been added sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Information has been added sucessfully'), 'data' => $item]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $item = Item::find($id);
        
        if (!$request->ajax()) {
            return view('backend.accounting.product.view', compact('item', 'id'));
        } else {
            return view('backend.accounting.product.modal.view', compact('item', 'id'));
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $item = Item::find($id);

        if (!$request->ajax()) {
            return view('backend.accounting.product.edit', compact('item', 'id'));
        } else {
            return view('backend.accounting.product.modal.edit', compact('item', 'id'));
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
            'item_name'     => 'required',
            'product_cost'  => 'required|numeric',
            'product_price' => 'required|numeric',
            'product_unit'  => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('products.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        //Update item
        $item = Item::find($id);

        if ($item) {

            $item->item_name = $request->input('item_name');
            $item->item_type = 'product';
            $item->barcode       = $request->input('barcode');
            $item->inisial       = $request->input('inisial');
            $item->id_kategori   = $request->input('id_kategori');
            $item->id_merek      = $request->input('id_merek');
            $item->tipe          = $request->input('tipe');
            $item->minimal_stok  = $request->input('minimal_stok');
            $item->minimal_jual  = $request->input('minimal_jual');

            $company_id = company_id();
    
            if ($request->hasFile('gambar')) {
                $image           = $request->file('gambar');
                $name            = 'product_gambar' . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/product');
                $image->move($destinationPath, $name);
                $item->gambar = $name;
            }
            else{
                $item->gambar = $request->input('gambar_old');;
            }


            $item->save();


            $product                = Product::where("item_id", $id)->first();
            $product->item_id       = $item->id;
            $product->supplier_id   = $request->input('supplier_id');
            $product->product_cost  = $request->input('product_cost');
            $product->product_price = $request->input('product_price');
            $product->product_unit  = $request->input('product_unit');
            $product->description   = $request->input('description');

            $product->save();
        } else {
            if (!$request->ajax()) {
                return redirect()->route('products.index')->with('error', _lang('Update Failed !'));
            } else {
                return response()->json(['result' => 'error', 'message' => _lang('Update Failed !')]);
            }
        }

        if (!$request->ajax()) {
            return redirect()->route('products.index')->with('success', _lang('Information has been updated sucessfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Information has been updated sucessfully'), 'data' => $product]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $item = Item::find($id);
        $item->delete();

        $product = Product::where("item_id", $id);
        $product->delete();
        return back()->with('success', _lang('Information has been deleted sucessfully'));
    }

    public function get_product(Request $request, $id) {
        $item = Item::find($id);
   

        if ($item->item_type == 'product') {
            echo json_encode(array("item" => $item, "product" => $item->product, "tax" => $item->product->tax, "available_quantity" => $item->product_stock->quantity));
        } else if ($item->item_type == 'service') {
            echo json_encode(array("item" => $item, "product" => $item->service, "tax" => $item->service->tax));
        }
    }

}