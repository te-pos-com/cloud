@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Invoice List') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto"
                    href="{{ route('invoices.create') }}"><i class="ti-plus"></i> {{ _lang('Create Invoice') }}</a>
            </div>

            <div class="card-body">
                <div class="row">
					<div class="col-lg-3 mb-2">
                     	<label>{{ _lang('Invoice Number') }}</label>
                     	<input type="text" class="form-control select-filter" name="invoice_number" id="invoice-number">
                    </div>	
					
					<div class="col-lg-3 mb-2">
                        <label>{{ _lang('Cabang') }}</label>
                        <select class="form-control select2 select-filter" name="cabang_id">
                            <option value="">{{ _lang('All Cabang') }}</option>
                            {{ create_option('cabang','id','cabang_name','',array('company_id=' => company_id())) }}
                        </select>
                    </div>
					
					<div class="col-lg-3 mb-2">
                     	<label>{{ _lang('Customer') }}</label>
						<select class="form-control select2 select-filter" name="client_id">
                            <option value="">{{ _lang('All Customer') }}</option>
							{{ create_option('contacts','id','contact_name','',array('company_id=' => company_id())) }}
                     	</select>
                    </div>	
					@if (jenis_langganan()=="POS")
                    @else
                    <div class="col-lg-3 mb-2">
                     	<label>{{ _lang('Status') }}</label>
                     	<select class="form-control select2 select-filter" data-placeholder="{{ _lang('Invoice Status') }}" name="status" multiple="true">
							<option value="Unpaid">{{ _lang('Unpaid') }}</option>
							<option value="Paid">{{ _lang('Paid') }}</option>
							<option value="Partially_Paid">{{ _lang('Partially Paid') }}</option>
							<option value="Canceled">{{ _lang('Canceled') }}</option>
                     	</select>
                    </div>
                    @endif	

                    <div class="col-lg-3">
                     	<label>{{ _lang('Date Range') }}</label>
                     	<input type="text" class="form-control select-filter" id="date_range" autocomplete="off" name="date_range">
                    </div>	
	
                </div>

                <hr>
                <table class="table table-bordered" id="invoice-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Invoice Number') }}</th>
                            <th>{{ _lang('Pelanggan') }}</th>
                            <th>{{ _lang('Invoice Date') }}</th>
                            <th>{{ _lang('Due Date') }}</th>
                            <th class="text-right">{{ _lang('Grand Total') }}</th>
                            <th>{{ _lang('Status') }}</th>
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
<script src="{{ asset('public/backend/assets/js/datatables/invoice-table.js?v=1.1') }}"></script>
@endsection