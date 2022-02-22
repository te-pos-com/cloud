<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('cabang.store') }}"
    enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Name') }}</label>
            <input type="text" class="form-control" name="cabang_name" value="{{ old('cabang_name') }}" required>
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Phone') }}</label>
            <input type="text" class="form-control" name="cabang_phone" value="{{ old('cabang_phone') }}">
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Email') }}</label>
            <input type="text" class="form-control" name="cabang_email" value="{{ old('cabang_email') }}">
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Alamat') }}</label>
            <input type="text" class="form-control" name="cabang_alamat" value="{{ old('cabang_alamat') }}" required>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save') }}</button>
        </div>
    </div>
</form>