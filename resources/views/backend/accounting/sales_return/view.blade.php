@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="btn-group group-buttons">
            <a class="btn btn-primary btn-xs print" href="#" data-print="invoice-view"><i class="ti-printer"></i>
                {{ _lang('Print A4') }}</a>
            <a class="btn btn-primary btn-sm" href="#" onClick="printReceiptLg()"><i class="ti-printer"></i>
                {{ _lang('Print Nota') }}</a>
            @if($sales_return->payment_status != 1)
                <a class="btn btn-success btn-sm ajax-modal" data-title="{{ _lang('Make Payment') }}"
                href="{{ route('sales_return.create_payment', $sales_return->id) }}"><i class="ti-credit-card"></i>
				{{ _lang('Record a Payment') }}</a>
			    <a class="btn btn-warning btn-sm" href="{{ action('SalesReturnController@edit', $sales_return->id) }}"><i class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
            @else
                <button class="btn btn-success btn-sm" disabled><i class="ti-receipt"></i>
                {{ _lang('PAID') }}</button>
            @endif
        </div>

        <div class="card">

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
                                    <h5><b>{{ _lang('Customer Details') }}</b></h5>
                                    @if(isset($sales_return->customer))
                                    <b>{{ _lang('Name') }}</b> : {{ $sales_return->customer->contact_name }}<br>
                                    <b>{{ _lang('Email') }}</b> : {{ $sales_return->customer->contact_email }}<br>
                                    <b>{{ _lang('Phone') }}</b> :
                                    {{ $sales_return->customer->contact_phone != '' ? $sales_return->customer->contact_phone  : _lang('N/A')  }}<br>
                                    @endif
                                </td>
                                <td class="auto-column pt-4">
                                    <h5><b>{{ _lang('Sales Return') }}</b></h5>

                                    <b>{{ _lang('Return Number') }} #:</b> {{ $sales_return->invoice_number }}<br>
                                    <b>{{ _lang('Return Date') }}:</b> {{ $sales_return->return_date }}<br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--End quotation Information-->

                    @php $currency = currency(); @endphp

                    <!--Invoice Product-->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-2" id="invoice-item-table">
                            <thead>
                                <tr>
                                    <th>{{ _lang('Name') }}</th>
                                    <th class="text-center wp-100">{{ _lang('Quantity') }}</th>
                                    <th class="text-right">{{ _lang('Unit Cost') }}</th>
                                    <th class="text-right wp-100">{{ _lang('Discount')}}</th>
                                    <th>{{ _lang('Tax') }}</th>
                                    <th class="text-right">{{ _lang('Line Total') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($sales_return->sales_return_items as $item)
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
                                        <span>{{ decimalPlace($sales_return->product_total, $currency) }}</span>
                                    </td>
                                </tr>
                                @foreach($sales_return_taxes as $tax)
                                <tr>
                                    <td>{{ $tax->name }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace($tax->tax_amount, $currency) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td><b>{{ _lang('Grand Total') }}</b></td>
                                    <td class="text-right">
                                        <b>{{ decimalPlace($sales_return->grand_total, $currency) }}</b>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Total Paid') }}</td>
                                    <td class="text-right">
                                        <span>{{ decimalPlace($sales_return->paid, $currency) }}</span>
                                    </td>
                                </tr>
                                @if($sales_return->payment_status == 0)
                                    <tr>
                                        <td>{{ _lang('Amount Due') }}</td>
                                        <td class="text-right">
                                            <span>{{ decimalPlace(($sales_return->grand_total - $sales_return->paid), $currency) }}</span>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!--End Summary Table-->

                    <div class="clearfix"></div>

                    <!--Invoice Note-->
                    @if($sales_return->note != '')
                    <div class="invoice-note border-top pt-4">{{ $sales_return->note }}</div>
                    @endif
                    <!--End Invoice Note-->

                </div>
            </div>
        </div>
    </div>
    <!--End Classic Invoice Column-->


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
                            {{ _lang('Name') }} : {{ $sales_return->customer->contact_name }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('No Retur Sales') }} #: {{ $sales_return->invoice_number }}<br>
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-6">
                            {{ _lang('Email') }} : {{ $sales_return->customer->contact_email }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Tanggal') }}: {{ $sales_return->return_date }}<br>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            {{ _lang('Phone') }} : {{ $sales_return->customer->address }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Order Status') }}:
                            @if($sales_return->status == 0)
                            <span class="badge badge-danger">{{ _lang('Pending') }}</span><br>
                            @elseif($sales_return->status == 1)
                            <span class="badge badge-success">{{ _lang('Received') }}</span><br>
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
                        $detail = 13;
                        $totalqty = 0
                    @endphp
                    @foreach($sales_return->sales_return_items as $item)
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
                        $totalqty = $totalqty+$item->quantity ;
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
                        <td style="width: 9%">{{ decimalPlace($totalqty) }}</td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($sales_return->grand_total - $sales_return->tax_total, $currency) }}
                        </td>
                    </tr>
                    @foreach($sales_return_taxes as $tax)
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
                        <td style="width: 30%">{{ _lang('Netto') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <b>{{ decimalPlace($sales_return->grand_total, $currency) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">{{ _lang('Total Paid') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace($sales_return->paid, $currency) }}</span>
                        </td>
                    </tr>
                    @if($sales_return->payment_status != '1')
                    <tr>
                        <td style="width: 30%">{{ _lang('Amount Due') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace($sales_return->grand_total - $sales_return->paid, $currency) }}</span>
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
</div>
<!--End Classic Invoice Row-->
@endsection

@section('js-script')
<script>
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
</script>
@endsection