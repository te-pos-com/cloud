@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4 class="header-title">{{ _lang('Staff List') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Staff') }}"
                    href="{{ route('staffs.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th class="text-center">{{ _lang('Image') }}</th>
                            <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Email') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th>{{ _lang('Role') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach($users as $user)
                        <tr id="row_{{ $user->id }}">
                            <td class="text-center"><img
                                    src="{{ asset('public/uploads/profile/'.$user->profile_picture) }}"
                                    class="thumb-sm img-thumbnail"></td>
                            <td class='name'>{{ $user->name }}</td>
                            <td class='email'>{{ $user->email }}</td>
                            <td class='status'>{{ $user->status == 1 ? _lang('Active') : _lang('In-Active') }}</td>
                            <td class='role_id'>{{ $user->role->name }}</td>
                            <td class="text-center">
                                <form action="{{action('StaffController@destroy', $user['id'])}}" method="post">
                                    <a href="{{action('StaffController@edit', $user['id'])}}"
                                        data-title="{{ _lang('Update Staff') }}"
                                        class="btn btn-warning btn-sm ajax-modal"><i class="ti-pencil-alt"></i></a>
                                    <a href="{{action('StaffController@show', $user['id'])}}"
                                        data-title="{{ _lang('Staf Details') }}"
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