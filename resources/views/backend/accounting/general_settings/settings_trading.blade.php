@extends('layouts.app')

@section('content')

<h4 class="page-title">{{ _lang('Company Settings') }}</h4>
<div class="row">
    <div class="col-sm-3">
        <ul class="nav flex-column nav-tabs settings-tab" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#general"><i
                        class="ti-settings"></i> {{ _lang('General Settings') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#invoice_settings"><i
                        class="ti-receipt"></i> {{ _lang('Nomor Invoice') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#payment_gateway"><i
                        class="ti-credit-card"></i> {{ _lang('Payment Gateway') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#logo"><i class="ti-image"></i>
                    {{ _lang('Logo') }}</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#defaultaccount"><i class="ti-settings"></i>
                    {{ _lang('Default Account') }}</a></li>
        </ul>
    </div>

    <div class="col-sm-9">
        <div class="tab-content">

            <div id="general" class="tab-pane active">
                <div class="card card-default">
                    <div class="card-header"><span class="header-title">{{ _lang('Company Settings') }}</span></div>

                    <div class="card-body">
                        <form method="post" class="settings-submit params-card" autocomplete="off"
                            action="{{ route('company.change_settings', 'store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Company Name') }}</label>
                                        <input type="text" class="form-control" name="company_name"
                                            value="{{ get_company_option('company_name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Phone') }}</label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ get_company_option('phone') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Email') }}</label>
                                        <input type="text" class="form-control" name="email"
                                            value="{{ get_company_option('email') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Currency') }}</label>
                                        <select class="form-control select2 auto-select"
                                            data-selected="{{ get_company_option('currency', get_option('currency')) }}"
                                            name="currency" id="currency" required>
                                            <option value="">{{ _lang('Select One') }}</option>
                                            {{ get_currency_list() }}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('VAT ID') }}</label>
                                        <input type="text" class="form-control" name="vat_id"
                                            value="{{ get_company_option('vat_id') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Language') }}</label>
                                        <select class="form-control select2" name="language">
                                            <option value="">{{ _lang('Select One') }}</option>
                                            {{ load_language( get_company_option('language') ) }}
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <a href="{{ route('cabang.create') }}" data-reload="false"
                                            data-title="{{ _lang('Add Cabang') }}" class="ajax-modal-2 select2-add"><i
                                                class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                        <label class="control-label">{{ _lang('Default Cabang') }}</label>
                                        <select class="form-control select2-ajax" data-value="id" data-display="cabang_name"
                                            data-table="cabang" data-where="1" name="cabang_id">
                                            {{ create_option("cabang","id","cabang_name",get_company_option('cabang_id'),array("company_id="=>company_id())) }}
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <a href="{{ route('gudang.create') }}" data-reload="false"
                                            data-title="{{ _lang('Add Gudang') }}" class="ajax-modal-2 select2-add"><i
                                                class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                        <label class="control-label">{{ _lang('Default Gudang') }}</label>
                                        <select class="form-control select2-ajax" data-value="id" data-display="gudang_name"
                                            data-table="gudang" data-where="1" name="gudang_id">
                                            {{ create_option("gudang","id","gudang_name",get_company_option('gudang_id'),array("company_id="=>company_id())) }}
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Address') }}</label>
                                        <textarea class="form-control"
                                            name="address">{{ get_company_option('address') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg"><i
                                                class="ti-save"></i> {{ _lang('Save Settings') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="invoice_settings" class="tab-pane fade">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="header-title">{{ _lang('Nomor Invoice') }}</span>
                    </div>
                    <div class="card-body">
                        <form method="post" class="settings-submit params-card" autocomplete="off"
                            action="{{ route('company.change_settings', 'store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Order Pembelian') }}</label>
                                        <input type="text" class="form-control" name="invoice_order_pembelian_perfix"
                                            value="{{ get_company_option('invoice_order_pembelian_perfix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Nomor Faktur Order Pembelian Selanjutnya') }}</label>
                                        <input type="number" class="form-control" name="invoice_order_pembelian" min="1"
                                            value="{{ get_company_option('invoice_order_pembelian',1001) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Pembelian') }}</label>
                                        <input type="text" class="form-control" name="invoice_pembelian_perfix"
                                            value="{{ get_company_option('invoice_pembelian_perfix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Nomor Faktur Pembelian Selanjutnya') }}</label>
                                        <input type="number" class="form-control" name="invoice_pembelian" min="1"
                                            value="{{ get_company_option('invoice_pembelian',1001) }}" required>
                                    </div>
                                </div>
                                

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Prefix') }}</label>
                                        <input type="text" class="form-control" name="invoice_prefix"
                                            value="{{ get_company_option('invoice_prefix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Next Invoice Number') }}</label>
                                        <input type="number" class="form-control" name="invoice_starting" min="1"
                                            value="{{ get_company_option('invoice_starting',1001) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Quotation Prefix') }}</label>
                                        <input type="text" class="form-control" name="quotation_prefix"
                                            value="{{ get_company_option('quotation_prefix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Next Quotation Number') }}</label>
                                        <input type="number" class="form-control" name="quotation_starting" min="1"
                                            value="{{ get_company_option('quotation_starting',1001) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Retur Pembelian') }}</label>
                                        <input type="text" class="form-control" name="invoice_retur_pembelian_perfix"
                                            value="{{ get_company_option('invoice_retur_pembelian_perfix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Nomor Faktur Retur Pembelian Selanjutnya') }}</label>
                                        <input type="number" class="form-control" name="invoice_retur_pembelian" min="1"
                                            value="{{ get_company_option('invoice_retur_pembelian',1001) }}" required>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Retur Penjualan') }}</label>
                                        <input type="text" class="form-control" name="invoice_retur_penjualan_perfix"
                                            value="{{ get_company_option('invoice_retur_penjualan_perfix') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Nomor Faktur Retur Penjualan Selanjutnya') }}</label>
                                        <input type="number" class="form-control" name="invoice_retur_penjualan" min="1"
                                            value="{{ get_company_option('invoice_retur_penjualan',1001) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Footer') }}</label>
                                        <textarea class="form-control summernote"
                                            name="invoice_footer">{{ get_company_option('invoice_footer') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Quotation Footer') }}</label>
                                        <textarea class="form-control summernote"
                                            name="quotation_footer">{{ get_company_option('quotation_footer') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Faktur Order Pembelian') }}</label>
                                        <textarea class="form-control summernote"
                                            name="orderpembelian_footer">{{ get_company_option('orderpembelian_footer') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Faktur Pembelian') }}</label>
                                        <textarea class="form-control summernote"
                                            name="pembelian_footer">{{ get_company_option('pembelian_footer') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Faktur Retur Pembelian') }}</label>
                                        <textarea class="form-control summernote"
                                            name="returpembelian_footer">{{ get_company_option('returpembelian_footer') }}</textarea>
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Faktur Retur Penjualan') }}</label>
                                        <textarea class="form-control summernote"
                                            name="returpenjualan_footer">{{ get_company_option('returpenjualan_footer') }}</textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg"><i
                                                class="ti-save"></i> {{ _lang('Save Settings') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="payment_gateway" class="tab-pane fade">
                <div class="card card-default">
                    <div class="card-header">
                        <span class="header-title">{{ _lang('Payment Gateway') }}</span>
                    </div>
                    <div class="card-body">
                        <form method="post" class="settings-submit params-card" autocomplete="off"
                            action="{{ route('company.change_settings', 'store') }}" enctype="multipart/form-data">

                            @csrf

                            <h5 class="header-title">{{ _lang('Invoice Payment Configuration') }}</h5>
                            <div class="params-card border border-secondary p-3 mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Default Account') }}</label>
                                            <select class="form-control select2" name="default_account" required>
                                                <option value="">{{ _lang('Select One') }}</option>
                                                {{ create_option("accounts","id","account_title",get_company_option('default_account'),array("company_id="=>company_id())) }}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ _lang('Default Income Category') }}</label>
                                            <select class="form-control select2" name="default_chart_id" required>
                                                <option value="">{{ _lang('Select One') }}</option>
                                                {{ create_option("chart_of_accounts","id","name",get_company_option('default_chart_id'),array("type="=>"income","AND company_id="=>company_id())) }}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                            
                        </form>
                    </div>
                </div>
            </div>


            <div id="logo" class="tab-pane fade">
                <div class="card card-default">
                    <div class="card-header"><span class="header-title">{{ _lang('Logo Upload') }}</span></div>
                    <div class="card-body">
                        <form method="post" class="settings-submit params-card" autocomplete="off"
                            action="{{ route('company.change_logo') }}" enctype="multipart/form-data">

                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Upload Logo') }}</label>
                                        <input type="file" class="form-control dropify" name="logo"
                                            data-max-file-size="8M"
                                            data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG"
                                            data-default-file="{{ get_company_logo() }}" required>
                                    </div>
                                </div>

                                <br>
                                <div class="col-md-4 offset-md-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg"><i
                                                class="ti-upload"></i> {{ _lang('Upload') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div id="defaultaccount" class="tab-pane fade">
                <div class="card card-default">
                    <div class="card-header"><span class="header-title">{{ _lang('Default Account') }}</span></div>
                    <div class="card-body">
                        <form method="post" class="settings-submit params-card" autocomplete="off"
                            action="{{ route('company.change_logo') }}" enctype="multipart/form-data">

                            @csrf
                            
                           <ul class="nav nav-pills" role="tablist">
                              <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" href="#pembelian">Pembelian</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#returpembelian">Retur Pembelian</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#penjualan">Penjualan</a>
                              </li>
                              <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" href="#returpenjualan">Retur Penjualan</a>
                              </li>
                            </ul>
                            
                            
                            
                            <div class="tab-content" style="margin-top:30px;">
                              <div id="pembelian" class="container tab-pane active">
                                    <div class="row">
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Persediaan (D)') }}</label>
                                                <select class="form-control select2" name="persediaan_pembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('pembelian_persediaan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('PPN (D)') }}</label>
                                                <select class="form-control select2" name="ppn_pembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('pembelian_ppn_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Biaya (D)') }}</label>
                                                <select class="form-control select2" name="biaya_pembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('pembelian_biaya_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Diskon (C)') }}</label>
                                                <select class="form-control select2" name="diskon_pembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('pembelian_diskon_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Hutang Dagang (C)') }}</label>
                                                <select class="form-control select2" name="hutangdagang_pembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('pembelian_hutangdagang_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                              </div>
                              
                              <div id="returpembelian" class="container tab-pane fade">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Hutang Dagang (D)') }}</label>
                                                <select class="form-control select2" name="hutangdagang_returpembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpembelian_hutangdagang_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>

                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('PPN (C)') }}</label>
                                                <select class="form-control select2" name="ppn_returpembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpembelian_ppn_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Persediaan (C)') }}</label>
                                                <select class="form-control select2" name="persediaan_returpembelian">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpembelian_persediaan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                    </div>
                              </div>
                              <div id="penjualan" class="container tab-pane fade">
                                    <div class="row">

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Piutang Dagang (D)') }}</label>
                                                <select class="form-control select2" name="piutangdagang_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_piutangdagang_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Diskon (D)') }}</label>
                                                <select class="form-control select2" name="diskon_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_diskon_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('HPP (D)') }}</label>
                                                <select class="form-control select2" name="hpp_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_hpp_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('PPN (C)') }}</label>
                                                <select class="form-control select2" name="ppn_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_ppn_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                    
                                    <div class="col-md-11" style="margin-left:60px;">
                                        <div class="form-group">
                                            <a href="{{ route('accounts.create') }}" data-reload="false"
                                                data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                    class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                            <label class="control-label">{{ _lang('Biaya (C)') }}</label>
                                            <select class="form-control select2" name="biaya_penjualan">
                                                <option value="">{{ _lang('- Select Account -') }}</option>
                                                {{ create_option("accounts","id","account_title",get_company_option('penjualan_biaya_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-11" style="margin-left:60px;">
                                        <div class="form-group">
                                            <a href="{{ route('accounts.create') }}" data-reload="false"
                                                data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                    class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                            <label class="control-label">{{ _lang('Penjualan (C)') }}</label>
                                            <select class="form-control select2" name="penjualan_penjualan">
                                                <option value="">{{ _lang('- Select Account -') }}</option>
                                                {{ create_option("accounts","id","account_title",get_company_option('penjualan_penjualan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-11" style="margin-left:60px;">
                                        <div class="form-group">
                                            <a href="{{ route('accounts.create') }}" data-reload="false"
                                                data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                    class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                            <label class="control-label">{{ _lang('Persediaan (C)') }}</label>
                                            <select class="form-control select2" name="persediaan_penjualan">
                                                <option value="">{{ _lang('- Select Account -') }}</option>
                                                {{ create_option("accounts","id","account_title",get_company_option('penjualan_persediaan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                            </select>
                                        </div>
                                    </div>
                                </div>    
                              </div>
                              
                              <div id="returpenjualan" class="container tab-pane fade">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Penjualan (D)') }}</label>
                                                <select class="form-control select2" name="penjualan_returpenjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpenjualan_penjualan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('PPN (D)') }}</label>
                                                <select class="form-control select2" name="ppn_returpenjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpenjualan_ppn_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Persediaan (D)') }}</label>
                                                <select class="form-control select2" name="persediaan_returpenjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('returpenjualan_persediaan_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('Piutang Dagang (C)') }}</label>
                                                <select class="form-control select2" name="piutangdagang_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_piutangdagang_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-11" style="margin-left:60px;">
                                            <div class="form-group">
                                                <a href="{{ route('accounts.create') }}" data-reload="false"
                                                    data-title="{{ _lang('Add Account') }}" class="ajax-modal-2 select2-add"><i
                                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                                <label class="control-label">{{ _lang('HPP (C)') }}</label>
                                                <select class="form-control select2" name="hpp_penjualan">
                                                    <option value="">{{ _lang('- Select Account -') }}</option>
                                                    {{ create_option("accounts","id","account_title",get_company_option('penjualan_hpp_id'),array("company_id="=>company_id(),"AND jenis="=>"D")) }}
                                                </select>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                    </div>                                    
                              </div>
                            </div>
                            
                            <div class="col-md-12" style="margin-top:100px;">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg"><i
                                            class="ti-save"></i> {{ _lang('Save Settings') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
  
    @endsection