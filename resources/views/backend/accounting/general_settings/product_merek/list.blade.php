@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Product Merek') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Product Merek') }}"
                    href="{{ route('product_merek.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Merek Name') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productmerek as $productmerek)
                        <tr id="row_{{ $productmerek->id }}">
                            <td class='merek_name'>{{ $productmerek->merek_name }}</td>
                            <td class="text-center">
                                <form action="{{action('ProductMerekController@destroy', $productmerek['id'])}}"
                                    method="post">
                                    <a href="{{action('ProductMerekController@edit', $productmerek['id'])}}"
                                        data-title="{{ _lang('Update Product Merek') }}"
                                        class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a>
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