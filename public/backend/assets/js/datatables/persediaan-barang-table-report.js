(function($) {
    var getUrl = window.location;
    var baseUrl = getUrl .protocol + "//" + getUrl.host;
    $(document).ready(function() {
        actControl('data');
       $("#submit").click(function() {
            actControl('data');
        });
        function actControl(x) {
            var cabang   = JSON.stringify($('select[name=cabang]').val());
            var gudang   = JSON.stringify($('select[name=gudang]').val());
            var produk   = JSON.stringify($('select[name=produk]').val());
            var merek    = JSON.stringify($('select[name=merek]').val());
            var kategori = JSON.stringify($('select[name=kategori]').val());
            
            if (x == 'data') {
                $("#isi").html('<img src="'+ baseUrl +'/public/uploads/icon/1481.gif" alt="this slowpoke moves"  width="50" style="margin-left:45%;" />');
                $("#isi").load( baseUrl +'/reports/persediaan_barang_isi?cabang=' + cabang + '&gudang='+ gudang + '&produk='+ produk + '&merek='+ merek + '&kategori='+ kategori);
            }
        }
    });
})(jQuery);