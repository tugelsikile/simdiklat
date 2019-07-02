<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="dk_id" value="<?php echo $data->dk_id;?>">
    <input type="hidden" name="kel_id" value="<?php echo $data->kel_id;?>">
    <div class="form-group col-md-12">
        <label for="kel_name">Nama Kelas</label>
        <input type="text" id="kel_name" name="kel_name" class="form-control" autocomplete="nope" value="<?php echo $data->kel_name;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="kel_periode_a">Periode Pelaksanaan Mulai</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="kel_periode_a" class="form-control pull-right" id="kel_periode_a" value="<?php echo $data->kel_periode_a;?>">
        </div>
    </div>
    <div class="form-group col-md-4">
        <label for="kel_periode_b">Periode Pelaksanaan Berakhir</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="kel_periode_b" class="form-control pull-right" id="kel_periode_b" value="<?php echo $data->kel_periode_b;?>">
        </div>
    </div>
    <div class="form-group col-md-12">
        <label for="pb_id">Pusat Belajar</label>
        <select name="pb_id" id="pb_id" style="width: 100%" class="form-control">
            <option value="">Pusat Belajar</option>
            <?php
            foreach ($pb as $val){
                echo '<option value="'.$val->pb_id.'">'.$val->pb_name.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#pb_id').val('<?php echo $data->pb_id;?>');
    $('#pb_id').select2();
    $('#kel_periode_a,#kel_periode_b').datepicker({
        autoclose: true,
        format : 'yyyy-mm-dd'
    })
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'diklat/edit_kelas_submit',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                } else {
                    show_msg(dt.msg);
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                    load_table();
                    $('#MyModal').modal('hide');
                }
            }
        })
        return false;
    })
</script>