@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Product List') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Product') }}"
                    href="{{route('products.create')}}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Product') }}</th>
                            <th>{{ _lang('Product Cost') }}</th>
                            <th>{{ _lang('Product Price') }}</th>
                            <th>{{ _lang('Product Unit') }}</th>
                            <th class="text-center">{{ _lang('Available Stock') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @php $currency = currency(); @endphp
                        @foreach($items as $item)
                        <tr id="row_{{ $item->id }}">
                            <td class='item_id'>{{ $item->item_name }}</td>
                            <td class='product_cost'>{{ decimalPlace($item->product->product_cost, $currency) }}</td>
                            <td class='product_price'>{{ decimalPlace($item->product->product_price, $currency) }}</td>
                            <td class='product_unit'>{{ $item->product->units->unit_name }}</td>
                            <td class='product_stock text-center'>{{ $item->product_stock->quantity }}</td>
                            <td class="text-center">
                                <form action="{{action('ProductController@destroy', $item['id'])}}" method="post">
                                    <a href="{{action('ProductController@edit', $item['id'])}}"
                                        data-title="{{ _lang('Update Product') }}"
                                        class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a>
                                    <a href="{{action('ProductController@show', $item['id'])}}"
                                        data-title="{{ _lang('View Product') }}"
                                        class="btn btn-primary btn-sm ajax-modal"><i class="ti-eye"></i></a>
                                    {{ csrf_field() }}
                                    <input name="_method" type="hidden" value="DELETE">
                                    <button class="btn btn-danger btn-sm btn-remove"
                                        type="submit"><i class="ti-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection