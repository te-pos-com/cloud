@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Product Kategori') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Product Kategori') }}"
                    href="{{ route('product_kategori.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Unit Kategori') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productkategori as $productkategori)
                        <tr id="row_{{ $productkategori->id }}">
                            <td class='kategori_name'>{{ $productkategori->kategori_name }}</td>
                            <td class="text-center">
                                <form action="{{action('ProductKategoriController@destroy', $productkategori['id'])}}"
                                    method="post">
                                    <a href="{{action('ProductKategoriController@edit', $productkategori['id'])}}"
                                        data-title="Update Kategori"
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