(function($) {
    $(document).ready(function() {
        actControl('data');
       $("#submit").click(function() {
            actControl('data');
        });
        function actControl(x) {
            var order_status = JSON.stringify($('select[name=order_status]').val());
            var supplier = JSON.stringify($('select[name=supplier]').val());
            var cabang = JSON.stringify($('select[name=cabang]').val());
            var date1 = $('#date1').val();
            var date2 = $('#date2').val();
            if (x == 'data') {
                $("#isi").html('<img src="https://cloud.te-pos.com/public/uploads/icon/1481.gif" alt="this slowpoke moves"  width="50" style="margin-left:45%;" />');
                $("#isi").load('https://cloud.te-pos.com/reports/returpembelian_isi?date1='+ date1 +'&date2='+ date2 +'&order_status='+ order_status + '&supplier='+ supplier + '&cabang=' + cabang);
            }
        }
    });
})(jQuery);	