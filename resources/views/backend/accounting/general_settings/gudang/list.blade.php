@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Gudang') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Gudang') }}"
                    href="{{ route('gudang.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Gudang Name') }}</th>
                            <th>{{ _lang('Cabang Name') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gudang as $gudang)
                        <tr id="row_{{ $gudang->id }}">
                            <td class='gudang_name'>{{ $gudang->gudang_name }}</td>
                            <td class='cabang_name'>{{$gudang->cabang->cabang_name }}</td>
                            <td class="text-center">
                                <form action="{{action('GudangController@destroy', $gudang['id'])}}"
                                    method="post">
                                    <a href="{{action('GudangController@edit', $gudang['id'])}}"
                                        data-title="{{ _lang('Update Gudang') }}"
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