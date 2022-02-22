<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('gudang.store') }}"
    enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">{{ _lang('Gudang Name') }}</label>
            <input type="text" class="form-control" name="gudang_name" value="{{ old('gudang_name') }}" required>
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="form-group">
            <a href="{{ route('cabang.create') }}" data-reload="false"
                data-title="{{ _lang('Add Cabang') }}" class="ajax-modal-2 select2-add"><i
                    class="ti-plus"></i> {{ _lang('Add New') }}</a>
            <label class="control-label">{{ _lang('Cabang') }}</label>
            <select class="form-control select2-ajax" data-value="id" data-display="cabang_name"
                data-table="cabang" data-where="1" name="cabang_id" required>
                <option value="">{{ _lang('- Select Cabang -') }}</option>
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save') }}</button>
        </div>
    </div>
</form>