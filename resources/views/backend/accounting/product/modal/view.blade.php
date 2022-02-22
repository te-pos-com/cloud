<table class="table table-bordered">
    <tr>
        <td colspan="2">
            <div class="card card-default">
                <div class="card-header"><span class="header-title">{{ _lang('Gambar') }}</span></div>
                    <div class="card-body">
                        <img src="{{URL::to('/')}}/public/uploads/product/{{ $item->gambar }}" style="width:100%;height:300px;"></td>
                    </div>
                </div>
            </div>
    </tr>
    <tr>
        <td>{{ _lang('Item ID') }}</td>
        <td>{{ $item->id }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Name') }}</td>
        <td>{{ $item->item_name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Inisial') }}</td>
        <td>{{ $item->inisial }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Barcode') }}</td>
        <td>{{ $item->barcode }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Cost') }}</td>
        <td>{{ currency()." ".$item->product->product_cost }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Price') }}</td>
        <td>{{ currency()." ".$item->product->product_price }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Product Unit') }}</td>
        <td>{{ $item->product->units->unit_name }}</td>
    </tr>
    <tr>
        <td>Kategori Produk</td>
        <td>{{ $item->Kategori->kategori_name }}</td>
    </tr>
    <tr>
        <td>Merek Produk</td>
        <td>{{ $item->Merek->merek_name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Available Quantity') }}</td>
        <td>{{ $item->product_stock->quantity.' '.$item->product->units->unit_name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Minimal Stok') }}</td>
        <td>{{ $item->minimal_jual.' '.$item->product->units->unit_name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Minimal Jual') }}</td>
        <td>{{ currency()." ".$item->minimal_jual }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Description') }}</td>
        <td>{{ $item->product->description }}</td>
    </tr>
</table>