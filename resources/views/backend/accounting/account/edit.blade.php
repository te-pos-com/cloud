@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Update Account') }}</h4>
            </div>

            <div class="card-body">
                <form method="post" class="validate" autocomplete="off"
                    action="{{action('AccountController@update', $id)}}" enctype="multipart/form-data">
                    {{ csrf_field()}}
                    <input name="_method" type="hidden" value="PATCH">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Account Title') }}</label>
                                        <input type="text" class="form-control" name="account_title"
                                            value="{{ $account->account_title }}" required>
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
                                        <input type="text" class="form-control" name="account_number"
                                            value="{{ $account->account_number }}">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label
                                            class="control-label">{{ _lang('Opening Balance')." ".currency() }}</label>
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
                                        <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save Changes') }}</button>
                                    </div>
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