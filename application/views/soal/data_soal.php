<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Daftar Soal
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('soal/paket');?>" data-target="soal" onclick="load_page(this);return false">Paket Soal</a></li>
        <li class="active">Daftar Soal</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
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
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" class="btn btn-sm btn-primary" href="javascript:;" onclick="show_modal({'href':base_url+'soal/add_soal/'+$('#pk_id').val(),'title':'Tambah soal'});return false" title="Tambah soal"><i class="fa fa-plus"></i> Tambah Soal</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-danger btn-delete disabled" href="javascript:;" onclick="bulk_delete();return false" title="Hapus paket soal"><i class="fa fa-trash"></i> Hapus Soal</a>
                </div>
            </div>
            <div class="box-body box-body table-responsive no-padding">
                <div class="clearfix" style="margin:10px auto">
                    <div class="col-md-6">
                        <select id="dk_id" class="form-control" style="width:100%" onchange="dk_select()">
                            <?php
                            if ($diklat){
                                foreach ($diklat as $val){
                                    echo '<option value="'.$val->dk_id.'">'.$val->dk_name.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="pk_id" class="form-control" style="width: 100%" onchange="load_table()">
                            <option value="">== Paket Soal ==</option>
                            <?php
                            if ($paket){
                                foreach ($paket as $val){
                                    echo '<option value="'.$val->pk_id.'">'.$val->pk_name.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input onkeyup="doSearch();" type="text" class="form-control search" placeholder="Cari ...">
                    </div>
                </div>
                <form id="formTable">
                    <table id="DataTable" class="display table table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <td width="20px"><input type="checkbox" onclick="icbxall(this)"></td>
                            <th>Nama / Kode Paket Soal</th>
                            <th width="100px">Nomor Soal</th>
                            <th width="50px">Jawaban</th>
                            <th width="100px">Aksi</th>
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
    function dk_select() {
        var dk_id       = $('#dk_id').val();
        $.ajax({
            url     : base_url + 'soal/dk_select',
            type    : 'POST',
            dataType: 'JSON',
            data    : { dk_id : dk_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#pk_id').html('<option value="">'+dt.msg+'</option>');
                } else {
                    $('#pk_id').html('<option value="">== Paket Soal ==</option>');
                    $.each(dt.data,function (i,v) {
                        $('#pk_id').append('<option value="'+v.pk_id+'">'+v.pk_name+'</option>');
                        if (i + 1 >= dt.data.length){
                            load_table();
                        }
                    });
                }
            }
        });
    }
    $('#pk_id').val('<?php echo $pk_id;?>');
    $('#dk_id').val('<?php echo $dk_id;?>');
    $('#pk_id,#dk_id').select2();
    $('.print_wrapper').hide();
    var delayTimer;
    function doSearch() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function() {
            load_table();
        }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
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
                url     : base_url + 'soal/bulk_delete_paket',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="4">Data tidak ditemukan</td></tr>');
                        }
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus paket soal ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'soal/delete_paket',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="4">Data tidak ditemukan</td></tr>');
                        }
                    }
                }
            })
        }
    }
    function load_table() {
        $('.overlay').show();
        var keyword = $('.search').val();
        var pk_id   = $('#pk_id').val();
        $.ajax({
            url     : base_url + 'soal/data_data_soal',
            data    : { keyword : keyword, pk_id : pk_id },
            dataType: 'JSON',
            type    : 'POST',
            success : function (dt) {
                if (dt.t == 0){
                    $('#DataTable tbody').html('<tr class="row_zero"><td colspan="4">'+dt.msg+'</td></tr>');
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