@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
				<h4 class="header-title">{{ _lang('Quotation List') }}</h4>
                <a class="btn btn-primary btn-sm ml-auto"
                    href="{{ route('quotations.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="quotation-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('Quotation Number') }}</th>
                            <th>{{ _lang('Client') }}</th>
                            <th>{{ _lang('Quotation Date') }}</th>
                            <th class="text-right">{{ _lang('Grand Total') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/datatables/quotation-table.js?v=1.1') }}"></script>
@endsection