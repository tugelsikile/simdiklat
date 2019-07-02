<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Data Pengajar
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('kelas');?>" data-target="kelas" onclick="load_page(this);return false">Kelas Diklat</a></li>
        <li class="active">Data Pengajar</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">&nbsp;</h3>
            <div class="box-tools pull-right">
                <div class="pull-left margin-r-5">
                    <a data-toggle="tooltip" title="Tambah pengajar" onclick="do_action(this);return false" data-action="add_data" class="btn-sm btn btn-primary"><i class="fa fa-plus"></i> Tambah Pengajar</a>
                    <a data-toggle="tooltip" title="Daftar hadir pengajar" onclick="load_page({'href':base_url+'pengajar/hadir/'+$('#kel_id').val(),'data-target':'kelas'});return false" href="javascript:;" class="btn btn-sm btn-primary"><i class="fa fa-calendar-check-o"></i> Daftar Hadir</a>
                    <a data-toggle="tooltip" title="Hapus peserta" onclick="bulk_delete();return false" class="btn-sm btn-delete disabled btn btn-danger"><i class="fa fa-trash"></i> Hapus Pengajar</a>
                </div>
            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="clearfix" style="margin:10px auto">
                <div class="col-md-6">
                    <select id="kel_id" onchange="load_table()" style="width: 100%;" class="">
                        <?php
                        foreach ($kel as $val){
                            echo '<option value="'.$val->kel_id.'">'.$val->kel_name.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <input onkeyup="doSearch();" type="text" class="form-control search" placeholder="Cari ...">
                </div>
            </div>
            <form id="formTable">
                <table id="DataTable" class="display table table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th width="30px"><input type="checkbox" id="cbxall" onclick="icbxall(this)"></th>
                        <th width="100px">Nopes UKG</th>
                        <th width="">Nama Lengkap</th>
                        <th width="200px">Nama Institusi / Sekolah</th>
                        <th width="150px">Kab/Kota</th>
                        <th width="150px">Provinsi</th>
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
    <!-- /.box -->

</section>
<!-- /.content -->
<script>
    $('#kel_id').val('<?php echo $data->kel_id;?>');
    $('#kel_id').select2();
    $('[data-toggle="tooltip"]').tooltip();
    function do_action(ob) {
        var act     = $(ob).attr('data-action');
        var kel_id  = $('#kel_id').val();
        if (!act){
            show_msg('Invalid click','error');
        } else if (act == 'add_data'){
            var url = base_url + 'pengajar/add_data/' + kel_id;
            var dt = {'href':url,'title':'Tambah data peserta'}
            show_modal(dt);
        } else if (act == 'import'){
            var url     = base_url + 'pengajar/import/' + kel_id;
            var dt      = {'href':url, 'title':'Import data peserta'}
            show_modal(dt);
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
                url     : base_url + 'pengajar/bulk_delete',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="7">Data tidak ditemukan</td></tr>');
                        }
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus pengajar ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'pengajar/delete_data',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="7">Data tidak ditemukan</td></tr>');
                        }
                    }
                }
            })
        }
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
        var kel_id   = $('#kel_id').val();
        $.ajax({
            url     : base_url + 'pengajar/data_home',
            data    : { keyword : keyword, kel_id : kel_id },
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