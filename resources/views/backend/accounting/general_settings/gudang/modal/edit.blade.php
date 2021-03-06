<form method="post" class="ajax-submit" autocomplete="off" action="{{ action('GudangController@update', $id) }}"
    enctype="multipart/form-data">
    {{ csrf_field()}}
    <input name="_method" type="hidden" value="PATCH">

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Gudang Name') }}</label>
            <input type="text" class="form-control" name="gudang_name" value="{{ $gudang->gudang_name }}" required>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Update') }}</button>
        </div>
    </div>
</form>