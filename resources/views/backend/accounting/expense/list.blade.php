@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="header-title">{{ _lang('List Expense') }}</span>
                @if (jenis_langganan()=="POS")
                @elseif (jenis_langganan()=="TRADING")
                @else
                    <a class="btn btn-primary btn-sm ml-auto ajax-modal" data-title="{{ _lang('Add Expense') }}"
                        href="{{ route('expense.create') }}"><i class="ti-plus"></i> {{ _lang('Add New') }}</a>
                @endif
            </div>

            <div class="card-body">
                <table id="expense-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ _lang('Date') }}</th>
                            @if (jenis_langganan()=="POS" || jenis_langganan()=="TRADING")
                            @else
                                <th>{{ _lang('Account') }}</th>
                            @endif
                            <th>{{ _lang('Expense Type') }}</th>
                            <th class="text-right">{{ _lang('Amount') }}</th>
                            <th>{{ _lang('Payee') }}</th>
                            <th>{{ _lang('Payment Method') }}</th>
                            <th class="action-col">{{ _lang('Action') }}</th>
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
@if (jenis_langganan()=="POS" || jenis_langganan()=="TRADING")
    <script src="{{ asset('public/backend/assets/js/datatables/expense-pos-table.js?v=1.1') }}"></script>
@else
    <script src="{{ asset('public/backend/assets/js/datatables/expense-table.js?v=1.1') }}"></script>
@endif
@endsection