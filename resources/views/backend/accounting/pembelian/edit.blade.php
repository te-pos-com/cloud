@extends('layouts.app')

@section('content')
<link href="{{ asset('public/backend/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Update Purchase') }}</h4>
            </div>
            <div class="card-body">
                <form method="post" class="validate" autocomplete="off"
                    action="{{ action('PembelianController@update', $id) }}" enctype="multipart/form-data">

                    {{ csrf_field()}}
                    <input name="_method" type="hidden" value="PATCH">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Order Date') }}</label>
                                <input type="text" class="form-control" name="order_date"
                                    value="{{ $pembelian->getRawOriginal('order_date') }}" readOnly="true" required>
                            </div>
                        </div>
                        
                                                
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Invoice Number') }}</label>
                                <input type="text" class="form-control" name="invoice_number"
                                    value="{{ $pembelian->invoice_number }}" readOnly="true" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <a href="{{ route('cabang.create') }}" data-reload="false"
                                    data-title="{{ _lang('Add Cabang') }}" class="ajax-modal-2 select2-add"><i
                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                <label class="control-label">{{ _lang('Cabang') }}</label>
                                <select class="form-control select2-ajax" name="cabang_id" data-value="id"
                                    data-display="cabang_name" data-table="cabang" data-where="1" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                    {{ create_option("cabang","id","cabang_name",$pembelian->cabang_id,array("company_id="=>company_id())) }}
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <a href="{{ route('suppliers.create') }}" data-reload="false"
                                    data-title="{{ _lang('Add Supplier') }}" class="ajax-modal-2 select2-add"><i
                                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                                <label class="control-label">{{ _lang('Supplier') }}</label>
                                <select class="form-control select2-ajax" name="supplier_id" data-value="id"
                                    data-display="supplier_name" data-table="suppliers" data-where="1" required>
                                    <option value="">{{ _lang('Select One') }}</option>
                                    {{ create_option("suppliers","id","supplier_name",$pembelian->supplier_id,array("company_id="=>company_id())) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
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

                        @if(jenis_langganan()=="POS")
                        @else
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Payment Status') }}</label>
                                <select class="form-control select2" name="payment_status" required>
                                    <option value="1" {{ $pembelian->payment_status == '0' ? 'selected' : '' }}>
                                        {{ _lang('Due') }}</option>
                                    <option value="2" {{ $pembelian->payment_status == '1' ? 'selected' : '' }}>
                                        {{ _lang('Paid') }}</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Attachemnt') }}</label>
                                <input type="file" class="form-control trickycode-file"
                                    data-value="{{ $pembelian->attachemnt }}" name="attachemnt">
                            </div>
                        </div>

                        @php $currency = currency(); @endphp
                        @php $taxes = App\Tax::where("company_id",company_id())->get(); @endphp

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
                                        @foreach($pembelian->pembelian_items as $item)
                                        <tr id="product-{{ $item->product_id }}">
                                            <td>
                                                <b>{{ $item->item->item_name }}</b><br>
                                            </td>
                                            <td class="description">
                                                <select class="form-control selectpicker"
                                                    name="gudang_id[]"
                                                    title="- Pilih Gudang -">
                                                    @foreach(App\Gudang::where("company_id",company_id())->get() as $gudang)
                                                        <option value="{{ $gudang->id }}" 
                                                        @if ($gudang->id==$item->gudang_id)
                                                            selected="selected"
                                                        @endif
                                                        >{{ $gudang->gudang_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-center quantity"><input type="number" name="quantity[]"
                                                    min="1" class="form-control input-quantity text-center"
                                                    value="{{ $item->quantity }}"></td>
                                            <td class="text-right unit-cost"><input type="text" name="unit_cost[]"
                                                    class="form-control input-unit-cost text-right"
                                                    value="{{ $item->unit_cost }}"></td>
                                            <td class="text-right discount"><input type="text" name="discount[]"
                                                    class="form-control input-discount text-right"
                                                    value="{{ $item->discount }}"></td>
                                            <td class="text-right tax">
                                                <select class="form-control auto-multiple-select selectpicker input-tax"
                                                    name="tax[{{ $item->product_id }}][]"
                                                    title="{{ _lang('Select TAX') }}"
                                                    data-selected="{{ $item->taxes->pluck('tax_id') }}" multiple="true">
                                                    @foreach($taxes as $tax)
                                                    <option value="{{ $tax->id }}" data-tax-type="{{ $tax->type }}"
                                                        data-tax-rate="{{ $tax->rate }}">{{ $tax->tax_name }} -
                                                        {{ $tax->type =='percent' ? $tax->rate.' %' : $tax->rate }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-right sub-total"><input type="text" name="sub_total[]"
                                                    class="form-control input-sub-total text-right"
                                                    value="{{ $item->sub_total }}" readonly></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-xs remove-product"><i
                                                        class='ti-trash'></i></button>
                                            </td>
                                            <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                            <input type="hidden" name="product_tax[]" class="input-product-tax"
                                                value="{{ $item->tax_amount }}">
                                        </tr>
                                        @endforeach
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
                                    value="{{ $pembelian->order_discount }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Shipping Cost')." ".$currency }}</label>
                                <input type="text" class="form-control float-field" name="shipping_cost"
                                    value="{{ $pembelian->shipping_cost }}">
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
                                <button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<select class="form-control d-none" id="tax-selector">
    @foreach($taxes as $tax)
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
<script src="{{ asset('public/backend/assets/js/purchase_order.js?v=1.0') }}"></script>
@endsection