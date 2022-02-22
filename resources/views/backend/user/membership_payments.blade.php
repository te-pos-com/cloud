@extends('layouts.app')

@section('content')
<h4 class="page-title">{{ _lang('Membership Payment History') }}</h4>

<div class="row">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header d-flex align-items-center">
                <h4 class="header-title">{{ _lang('Membership Payments') }}</h4>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="users_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('User') }}</th>
                                <th>{{ _lang('Extend') }}</th>
                                <th>{{ _lang('Method') }}</th>
                                <th>{{ _lang('Amount') }}</th>
                                <th>{{ _lang('Date') }}</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $currency = currency(get_option('currency','USD')); @endphp
                            @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ $payment->title }}</td>
                                <td>{{ $payment->method }}</td>
                                <td>{{ decimalPlace($payment->amount, $currency) }}</td>
                                <td>{{ $payment->created_at }}</td>
                                <td>{{ $payment->status }}</td>
                                @if ($payment->status=='paid')
                                    <td></td>
                                @elseif ($payment->status=='pending')
                                    <td>
                                        <a href="{{ action('MembershipController@transfer_payment_authorize', $payment->id) }}" class="btn btn-primary btn-block">Aproved</a>
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection