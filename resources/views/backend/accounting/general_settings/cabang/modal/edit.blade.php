<form method="post" class="ajax-submit" autocomplete="off" action="{{ action('CabangController@update', $id) }}"
    enctype="multipart/form-data">
    {{ csrf_field()}}
    <input name="_method" type="hidden" value="PATCH">

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Name') }}</label>
            <input type="text" class="form-control" name="cabang_name" value="{{ $cabang->cabang_name }}" required>
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Phone') }}</label>
            <input type="text" class="form-control" name="cabang_phone" value="{{ $cabang->cabang_phone }}" >
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Email') }}</label>
            <input type="text" class="form-control" name="cabang_email" value="{{ $cabang->cabang_email }}">
        </div>
        <div class="form-group">
            <label class="control-label">{{ _lang('Cabang Alamat') }}</label>
            <input type="text" class="form-control" name="cabang_alamat" value="{{ $cabang->cabang_alamat }}">
        </div>
    </div>


    <div class="form-group">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Update') }}</button>
        </div>
    </div>
</form>