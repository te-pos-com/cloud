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
        <h5>{{ _lang('Laporan Persediaan Barang') }}</h5>
        <h6>{{ get_company_option('company_name') }}</h6>
    </div>

    <table class="table table-bordered report-table">
        <thead>
            <th width="60%">{{ _lang('Nama Barang') }}</th>
            <th class="text-center">{{ _lang('Qty') }}</th>
            <th class="text-right">{{ _lang('Harga') }}</th>
        </thead>
        <tbody>
            @if(isset($report_data))
            @php 
                $stok=0;
                $currency = currency();
            @endphp   
            
            @foreach($report_data as $report)
            <tr>
                @php
                    $stok = $stok+$report->stok;
                @endphp
                <td>{{ $report->item_name }}</td>
                <td class="text-center">{{ $report->stok }}</td>
                <td class="text-right">{{ decimalPlace($report->harga, $currency)  }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <th class="text-center" width="30%"><b>Total</b></th>
            <th class="text-center">{{ $stok }}</th>
            <th class="text-center"></th>
        </tfoot>
    </table>
    
    <!-- Datatable js -->
    <script src="https://cloud.te-pos.com/public/backend/plugins/datatable/datatables.min.js"></script>
    <!-- App js -->
    <script src="https://cloud.te-pos.com/public/backend/assets/js/scripts.js?v=1.1"></script>
