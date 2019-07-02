<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Kelas Diklat
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Kelas</li>
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
                    <a data-toggle="tooltip" class="btn btn-sm btn-primary btn-delete disabled" href="javascript:;" onclick="print_data();return false" title="Cetak surat pemanggilan"><i class="fa fa-print"></i> Cetak Surat Pemanggilan</a>
                </div>
            </div>
            <div class="box-body box-body table-responsive no-padding">
                <div class="clearfix" style="margin:10px auto">
                    <div class="col-md-9">
                        <select id="dk_id" onchange="load_table()" style="width: 100%;" class="">
                            <?php
                            if (!$diklat){
                                echo '<option value="">Tidak ada data diklat</option>';
                            }
                            foreach ($diklat as $val){
                                echo '<option value="'.$val->dk_id.'">'.$val->dk_name.'</option>';
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
                            <th>Nama Kelas Diklat</th>
                            <th width="100px">Periode</th>
                            <th width="100px">Pusat Belajar</th>
                            <th width="50px">Jml Peserta</th>
                            <th width="300px">Aksi</th>
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
            });
            var url = base_url + 'kelas/cetak_pemanggilan/'+data;
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
    $('#dk_id').select2();
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
        var dk_id   = $('#dk_id').val();
        $.ajax({
            url     : base_url + 'kelas/data_home',
            data    : { keyword : keyword, dk_id : dk_id },
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