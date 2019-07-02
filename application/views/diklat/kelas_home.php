<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        <?php echo $data->dk_name; ?>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('diklat');?>" data-target="diklat" onclick="load_page(this);return false">Diklat</a></li>
        <li class="active">Kelas</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">&nbsp;</h3>
            <div class="box-tools pull-right">
                <a data-toggle="tooltip" title="Tambah kelas" onclick="show_modal(this);return false" href="<?php echo base_url('diklat/add_kelas/'.$data->dk_id);?>" class="pull-left margin-r-5 btn btn-sm btn-primary"><i class="fa fa-plus"></i> Tambah Kelas Diklat</a>
                <a data-toggle="tooltip" title="Hapus kelas terpilih" onclick="bulk_delete();return false" href="javascript:;" class="pull-left margin-r-5 btn btn-sm btn-danger btn-delete disabled"><i class="fa fa-trash"></i> Hapus Diklat</a>

                <div class="pull-left" style="">
                    <div class="pull-left margin-r-5">
                        <select id="pb_id" onchange="load_table()">
                            <option value="">==Pusat Belajar==</option>
                            <?php
                            foreach ($pb as $val){
                                echo '<option value="'.$val->pb_id.'">'.$val->pb_name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="pull-left">
                        <input onkeyup="doSearch();" type="text" class="form-control search" placeholder="Cari ...">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="box-body">
            <form id="formTable">
                <table id="DataTable" class="display table table-bordered table-responsive" style="width:100%">
                    <thead>
                    <tr>
                        <th width="30px"><input type="checkbox" id="cbxall" onclick="icbxall(this)"></th>
                        <th width="100px">Aksi</th>
                        <th>Nama Kelas Diklat</th>
                        <th>Periode</th>
                        <th>Pusat Belajar</th>
                        <th width="50px">Jml Peserta</th>
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
    $('#pb_id').select2();
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
                url     : base_url + 'diklat/bulk_kelas_delete',
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
        var kon = confirm('Anda yakin ingin menghapus kelas ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'diklat/delete_kelas',
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
        var dk_id   = '<?php echo $data->dk_id;?>';
        var pb_id   = $('#pb_id').val();
        $.ajax({
            url     : base_url + 'diklat/data_kelas',
            data    : { keyword : keyword, dk_id : dk_id, pb_id : pb_id },
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