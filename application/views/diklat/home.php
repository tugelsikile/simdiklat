<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Diklat
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Diklat</li>
        <!--<li class="active">Blank page</li>-->
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="print_wrapper">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Cetak</h3>
                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" title="Cetak sekarang" onclick="print_now();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak</a>
                    <a data-toggle="tooltip" title="Batal cetak" onclick="cancel_print();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-danger"><i class="fa fa-close"></i> Batal Cetak</a>
                </div>
            </div>
            <div class="box-body no-padding">
                <iframe name="printFrame" id="printFrame" style="width: 100%;border:none;height:600px" src=""></iframe>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

            </div>
            <div class="overlay"><i class="fa fa-spin fa-refresh"></i></div>
            <!-- /.box-footer-->
        </div>
    </div>
    <div class="no_print">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Diklat</h3>
                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" title="Tambah diklat" onclick="show_modal(this);return false" href="<?php echo base_url('diklat/add_data');?>" class="pull-left margin-r-5 btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Diklat</a>
                    <a data-toggle="tooltip" title="Cetak surat pemanggilan" onclick="print_data();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-info btn-delete disabled"><i class="fa fa-print"></i> Cetak Pemanggilan</a>
                    <a data-toggle="tooltip" title="Hapus diklat terpilih" onclick="bulk_delete();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-danger btn-delete disabled"><i class="fa fa-trash"></i> Hapus Diklat</a>

                    <div class="pull-left" style="">
                        <input onkeyup="doSearch();" type="text" class="form-control search input-sm" placeholder="Cari ...">
                    </div>
                </div>
            </div>
            <div class="box-body">
                <form id="formTable">
                    <table id="DataTable" class="display table table-bordered table-responsive" style="width:100%">
                        <thead>
                        <tr>
                            <th width="30px"><input type="checkbox" id="cbxall" onclick="icbxall(this)"></th>
                            <th width="100px">Aksi</th>
                            <th>Nama Diklat</th>
                            <th>Tempat Pelaksanaan</th>
                            <th width="80px">Jml Kelas</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">

            </div>
            <div class="overlay"><i class="fa fa-spin fa-refresh"></i></div>
            <!-- /.box-footer-->
        </div>
    </div>
    <!-- /.box -->

</section>
<!-- /.content -->
<script>
    $('[data-toggle="tooltip"]').tooltip();
    $('.print_wrapper').hide();
    function print_data() {
        var dtlen = $('#DataTable tbody input:checkbox:checked').length;
        if (dtlen == 0){
            show_msg('Pilih data lebih dulu','error');
        } else {
            var data = '';
            $.each($('#DataTable tbody input:checkbox:checked'),function (i,v) {
                console.log(v);
                data += $(this).val()+'-';
            })
            var url = base_url + 'diklat/surat_pemanggilan/'+data;
            $('.print_wrapper').show();
            $('.no_print').hide();
            $('#printFrame').attr({'src':url});
        }
    }
    function print_now() {
        window.frames["printFrame"].focus();
        window.frames["printFrame"].print();
    }
    function cancel_print() {
        $('.no_print').show();
        $('.print_wrapper').hide();
        $('#printFrame').attr({'src':''});
    }
    function icbxall(ob) {
        if ($(ob).prop('checked') == true){
            $('#DataTable tbody input:checkbox').prop({'checked':true});
            if ($('#DataTable tbody input:checkbox:checked').length > 0){
                $('.btn-delete').removeClass('disabled');
            }
        } else {
            $('#DataTable tbody input:checkbox').prop({'checked':false});
            $('.btn-delete').addClass('disabled');
        }
    }
    function bulk_delete() {
        var len = $('#DataTable tbody input:checkbox:checked').length;
        var kon = confirm('Anda yakin ingin menghapus data terpilih ?');
        if (len == 0){
            show_msg('Pilih data yang akan dihapus','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'diklat/bulk_delete',
                type    : 'POST',
                dataType: 'JSON',
                data    : $('#formTable').serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        show_msg(dt.msg);
                        $.each(dt.data,function (i,v) {
                            $('.row_'+v).remove();
                        });
                        $('.overlay').hide();
                        $('.btn-delete').addClass('disabled');
                        if ($('#DataTable tbody tr').length == 0){
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="5">Tidak ada data</td></tr>');
                        }
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus diklat ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'diklat/delete_data',
                type    : 'POST',
                dataType: 'JSON',
                data    : { id : id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        $('.row_'+id).remove();
                        show_msg(dt.msg);
                        $('.overlay').hide();
                        if ($('#DataTable tbody tr').length == 0){
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="5">Tidak ada data</td></tr>');
                        }
                    }
                }
            })
        }
    }
    var delayTimer;
    function doSearch() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function() {
            load_table();
        }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
    }
    function load_table() {
        $('.overlay').show();
        var keyword = $('.search').val();
        $.ajax({
            url     : base_url + 'diklat/data_table',
            data    : { keyword : keyword },
            dataType: 'JSON',
            type    : 'POST',
            success : function (dt) {
                if (dt.t == 0){
                    $('#DataTable tbody').html('<tr class="row_zero"><td colspan="5">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                    $('.btn-delete').addClass('disabled');
                    $('#cbxall').prop({'checked':false});
                } else {
                    $('#DataTable tbody').html(dt.html);
                    $('.overlay').hide();
                    $('.btn-delete').addClass('disabled');
                    $('#cbxall').prop({'checked':false});
                }
            }
        })
    }
    load_table();
</script>