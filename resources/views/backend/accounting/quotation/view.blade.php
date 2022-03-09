@extends('layouts.app')

@section('content')
<div class="row">

    <div class="col-md-12">
        <div class="btn-group pull-right">
            <a class="btn btn-info btn-sm ajax-modal" data-title="{{ _lang('Send Email') }}"
                href="{{ route('quotations.send_email', $quotation->id) }}"><i class="ti-email"></i>
                {{ _lang('Send Email') }}</a>
            @if($quotation->status == 0)
            <a class="btn btn-success btn-sm" href="{{ route('quotations.convert_invoice',$quotation->id) }}"><i
                    class="ti-exchange-vertical"></i> {{ _lang('Convert to Invoice') }}</a>
            <a class="btn btn-warning btn-sm" href="{{ action('QuotationController@edit', $quotation->id) }}"><i
                    class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
            @else
            <a class="btn btn-success btn-sm disabled" href=""><i class="ti-exchange-vertical"></i>
                {{ _lang('Converted') }}</a>
            @endif
            <a class="btn btn-primary btn-sm print" href="#" data-print="invoice-view"><i class="ti-printer"></i>
                {{ _lang('Print A4') }}</a>
            <a class="btn btn-primary btn-sm" href="#" onClick="printReceiptLg()"><i class="ti-printer"></i>
                {{ _lang('Print Nota') }}</a>
            <a class="btn btn-danger btn-sm" href="{{ route('quotations.download_pdf',$quotation->id) }}"><i
                    class="ti-file"></i> {{ _lang('Export PDF') }}</a>
        </div>
        <div class="card card-default clear">
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
                                        <h5><b>{{ _lang('Quotation To') }}</b></h5>
                                        @if(isset($quotation->client))
                                        {{ $quotation->client->contact_name }}<br>
                                        {{ $quotation->client->contact_email }}<br>
                                        {!! $quotation->client->company_name != '' ?
                                        xss_clean($quotation->client->company_name).'<br>' : '' !!}
                                        {!! $quotation->client->address != '' ?
                                        xss_clean($quotation->client->address).'<br>' : '' !!}
                                        @endif
                                    </td>
                                    <td class="auto-column pt-4">
                                        <h5><b>{{ _lang('Quotation Details') }}</b></h5>

                                        <b>{{ _lang('Quotation') }} #:</b> {{ $quotation->quotation_number }}<br>

                                        <b>{{ _lang('Quotation Date') }}:</b>
                                        {{ $quotation->quotation_date }}<br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!--End quotation Information-->

                    @php $currency = currency(); @endphp

                    <!--quotation Product-->
                    <div class="table-responsive">
                        <table class="table table-bordered mt-2" id="quotation-item-table">
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
                            <tbody id="quotation">
                                @foreach($quotation->quotation_items as $item)
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
                    <!--End quotation Product-->

                    <!--Summary Table-->
                    <div class="quotation-summary-right">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="quotation-summary-table">
                                <tbody>
                                    <tr>
                                        <td><b>{{ _lang('Sub Total') }}</b></td>
                                        <td class="text-right">
                                            <b>{{ decimalPlace($quotation->grand_total - $quotation->tax_total, $currency) }}</b>
                                        </td>
                                    </tr>
                                    @foreach($quotation_taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->name }}</td>
                                        <td class="text-right">
                                            <span>{{ decimalPlace($tax->tax_amount, $currency) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td>{{ _lang('Netto') }}</td>
                                        <td class="text-right">{{ decimalPlace($quotation->grand_total, $currency) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--End Summary Table-->

                    <div class="clearfix"></div>

                    <!--quotation Note-->
                    @if($quotation->note != '')
                    <div class="invoice-note">{!! xss_clean($quotation->note) !!}</div>
                    @endif
                    <!--End quotation Note-->

                    <!--quotation Footer Text-->
                    @if(get_company_option('quotation_footer') != '')
                    <div class="invoice-note">{!! xss_clean(get_company_option('quotation_footer')) !!}</div>
                    @endif
                    <!--End quotation Note-->
                </div>
                <!--End Quotation View-->

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
                            {{ _lang('Name') }} : {{ $quotation->client->contact_name }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('No Quotation') }} #: {{ $quotation->quotation_number }}<br>
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-6">
                            {{ _lang('Email') }} : {{ $quotation->client->contact_email }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Tanggal') }}: {{ $quotation->quotation_date }}<br>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            {{ _lang('Phone') }} : {{ $quotation->client->address }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Order Status') }}:
                            @if($quotation->status == 0)
                            <span class="badge badge-danger">{{ _lang('Pending') }}</span><br>
                            @elseif($quotation->status == 1)
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
                    @foreach($quotation->quotation_items as $item)
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
                            {{ decimalPlace($quotation->grand_total - $quotation->tax_total, $currency) }}
                        </td>
                    </tr>
                    @foreach($quotation_taxes as $tax)
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
                            <b>{{ decimalPlace($quotation->grand_total, $currency) }}</b>
                        </td>
                    </tr>

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