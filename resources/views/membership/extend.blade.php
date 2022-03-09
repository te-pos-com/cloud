@extends('layouts.public')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card my-5">
                @if (\Session::has('message'))
                <div class="alert alert-danger text-center">
                    <b>{{ \Session::get('message') }}</b>
                </div>
                @endif
                <div class="card-header text-center">
                    {{ _lang('Extend Membership') }}
                </div>

                <div class="card-body" id="extend_membership">
                    @if(! isset($payment_id))
                    <form method="POST" class="form-signin" action="{{ route('membership.pay') }}">
                        @csrf
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Extend Monthly/Yearly') }} {{cabang_aktif()}} Cabang Aktif</label>
                                <select class="form-control" name="extend_type" id="extend_type" required>
                                    <option value="yearly">
                                        {{ _lang('Yearly').' / '.currency( get_option('currency','USD') ).' '.number_format(get_option('yearly_cost')*cabang_aktif()) }}
                                    </option>
                                    <option value="montly">
                                        {{ _lang('Monthly').' / '.currency( get_option('currency','USD') ).' '.number_format(get_option('monthly_cost')*cabang_aktif()) }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="year-field">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Select Year') }}</label>
                                <select class="form-control" name="year" required>
                                    <option value="1">{{ _lang('1 Year') }}</option>
                                    <option value="2">{{ _lang('2 Year') }}</option>
                                    <option value="3">{{ _lang('3 Year') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" id="month-field" style="display:none;">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Select Month') }}</label>
                                <select class="form-control" name="month" required>
                                    @for ($i = 1; $i < 12; $i++) <option value="{{ $i }}">{{ $i." "._lang('Month') }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Payment Gateway') }}</label>
                                <select class="form-control" name="gateway" id="gateway" required>
                                    @if (get_option('paypal_active') == 'Yes')
                                    <option value="PayPal">{{ _lang('PayPal') }}</option>
                                    @endif
                                    @if (get_option('stripe_active') == 'Yes')
                                    <option value="Stripe">{{ _lang('Stripe') }}</option>
                                    @endif
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary btn-block">{{ _lang('Process') }}</button>
                            </div>
                        </div>
                    </form>
                    @else
                    <table class="table">
                        <thead>
							<th>{{ _lang('Extend') }}</th>
							<th>{{ _lang('Cost') }}</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $title }}</td>
                                <td>{{ currency(get_option('currency','USD')).' '.decimalPlace($amount) }}</td>
                            </tr>
							<tr>
								<td colspan="2">
									@if($payment->method == 'PayPal')
                                        @include('membership.gateway.paypal')
                                    @elseif($payment->method == 'Stripe')
                                        @include('membership.gateway.stripe')
                                    @elseif($payment->method == 'Transfer')
                                        @include('membership.gateway.transfer')
                                    @endif
								</td>
							</tr>
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card my-5">
                <img src="{{url('')}}/public/uploads/icon/2903544.jpg" width="100%" height="390px">
            </div>
        </div>
    </div>

@section('js-script')
<script>
(function($) {
    "use strict";

    $(document).on('change', '#extend_type', function() {
        if ($(this).val() == "montly") {
            $("#year-field").css("display", "none");
            $("#month-field").fadeIn(500);
        } else {
            $("#month-field").css("display", "none");
            $("#year-field").fadeIn(500);
        }
    });

})(jQuery);
</script>
@endsection