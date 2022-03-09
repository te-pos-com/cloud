@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/invoice.css') }}">

<div class="row">
    <div class="col-md-12">
        <div class="btn-group pull-right">
            <a class="btn btn-info btn-sm ajax-modal" data-title="{{ _lang('Send Email') }}"
                href="{{ route('invoices.send_email',$invoice->id) }}"><i class="ti-email"></i>
                {{ _lang('Send Email') }}</a>
            <a class="btn btn-primary btn-sm print" href="#" data-print="invoice-view"><i class="ti-printer"></i>
                {{ _lang('Print A4') }}</a>
            <a class="btn btn-primary btn-sm" href="#" onClick="printReceiptSmCloseOutlet()"><i class="ti-printer"></i>
                {{ _lang('Print Struk') }}</a>
            <a class="btn btn-primary btn-sm" href="#" onClick="printReceiptLg()"><i class="ti-printer"></i>
                {{ _lang('Print Nota') }}</a>
            <a class="btn btn-primary btn-sm" href="#" onClick="suratjalan()"><i class="ti-printer"></i>
                {{ _lang('Print Surat Jalan') }}</a>
            <a class="btn btn-danger btn-sm" href="{{ route('invoices.download_pdf',$invoice->id) }}"><i
                    class="ti-file"></i> {{ _lang('Export PDF') }}</a>
            @if($invoice->status != 'Paid')
            <a class="btn btn-success btn-sm ajax-modal" data-title="{{ _lang('Make Payment') }}"
                href="{{ route('invoices.create_payment',$invoice->id) }}"><i class="ti-credit-card"></i>
                {{ _lang('Make Payment') }}</a>
            <a class="btn btn-warning btn-sm" href="{{ action('InvoiceController@edit', $invoice->id) }}"><i
                class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
            @endif   

        </div>
        <div class="card">
            <div class="card-body">

                <div id="invoice-view">
                    <div class="table-responsive">
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
                                        <h5><b>{{ _lang('Invoice To') }}</b></h5>
                                        @if(isset($invoice->client))
                                        {{ $invoice->client->contact_name }}<br>
                                        {{ $invoice->client->contact_email }}<br>
                                        {!! $invoice->client->company_name != '' ?
                                        xss_clean($invoice->client->company_name).'<br>' : '' !!}
                                        {!! $invoice->client->address != '' ?
                                        xss_clean($invoice->client->address).'<br>' : '' !!}
                                        @endif
                                    </td>
                                    <td class="auto-column pt-4">
                                        <h5><b>{{ _lang('Invoice Details') }}</b></h5>

                                        <b>{{ _lang('Invoice') }} #:</b> {{ $invoice->invoice_number }}<br>

                                        <b>{{ _lang('Invoice Date') }}:</b>
                                        {{ $invoice->invoice_date }}<br>
                                        @if (jenis_langganan()=="POS")
                                        @elseif (jenis_langganan()=="TRADING")
                                            <b>{{ _lang('Due Date') }}:</b>
                                            {{ $invoice->due_date }}<br>
                                        @else
                                            <b>{{ _lang('Due Date') }}:</b>
                                            {{ $invoice->due_date }}<br>
                                        @endif
                                        <b>{{ _lang('Payment Status') }}:</b>
                                        @if ($invoice->status=="Paid")                                        
                                            <span class="badge badge-success">{{ _lang('Lunas') }}</span><br>
                                        @else
                                            <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span><br>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--End Invoice Information-->

                    @php $currency = currency(); @endphp

                    <!--Invoice Product-->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-2" id="invoice-item-table">
                            <thead class="base_color">
                                <tr>
                                    <th>{{ _lang('Name') }}</th>
                                    <th class="text-center wp-100">{{ _lang('Quantity') }}</th>
                                    <th class="text-right">{{ _lang('Unit Cost') }}</th>
                                    <th class="text-right wp-100">{{ _lang('Discount') }}</th>
                                    <th class="text-right">{{ _lang('Tax') }}</th>
                                    <th class="text-right">{{ _lang('Sub Total') }}</th>
                                </tr>
                            </thead>
                            <tbody id="invoice">
                                @foreach($invoice->invoice_items as $item)
                                <tr id="product-{{ $item->item_id }}">
                                    <td>
                                        <b>{{ $item->item->item_name }}</b><br>{{ $item->description }}
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} {{$item->item->product->units->unit_name }}</td>
                                    <td class="text-right">{{ decimalPlace($item->unit_cost, $currency) }}</td>
                                    <td class="text-right">{{ decimalPlace($item->discount, $currency) }}</td>
                                    <td class="text-right">{!! xss_clean(object_to_tax($item->taxes, 'name')) !!}</td>
                                    <td class="text-right">{{ decimalPlace($item->sub_total, $currency) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!--End Invoice Product-->

                    <!--Summary Table-->
                    <div class="invoice-summary-right">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="invoice-summary-table">
                                <tbody>
                                    <tr>
                                        <td><b>{{ _lang('Sub Total') }}</b></td>
                                        <td class="text-right">
                                            <b>{{ decimalPlace($invoice->grand_total - $invoice->tax_total, $currency) }}</b>
                                        </td>
                                    </tr>
                                    @foreach($invoice_taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td class="text-right">
                                            <span>{{ decimalPlace($tax->tax_amount, $currency) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ _lang('Grand Total') }}</td>
                                        <td class="text-right">{{ decimalPlace($invoice->grand_total, $currency) }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('Total Paid') }}</td>
                                        <td class="text-right">{{ decimalPlace($invoice->paid, $currency) }}</td>
                                    </tr>
                                    @if($invoice->status != 'Paid')
                                    <tr>
                                        <td>{{ _lang('Amount Due') }}</td>
                                        <td class="text-right">
                                            {{ decimalPlace($invoice->grand_total - $invoice->paid, $currency) }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--End Summary Table-->

                    <div class="clearfix"></div>

                    <!--Related Transaction-->
                    @if( ! $transactions->isEmpty() )
                    <div class="table-responsive">
                        <table class="table table-bordered" id="invoice-payment-history-table">
                            <thead class="base_color">
                                <tr>
                                    <td colspan="4" class="text-center"><b>{{ _lang('Payment History') }}</b></td>
                                </tr>
                                <tr>
                                    <th>{{ _lang('Date') }}</th>
                                    <th class="text-right">{{ _lang('Amount') }}</th>
                                    <th>{{ _lang('Payment Method') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr id="transaction-{{ $transaction->id }}">
                                    <td>{{ $transaction->trans_date }}</td>
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
                    @if($invoice->note != '')
                    <div class="invoice-note">{!! xss_clean($invoice->note) !!}</div>
                    @endif
                    <!--End Invoice Note-->

                    <!--Invoice Footer Text-->
                    @if(get_company_option('invoice_footer') != '')
                    <div class="invoice-note">{!! xss_clean(get_company_option('invoice_footer')) !!}</div>
                    @endif
                    <!--End Invoice Note-->
                </div>
                <!--End Invoice View-->

            </div>
        </div>
    </div>


    <div id="ticket-print-close_struk" class="ticket" style="display:none">
            <img
                alt="Logo" src="{{ get_company_logo() }}" class="" style="width: 50px; height: auto" />
                <p class="text-left">
                <table>
                    <tr>
                        <td><b>Dari :</b></td>
                    </tr>
                    <tr>
                        <td>
                            <b>{{ get_company_option('company_name') }}</b><br>
                            {{ get_company_option('address') }}<br>
                            {{ get_company_option('email') }}<br>
                            {!! get_company_option('vat_id') != '' ? _lang('VAT ID').':
                            '.xss_clean(get_company_option('vat_id')).'<br>' : '' !!}
                        </td>
                    </tr>

                    <tr>
                        <td><br><br><b>Kepada :</b></td>
                    </tr>
                    <tr>
                        <td class="pt-4">
                            {{ _lang('Name') }} : {{ $invoice->client->contact_name }}<br>
                            {{ _lang('Email') }} : {{ $invoice->client->contact_email }}<br>
                            {{ _lang('Phone') }} : {!! $invoice->client->company_name != '' ?
                                        xss_clean($invoice->client->company_name).'<br>' : '' !!}
                                        {!! $invoice->client->address != '' ?
                                        xss_clean($invoice->client->address).'<br>' : '' !!}<br>
                            <br>
                            {{ _lang('No Invoice') }} #: {{ $invoice->invoice_number }}<br>
                            {{ _lang('Tanggal') }}: {{ $invoice->invoice_date }}<br>
                            {{ _lang('Payment') }}:
                            @if($invoice->payment_status == 0)
                            <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span>
                            @else
                            <span class="badge badge-success">{{ _lang('Lunas') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </p>
           

            <div style="width: 100%">
                <table>
                    <tr>
                        <td style="width: 40%">Item</td>
                        <td style="width: 5%">Qty</td>
                        <td class="text-right" style="width: 50%">Total</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    @foreach($invoice->invoice_items as $item)
                    <tr>
                        <td style="width: 60%">{{ $item->item->item_name }}</td>
                        <td style="width: 5%">{{ $item->quantity }} {{$item->item->product->units->unit_name }}</td>
                        <td align="right" style="width: 35%">
                            {{ decimalPlace($item->sub_total, $currency) }}
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="3">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 60%">{{ _lang('Sub Total') }}</td>
                        <td style="width: 5%"></td>
                        <td align="right" style="width: 35%">
                            {{ decimalPlace($invoice->product_total, $currency) }}
                        </td>
                    </tr>
                    @foreach($invoice_taxes as $tax)
                    <tr>
                        <td style="width: 60%">{{ $tax->name }}</td>
                        <td style="width: 5%"></td>
                        <td align="right" style="width: 35%">
                            {{ decimalPlace($tax->tax_amount, $currency) }}
                        </td>

                    </tr>
                    @endforeach
                    <tr>

                        <td style="width: 60%">{{ _lang('Grand Total') }}</td>
                        <td style="width: 5%"></td>
                        <td align="right" style="width: 35%">
                            <b>{{ decimalPlace($invoice->grand_total, $currency) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 60%">{{ _lang('Total Paid') }}</td>
                        <td style="width: 10%"></td>
                        <td align="right" style="width: 35%">
                            <span>{{ decimalPlace($invoice->paid, $currency) }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


    <div id="ticket-print-lg" class="ticket" style="display:none">
                <table style="width:100%">
                    <tr>
                        <td>
                            <h4><b>FAKTUR PENJUALAN</b></h4>
                            <b>{{ get_company_option('company_name') }}</b><br>
                            {{ get_company_option('address') }}<br>
                            {{ get_company_option('email') }}<br>
                            {!! get_company_option('vat_id') != '' ? _lang('VAT ID').':
                            '.xss_clean(get_company_option('vat_id')).'<br>' : '' !!}
                        </td>
                        <td>
                            <img src="{{ get_company_logo() }}" style="width:80px">
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            <b>Pelanggan : </b><br>
                            {{ _lang('Name') }} : {{ $invoice->client->contact_name }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('No Invoice') }} #: {{ $invoice->invoice_number }}<br>
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-6">
                            {{ _lang('Email') }} : {{ $invoice->client->contact_email }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Tanggal') }}: {{ $invoice->invoice_date }}<br>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            {{ _lang('Phone') }} : {{ $invoice->client->address }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Payment') }}:
                            @if($invoice->payment_status == 0)
                            <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span>
                            @else
                            <span class="badge badge-success">{{ _lang('Lunas') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </p>
           

            <div style="width: 100%">
                <table style="width: 100%">
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">Item</td>
                        <td style="width: 15%">Gudang</td>
                        <td style="width: 9%">Qty</td>
                        <td align="right"style="width: 16%">Harga</td>
                        <td align="right" class="text-right" style="width: 30%">Total</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    @php
                        $datadetail = 0;
                        $detail = 12;
                    @endphp
                    @foreach($invoice->invoice_items as $item)
                    <tr>
                        <td style="width: 30%">{{ $item->item->item_name }}</td>
                        <td style="width: 15%">{{ $item->gudang->gudang_name}}</td>
                        <td style="width: 9%">{{ $item->quantity }} {{$item->item->product->units->unit_name }}</td>
                        <td align="right" style="width: 16%">{{ decimalPlace($item->unit_cost, $currency) }}</td>
                        <td align="right" style="width: 305%">
                            {{ decimalPlace($item->sub_total, $currency) }}
                        </td>
                    </tr>
                    @php
                        $datadetail  = $datadetail+1;
                    @endphp
                    @endforeach
                    @if ($detail>$datadetail)
                        @for($x=0;$x<($detail-$datadetail);$x++)
                        <tr>
                            <td style="width: 30%">&emsp;</td>
                            <td style="width: 15%"></td>
                            <td style="width: 9%"></td>
                            <td align="right" style="width: 16%"></td>
                            <td align="right" style="width: 305%"><td>
                        </tr>
                        @endfor
                    @endif
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 30%">{{ _lang('Sub Total') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($invoice->product_total, $currency) }}
                        </td>
                    </tr>
                    @foreach($invoice_taxes as $tax)
                    <tr>
                        <td style="width: 30%">{{ $tax->name }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($tax->tax_amount, $currency) }}
                        </td>

                    </tr>
                    @endforeach
                    <tr>

                        <td style="width: 30%">{{ _lang('Grand Total') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <b>{{ decimalPlace($invoice->grand_total, $currency) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">{{ _lang('Total Paid') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace($invoice->paid, $currency) }}</span>
                        </td>
                    </tr>
                    @if($invoice->status != 'Paid')
                    <tr>
                        <td style="width: 30%">{{ _lang('Amount Due') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace($invoice->grand_total - $invoice->paid, $currency) }}</span>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td style="width: 30%">&emsp;</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%"></td>
                    </tr>
                    <tr>
                        <td align="center" style="width: 30%">Penerima</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="center" style="width: 30%">Hormat Kami</td>
                    </tr>
                    <tr>
                        <td style="width: 30%">&emsp;</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%"></td>
                    </tr>
                    <tr>
                        <td align="center" style="width: 30%">(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="center" style="width: 30%">(&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;)</td>
                    </tr>               
                </table>
            </div>
        </div>
    </div>


    <div id="suratjalan-lg" class="ticket" style="display:none">
                <table style="width:100%">
                    <tr>
                        <td>
                            <h4><b>SURAT JALAN</b></h4>
                            <b>{{ get_company_option('company_name') }}</b><br>
                            {{ get_company_option('address') }}<br>
                            {{ get_company_option('email') }}<br>
                            {!! get_company_option('vat_id') != '' ? _lang('VAT ID').':
                            '.xss_clean(get_company_option('vat_id')).'<br>' : '' !!}
                        </td>
                        <td>
                            <img src="{{ get_company_logo() }}" style="width:80px">
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            <b>Pelanggan : </b><br>
                            {{ _lang('Name') }} : {{ $invoice->client->contact_name }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('No Invoice') }} #: {{ $invoice->invoice_number }}<br>
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-6">
                            {{ _lang('Email') }} : {{ $invoice->client->contact_email }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Tanggal') }}: {{ $invoice->invoice_date }}<br>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            {{ _lang('Phone') }} : {{ $invoice->client->address }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Payment') }}:
                            @if($invoice->payment_status == 0)
                            <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span>
                            @else
                            <span class="badge badge-success">{{ _lang('Lunas') }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </p>
           

            <div style="width: 100%">
                <table style="width: 100%">
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 70%">Item</td>
                        <td style="width: 15%">Gudang</td>
                        <td style="width: 15%">Qty</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    @php
                        $datadetail = 0;
                        $detail = 13;
                        $totalqty = 0;
                    @endphp
                    @foreach($invoice->invoice_items as $item)
                    <tr>
                        <td style="width: 70%">{{ $item->item->item_name }}</td>
                        <td style="width: 15%">{{ $item->gudang->gudang_name}}</td>
                        <td style="width: 15%">{{ $item->quantity }} {{$item->item->product->units->unit_name }}</td>
                        </td>
                    </tr>
                    @php
                        $datadetail  = $datadetail+1;
                        $totalqty = $totalqty+ $item->quantity;
                    @endphp
                    @endforeach
                    @if ($detail>$datadetail)
                        @for($x=0;$x<($detail-$datadetail);$x++)
                        <tr>
                            <td style="width: 70%">&emsp;</td>
                            <td style="width: 15%"></td>
                            <td style="width: 15%"></td>
                        </tr>
                        @endfor
                    @endif
                    <tr>
                        <td colspan="5">
                            <div style="width: 100%; border-top-style: solid;"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style="width: 70%">Total</td>
                        <td style="width: 15%"></td>
                        <td style="width: 15%">{{decimalPlace($totalqty)}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection


@section('js-script')
<script>
    function printReceiptSmCloseOutlet() {
      const prtHtml = document.getElementById("ticket-print-close_struk").innerHTML;
      const WinPrint = window.open(
        "",
        "",
        "left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0"
      );

      WinPrint.document.write(`<!DOCTYPE html>
        <html>
          <head>
          <style>
          body {
            font-family: Avenir, Helvetica, Arial, sans-serif;
          }
          td,
          th,
          tr,
          table {
            /*border: 1px solid black;*/
            border-collapse: collapse;
            width: 100%;
          }

          td.description {
            text-align: left;
          }

          td.quantity {
            text-align: left;
          }

          td.subtotal {
            text-align: right;
          }

          .centered {
            text-align: center;
            align-content: center;
          }

          .ticket {
            width: 280px;
            max-width: 280px;
          }

          img {
            max-width: inherit;
            width: inherit;
          }
          .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
            height: 110px;
            width: auto;
          }
          </style>
          </head>
          <body style="background-color:white;">
            ${prtHtml}
          </body>
        </html>`);

      WinPrint.document.close();
      WinPrint.focus();
      WinPrint.print();
      setTimeout(() => WinPrint.close(), 1000);

      return true;
    }
    // cetak struk big
    function printReceiptLg(){
      const prtHtml = document.getElementById('ticket-print-lg').innerHTML;

        // Open the print window
        const WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

        WinPrint.document.write(`<!DOCTYPE html>
        <html>
          <head>
          <style>
          body {
            font-family: Avenir, Helvetica, Arial, sans-serif;
          }
          </style>
          </head>
          <body style="background-color:white;">
            ${prtHtml}
          </body>
        </html>`);

        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
        setTimeout(() => WinPrint.close(), 1000);

        return true;
    }
        // cetak struk big
    function suratjalan(){
      const prtHtml = document.getElementById('suratjalan-lg').innerHTML;

        // Open the print window
        const WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');

        WinPrint.document.write(`<!DOCTYPE html>
        <html>
          <head>
          <style>
          body {
            font-family: Avenir, Helvetica, Arial, sans-serif;
          }
          </style>
          </head>
          <body style="background-color:white;">
            ${prtHtml}
          </body>
        </html>`);

        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print();
        WinPrint.close();
        setTimeout(() => WinPrint.close(), 1000);

        return true;
    }
</script>
@endsection