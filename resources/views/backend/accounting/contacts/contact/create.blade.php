@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('contacts.store') }}"
    enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ _lang('Add New Contact') }}</h4>
                </div>

                <div class="card-body">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Profile Type') }}</label>
                                <select class="form-control select2" name="profile_type" required>
                                    <option value="Company" {{ old('profile_type')=="Company" ? "selected" : "" }}>
                                        {{ _lang('Company') }}</option>
                                    <option value="Individual"
                                        {{ old('profile_type')=="Individual" ? "selected" : "" }}>
                                        {{ _lang('Individual') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Company Name') }}</label>
                                <input type="text" class="form-control" name="company_name"
                                    value="{{ old('company_name') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Name') }}</label>
                                <input type="text" class="form-control" name="contact_name"
                                    value="{{ old('contact_name') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Email') }}</label>
                                <input type="text" class="form-control" name="contact_email"
                                    value="{{ old('contact_email') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Phone') }}</label>
                                <input type="text" class="form-control" name="contact_phone"
                                    value="{{ old('contact_phone') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Country') }}</label>
                                <select class="form-control select2" name="country">
                                    {{ get_country_list( old('country') ) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Group') }}</label>
                                <select class="form-control select2" name="group_id">
                                    <option value="">{{ _lang('- Select Group -') }}</option>
                                    {{ create_option("contact_groups", "id", "name", old('group_id'), array("company_id="=>company_id())) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('City') }}</label>
                                <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('State') }}</label>
                                <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Zip') }}</label>
                                <input type="text" class="form-control" name="zip" value="{{ old('zip') }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Address') }}</label>
                                <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Remarks') }}</label>
                                <textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Facebook') }}</label>
                                <input type="text" class="form-control" name="facebook" value="{{ old('facebook') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Twitter') }}</label>
                                <input type="text" class="form-control" name="twitter" value="{{ old('twitter') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Linkedin') }}</label>
                                <input type="text" class="form-control" name="linkedin" value="{{ old('linkedin') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="header-title">{{ _lang('Contact Image') }}</h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Image') }}</label>
                                <input type="file" class="form-control dropify" name="contact_image"
                                    data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="togglebutton">
                        <h4 class="header-title d-flex align-items-center">{{ _lang('Login Details') }}&nbsp;&nbsp;
                            <input type="checkbox" id="client_login" value="1" name="client_login">
                        </h4>
                    </div>
                </div>

                <div class="card-body" id="client_login_card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Email') }}</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Password') }}</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Confirm Password') }}</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Status') }}</label>
                                <select class="form-control" name="status">
                                    <option value="1">{{ _lang('Active') }}</option>
                                    <option value="0">{{ _lang('Inactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save Contact') }}</button>
        </div>

    </div>
</form>
@endsection