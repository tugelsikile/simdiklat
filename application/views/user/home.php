<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Pengguna
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pengguna</li>
        <!--<li class="active">Blank page</li>-->
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Data Pengguna</h3>
            <div class="box-tools pull-right">
                <a data-toggle="tooltip" title="Tambah pengguna" onclick="show_modal(this);return false" href="<?php echo base_url('user/add_user');?>" class="pull-left margin-r-5 btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Pengguna</a>
                <a data-toggle="tooltip" title="Hapus pengguna terpilih" onclick="bulk_delete();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-danger btn-delete disabled"><i class="fa fa-trash"></i> Hapus Pengguna</a>
                <a data-toggle="tooltip" title="Reset kata sandi pengguna" onclick="bulk_reset();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-warning btn-delete disabled"><i class="fa fa-refresh"></i> Reset Kata Sandi</a>
                <div class="pull-left" style="">
                    <input onkeyup="doSearch();" type="text" class="form-control search input-sm" placeholder="Cari ...">
                </div>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <form id="formTable">
                <table id="DataTable" class="display table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="30px"><input type="checkbox" id="cbxall" onclick="icbxall(this)"></th>
                        <th>Nama Pengguna</th>
                        <th width="200">Level Kewenangan</th>
                        <th>Pusat Belajar</th>
                        <th width="150px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </form>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            Footer
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
    function bulk_reset() {
        var len = $('#DataTable tbody input:checkbox:checked').length;
        var kon = confirm('Anda yakin ingin mereset kata sandi data terpilih ?');
        if (len == 0){
            show_msg('Pilih data yang akan direset kata sandinya','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'user/bulk_reset',
                type    : 'POST',
                dataType: 'JSON',
                data    : $('#formTable').serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        show_msg(dt.msg);
                        $('.overlay').hide();
                    }
                }
            });
        }
    }
    function reset_pass(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin mereset kata sandi pengguna ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'user/reset_pass_data',
                type    : 'POST',
                dataType: 'JSON',
                data    : { id : id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        show_msg(dt.msg);
                        $('.overlay').hide();
                    }
                }
            })
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
                url     : base_url + 'user/bulk_delete',
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
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus pengguna ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'user/delete_data',
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
            url     : base_url + 'user/data_table',
            data    : { keyword : keyword },
            dataType: 'JSON',
            type    : 'POST',
            success : function (dt) {
                if (dt.t == 0){
                    $('#DataTable tbody').html('<tr><td colspan="5">'+dt.msg+'</td></tr>');
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