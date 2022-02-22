@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header header-title">{{ _lang('Create New Staff') }}</div>

            <div class="card-body">
                <form method="post" class="validate" autocomplete="off" action="{{ route('staffs.store') }}"
                    enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            {{ csrf_field() }}

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Name') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                                        required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}"
                                        required>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Password') }}</label>
                                <div class="col-xl-9">
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Confirm Password') }}</label>
                                <div class="col-xl-9">
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
                                <div class="col-xl-9">
                                    <select class="form-control auto-select" data-selected="{{ old('status') }}"
                                        name="status" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="1">{{ _lang('Active') }}</option>
                                        <option value="0">{{ _lang('In Active') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Staff Role') }}</label>
                                <div class="col-xl-9">
                                <select class="form-control select2" id="role_id" name="role_id" required>
                                        {{ create_option('staff_roles','id','name', old('role_id'), array('company_id=' => company_id())) }}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-xl-3 col-form-label">{{ _lang('Profile Picture') }}</label>
                                <div class="col-xl-9">
                                    <input type="file" class="form-control dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-xl-9 offset-xl-3">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Create Staff') }}</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection