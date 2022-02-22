@extends('layouts.app')

@section('content')
<div class="row">
    @php $currency = currency(get_option('currency','USD')); @endphp
    <div class="col-xl-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ _lang('Total Users') }}</h5>
                <h6 class="pt-1"><b>{{ $total_user }}</b></h6>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ _lang('Trail Users') }}</h5>
                <h6 class="pt-1"><b>{{ $trail_user }}</b></h6>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ _lang('Paid Users') }}</h5>
                <h6 class="pt-1"><b>{{ $paid_user }}</b></h6>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5>{{ _lang('Total Payment') }}</h5>
                <h6 class="pt-1"><b>{{ decimalPlace($total_payment, $currency) }}</b></h6>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        {{ _lang('New Registered Users') }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ _lang('Name') }}</th>
                        <th>{{ _lang('Email') }}</th>
                        <th>{{ _lang('Membership Type') }}</th>
                        <th>{{ _lang('Valid Until') }}</th>
                        <th class="text-center">{{ _lang('Details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_users as $user)
                    <tr id="row_{{ $user->id }}">
                        <td class='name'>
                            <div class="media">
                                <img src="{{ profile_picture($user->profile_picture) }}" alt="avatar"
                                    class="thumb-sm rounded-circle mr-2">
                                <div class="media-body align-self-center text-truncate">
                                    <h6 class="my-0 text-dark">{{ _lang('USER ID') }} - #{{ $user->id }}</h6>
                                    <p class="text-muted mb-0">{{ $user->name }}</p>
                                </div>
                                <!--end media-body-->
                            </div>
                        </td>
                        <td class='email'>{{ $user->email }}</td>
                        <td class='membership_type'>{{ strtoupper($user->membership_type) }}</td>
                        <td class='valid_to'>{{ $user->validUntil() }}</td>
                        <td class="text-center">
                            <a href="{{ action('UserController@show', $user['id'])}}" data-title="{{ $user->name }}"
                                class="btn btn-secondary btn-sm ajax-modal">{{ _lang('View') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        {{ _lang('Recent Payments') }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ _lang('Date') }}</th>
                        <th>{{ _lang('Name') }}</th>
                        <th>{{ _lang('Method') }}</th>
                        <th class="text-right">{{ _lang('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recent_payments as $history)
                    <tr>
                        <td>{{ $history->created_at }}</td>
                        <td>{{ isset($history->user->name)? $history->user->name : '' }}</td>
                        <td>{{ $history->method }}</td>
                        <td class="text-right"><b>{{ decimalPlace($history->amount, $currency) }}</b></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection