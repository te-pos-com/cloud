@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Laporan Laba/Rugi') }}</h4>
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
                                        data-selected="1" multiple="false">
                                        {{ create_option('cabang','id','cabang_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Customer') }}</label>
                                    <select class="form-control select2 select2 select-filter" name="customer"
                                        data-selected="1" multiple="false">
                                        {{ create_option('contacts','id','contact_name','',array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>


                            <div class="col-md-2" style="margin-top:-10px">
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


<script src="{{ asset('public/backend/assets/js/datatables/laba-rugi-teble-report.js?v=1.1') }}"></script>