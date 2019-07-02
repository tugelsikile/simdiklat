<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="kel_id" value="<?php echo $kelas->kel_id;?>">
    <input type="hidden" name="dh_id" value="<?php echo $dh->dh_id;?>">
    <div class="form-group col-md-4">
        <label for="dm_jam">Jam Pelajaran</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" required name="dm_jam" class="form-control pull-right pes-data" id="dm_jam" value="" placeholder="08:00">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-12">
        <label for="dm_materi">Materi Pembelajaran</label>
        <input type="text" name="dm_materi" class="form-control" required>
    </div>
    <div class="form-group col-md-12">
        <label for="tutor_1">Pengajar 1</label>
        <select name="tutor_1" id="tutor_1" class="form-control" style="width: 100%">
            <option value="">Nama Pengajar</option>
            <?php
            foreach ($tutor as $val){
                $fullname = '';
                if (strlen($val->pes_gelar_depan) > 0){ $fullname = $val->pes_gelar_depan.'. '; }
                $fullname .= $val->pes_fullname;
                if (strlen($val->pes_gelar_blk) > 0){ $fullname .= $val->pes_gelar_blk; }
                $fullname .= ' - '.$val->sch_name;
                echo '<option value="'.$val->pes_id.'">'.$fullname.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-12">
        <label for="tutor_2">Pengajar 2</label>
        <select name="tutor_2" id="tutor_2" class="form-control" style="width: 100%">
            <option value="">Nama Pengajar</option>
            <?php
            foreach ($tutor as $val){
                $fullname = '';
                if (strlen($val->pes_gelar_depan) > 0){ $fullname = $val->pes_gelar_depan.'. '; }
                $fullname .= $val->pes_fullname;
                if (strlen($val->pes_gelar_blk) > 0){ $fullname .= $val->pes_gelar_blk; }
                $fullname .= ' - '.$val->sch_name;
                echo '<option value="'.$val->pes_id.'">'.$fullname.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-12">
        <label for="tutor_3">Pengajar 3</label>
        <select name="tutor_3" id="tutor_3" class="form-control" style="width: 100%">
            <option value="">Nama Pengajar</option>
            <?php
            foreach ($tutor as $val){
                $fullname = '';
                if (strlen($val->pes_gelar_depan) > 0){ $fullname = $val->pes_gelar_depan.'. '; }
                $fullname .= $val->pes_fullname;
                if (strlen($val->pes_gelar_blk) > 0){ $fullname .= $val->pes_gelar_blk; }
                $fullname .= ' - '.$val->sch_name;
                echo '<option value="'.$val->pes_id.'">'.$fullname.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-12">
        <label for="tutor_4">Pengajar 4</label>
        <select name="tutor_4" id="tutor_4" class="form-control" style="width: 100%">
            <option value="">Nama Pengajar</option>
            <?php
            foreach ($tutor as $val){
                $fullname = '';
                if (strlen($val->pes_gelar_depan) > 0){ $fullname = $val->pes_gelar_depan.'. '; }
                $fullname .= $val->pes_fullname;
                if (strlen($val->pes_gelar_blk) > 0){ $fullname .= $val->pes_gelar_blk; }
                $fullname .= ' - '.$val->sch_name;
                echo '<option value="'.$val->pes_id.'">'.$fullname.'</option>';
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
    $('#tutor_1,#tutor_2,#tutor_3,#tutor_4').select2();
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'pengajar/add_pembelajaran_submit',
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