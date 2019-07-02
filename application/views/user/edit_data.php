<form id="modalForm" class="form">
    <input type="hidden" name="user_id" value="<?php echo $data->user_id;?>">
    <div class="form-group col-md-6">
        <label for="user_name">Nama Pengguna</label>
        <input type="text" id="user_name" name="user_name" class="form-control" autocomplete="off" value="<?php echo $data->user_name;?>">
    </div>
    <div class="form-group col-md-6">
        <label for="user_password">Kata Sandi</label>
        <input type="password" id="user_password" name="user_password" class="form-control" autocomplete="off" placeholder="Biarkan kosong jika tidak ingin dirubah">
    </div>
    <div class="form-group col-md-4">
        <label for="lvl_id">Level Pengguna</label>
        <select name="lvl_id" id="lvl_id" class="form-control" onchange="chkpb()">

            <?php
            foreach ($level as $val){
                echo '<option value="'.$val->lvl_id.'">'.$val->lvl_name.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-8 pb-group">
        <label for="pb_id">Pusat Belajar</label>
        <select name="pb_id" id="pb_id" class="form-control" style="width: 100%;">
            <option value="">Pusat Belajar</option>
            <?php
            foreach ($pb as $val){
                echo '<option value="'.$val->pb_id.'">'.$val->pb_name.'</option>';
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
    $('#lvl_id').val('<?php echo $data->user_level;?>');
    $('#pb_id').val('<?php echo $data->pb_id;?>');
    $('#pb_id').select2();
    chkpb();
    function chkpb() {
        var lvl_id = $('#lvl_id').val();
        if (lvl_id == 2){
            $('.pb-group').show();
        } else {
            $('.pb-group').hide();
        }
    }
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'user/edit_data_submit',
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