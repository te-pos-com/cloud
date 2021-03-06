@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-default">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Update Gudang') }}</h4>
            </div>

            <div class="card-body">
                <div class="col-md-6">
                    <form method="post" class="validate" autocomplete="off"
                        action="{{ action('GudangController@update', $id) }}" enctype="multipart/form-data">
                        {{ csrf_field()}}
                        <input name="_method" type="hidden" value="PATCH">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Gudang Name') }}</label>
                                    <input type="text" class="form-control" name="gudang_name"
                                        value="{{ $cabang->gudang_name }}" required>
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