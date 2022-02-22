@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ action('ContactController@update', $id) }}"
    enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ _lang('Add New Contact') }}</h4>
                </div>

                <div class="card-body">
                    {{ csrf_field() }}
                    <input name="_method" type="hidden" value="PATCH">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Profile Type') }}</label>
                                <select class="form-control select2" name="profile_type" required>
                                    <option value="Company" {{ $contact->profile_type == "Company" ? "selected" : "" }}>
                                        {{ _lang('Company') }}</option>
                                    <option value="Individual"
                                        {{ $contact->profile_type == "Individual" ? "selected" : "" }}>
                                        {{ _lang('Individual') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Company Name') }}</label>
                                <input type="text" class="form-control" name="company_name"
                                    value="{{ $contact->company_name }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Name') }}</label>
                                <input type="text" class="form-control" name="contact_name"
                                    value="{{ $contact->contact_name }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Email') }}</label>
                                <input type="text" class="form-control" name="contact_email"
                                    value="{{ $contact->contact_email }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Contact Phone') }}</label>
                                <input type="text" class="form-control" name="contact_phone"
                                    value="{{ $contact->contact_phone }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Country') }}</label>
                                <select class="form-control select2" name="country">
                                    {{ get_country_list( $contact->country ) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Group') }}</label>
                                <select class="form-control select2" name="group_id">
                                    <option value="">{{ _lang('- Select Group -') }}</option>
                                    {{ create_option("contact_groups","id","name",$contact->group_id,array("company_id="=>company_id())) }}
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('City') }}</label>
                                <input type="text" class="form-control" name="city" value="{{ $contact->city }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('State') }}</label>
                                <input type="text" class="form-control" name="state" value="{{ $contact->state }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Zip') }}</label>
                                <input type="text" class="form-control" name="zip" value="{{ $contact->zip }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Address') }}</label>
                                <textarea class="form-control" name="address">{{ $contact->address }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Remarks') }}</label>
                                <textarea class="form-control" name="remarks">{{ $contact->remarks }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Facebook') }}</label>
                                <input type="text" class="form-control" name="facebook"
                                    value="{{ $contact->facebook }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Twitter') }}</label>
                                <input type="text" class="form-control" name="twitter" value="{{ $contact->twitter }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Linkedin') }}</label>
                                <input type="text" class="form-control" name="linkedin"
                                    value="{{ $contact->linkedin }}">
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
                                    data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG"
                                    data-default-file="{{ asset('public/uploads/contacts/'.$contact->contact_image) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="togglebutton">
                        <h4 class="header-title d-flex align-items-center">{{ _lang('Login Details') }}&nbsp;&nbsp;
                            <input type="checkbox" id="client_login" value="1" name="client_login"
                                {{ $contact-> user_id != NULL ? 'checked' : '' }}>
                        </h4>
                    </div>
                </div>

                <div class="card-body" id="client_login_card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ $contact->user->name }}">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Email') }}</label>
                                <input type="email" class="form-control" name="email"
                                    value="{{ $contact->user->email }}">
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
                                <select class="form-control auto-select" data-selected="{{ $contact->user->status }}"
                                    name="status">
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
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                {{ _lang('Update Contact') }}</button>
        </div>
    </div>
</form>
@endsection