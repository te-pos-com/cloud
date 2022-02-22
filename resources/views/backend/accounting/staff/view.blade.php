@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('View Staff') }}</h4>
            </div>

            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <td colspan="2" class="text-center"><img style="margin: auto;" class="thumb-image-md thumbnail"
                                src="{{ profile_picture($user->profile_picture) }}">
                        </td>
                    </tr>
                    <tr>
                        <td>{{ _lang('Name') }}</td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ _lang('Email') }}</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td>{{ _lang('User Type') }}</td>
                        <td>{{ $user->user_type }}</td>
                    </tr>
                    <tr>
                        <td>{{ _lang('User Role') }}</td>
                        <td>{{ $user->role->name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection