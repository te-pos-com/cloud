@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Add Product Kategori') }}</h4>
            </div>

            <div class="card-body">
                <div class="col-md-6">
                    <form method="post" class="validate" autocomplete="off" action="{{ route('product_kategori.store') }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Kategori Name') }}</label>
                                    <input type="text" class="form-control" name="kategori_name"
                                        value="{{ old('kategori_name') }}" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="ti-save"></i> {{ _lang('Save') }}</button>
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