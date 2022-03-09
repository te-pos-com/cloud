@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Mutasi Stok') }}</h4>
            </div>

            <div class="card-body">
                <div class="report-params">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="cabang"
                                        data-selected="1" multiple="false">
                                        {{ create_option('cabang','id','cabang_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Gudang') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="gudang"
                                        data-selected="1" multiple="false">
                                        {{ create_option('gudang','id','gudang_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Produk') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="produk"
                                        data-selected="1" multiple="false">
                                        {{ create_option('items','id','item_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>
                            

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Merek') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="merek"
                                        data-selected="1" multiple="false">
                                        {{ create_option('product_merek','id','merek_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Kategori') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="kategori"
                                        data-selected="1" multiple="false">
                                        {{ create_option('product_kategori','id','kategori_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>                            

                            <div class="col-md-2">
                                <button type="button" id="submit" class="btn btn-primary btn-sm">{{ _lang('View Report') }}</button>
                            </div>
                        </div>
                </div>
                <!--End Report param-->

                <div id="isi">
                    
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

<script src="{{ asset('public/backend/assets/js/datatables/mutasi-stok-table-report.js?v=1.1') }}"></script>
