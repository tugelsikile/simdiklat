<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pusat Belajar
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('diklat');?>" data-target="diklat" onclick="load_page(this);return false">Diklat</a></li>
        <li class="active">Pusat Belajar</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">&nbsp;</h3>
            <div class="box-tools pull-right">
                <a data-toggle="tooltip" title="Tambah pusat belajar" onclick="show_modal(this);return false" href="<?php echo base_url('diklat/add_pb');?>" class="pull-left margin-r-5 btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Pusat Belajar</a>
                <a data-toggle="tooltip" title="Hapus pusat belajar terpilih" onclick="bulk_delete();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-danger btn-delete disabled"><i class="fa fa-trash"></i> Hapus Pusat Belajar</a>

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
                        <th width="40%">Nama Pusat Belajar</th>
                        <th>Alamat</th>
                        <th width="100px">No. Telp</th>
                        <th width="80px">Jml Diklat</th>
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
    <!-- /.box -->

</section>
<!-- /.content -->
<script>
    $('[data-toggle="tooltip"]').tooltip();
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
                url     : base_url + 'diklat/bulk_pb_delete',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="6">Tidak ada data</td></tr>');
                        }
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus Pusat Belajar ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'diklat/delete_pb',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="6">Tidak ada data</td></tr>');
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
            url     : base_url + 'diklat/data_pb',
            data    : { keyword : keyword },
            dataType: 'JSON',
            type    : 'POST',
            success : function (dt) {
                if (dt.t == 0){
                    $('#DataTable tbody').html('<tr class="row_zero"><td colspan="6">'+dt.msg+'</td></tr>');
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