<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Data Pembelajaran
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('kelas');?>" data-target="kelas" onclick="load_page(this);return false">Kelas Diklat</a></li>
        <li><a href="javascript:;" data-target="kelas" onclick="load_page({'href':base_url+'pengajar/daftar/'+$('#kel_id').val()});return false">Pengajar Diklat</a></li>
        <li><a href="javascript:;" data-target="kelas" onclick="load_page({'href':base_url+'pengajar/hadir/'+$('#kel_id').val()});return false">Daftar Hadir</a></li>
        <li class="active">Daftar Pembelajaran</li>
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
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">&nbsp;</h3>
                <div class="box-tools pull-right">
                    <div class="pull-left margin-r-5">
                        <a data-toggle="tooltip" title="Tambah jam" onclick="do_action(this);return false" data-action="add_data" class="btn-sm btn btn-primary"><i class="fa fa-plus"></i> Tambah Jam</a>
                        <a data-toggle="tooltip" title="Hapus jam" onclick="bulk_delete();return false" class="btn-sm btn-delete disabled btn btn-danger"><i class="fa fa-trash"></i> Hapus Jam</a>
                    </div>
                    <div class="pull-left margin-r-5">

                    </div>
                    <div class="pull-left" style="">

                    </div>
                </div>
            </div>
            <div class="box-body table-responsive no-padding">
                <div class="clearfix" style="margin: 10px auto;">
                    <div class="col-md-6">
                        <select id="kel_id" onchange="kelas_select()" style="width: 100%;" class="">
                            <?php
                            foreach ($kel as $val){
                                echo '<option value="'.$val->kel_id.'">'.$val->kel_name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="dh_date_in" onchange="load_table()" style="width: 100%;" class="">
                            <?php
                            foreach ($data as $val){
                                echo '<option value="'.$val->dh_date.'">'.$val->dh_date.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input onkeyup="doSearch();" type="text" class="form-control search" placeholder="Cari ...">
                    </div>
                </div>
                <form id="formTable">
                    <table id="DataTable" class="display table table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th width="30px"><input type="checkbox" id="cbxall" onclick="icbxall(this)"></th>
                            <th width="200px">Hari, Tanggal</th>
                            <th width="30px">Jam Ke</th>
                            <th width="120px">Pukul</th>
                            <th width="">Materi Pembelajaran</th>
                            <th width="">Pengajar 1</th>
                            <th width="">Pengajar 2</th>
                            <th width="">Pengajar 3</th>
                            <th width="">Pengajar 4</th>
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
    <!-- Default box -->

    <!-- /.box -->

</section>
<!-- /.content -->
<script>
    $('.print_wrapper').hide();
    function print_data(ob) {
        var dtlen = $('#DataTable tbody input:checkbox:checked').length;
        if (dtlen == 0){
            show_msg('Pilih data lebih dulu','error');
        } else {
            var tipe = $(ob).attr('data-type');
            var data = '';
            $.each($('#DataTable tbody input:checkbox:checked'),function (i,v) {
                console.log(v);
                data += $(this).val()+'-';
            });
            if (tipe == 'hadir'){
                var url = base_url + 'pengajar/cetak_hadir/'+data;
            } else {
                var url = base_url + 'pengajar/cetak_gambar_hadir/'+data;
            }

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

    $('#kel_id').val('<?php echo $kel_id;?>');
    $('#dh_date_in').val('<?php echo $dh_date; ?>');
    $('#dh_date_in,#kel_id').select2();
    $('[data-toggle="tooltip"]').tooltip();
    function do_action(ob) {
        var act     = $(ob).attr('data-action');
        var kel_id  = $('#kel_id').val();
        var dh_date = $('#dh_date_in').val();
        if (!act){
            show_msg('Invalid click','error');
        } else if (act == 'add_data'){
            var url = base_url + 'pengajar/add_pembelajaran/' + kel_id + '/' + dh_date;
            var dt = {'href':url,'title':'Tambah jam pembelajaran'}
            show_modal(dt);
        } else if (act == 'upload'){
            var dh_id   = $('#DataTable tbody input:checkbox:checked');
            if (dh_id.length == 0){
                show_msg('Pilih salah satu data','error');
            } else if (dh_id.length > 1){
                show_msg('Pilih satu data saja','error');
            } else {
                var url     = base_url + 'pengajar/upload_hadir/' + dh_id.val();
                var dt      = {'href':url, 'title':'Upload daftar hadir'}
                show_modal(dt);
            }
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
                url     : base_url + 'pengajar/bulk_delete_pembelajaran',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="6">Data tidak ditemukan</td></tr>');
                        }
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var id  = $(ob).attr('data-id');
        var kon = confirm('Anda yakin ingin menghapus tanggal kehadiran ini ?');
        if (!id){
            show_msg('Pilih data','error');
        } else if (kon){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'pengajar/delete_pembelajaran',
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
                            $('#DataTable tbody').html('<tr class="row_zero"><td colspan="6">Data tidak ditemukan</td></tr>');
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
    function kelas_select() {
        var kel_id  = $('#kel_id').val();
        $.ajax({
            url     : base_url + 'pengajar/kelas_select',
            type    : 'POST',
            dataType: 'JSON',
            data    : { kel_id : kel_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dh_date_in').html('<option value="">'+dt.msg+'</option>');
                    load_table();
                } else {
                    $('#dh_date_in').html('');
                    $.each(dt.data,function (i,v) {
                        $('#dh_date_in').append('<option value="'+v.dh_date+'">'+v.dh_date+'</option>');
                        if (i + 1 >= dt.data.length){
                            load_table();
                        }
                    })
                }
            }
        })
    }
    function load_table() {
        $('.overlay').show();
        var keyword = $('.search').val();
        var kel_id  = $('#kel_id').val();
        var dh_date = $('#dh_date_in').val();
        $.ajax({
            url     : base_url + 'pengajar/data_pembelajaran',
            data    : { keyword : keyword, kel_id : kel_id, dh_date : dh_date },
            dataType: 'JSON',
            type    : 'POST',
            success : function (dt) {
                if (dt.t == 0){
                    $('#DataTable tbody').html('<tr class="row_zero"><td colspan="9">'+dt.msg+'</td></tr>');
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