<form method="post" class="ajax-submit" autocomplete="off" action="{{route('accounts.store')}}"
    enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="row p-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Title') }}</label>
                <input type="text" class="form-control" name="account_title" value="{{ old('account_title') }}"
                    required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Opening Date') }}</label>
                <input type="text" class="form-control datepicker" name="opening_date" value="{{ old('opening_date') }}"
                    required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Number') }}</label>
                <input type="text" class="form-control" name="account_number" value="{{ old('account_number') }}">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Level') }}</label>
                <select class="form-control select2" name="level">
                    <option value="0">Level 0</option>
                    <option value="1" selected="selected">Level 1</option>
                    <option value="2">Level 2</option>
                    <option value="3">Level 3</option>
                    <option value="4">Level 4</option>
                    <option value="5">Level 5</option>
                    <option value="6">Level 6</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Tipe') }}</label>
                <select class="form-control select2" name="tipe">
                    <option value="H">Header</option>
                    <option value="D" selected="selected">Detail</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Opening Balance')." ".currency() }}</label>
                <input type="text" class="form-control float-field" name="opening_balance"
                    value="0" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Note') }}</label>
                <textarea class="form-control" name="note">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                    {{ _lang('Save Changes') }}</button>
            </div>
        </div>
    </div>
</form>