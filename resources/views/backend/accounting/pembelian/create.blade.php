@extends('layouts.app')

@section('content')
<link href="{{ asset('public/backend/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Create Purchase') }}</h4>
            </div>

            @php $currency = currency(); @endphp

            <div class="card-body">
                <form method="post" class="validate" autocomplete="off" action="{{ route('pembelian.store') }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Order Date') }}</label>
                                <input type="text" class="form-control datepicker" name="order_date"
                                    value="{{ old('order_date') }}" readOnly="true" required>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Invoice Number') }}</label>
                                <input type="text" class="form-control" name="invoice_number"
                                    value="{{ old('invoice_number',get_company_option('invoice_pembelian_perfix').get_company_option('invoice_pembelian')) }}"
                                    required>
                                <input type="hidden" name="invoice_pembelian"
                                    value="{{ get_company_option('invoice_pembelian') }}">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
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
                        

                        <div class="col-md-3">
                            <div class="form-group">
                                <a href="{{ route('suppliers.create') }}" data-reload="false"
                                    data-title="{{ _lang('Add Supplier') }}" class="ajax-modal-2 select2-add"><i
                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                <label class="control-label">{{ _lang('Supplier') }}</label>
                                <select class="form-control select2-ajax" data-value="id" data-display="supplier_name"
                                    data-table="suppliers" data-where="1" name="supplier_id" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Attachemnt') }}</label>
                                <input type="file" class="form-control trickycode-file" name="attachemnt">
                            </div>
                        </div>

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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purchase Order') }}</label>
                                    <select class="form-control select2-ajax" name="id" data-value="id"
                                        data-display="invoice_number" data-table="purchase_orders" data-where="1">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option("purchase_orders","id","invoice_number",old('po_number'),array("company_id="=>company_id())) }}
                                    </select>
                                </div>
                            </div>
                        @else
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Purchase Order') }}</label>
                                    <select class="form-control select2-ajax" name="id" data-value="id"
                                        data-display="invoice_number" data-table="purchase_orders" data-where="1">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option("purchase_orders","id","invoice_number",old('po_number'),array("company_id="=>company_id())) }}
                                    </select>
                                </div>
                            </div>
                        @endif

                        <!--Order table -->
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
                                            <th class="text-right">{{ _lang('Sub Total') }}</th>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Order Discount')." ".$currency }}</label>
                                <input type="text" class="form-control float-field" name="order_discount"
                                    value="{{ old('order_discount',0) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Shipping Cost')." ".$currency }}</label>
                                <input type="text" class="form-control float-field" name="shipping_cost"
                                    value="{{ old('shipping_cost',0) }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Note') }}</label>
                                <textarea class="form-control" name="note">{{ old('note') }}</textarea>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<select class="form-control d-none" id="tax-selector">
    @foreach(App\Tax::where("company_id",company_id())->get() as $tax)
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
<script src="{{ asset('public/backend/assets/js/pembelian.js?v=1.0') }}"></script>
@endsection