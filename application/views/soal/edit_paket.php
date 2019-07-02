<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="pk_id" value="<?php echo $data->pk_id;?>">
    <div class="form-group col-md-12">
        <label for="pk_name">Nama / Kode Paket Soal</label>
        <input type="text" id="pk_name" name="pk_name" class="form-control" autocomplete="nope" value="<?php echo $data->pk_name;?>">
    </div>
    <div class="form-group col-md-12">
        <label for="frdk_id">Nama Diklat</label>
        <select name="dk_id" id="frdk_id" class="form-control" style="width: 100%">
            <?php
            if ($diklat){
                foreach ($diklat as $val){
                    echo '<option value="'.$val->dk_id.'">'.$val->dk_name.'</option>';
                }
            }
            ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#frdk_id').val('<?php echo $data->dk_id;?>');
    $('#frdk_id').select2();
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'soal/edit_paket_submit',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                } else {
                    load_table();
                    show_msg(dt.msg);
                    $('#MyModal').modal('hide');
                }
            }
        })
        return false;
    })
</script>