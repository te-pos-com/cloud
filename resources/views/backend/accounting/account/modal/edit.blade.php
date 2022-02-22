<form method="post" class="ajax-submit" autocomplete="off" action="{{action('AccountController@update', $id)}}"
    enctype="multipart/form-data">
    {{ csrf_field()}}
    <input name="_method" type="hidden" value="PATCH">

    <div class="row p-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Title') }}</label>
                <input type="text" class="form-control" name="account_title" value="{{ $account->account_title }}"
                    required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Opening Date') }}</label>
                <input type="text" class="form-control datepicker" name="opening_date"
                    value="{{ $account->opening_date }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Number') }}</label>
                <input type="text" class="form-control" name="account_number" value="{{ $account->account_number }}">
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Level') }}</label>
                <select class="form-control select2" name="level">
                    <option value="0"
                    @if ($account->tipe==0)
                        selected="selected"
                    @endif
                    >Level 0</option>
                    <option value="1" 
                    @if ($account->tipe==1)
                        selected="selected"
                    @endif
                    >Level 1</option>
                    <option value="2"
                    @if ($account->tipe==2)
                        selected="selected"
                    @endif
                    >Level 2</option>
                    <option value="3"
                    @if ($account->tipe==3)
                        selected="selected"
                    @endif
                    >Level 3</option>
                    <option value="4"
                    @if ($account->tipe==4)
                        selected="selected"
                    @endif
                    >Level 4</option>
                    <option value="5"
                    @if ($account->tipe==5)
                        selected="selected"
                    @endif
                    >Level 5</option>
                    <option value="6"
                    @if ($account->tipe==6)
                        selected="selected"
                    @endif
                    >Level 6</option>
                </select>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Account Tipe') }}</label>
                <select class="form-control select2" name="tipe">
                    <option value="H"
                    @if ($account->jenis=="H")
                        selected="selected"
                    @endif
                    >Header</option>
                    <option value="D" 
                    @if ($account->jeins=="D")
                        selected="selected"
                    @endif
                    >Detail</option>
                </select>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Opening Balance')." ".currency() }}</label>
                <input type="text" class="form-control float-field" name="opening_balance"
                    value="{{ $account->opening_balance }}" disabled>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Note') }}</label>
                <textarea class="form-control" name="note">{{ $account->note }}</textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                    {{ _lang('Save Changes') }}</button>
            </div>
        </div>
    </div>
</form>