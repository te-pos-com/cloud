    <script type="text/javascript">
	var _date_format = "d-m-Y";
	var _backend_direction = "ltr";
	var _currency = "Rp";
	var $lang_alert_title = "Apa kamu yakin?";
	var $lang_alert_message = "Setelah dihapus, Anda tidak akan dapat memulihkan informasi ini!";
	var $lang_confirm_button_text = "Ya, hapus!";
	var $lang_cancel_button_text = "Membatalkan";
    var $lang_no_data_found = "Tidak ada data ditemukan";
	var $lang_showing = "Menampilkan";
	var $lang_to = "ke";
	var $lang_of = "dari";
	var $lang_entries = "Entri";
	var $lang_showing_0_to_0_of_0_entries = "Menampilkan 0 Sampai 0 Dari 0 Entri";
	var $lang_show = "Menunjukkan";
	var $lang_loading = "Memuat...";
	var $lang_processing = "Pengolahan...";
	var $lang_search = "Mencari";
	var $lang_no_matching_records_found = "Tidak ada catatan yang cocok ditemukan";
	var $lang_first = "Pertama";
	var $lang_last = "Terakhir";
	var $lang_next = "Berikutnya";
	var $lang_previous = "Sebelumnya";
	var $lang_copy = "Salinan";
	var $lang_excel = "Excel";
	var $lang_pdf = "PDF";
	var $lang_print = "Mencetak";
	var $lang_income = "Penghasilan";


    </script>

    
    @php $date_format = get_date_format(); @endphp

    <div class="report-header">
        <h5>{{ _lang('Laporan Quotation') }}</h5>
        <h6>{{ isset($date1) ? date($date_format,strtotime($date1)).' '._lang('to').' '.date($date_format,strtotime($date2)) : '-------------  '._lang('to').'  -------------' }}</h6>
    </div>

    <table class="table table-bordered report-table">
        <thead>
            <th>{{ _lang('Date') }}</th>
            <th>{{ _lang('No Transaksi') }}</th>
            <th>{{ _lang('Customer') }}</th>
            <th width="20%">{{ _lang('Nama Barang') }}</th>
            <th>{{ _lang('Gudang') }}</th>
            <th class="text-center">{{ _lang('Qty') }}</th>
            <th class="text-right">{{ _lang('Harga') }}</th>
            <th class="text-right">{{ _lang('Total') }}</th>
        </thead>
        <tbody>
            @if(isset($report_data))
            @php 
                $currency = currency();
                $noinv="";
                $qty=0;
                $sub_total=0;
            @endphp   
            
            @foreach($report_data as $report)
            <tr>
                @php
                    $qty = $qty + $report->quantity;
                    $sub_total = $sub_total + $report->sub_total;
                @endphp
                @if ($noinv!=$report->id)
                    @php 
                        $noinv=$report->id
                    @endphp
                    <td>{{ date($date_format, strtotime($report->quotation_date)) }}</td>
                    <td>{{ $report->quotation_number }}</td>
                    <td>{{ $report->contact_name }}</td>    
                @else
                    <td></td>
                    <td></td>
                    <td></td>
                @endif
                <td>{{ $report->item_name }}</td>
                <td>{{ $report->gudang_name }}</td>
                <td class="text-center">{{ $report->quantity }}</td>
                <td class="text-right">{{ decimalPlace($report->unit_cost, $currency) }}</td>
                <td class="text-right">{{ decimalPlace($report->sub_total, $currency)  }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
        
        <tfoot>
            <th class="text-center" colspan="5"><b>Total</b></th>
            <th class="text-center">{{ $qty }}</th>
            <th class="text-center"></th>
            <th class="text-right">{{ decimalPlace($sub_total, $currency) }}</th>
        </tfoot>
    </table>
    
    <!-- Datatable js -->
    <script src="https://cloud.te-pos.com/public/backend/plugins/datatable/datatables.min.js"></script>
    <!-- App js -->
    <script src="https://cloud.te-pos.com/public/backend/assets/js/scripts.js?v=1.1"></script>
