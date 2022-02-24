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
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#logo"><i class="ti-image"></i>
                    {{ _lang('Logo') }}</a></li>
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

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Invoice Footer') }}</label>
                                        <textarea class="form-control summernote"
                                            name="invoice_footer">{{ get_company_option('invoice_footer') }}</textarea>
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
                                        <button type="submit" class="btn btn-primary btn-lg"><i
                                                class="ti-save"></i> {{ _lang('Save Settings') }}</button>
                                    </div>
                                </div>
                            </div>
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
            
            
        </div>
    </div>
  
    @endsection