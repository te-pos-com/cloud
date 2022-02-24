@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/plugins/chartJs/Chart.min.css') }}">

@php $currency = currency(); @endphp
@php $permissions = permission_list(); @endphp


<div class="row">
    <div class="col-xl-4 col-md-6">
        @if (in_array('dashboard.current_day_income',$permissions))
            <div class="card mb-5">
                <div class="card-body" style="background-color:#CB09EE;color:#fff;border-radius:7px;">
                    <h6>{{ _lang('PENJUALAN HARI INI') }}</h6>
                    <h6 class="pt-1"><b>{{ decimalPlace($current_day_income, $currency) }}</b></h6>
                </div>
            </div>
        @endif
        @if (in_array('dashboard.current_month_income',$permissions))
            <div class="card mb-5">
                <div class="card-body" style="background-color:#1EB125;color:#fff;border-radius:7px;">
                    <h6>{{ _lang('PENJUALAN PER BULAN') }}</h6>
                    <h6 class="pt-1"><b>{{ decimalPlace($current_month_income, $currency) }}</b></h6>
                </div>
            </div>
        @endif
        @if (in_array('dashboard.monthly_income_vs_expense',$permissions))
        <div class="card mb-5">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Pembelian vs Penjualan')." ".date('M, Y') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="dn_income_expense" width="100%" height="40"></canvas>
            </div>
        </div>
        @endif
    </div>
    @if (in_array('dashboard.yearly_income_vs_expense',$permissions))
    <div class="col-xl-8 col-md-20">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Grafik Transaksi')." ".date('Y') }}</h4>
            </div>
            <div class="card-body">
                <canvas id="yearly_income_expense" width="100%" height="60"></canvas>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row">
    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('5 USER PENJUALAN TERBANYAK') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Nomor') }}</th>
                                <th>{{ 'User' }}</th>
                                <th class="text-right">{{ _lang('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            ?>
                            @foreach($income_user as $transactionuser)
 
                            <tr>
                                <td class='trans_date'>{{$no}}</td>
                                <td class='chart_id'>{{$transactionuser->name}}</td>
                                <td class='amount text-right'>{{ decimalPlace($transactionuser->amount, $currency) }}
                                </td>
                            </tr>
                            <?php $no++;?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('5 BARANG TERLARIS') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ _lang('No') }}</th>
                                <th>{{ _lang('Nama Barang') }}</th>
                                <th class="text-right">{{ _lang('Amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            ?>
                            @foreach($income_produk as $item)
                            <tr>
                                <td>{{ $no }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td class='amount text-right'>{{ decimalPlace($item->amount, $currency) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6">
        @if (in_array('dashboard.latest_income',$permissions))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Last 5 Income') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Date') }}</th>
                                <th>{{ _lang('Type') }}</th>
                                <th class="text-right">{{ _lang('Amount') }}</th>
                                <th class="text-center">{{ _lang('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($latest_income as $transaction)
                            <tr id="row_{{ $transaction->id }}">
                                <td class='trans_date'>{{ $transaction->trans_date }}</td>
                                <td class='chart_id'>
                                    {{ isset($transaction->income_type->name) ? $transaction->income_type->name : _lang('Transfer') }}
                                </td>
                                <td class='amount text-right'>{{ decimalPlace($transaction->amount, $currency) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{action('IncomeController@show', $transaction['id'])}}"
                                        data-title="{{ _lang('View Income') }}"
                                        class="btn btn-light btn-sm ajax-modal">{{ _lang('View Details') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-xl-6">
        @if (in_array('dashboard.latest_expense',$permissions))
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('Last 5 Expense') }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Date') }}</th>
                                <th>{{ _lang('Type') }}</th>
                                <th class="text-right">{{ _lang('Amount') }}</th>
                                <th class="text-center">{{ _lang('Details') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($latest_expense as $expense)
                            <tr id="row_{{ $expense->id }}">
                                <td class='trans_date'>{{ $expense->trans_date }}</td>
                                <td class='chart_id'>
                                    {{ isset($expense->expense_type->name) ? $expense->expense_type->name : _lang('Transfer') }}
                                </td>
                                <td class='amount text-right'>{{ decimalPlace($expense->amount, $currency) }}
                                </td>
                                <td class="text-center">
                                    <a href="{{action('ExpenseController@show', $expense['id'])}}"
                                        data-title="{{ _lang('View Expense') }}"
                                        class="btn btn-light btn-sm ajax-modal">{{ _lang('View Details') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>


@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/chartJs/Chart.min.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('public/backend/assets/js/dashboard_pos.js') }}"></script>
@endsection