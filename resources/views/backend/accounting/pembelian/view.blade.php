@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/invoice.css') }}">

<div class="row">
    <div class="col-12">
        <div class="btn-group group-buttons">
            <a class="btn btn-primary btn-sm print" href="#" data-print="invoice-view"><i class="ti-printer"></i>
                {{ _lang('Print') }}</a>
            <a class="btn btn-danger btn-sm" href="{{ route('pembelian.download_pdf', $pembelian->id) }}"><i
                    class="ti-file"></i> {{ _lang('Export PDF') }}</a>
            @if($pembelian->payment_status != 1)
            <a class="btn btn-success btn-sm ajax-modal" data-title="{{ _lang('Make Payment') }}"
                href="{{ route('pembelian.create_payment', $pembelian->id) }}"><i class="ti-credit-card"></i>
                {{ _lang('Record a Payment') }}</a>
            @else
            <button class="btn btn-success btn-sm" disabled><i class="ti-receipt"></i>
                {{ _lang('PAID') }}</button>
            @endif
            <a class="btn btn-warning btn-sm" href="{{ action('PembelianController@edit', $pembelian->id) }}"><i
                    class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
        </div>

        @php $date_format = get_company_option('date_format','Y-m-d'); @endphp

        <div class="card clearfix">

            <span class="panel-title d-none">{{ _lang('Purchase') }}</span>

            <div class="card-body">
                <div id="invoice-view">
                    <table class="classic-table">
                        <tbody>
                            <tr>
                                <td>
                                    <h4><b>{{ get_company_option('company_name') }}</b></h4>
                                    {{ get_company_option('address') }}<br>
                                    {{ get_company_option('email') }}<br>
                                    {!! get_company_option('vat_id') != '' ? _lang('VAT ID').':
                                    '.xss_clean(get_company_option('vat_id')).'<br>' : '' !!}
                                </td>
                                <td>
                                    <img src="{{ get_company_logo() }}" class="mh-80">
                                </td>
                            </tr>

                            <tr class="information">
                                <td class="pt-4">
                                    <h5><b>{{ _lang('Supplier Details') }}</b></h5>
                                    @if(isset($pembelian->supplier))
                                    <b>{{ _lang('Name') }}</b> : {{ $pembelian->supplier->supplier_name }}<br>
                                    <b>{{ _lang('Email') }}</b> : {{ $pembelian->supplier->email }}<br>
                                    <b>{{ _lang('Phone') }}</b> : {{ $pembelian->supplier->phone }}<br>
                                    <b>{{ _lang('VAT Number') }}</b> :
                                    {{ $pembelian->supplier->vat_number == '' ? _lang('N/A') : $pembelian->supplier->vat_number }}<br>
                                    @endif
                                </td>
                                <td class="auto-column pt-4">
                                    <h5><b>{{ _lang('Purchase') }}</b></h5>
                                    <b>{{ _lang('No Pembelian') }} #:</b> {{ $pembelian->invoice_number }}<br>
                                    <b>{{ _lang('Tanggal') }}:</b> {{ $pembelian->order_date }}<br>
                                    <b>{{ _lang('Payment') }}:</b>
                                    @if($pembelian->payment_status == 0)
                                    <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span>
                                    @else
                                    <span class="badge badge-success">{{ _lang('Lunas') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--End Invoice Information-->

                    @php $currency = currency(); @endphp

                    <!--Invoice Product-->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-2" id="invoice-item-table">
                            <thead>
                                <tr>
                                    <th width="30%">{{ _lang('Name') }}</th>
                                    <th class="text-center wp-100">{{ _lang('Quantity') }}</th>
                                    <th class="text-right">{{ _lang('Unit Cost') }}</th>
                                    <th class="text-right wp-100">{{ _lang('Discount')}}</th>
                                    <th>{{ _lang('Tax') }}</th>
                                    <th class="text-right">{{ _lang('Sub Total') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($pembelian->pembelian_items as $item)
                                <tr id="product-{{ $item->product_id }}">
                                    <td>
                                        <b>{{ $item->item->item_name }}</b><br>
                                        {{ $item->description }}
                                    </td>
                                    <td class="text-center quantity">{{ $item->quantity }}</td>
                                    <td class="text-right unit-cost">{{ decimalPlace($item->unit_cost, $currency) }}
                                    </td>
                                    <td class="text-right discount">{{ decimalPlace($item->discount, $currency) }}</td>
                                    <td>{!! xss_clean(object_to_tax($item->taxes, 'name')) !!}</td>
                                    <td class="text-right sub-total">{{ decimalPlace($item->sub_total, $currency) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--End Invoice Product-->


                    <!--Summary Table-->
                    <div class="invoice-summary-right">
                        <table class="table table-bordered" id="invoice-summary-table">
                            <tbody>
                                <tr>
                                    <td>{{ _lang('Sub Total') }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace($pembelian->product_total, $currency) }}</span>
                                    </td>
                                </tr>
                                @foreach($pembelian_taxes as $tax)
                                <tr>
                                    <td>{{ $tax->name }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace($tax->tax_amount, $currency) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td>{{ _lang('Shipping Cost') }}</td>
                                    <td class="text-right">
                                        <span>+ {{ decimalPlace($pembelian->shipping_cost, $currency) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Discount') }}</td>
                                    <td class="text-right">
                                        <span>- {{ decimalPlace($pembelian->order_discount, $currency) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>{{ _lang('Grand Total') }}</b></td>
                                    <td class="text-right">
                                        <b>{{ decimalPlace($pembelian->grand_total, $currency) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Total Paid') }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace($pembelian->paid, $currency) }}</span>
                                    </td>
                                </tr>
                                @if($pembelian->payment_status == 0)
                                <tr>
                                    <td>{{ _lang('Amount Due') }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace(($pembelian->grand_total - $pembelian->paid), $currency) }}</span>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--End Summary Table-->

                    <div class="clearfix"></div>

                    <!--Related Transaction-->
                    @if( ! $transactions->isEmpty() )
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoice-payment-history-table">
                            <thead class="base_color">
                                <tr>
                                    <td colspan="7" class="text-center"><b>{{ _lang('Payment History') }}</b></td>
                                </tr>
                                <tr>
                                    <th>{{ _lang('Date') }}</th>
                                    <th>{{ _lang('Account') }}</th>
                                    <th class="text-right">{{ _lang('Amount') }}</th>
                                    <th>{{ _lang('Payment Method') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr id="transaction-{{ $transaction->id }}">
                                    <td>{{ date($date_format, strtotime($transaction->trans_date)) }}</td>
                                    <td>{{ $transaction->account->account_title.' - '.$transaction->account->account_currency }}
                                    </td>

                                    <td class="text-right">{{ decimalPlace($transaction->amount, $currency) }}</td>
                                    <td>{{ $transaction->payment_method->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <!--END Related Transaction-->

                    <!--Invoice Note-->
                    @if($pembelian->note != '')
                    <div class="invoice-note border-top pt-4">{{ $pembelian->note }}</div>
                    @endif
                    <!--End Invoice Note-->

                </div>
            </div>
        </div>
    </div>
    <!--End Classic Invoice Column-->
</div>
<!--End Classic Invoice Row-->
@endsection