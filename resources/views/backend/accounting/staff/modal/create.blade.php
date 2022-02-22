<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('staffs.store') }}"
    enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row p-2">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Name') }}</label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Email') }}</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Password') }}</label>
                <input type="password" class="form-control" name="password" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Confirm Password') }}</label>
                <input type="password" class="form-control" name="password_confirmation" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Status') }}</label>
                <select class="form-control select2" id="status" name="status" required>
                    <option value="1">{{ _lang('Active') }}</option>
                    <option value="0">{{ _lang('Inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Staff Role') }}</label>
                <select class="form-control select2" id="role_id" name="role_id" required>
                {{ create_option('staff_roles','id','name', old('role_id'), array('company_id=' => company_id())) }}
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }}
                    )</label>
                <input type="file" class="dropify" name="profile_picture"
                    data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                    {{ _lang('Save') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
$("#user_type").val("{{ old('user_type') }}");
</script>