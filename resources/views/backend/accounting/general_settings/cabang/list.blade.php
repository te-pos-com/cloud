@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Cabang') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Cabang') }}"
                    href="{{ route('cabang.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Cabang Name') }}</th>
                            <th>{{ _lang('Cabang Phone') }}</th>
                            <th>{{ _lang('Cabang Email') }}</th>
                            <th>{{ _lang('Cabang Alamat') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cabang as $cabang)
                        <tr id="row_{{ $cabang->id }}">
                            <td class='cabang_name'>{{ $cabang->cabang_name }}</td>
                            <td class='cabang_name'>{{ $cabang->cabang_phone }}</td>
                            <td class='cabang_name'>{{ $cabang->cabang_email }}</td>
                            <td class='cabang_name'>{{ $cabang->cabang_alamat }}</td>
                            <td class="text-center">
                                <form action="{{action('CabangController@destroy', $cabang['id'])}}"
                                    method="post">
                                    <a href="{{action('CabangController@edit', $cabang['id'])}}"
                                        data-title="{{ _lang('Update Cabang') }}"
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