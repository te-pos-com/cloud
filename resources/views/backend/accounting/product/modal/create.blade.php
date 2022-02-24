<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('products.store') }}"
    enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row p-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Product Name') }}</label>
                <input type="text" class="form-control" name="item_name" value="{{ old('item_name') }}" required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Tipe') }}</label>
                <select class="form-control select2" name="tipe">
                    @if(jenis_langganan()=="POS")
                    @else
                        <option value="1">{{ _lang('Barang Dengan Imei') }}</option>
                    @endif
                    <option value="2" selected="selected">{{ _lang('Barang Dengan Stok') }}</option>
                    <option value="3">{{ _lang('Barang Tanpa Stok') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Barcode') }}</label>
                <input type="text" class="form-control" name="barcode" value="" required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Inisial') }}</label>
                <input type="text" class="form-control" name="inisial" value="" required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <a href="{{ route('product_kategori.create') }}" data-reload="false"
                    data-title="{{ _lang('Add Product Kategori') }}" class="ajax-modal-2 select2-add"><i
                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                <label class="control-label">{{ _lang('Product Kategori') }}</label>
                <select class="form-control select2-ajax" data-value="id" data-display="kategori_name"
                    data-table="product_kategori" data-where="1" name="id_kategori" required>
                    <option value="">{{ _lang('- Select Product Kategori -') }}</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <a href="{{ route('product_merek.create') }}" data-reload="false"
                    data-title="{{ _lang('Add Product Merek') }}" class="ajax-modal-2 select2-add"><i
                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                <label class="control-label">{{ _lang('Product Merek') }}</label>
                <select class="form-control select2-ajax" data-value="id" data-display="merek_name"
                    data-table="product_merek" data-where="1" name="id_merek" required>
                    <option value="">{{ _lang('- Select Product Merek -') }}</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <a href="{{ route('product_units.create') }}" data-reload="false"
                    data-title="{{ _lang('Add Product Unit') }}" class="ajax-modal-2 select2-add"><i
                        class="ti-plus"></i> {{ _lang('Add New') }}</a>
                <label class="control-label">{{ _lang('Product Unit') }}</label>
                <select class="form-control select2-ajax" data-value="id" data-display="unit_name"
                    data-table="product_units" data-where="1" name="product_unit" required>
                    <option value="">{{ _lang('- Select Product Unit -') }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Product Cost').' '.currency() }}</label>
                <input type="text" class="form-control" name="product_cost" value="{{ old('product_cost') }}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Product Price') .' '.currency() }}</label>
                <input type="text" class="form-control" name="product_price" value="{{ old('product_price') }}"
                    required>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Minimal Stok') }}</label>
                <input type="text" class="form-control" name="minimal_stok" value="0">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Minimal Jual') }}</label>
                <input type="text" class="form-control" name="minimal_jual" value="0">
            </div>
        </div>

        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header"><span class="header-title">{{ _lang('Gambar') }}</span></div>
                <div class="card-body">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="file" class="form-control dropify" name="gambar"
                                        data-max-file-size="8M"
                                        data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
                                </div>
                            </div>
    
                            <br>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Description') }}</label>
                <textarea class="form-control" name="description">{{ old('description') }}</textarea>
            </div>
        </div>


        <div class="form-group">
            <div class="col-md-12">

                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                    {{ _lang('Save Changes') }}</button>
            </div>
        </div>
    </div>
</form>