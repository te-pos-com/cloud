@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Laporan Quotation') }}</h4>
            </div>

            <div class="card-body">
                <div class="report-params">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="date1" id="date1"
                                        value="{{ isset($date1) ? $date1 : old('date1') }}" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="date2" id="date2"
                                        value="{{ isset($date2) ? $date2 : old('date2') }}" required>
                                </div>
                            </div>



                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="cabang"
                                        data-selected="1" multiple="true">
                                        {{ create_option('cabang','id','cabang_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Customer') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="customer"
                                        data-selected="1" multiple="true">
                                        {{ create_option('contacts','id','contact_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label>{{ _lang('Status') }}</label>
                                <select class="form-control select2 select-filter" name="order_status"
                                data-selected="1" multiple="true">
                                    <option value="1">{{ _lang('Dikonversi') }}</option>
                                    <option value="0">{{ _lang('Belum Dikonversi') }}</option>
                                </select>
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


@section('js-script')
<script src="{{ asset('public/backend/assets/js/datatables/quotation-table-report.js?v=1.1') }}"></script>
@endsection