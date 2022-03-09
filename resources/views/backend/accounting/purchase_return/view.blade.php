@extends('layouts.app')

@section('content')
  <div class="row">
	<div class="col-12">
		<div class="btn-group group-buttons">
			<a class="btn btn-primary btn-sm print" href="#" data-print="invoice-view"><i class="ti-printer"></i> {{ _lang('Print A4') }}</a>
			<a class="btn btn-primary btn-sm" href="#" onClick="printReceiptLg()"><i class="ti-printer"></i>
                {{ _lang('Print Nota') }}</a>
			@if($purchase_return->payment_status != 1)
            <a class="btn btn-success btn-sm ajax-modal" data-title="{{ _lang('Make Payment') }}"
                href="{{ route('purchase_return.create_payment', $purchase_return->id) }}"><i class="ti-credit-card"></i>
				{{ _lang('Record a Payment') }}</a>
			<a class="btn btn-warning btn-sm" href="{{ action('PurchaseReturnController@edit', $purchase_return->id) }}"><i class="ti-pencil-alt"></i> {{ _lang('Edit') }}</a>
			
			@else
            <button class="btn btn-success btn-sm" disabled><i class="ti-receipt"></i>
                {{ _lang('PAID') }}</button>
            @endif
		</div>

		<div class="card clearfix">

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
									@if(isset($purchase_return->supplier))	
										<b>{{ _lang('Name') }}</b> : {{ $purchase_return->supplier->supplier_name }}<br>
										<b>{{ _lang('Email') }}</b> : {{ $purchase_return->supplier->email }}<br>
										<b>{{ _lang('Phone') }}</b> : {{ $purchase_return->supplier->phone }}<br>
										<b>{{ _lang('VAT Number') }}</b> : {{ $purchase_return->supplier->vat_number == '' ? _lang('N/A') : $purchase_return->supplier->vat_number }}<br>
									@endif    
                                </td>
                                <td class="auto-column pt-4">
                                    <h5><b>{{ _lang('Purchase Return') }}</b></h5>

                                    <b>{{ _lang('Return ID') }} #:</b> {{ $purchase_return->id }}<br>
									<b>{{ _lang('Return Date') }}:</b> {{ $purchase_return->return_date }}<br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!--End Information-->
					
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
								@foreach($purchase_return->purchase_return_items as $item)
									<tr id="product-{{ $item->product_id }}">
										<td>
											<b>{{ $item->item->item_name }}</b><br>
											{{ $item->description }}
										</td>
										<td class="text-center quantity">{{ $item->quantity }} {{$item->item->product->units->unit_name }}</td>
										<td class="text-right unit-cost">{{ decimalPlace($item->unit_cost, $currency) }}</td>
										<td class="text-right discount">{{ decimalPlace($item->discount, $currency) }}</td>
										<td>{!! xss_clean(object_to_tax($item->taxes, 'name')) !!}</td>
										<td class="text-right sub-total">{{ decimalPlace($item->sub_total, $currency) }}</td>
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
											<span>{{ decimalPlace($purchase_return->product_total, $currency) }}</span>
										</td>
									</tr>
									@foreach($purchase_return_taxes as $tax)
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
											 <b>{{ decimalPlace($purchase_return->grand_total, $currency) }}</b>
										</td>
									</tr>
									<tr>
										<td>{{ _lang('Total Paid') }}</td>
										<td class="text-right">
											<span>{{ decimalPlace($purchase_return->paid, $currency) }}</span>
										</td>
									</tr>
									@if($purchase_return->payment_status == 0)
										<tr>
											<td>{{ _lang('Amount Due') }}</td>
											<td class="text-right">
												<span>{{ decimalPlace(($purchase_return->grand_total - $purchase_return->paid), $currency) }}</span>
											</td>
										</tr>
									@endif
							 </tbody>
						</table>
					</div>
					<!--End Summary Table-->
					 
					<div class="clearfix"></div>				 

					<!--Invoice Note-->
					@if($purchase_return->note  != '')
						<div class="invoice-note border-top pt-4">{{ $purchase_return->note }}</div> 
					@endif
					<!--End Invoice Note-->	
					 
				</div>
			</div>
		</div>
    </div><!--End Classic Invoice Column-->


    <div id="ticket-print-lg" class="ticket" style="display:none">
                <table>
                    <tr>
                        <td>
                            <h4>RETUR PEMBELIAN</h4>
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
                            <b>Supplier : </b><br>
                            {{ _lang('Name') }} : {{ $purchase_return->supplier->supplier_name }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('No Retur Pembelian') }} #: {{ $purchase_return->invoice_number }}<br>
                        </td>
                    </tr>

                    <tr>
                        <td class="pt-6">
                            {{ _lang('Email') }} : {{ $purchase_return->supplier->email }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Tanggal') }}: {{ $purchase_return->return_date }}<br>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6">
                            {{ _lang('Phone') }} : {{ $purchase_return->supplier->phone }}<br>
                        </td>
                        <td class="pt-4">
                            {{ _lang('Payment') }}:
                            @if($purchase_return->payment_status == 0)
                            <span class="badge badge-danger">{{ _lang('Belum Lunas') }}</span>
                            @else
                            <span class="badge badge-success">{{ _lang('Lunas') }}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-6" style="width:76%;">
                            {{ _lang('VAT Number') }} :
                            {{ $purchase_return->supplier->vat_number == '' ? _lang('N/A') : $purchase_return->supplier->vat_number }}<br>
                        </td>
                        <td class="pt-4">
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
                        $totqty = 0;
                    @endphp
                    @foreach($purchase_return->purchase_return_items as $item)
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
                        $totqty = $totqty + $item->quantity;
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
                        <td style="width: 9%">{{ decimalPlace($totqty) }}</td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($purchase_return->product_total, $currency) }}
                        </td>
                    </tr>
                    @foreach($purchase_return_taxes as $tax)
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
                        <td style="width: 30%">{{ _lang('Shipping Cost') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($purchase_return->shipping_cost, $currency) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">{{ _lang('Discount') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            {{ decimalPlace($purchase_return->order_discount, $currency) }}
                        </td>
                    </tr>
                    <tr>

                        <td style="width: 30%">{{ _lang('Grand Total') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <b>{{ decimalPlace($purchase_return->grand_total, $currency) }}</b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">{{ _lang('Total Paid') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace($purchase_return->paid, $currency) }}</span>
                        </td>
                    </tr>
                    @if($purchase_return->payment_status == 0)
                    <tr>
                        <td style="width: 30%">{{ _lang('Amount Due') }}</td>
                        <td style="width: 15%"></td>
                        <td style="width: 9%"></td>
                        <td style="width: 16%"></td>
                        <td align="right" style="width: 30%">
                            <span>{{ decimalPlace(($purchase_return->grand_total - $purchase_return->paid), $currency) }}</span>
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
    

</div><!--End Classic Invoice Row-->
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