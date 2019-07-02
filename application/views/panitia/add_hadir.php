<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="kel_id" value="<?php echo $data->kel_id;?>">
    <div class="form-group col-md-4">
        <label for="dh_date">Tanggal Kehadiran</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" required name="dh_date" class="form-control pull-right pes-data" id="dh_date" value="<?php echo $tgl->format('Y-m-d'); ?>">
        </div>
    </div>

    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#dh_format').select2();
    $('#dh_date').datepicker({
        autoclose   : true,
        format      : 'yyyy-mm-dd',
        endDate     : '<?php echo $data->kel_periode_b;?>',
        startDate   : '<?php echo $data->kel_periode_a;?>'
    });
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'panitia/add_hadir_submit',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                } else {
                    if ($('#DataTable tbody tr').length > 0){
                        $('.row_zero').remove();
                    }
                    show_msg(dt.msg);
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                    $('#DataTable tbody').append(dt.html);
                    $('#MyModal').modal('hide');
                }
            }
        })
        return false;
    })
</script>