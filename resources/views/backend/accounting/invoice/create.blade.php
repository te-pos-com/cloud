@extends('layouts.app')

@section('content')
<link href="{{ asset('public/backend/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Create Invoice') }}</h4>
            </div>

            <div class="card-body">
                <form method="post" class="validate" autocomplete="off" action="{{ route('invoices.store') }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}


                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Invoice Date') }}</label>
                                <input type="text" class="form-control datepicker" name="invoice_date"
                                    value="{{ old('invoice_date') }}" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Invoice Number') }}</label>
                                <input type="text" class="form-control" name="invoice_number"
                                    value="{{ old('invoice_number',get_company_option('invoice_prefix').get_company_option('invoice_starting')) }}"
                                    required>
                                <input type="hidden" name="invoice_starting_number"
                                    value="{{ get_company_option('invoice_starting') }}">
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Cabang') }}</label>
                                <select class="form-control select2" name="cabang_id" required>
                                    @foreach(App\Cabang::where("company_id",company_id())->get() as $cabang)
                                    <option value="{{ $cabang->id }}"
                                    @if(get_company_option('cabang_id')==$cabang->id)
                                        selected="selected"
                                    @endif
                                    >
                                        {{ $cabang->cabang_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                <a href="{{ route('contacts.create') }}" data-reload="false"
                                    data-title="{{ _lang('Add Client') }}" class="ajax-modal select2-add"><i
                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                <label class="control-label">{{ _lang('Pilih Pelanggan') }}</label>
                                <select class="form-control select2-ajax" data-value="id" data-display="contact_name"
                                    data-table="contacts" data-where="1" name="client_id" id="client_id" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                </select>
                            </div>
                        </div>

                        @if (jenis_langganan()=="POS")
                        @elseif (jenis_langganan()=="TRADING")
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Due Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="due_date"
                                        value="{{ old('due_date') }}" required>
                                </div>
                            </div>
                        @else
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Due Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="due_date"
                                        value="{{ old('due_date') }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Status') }}</label>
                                    <select class="form-control select2" name="status">
                                        <option value="Unpaid">{{ _lang('Unpaid') }}</option>
                                        <option value="Paid">{{ _lang('Paid') }}</option>
                                        <option value="Partially_Paid">{{ _lang('Partially Paid') }}</option>
                                        <option value="Canceled">{{ _lang('Canceled') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="form-group select-product-container">
                                <a href="{{ route('products.create') }}" data-reload="false"
                                    data-title="{{ _lang('Add Product') }}" class="ajax-modal select2-add"><i
                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                <label class="control-label">{{ _lang('Select Product') }}</label>
                                <select class="form-control select2-ajax" data-value="id" data-display="item_name"
                                    data-table="items" data-where="2" name="product" id="product">
                                    <option value="">{{ _lang('Select Product') }}</option>
                                </select>
                            </div>
                        </div>

                        @if (jenis_langganan()=="POS")
                        @elseif (jenis_langganan()=="TRADING")
                            <div class="col-md-6">
                                <div class="form-group select-product-container">
                                    <a href="{{ route('services.create') }}" data-reload="false"
                                        data-title="{{ _lang('Add Service') }}" class="ajax-modal select2-add"><i
                                            class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                    <label class="control-label">{{ _lang('Select Service') }}</label>
                                    <select class="form-control select2-ajax" data-value="id" data-display="item_name"
                                        data-table="items" data-where="5" name="service" id="service">
                                        <option value="">{{ _lang('Select Service') }}</option>
                                    </select>
                                </div>
                            </div>                        
                        @else
                            <div class="col-md-6">
                                <div class="form-group select-product-container">
                                    <a href="{{ route('services.create') }}" data-reload="false"
                                        data-title="{{ _lang('Add Service') }}" class="ajax-modal select2-add"><i
                                            class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                    <label class="control-label">{{ _lang('Select Service') }}</label>
                                    <select class="form-control select2-ajax" data-value="id" data-display="item_name"
                                        data-table="items" data-where="5" name="service" id="service">
                                        <option value="">{{ _lang('Select Service') }}</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <!--Order table -->
                        @php $currency = currency(); @endphp

                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="order-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ _lang('Name') }}</th>
                                            <th>{{ _lang('Gudang') }}</th>
                                            <th class="text-center wp-100">{{ _lang('Quantity') }}</th>
                                            <th class="text-right">{{ _lang('Unit Cost') }}</th>
                                            <th class="text-right wp-100">{{ _lang('Discount') }}</th>
                                            <th class="text-right">{{ _lang('Tax') }}</th>
                                            <th class="text-right">{{ _lang('Line Total') }}</th>
                                            <th class="text-center">{{ _lang('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot class="tfoot active">
                                        <tr>
                                            <th>{{ _lang('Total') }}</th>
                                            <th></th>
                                            <th class="text-center" id="total-qty">0</th>
                                            <th></th>
                                            <th class="text-right" id="total-discount">0.00</th>
                                            <th class="text-right" id="total-tax">0.00</th>
                                            <th class="text-right" id="total">0.00</th>
                                            <th class="text-center"></th>
                                            <input type="hidden" name="product_total" id="product_total" value="0">
                                            <input type="hidden" name="tax_total" id="tax_total" value="0">
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!--End Order table -->

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Note') }}</label>
                                <textarea class="form-control" rows="4" name="note">{{ old('note') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                                    {{ _lang('Save Invoice') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<select class="form-control d-none" id="tax-selector">
    @foreach(App\Tax::all() as $tax)
    <option value="{{ $tax->id }}" data-tax-type="{{ $tax->type }}" data-tax-rate="{{ $tax->rate }}">
        {{ $tax->tax_name }} - {{ $tax->type =='percent' ? $tax->rate.' %' : $tax->rate }}</option>
    @endforeach
</select>

<select class="form-control d-none" id="gudang-selector">
    @foreach(App\Gudang::where("company_id",company_id())->get() as $gudang)
    <option value="{{ $gudang->id }}"
    @if(get_company_option('gudang_id')==$gudang->id)
        selected="selected"
    @endif
    >
        {{ $gudang->gudang_name }}</option>
    @endforeach
</select>


@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/bootstrap-select/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/invoice.js') }}"></script>
@endsection