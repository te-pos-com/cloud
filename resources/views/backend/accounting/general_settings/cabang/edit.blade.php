@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Update Cabang') }}</h4>
            </div>

            <div class="card-body">
                <div class="col-md-6">
                    <form method="post" class="validate" autocomplete="off"
                        action="{{ action('CabangController@update', $id) }}" enctype="multipart/form-data">
                        {{ csrf_field()}}
                        <input name="_method" type="hidden" value="PATCH">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang Name') }}</label>
                                    <input type="text" class="form-control" name="cabang_name"
                                        value="{{ $cabang->cabang_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang Phone') }}</label>
                                    <input type="text" class="form-control" name="cabang_phone"
                                        value="{{ $cabang->cabang_phone }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang Email') }}</label>
                                    <input type="text" class="form-control" name="cabang_email"
                                        value="{{ $cabang->cabang_email }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Cabang Alamat') }}</label>
                                    <input type="text" class="form-control" name="cabang_alamat"
                                        value="{{ $cabang->cabang_alamat }}" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i>
                                        {{ _lang('Update') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection