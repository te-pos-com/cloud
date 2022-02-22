<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function account_statement(Request $request, $view = "") {

        if ($view == "") {
            return view('reports.account_statement');
        } else if ($view == "view") {
            $data       = array();
            $dr_cr      = $request->trans_type;
            $date1      = $request->date1;
            $date2      = $request->date2;
            $account    = $request->account;
            $company_id = company_id();

            if ($dr_cr == "dr") {
                $data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,'' as debit,opening_balance as credit FROM accounts WHERE id='$account' AND company_id = '$company_id'
			 	UNION ALL
			 	SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as debit FROM transactions WHERE dr_cr='dr' AND trans_date<'$date1' AND account_id='$account' AND company_id = '$company_id') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND trans_date < '$date1' AND account_id='$account' AND company_id = '$company_id') as credit
			 	UNION ALL
			 	SELECT trans_date,note, SUM(IF(dr_cr='dr',amount,NULL)) as debit, SUM(IF(dr_cr='cr',amount,NULL)) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account'  AND company_id = '$company_id' AND dr_cr='dr' GROUP BY(trans_date)");

            } else if ($dr_cr == "cr") {
                $data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,'' as debit,opening_balance as credit FROM accounts WHERE id='$account' AND company_id = '$company_id'
				UNION ALL
				SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as debit FROM transactions WHERE dr_cr='dr' AND trans_date < '$date1' AND account_id='$account' AND company_id = '$company_id') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND trans_date < '$date1' AND account_id='$account' AND company_id = '$company_id') as credit
				UNION ALL
				SELECT trans_date,note,SUM(IF(dr_cr='dr',amount,NULL)) as debit, SUM(IF(dr_cr='cr',amount,NULL)) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account' AND company_id = '$company_id' AND dr_cr='cr'  GROUP BY(trans_date)");

            } else if ($dr_cr == "all") {
                $data['report_data'] = DB::select("SELECT opening_date as date,'Account Opening Balance' as note,0 as debit,opening_balance as credit FROM accounts WHERE id='$account' AND company_id = '$company_id'
				UNION ALL
				SELECT '$date1' as date,'Opening Balance' as note,(SELECT IFNULL(SUM(amount),0) as debit FROM transactions WHERE dr_cr='dr' AND trans_date<'$date1' AND account_id='$account' AND company_id = '$company_id') as debit, (SELECT IFNULL(SUM(amount),0) as credit FROM transactions WHERE dr_cr='cr' AND trans_date < '$date1' AND account_id='$account' AND company_id = '$company_id') as credit
				UNION ALL
				SELECT trans_date,note,SUM(IF(dr_cr='dr',amount,NULL)) as debit,SUM(IF(dr_cr='cr',amount,NULL)) as credit FROM transactions WHERE trans_date BETWEEN '$date1' AND '$date2' AND account_id='$account' AND company_id = '$company_id' GROUP BY(trans_date)");
            }

            $data['dr_cr']   = $request->trans_type;
            $data['date1']   = $request->date1;
            $data['date2']   = $request->date2;
            $data['account'] = $request->account;

            return view('reports.account_statement', $data);
        }
    }


    public function orderpembelian_report(Request $request, $view = "") {
        return view('reports.orderpembelian_report');
    }
    public function orderpembelian_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_supplier = json_decode($request->get('supplier'));
        $str_supplier = "";
        foreach ($order_supplier as $supplier){
            if ($str_supplier == ""){
                $str_supplier = $supplier;
            }
            else{
                $str_supplier = $str_supplier .','. $supplier;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        $order_status = json_decode($request->get('order_status'));
        $str_order = "";
        foreach ($order_status as $order){
            if ($str_order == ""){
                $str_order = $order;
            }
            else{
                $str_order = $str_order .','. $order;   
            }
        }
        
        if ($str_supplier !=""){
            $supplier_query = 'AND purchase_orders.supplier_id IN (' . $str_supplier .')' ;
        }
        else{
            $supplier_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND purchase_orders.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        if ($str_order!=""){
            $order_status_query = 'AND purchase_orders.order_status IN (' . $str_order .')';
        }
        else{
            $order_status_query = '';
        }

        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,purchase_orders.id,purchase_order_items.quantity,items.item_name, suppliers.supplier_name,
        purchase_orders.order_status,purchase_orders.invoice_number,purchase_order_items.unit_cost,purchase_order_items.sub_total,purchase_orders.order_date 
        from purchase_orders INNER JOIN suppliers ON suppliers.id = purchase_orders.supplier_id and suppliers.company_id = purchase_orders.company_id 
        INNER JOIN purchase_order_items ON purchase_orders.id=purchase_order_items.purchase_order_id and purchase_orders.company_id=purchase_order_items.company_id 
        INNER JOIN items ON items.id = purchase_order_items.product_id and items.company_id = purchase_order_items.company_id 
        INNER JOIN gudang ON gudang.id = purchase_order_items.gudang_id and gudang.company_id = purchase_order_items.company_id
        where
        purchase_orders.order_date BETWEEN '$date1' AND '$date2' AND purchase_orders.company_id='$company_id' $supplier_query $cabang_query $order_status_query
        order by purchase_orders.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.orderpembelian_isi', $data);
    }


    public function pembelian_report(Request $request, $view = "") {
        return view('reports.pembelian_report');
    }
    public function pembelian_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_supplier = json_decode($request->get('supplier'));
        $str_supplier = "";
        foreach ($order_supplier as $supplier){
            if ($str_supplier == ""){
                $str_supplier = $supplier;
            }
            else{
                $str_supplier = $str_supplier .','. $supplier;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        $order_status = json_decode($request->get('order_status'));
        $str_order = "";
        foreach ($order_status as $order){
            if ($str_order == ""){
                $str_order = $order;
            }
            else{
                $str_order = $str_order .','. $order;   
            }
        }
        
        if ($str_supplier !=""){
            $supplier_query = 'AND pembelian.supplier_id IN (' . $str_supplier .')' ;
        }
        else{
            $supplier_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND pembelian.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        if ($str_order!=""){
            $order_status_query = 'AND pembelian.order_status IN (' . $str_order .')';
        }
        else{
            $order_status_query = '';
        }

        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,pembelian.id,pembelian_items.quantity,items.item_name, suppliers.supplier_name,
        pembelian.order_status,pembelian.invoice_number,pembelian_items.unit_cost,pembelian_items.sub_total,pembelian.order_date 
        from pembelian INNER JOIN suppliers ON suppliers.id = pembelian.supplier_id and suppliers.company_id = pembelian.company_id 
        INNER JOIN pembelian_items ON pembelian.id=pembelian_items.pembelian_id and pembelian.company_id=pembelian_items.company_id 
        INNER JOIN items ON items.id = pembelian_items.product_id and items.company_id = pembelian_items.company_id 
        INNER JOIN gudang ON gudang.id = pembelian_items.gudang_id and gudang.company_id = pembelian_items.company_id
        where
        pembelian.order_date BETWEEN '$date1' AND '$date2' AND pembelian.company_id='$company_id' $supplier_query $cabang_query $order_status_query
        order by pembelian.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.pembelian_isi', $data);
    }
    

    public function returpembelian_report(Request $request, $view = "") {
        return view('reports.returpembelian_report');
    }
    public function returpembelian_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_supplier = json_decode($request->get('supplier'));
        $str_supplier = "";
        foreach ($order_supplier as $supplier){
            if ($str_supplier == ""){
                $str_supplier = $supplier;
            }
            else{
                $str_supplier = $str_supplier .','. $supplier;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        
        if ($str_supplier !=""){
            $supplier_query = 'AND purchase_return.supplier_id IN (' . $str_supplier .')' ;
        }
        else{
            $supplier_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND purchase_return.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,purchase_return.id,purchase_return_items.quantity,items.item_name, suppliers.supplier_name,
        purchase_return.invoice_number,purchase_return_items.unit_cost,purchase_return_items.sub_total,purchase_return.return_date 
        from purchase_return INNER JOIN suppliers ON suppliers.id = purchase_return.supplier_id and suppliers.company_id = purchase_return.company_id 
        INNER JOIN purchase_return_items ON purchase_return.id=purchase_return_items.purchase_return_id and purchase_return.company_id=purchase_return_items.company_id 
        INNER JOIN items ON items.id = purchase_return_items.product_id and items.company_id = purchase_return_items.company_id 
        INNER JOIN gudang ON gudang.id = purchase_return_items.gudang_id and gudang.company_id = purchase_return_items.company_id
        where
        purchase_return.return_date BETWEEN '$date1' AND '$date2' AND purchase_return.company_id='$company_id' $supplier_query $cabang_query 
        order by purchase_return.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.returpembelian_isi', $data);
    }

    
    public function persediaan_barang_report(Request $request, $view = "") {
        return view('reports.persediaan_barang_report');
    }
    public function persediaan_barang_isi(Request $request, $view = "") {
        $company_id = company_id();
        
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND pembelian.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        
        $order_gudang = json_decode($request->get('gudang'));
        $str_gudang = "";
        foreach ($order_gudang as $gudang){
            if ($str_gudang == ""){
                $str_gudang = $gudang;
            }
            else{
                $str_gudang = $str_gudang .','. $gudang;   
            }
        }
        if ($str_gudang!=""){
            $gudang_query  = 'AND hpp.gudang_id IN (' .  $str_gudang .')';
        }
        else{
            $gudang_query  ='';
        }
        
        
        $order_produk = json_decode($request->get('produk'));
        $str_produk = "";
        foreach ($order_produk as $produk){
            if ($str_produk == ""){
                $str_produk = $produk;
            }
            else{
                $str_produk = $str_produk .','. $produk;   
            }
        }
        if ($str_produk!=""){
            $produk_query  = 'AND hpp.item_id IN (' .  $str_produk .')';
        }
        else{
            $produk_query  ='';
        }
        
        
        $order_merek = json_decode($request->get('merek'));
        $str_merek = "";
        foreach ($order_merek as $merek){
            if ($str_merek == ""){
                $str_merek = $merek;
            }
            else{
                $str_merek = $str_merek .','. $merek;   
            }
        }
        if ($str_merek!=""){
            $merek_query  = 'AND product_merek.id IN (' .  $str_merek .')';
        }
        else{
            $merek_query  ='';
        }
        
        
        $order_kategori = json_decode($request->get('kategori'));
        $str_kategori = "";
        foreach ($order_kategori as $kategori){
            if ($str_kategori == ""){
                $str_kategori = $kategori;
            }
            else{
                $str_kategori = $str_kategori .','. $kategori;   
            }
        }
        if ($str_kategori!=""){
            $kategori_query  = 'AND product_kategori.id IN (' .  $str_kategori .')';
        }
        else{
            $kategori_query  ='';
        }
        
        
        
        $data = array();

        $data['report_data'] = DB::select("SELECT items.item_name,sum(case when hpp.flag<=5 then stok else -stok end) as stok,sum(case when hpp.flag<=5 then harga*stok else 0 end)/sum(case when hpp.flag<=5 then stok else 0 end) as harga
        from hpp INNER JOIN items ON items.id = hpp.item_id AND items.company_id = hpp.company_id 
        INNER JOIN product_kategori ON product_kategori.id = items.id_kategori AND product_kategori.company_id AND items.company_id
        INNER JOIN product_merek ON product_merek.id = items.id_merek AND product_merek.company_id AND items.company_id
        WHERE hpp.company_id='$company_id' $cabang_query $gudang_query $produk_query $merek_query $kategori_query
        group by items.item_name
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.persediaan_barang_isi', $data);
    }



    public function mutasi_stok_report(Request $request, $view = "") {
        return view('reports.mutasi_stok_report');
    }
    public function mutasi_stok_isi(Request $request, $view = "") {
        $company_id = company_id();
        
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND pembelian.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        
        $order_gudang = json_decode($request->get('gudang'));
        $str_gudang = "";
        foreach ($order_gudang as $gudang){
            if ($str_gudang == ""){
                $str_gudang = $gudang;
            }
            else{
                $str_gudang = $str_gudang .','. $gudang;   
            }
        }
        if ($str_gudang!=""){
            $gudang_query  = 'AND hpp.gudang_id IN (' .  $str_gudang .')';
        }
        else{
            $gudang_query  ='';
        }
        
        
        $order_produk = json_decode($request->get('produk'));
        $str_produk = "";
        foreach ($order_produk as $produk){
            if ($str_produk == ""){
                $str_produk = $produk;
            }
            else{
                $str_produk = $str_produk .','. $produk;   
            }
        }
        if ($str_produk!=""){
            $produk_query  = 'AND hpp.item_id IN (' .  $str_produk .')';
        }
        else{
            $produk_query  ='';
        }
        
        
        $order_merek = json_decode($request->get('merek'));
        $str_merek = "";
        foreach ($order_merek as $merek){
            if ($str_merek == ""){
                $str_merek = $merek;
            }
            else{
                $str_merek = $str_merek .','. $merek;   
            }
        }
        if ($str_merek!=""){
            $merek_query  = 'AND product_merek.id IN (' .  $str_merek .')';
        }
        else{
            $merek_query  ='';
        }
        
        
        $order_kategori = json_decode($request->get('kategori'));
        $str_kategori = "";
        foreach ($order_kategori as $kategori){
            if ($str_kategori == ""){
                $str_kategori = $kategori;
            }
            else{
                $str_kategori = $str_kategori .','. $kategori;   
            }
        }
        if ($str_kategori!=""){
            $kategori_query  = 'AND product_kategori.id IN (' .  $str_kategori .')';
        }
        else{
            $kategori_query  ='';
        }
        
        
        
        $data = array();

        $data['report_data'] = DB::select("SELECT items.item_name,
        sum(case when hpp.flag=1 then stok else 0 end) as pembelian,
        sum(case when hpp.flag=7 then stok else 0 end) as returpembelian,
        sum(case when hpp.flag=6 then stok else 0 end) as penjualan,
        sum(case when hpp.flag=2 then stok else 0 end) as returpenjualan
        from hpp INNER JOIN items ON items.id = hpp.item_id AND items.company_id = hpp.company_id 
        INNER JOIN product_kategori ON product_kategori.id = items.id_kategori AND product_kategori.company_id AND items.company_id
        INNER JOIN product_merek ON product_merek.id = items.id_merek AND product_merek.company_id AND items.company_id
        WHERE hpp.company_id='$company_id' $cabang_query $gudang_query $produk_query $merek_query $kategori_query
        group by items.item_name
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.mutasi_stok_isi', $data);
    }


    public function quotation_report(Request $request, $view = "") {
        return view('reports.quotation_report');
    }
    public function quotation_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_customer = json_decode($request->get('customer'));
        $str_customer = "";
        foreach ($order_customer as $customer){
            if ($str_customer == ""){
                $str_customer = $customer;
            }
            else{
                $str_customer = $str_customer .','. $customer;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        $order_status = json_decode($request->get('order_status'));
        $str_order = "";
        foreach ($order_status as $order){
            if ($str_order == ""){
                $str_order = $order;
            }
            else{
                $str_order = $str_order .','. $order;   
            }
        }
        
        if ($str_customer !=""){
            $customer_query = 'AND quotations.client_id IN (' . $str_customer .')' ;
        }
        else{
            $customer_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND quotations.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        if ($str_order!=""){
            $order_status_query = 'AND quotations.status IN (' . $str_order .')';
        }
        else{
            $order_status_query = '';
        }

        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,quotations.id,quotation_items.quantity,items.item_name, contacts.contact_name,
        quotations.status,quotations.quotation_number,quotation_items.unit_cost,quotation_items.sub_total,quotations.quotation_date 
        from quotations 
        INNER JOIN contacts ON contacts.id = quotations.client_id and contacts.company_id = quotations.company_id 
        INNER JOIN quotation_items ON quotations.id=quotation_items.quotation_id and quotations.company_id=quotation_items.company_id 
        INNER JOIN items ON items.id = quotation_items.item_id and items.company_id = quotation_items.company_id 
        INNER JOIN gudang ON gudang.id = quotation_items.gudang_id and gudang.company_id = quotation_items.company_id
        where
        quotations.quotation_date BETWEEN '$date1' AND '$date2' AND quotations.company_id='$company_id' $customer_query $cabang_query $order_status_query
        order by quotations.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        $data['supplier'] = $request->supplier;
        $data['order_status'] = $request->order_status;
        return view('reports.isi.quotation_isi', $data);
    }


    public function penjualan_report(Request $request, $view = "") {
        return view('reports.penjualan_report');
    }
    public function penjualan_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_customer = json_decode($request->get('customer'));
        $str_customer = "";
        foreach ($order_customer as $customer){
            if ($str_customer == ""){
                $str_customer = $customer;
            }
            else{
                $str_customer = $str_customer .','. $customer;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        $order_status = json_decode($request->get('order_status'));
        $str_order = "";
        foreach ($order_status as $order){
            if ($str_order == ""){
                $str_order = "'".$order."'";
            }
            else{
                $str_order = $str_order .','. "'".$order."'";   
            }
        }
        
        if ($str_customer !=""){
            $customer_query = 'AND invoices.client_id IN (' . $str_customer .')' ;
        }
        else{
            $customer_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND invoices.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        if ($str_order!=""){
            $order_status_query = 'AND invoices.status IN (' . $str_order .')';
        }
        else{
            $order_status_query = '';
        }

        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,invoices.id,invoice_items.quantity,items.item_name, contacts.contact_name,
        invoices.status,invoices.invoice_number,invoice_items.unit_cost,invoice_items.sub_total,invoices.invoice_date 
        from invoices 
        INNER JOIN contacts ON contacts.id = invoices.client_id and contacts.company_id = invoices.company_id 
        INNER JOIN invoice_items ON invoices.id=invoice_items.invoice_id and invoices.company_id=invoice_items.company_id 
        INNER JOIN items ON items.id = invoice_items.item_id and items.company_id = invoice_items.company_id 
        INNER JOIN gudang ON gudang.id = invoice_items.gudang_id and gudang.company_id = invoice_items.company_id
        where
        invoices.invoice_date BETWEEN '$date1' AND '$date2' AND invoices.company_id='$company_id' $customer_query $cabang_query $order_status_query
        order by invoices.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        return view('reports.isi.penjualan_isi', $data);
    }
    
    public function returpenjualan_report(Request $request, $view = "") {
        return view('reports.returpenjualan_report');
    }
    public function returpenjualan_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        
        $order_customer = json_decode($request->get('customer'));
        $str_customer = "";
        foreach ($order_customer as $customer){
            if ($str_customer == ""){
                $str_customer = $customer;
            }
            else{
                $str_customer = $str_customer .','. $customer;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }
        
        if ($str_customer !=""){
            $customer_query = 'AND sales_return.customer_id IN (' . $str_customer .')' ;
        }
        else{
            $customer_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND sales_return.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        
        
        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,sales_return.id,sales_return_items.quantity,items.item_name, contacts.contact_name,
        sales_return.invoice_number,sales_return_items.unit_cost,sales_return_items.sub_total,sales_return.return_date 
        from sales_return 
        INNER JOIN contacts ON contacts.id = sales_return.customer_id and contacts.company_id = sales_return.company_id 
        INNER JOIN sales_return_items ON sales_return.id=sales_return_items.sales_return_id and sales_return.company_id=sales_return_items.company_id 
        INNER JOIN items ON items.id = sales_return_items.product_id and items.company_id = sales_return_items.company_id 
        INNER JOIN gudang ON gudang.id = sales_return_items.gudang_id and gudang.company_id = sales_return_items.company_id
        where
        sales_return.return_date BETWEEN '$date1' AND '$date2' AND sales_return.company_id='$company_id' $customer_query $cabang_query
        order by sales_return.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        return view('reports.isi.returpenjualan_isi', $data);
    }


    public function laba_rugi_report(Request $request, $view = "") {
        return view('reports.laba_rugi_report');
    }
    public function laba_rugi_isi(Request $request, $view = "") {
        $date1      = $request->date1;
        $date2      = $request->date2;
        $company_id = company_id();
        $order_customer = json_decode($request->get('customer'));
        $str_customer = "";
        foreach ($order_customer as $customer){
            if ($str_customer == ""){
                $str_customer = $customer;
            }
            else{
                $str_customer = $str_customer .','. $customer;   
            }
        }
        $order_cabang = json_decode($request->get('cabang'));
        $str_cabang = "";
        foreach ($order_cabang as $cabang){
            if ($str_cabang == ""){
                $str_cabang = $cabang;
            }
            else{
                $str_cabang = $str_cabang .','. $cabang;   
            }
        }

        if ($str_customer !=""){
            $customer_query = 'AND invoices.client_id IN (' . $str_customer .')' ;
        }
        else{
            $customer_query = '';
        }
        if ($str_cabang!=""){
            $cabang_query  = 'AND invoices.cabang_id IN (' .  $str_cabang .')';
        }
        else{
            $cabang_query  ='';
        }
        

        $data = array();

        $data['report_data'] = DB::select("SELECT gudang.gudang_name,invoices.id,invoice_items.hpp,invoice_items.quantity,items.item_name, contacts.contact_name,
        invoices.status,invoices.invoice_number,invoice_items.unit_cost,invoice_items.sub_total,invoices.invoice_date 
        from invoices 
        INNER JOIN contacts ON contacts.id = invoices.client_id and contacts.company_id = invoices.company_id 
        INNER JOIN invoice_items ON invoices.id=invoice_items.invoice_id and invoices.company_id=invoice_items.company_id 
        INNER JOIN items ON items.id = invoice_items.item_id and items.company_id = invoice_items.company_id 
        INNER JOIN gudang ON gudang.id = invoice_items.gudang_id and gudang.company_id = invoice_items.company_id
        where
        invoices.invoice_date BETWEEN '$date1' AND '$date2' AND invoices.company_id='$company_id' $customer_query $cabang_query 
        order by invoices.id asc
        ");

        $data['date1']    = $request->date1;
        $data['date2']    = $request->date2;
        $data['cabang']  = $request->cabang;
        return view('reports.isi.laba_rugi_isi', $data);
    }



    public function income_report(Request $request, $view = "") {
        if ($view == "") {
            return view('reports.income_report');
        } else {
            $date1      = $request->date1;
            $date2      = $request->date2;
            $account    = $request->account;
            $customer   = $request->customer;
            $category   = $request->category;
            $company_id = company_id();

            $customer_query = $customer != '' ? 'AND transactions.payer_payee_id = ' . $customer : '';
            $account_query  = $account != '' ? 'AND transactions.account_id = ' . $account : '';
            $category_query = $category != '' ? 'AND transactions.chart_id = ' . $category : '';

            $data = array();

            $data['report_data'] = DB::select("SELECT transactions.trans_date,chart_of_accounts.name as income_type,transactions.note,accounts.account_title as account,SUM(transactions.amount) as amount
			FROM transactions JOIN accounts ON transactions.account_id = accounts.id LEFT JOIN chart_of_accounts ON transactions.chart_id=chart_of_accounts.id
			WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' $category_query $account_query $customer_query AND transactions.company_id='$company_id' 
            GROUP BY transactions.chart_id
			UNION ALL
			SELECT '$date2','Total Amount','','',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id = accounts.id AND transactions.trans_date
			BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' $category_query $account_query $customer_query AND transactions.company_id='$company_id'");

            $data['date1']    = $request->date1;
            $data['date2']    = $request->date2;
            $data['account']  = $request->account;
            $data['customer'] = $request->customer;
            $data['category'] = $request->category;
            return view('reports.income_report', $data);
        }

    }

    //Expense Report
    public function expense_report(Request $request, $view = "") {
        if ($view == "") {
            return view('reports.expense_report');
        } else {
            $date1      = $request->date1;
            $date2      = $request->date2;
            $account    = $request->account;
            $category   = $request->category;
            $company_id = company_id();

            $account_query  = $account != '' ? 'AND transactions.account_id = ' . $account : '';
            $category_query = $category != '' ? 'AND transactions.chart_id = ' . $category : '';

            $data       = array();

            $data['report_data'] = DB::select("SELECT transactions.trans_date,chart_of_accounts.name as expense_type,transactions.note,accounts.account_title as account,sum(transactions.amount) as amount
			FROM transactions JOIN accounts ON transactions.account_id = accounts.id LEFT JOIN chart_of_accounts ON transactions.chart_id=chart_of_accounts.id
			WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='dr' $account_query $category_query AND transactions.company_id='$company_id' GROUP BY transactions.chart_id
            UNION ALL
			SELECT '$date2','Total Amount','','',SUM(transactions.amount) as amount FROM transactions,accounts WHERE transactions.account_id = accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='dr' AND transactions.company_id='$company_id'");

            $data['date1'] = $request->date1;
            $data['date2'] = $request->date2;
            $data['account']  = $request->account;
            $data['category'] = $request->category;
            return view('reports.expense_report', $data);
        }

    }

    public function transfer_report(Request $request, $view = "") {
        if ($view == "") {
            return view('reports.transfer_report');
        } else {
            $date1               = $request->date1;
            $date2               = $request->date2;
            $company_id          = company_id();
            $data                = array();
            $data['report_data'] = DB::select("SELECT transactions.trans_date,transactions.note,accounts.account_title as account,dr_cr,
		   IF(transactions.dr_cr='dr',transactions.amount,NULL) as debit,IF(transactions.dr_cr='cr',transactions.amount,NULL) as credit
		   FROM transactions,accounts WHERE transactions.account_id=accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2'
		   AND transactions.type='transfer' AND transactions.company_id='$company_id'");

            $data['date1'] = $request->date1;
            $data['date2'] = $request->date2;
            return view('reports.transfer_report', $data);
        }
    }

    //Income Vs Expense Report
    public function income_vs_expense(Request $request, $view = '') {
        if ($view == '') {
            return view('reports/income_vs_expense_report');
        } else if ($view == 'view') {
            $date1 = $request->date1;
            $date2 = $request->date2;

            $data['report_data'] = $this->get_income_vs_expense($date1, $date2);

            $data['date1'] = $request->date1;
            $data['date2'] = $request->date2;
            return view('reports/income_vs_expense_report', $data);
        }
    }

    //Report By Payer
    public function report_by_payer(Request $request, $view = "") {
        if ($view == "") {
            return view('reports.report_by_payer');
        } else {
            $date1      = $request->date1;
            $date2      = $request->date2;
            $payer_id   = $request->payer_id;
            $company_id = company_id();
            $data       = array();

            $data['report_data'] = DB::select("SELECT DATE_FORMAT(transactions.trans_date,'%d %b, %Y') as trans_date,chart_of_accounts.name as c_type,transactions.note,accounts.account_title as account,transactions.amount,contacts.contact_name as payer
		   FROM transactions,accounts,contacts,chart_of_accounts WHERE transactions.account_id=accounts.id AND transactions.payer_payee_id=contacts.id
		   AND transactions.chart_id=chart_of_accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND transactions.payer_payee_id='$payer_id' AND transactions.company_id='$company_id'
		   UNION ALL
		   SELECT '','','TOTAL AMOUNT','',SUM(transactions.amount) as amount,'' FROM transactions
		   WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='cr' AND transactions.payer_payee_id='$payer_id' AND transactions.company_id='$company_id'");

            $data['date1']    = $request->date1;
            $data['date2']    = $request->date2;
            $data['payer_id'] = $request->payer_id;
            return view('reports.report_by_payer', $data);
        }
    }

    //Report By Payee
    public function report_by_payee(Request $request, $view = "") {
        if ($view == "") {
            return view('reports.report_by_payee');
        } else {
            $date1      = $request->date1;
            $date2      = $request->date2;
            $payee_id   = $request->payee_id;
            $company_id = company_id();
            $data       = array();

            $data['report_data'] = DB::select("SELECT DATE_FORMAT(transactions.trans_date,'%d %b, %Y') as trans_date,chart_of_accounts.name as c_type,transactions.note,accounts.account_title as account,transactions.amount,contacts.contact_name as payee
		   FROM transactions,accounts,contacts,chart_of_accounts WHERE transactions.account_id=accounts.id AND transactions.payer_payee_id=contacts.id
		   AND transactions.chart_id=chart_of_accounts.id AND transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='dr' AND transactions.payer_payee_id='$payee_id' AND transactions.company_id='$company_id'
		   UNION ALL
		   SELECT '','','TOTAL AMOUNT','',SUM(transactions.amount) as amount,'' FROM transactions
		   WHERE transactions.trans_date BETWEEN '$date1' AND '$date2' AND transactions.dr_cr='dr' AND transactions.payer_payee_id='$payee_id' AND transactions.company_id='$company_id'");

            $data['date1']    = $request->date1;
            $data['date2']    = $request->date2;
            $data['payee_id'] = $request->payee_id;
            return view('reports.report_by_payee', $data);
        }
    }

    private function get_income_vs_expense($from_date, $to_date) {
        $company_id = company_id();

        $income = DB::select("SELECT id FROM transactions
				  WHERE dr_cr='cr' AND company_id='$company_id' AND trans_date between '" . $from_date . "'
				  AND '" . $to_date . "'");

        $expense = DB::select("SELECT id FROM transactions
				  WHERE dr_cr='dr' AND company_id='$company_id' AND trans_date between '" . $from_date . "'
				  AND '" . $to_date . "'");

        if (count($income) > count($expense)) {
            return DB::select("SELECT income.*,expense.* FROM (SELECT @a:=@a+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') income_date,transactions.note as income_note,chart_of_accounts.name as income_type,transactions.amount income_amount
			    FROM transactions,accounts,chart_of_accounts, (SELECT @a:= 0) AS a WHERE
				transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='cr'
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date') as income LEFT JOIN
				(SELECT @b:=@b+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') expense_date,transactions.note as expense_note,chart_of_accounts.name as expense_type,transactions.amount expense_amount FROM transactions,accounts,chart_of_accounts,
				(SELECT @b:= 0) AS a WHERE transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='dr'
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date') as expense ON income.sl=expense.sl");
        } else {
            return DB::select("SELECT income.*,expense.* FROM (SELECT @a:=@a+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') income_date,transactions.note as income_note,chart_of_accounts.name as income_type,transactions.amount income_amount
			    FROM transactions,accounts,chart_of_accounts, (SELECT @a:= 0) AS a WHERE
				transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='cr'
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date') as income RIGHT JOIN
				(SELECT @b:=@b+1 as sl,DATE_FORMAT(transactions.trans_date,'%d %b, %Y') expense_date,transactions.note as expense_note,chart_of_accounts.name as expense_type,transactions.amount expense_amount FROM transactions,accounts,chart_of_accounts,
				(SELECT @b:= 0) AS a WHERE transactions.account_id=accounts.id AND transactions.chart_id=chart_of_accounts.id AND transactions.dr_cr='dr'
				AND transactions.company_id='$company_id' AND trans_date between '$from_date' AND '$to_date') as expense ON income.sl=expense.sl");
        }

    }

}
