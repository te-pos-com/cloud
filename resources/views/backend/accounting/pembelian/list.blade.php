@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card mt-2">
            <div class="card-header d-flex align-items-center">
                <h4 class="header-title">{{ _lang('Purchase List') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto" href="{{ route('pembelian.create') }}"><i
                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col-lg-3 mb-2">
                        <label>{{ _lang('Supplier') }}</label>
                        <select class="form-control select2 select-filter" name="supplier_id">
                            <option value="">{{ _lang('All Supplier') }}</option>
                            {{ create_option('suppliers','id','supplier_name','',array('company_id=' => company_id())) }}
                        </select>
                    </div>
                    
                    <div class="col-lg-3 mb-2">
                        <label>{{ _lang('Cabang') }}</label>
                        <select class="form-control select2 select-filter" name="cabang_id">
                            <option value="">{{ _lang('All Cabang') }}</option>
                            {{ create_option('cabang','id','cabang_name','',array('company_id=' => company_id())) }}
                        </select>
                    </div>
                    @if (jenis_langganan()=="POS" || jenis_langganan()=="TRADING")
                    @else
                        <div class="col-lg-3 mb-2">
                            <label>{{ _lang('Payment Status') }}</label>
                            <select class="form-control select2 select-filter"
                                data-placeholder="{{ _lang('Payment Status') }}" name="payment_status" multiple="true">
                                <option value="1">{{ _lang('Paid') }}</option>
                                <option value="0">{{ _lang('UnPaid') }}</option>
                            </select>
                        </div>
                    @endif
                    <div class="col-lg-3">
                        <label>{{ _lang('Rentang Tanggal Pembelian') }}</label>
                        <input type="text" class="form-control select-filter" id="date_range" autocomplete="off"
                            name="date_range">
                    </div>
                </div>
                <hr>

                <table class="table table-bordered" id="purchase-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Tanggal Pembelian') }}</th>
                            <th>{{ _lang('Supplier') }}</th>
                            <th class="text-right">{{ _lang('Grand Total') }}</th>
                            <th class="text-right">{{ _lang('Paid') }}</th>
                            <th>{{ _lang('Payment Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('js-script')
<script src="{{ asset('public/backend/assets/js/datatables/pembelian-table.js?v=1.1') }}"></script>
@endsection