<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="dk_id" value="<?php echo $data->dk_id;?>">
    <div class="form-group col-md-12">
        <label for="dk_name">Nama Diklat</label>
        <input type="text" id="dk_name" name="dk_name" class="form-control" autocomplete="nope" value="<?php echo $data->dk_name;?>">
    </div>
    <div class="form-group col-md-12">
        <label for="dk_keterangan">Keterangan Diklat</label>
        <textarea name="dk_keterangan" id="dk_keterangan" class="form-control"><?php echo $data->dk_keterangan;?></textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="dk_nomor_surat">Nomor Surat</label>
        <input type="text" id="dk_nomor_surat" name="dk_nomor_surat" class="form-control" autocomplete="nope" value="<?php echo $data->dk_nomor_surat;?>">
    </div>
    <div class="form-group col-md-3">
        <label for="dk_date_start">Tgl Pelaksanaan</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_date_start" class="form-control pull-right" id="dk_date_start" value="<?php echo $data->dk_date_start;?>">
        </div>
    </div>
    <div class="form-group col-md-3">
        <label for="dk_date_end">Tgl Selesai</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_date_end" class="form-control pull-right" id="dk_date_end" value="<?php echo $data->dk_date_end;?>">
        </div>
    </div>
    <div class="form-group col-md-12">
        <label for="dk_place">Tempat Pelaksanaan</label>
        <input type="text" id="dk_place" name="dk_place" class="form-control" autocomplete="nope" value="<?php echo $data->dk_place;?>">
    </div>
    <div class="form-group col-md-12">
        <label for="dk_place_address">Alamat Tempat Pelaksanaan</label>
        <textarea name="dk_place_address" id="dk_place_address" class="form-control"><?php echo $data->dk_place_address;?></textarea>
    </div>
    <div class="form-group col-md-3">
        <label for="dk_check_in">Tgl <em>Check-in</em></label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_check_in" class="form-control pull-right" id="dk_check_in" value="<?php echo date('Y-m-d',strtotime($data->dk_check_in));?>">
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="dk_check_in_jam">Jam <em>Check-in</em></label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" name="dk_check_in_jam" class="form-control pull-right" id="dk_check_in_jam" value="<?php echo date('H:i',strtotime($data->dk_check_in));?>">
        </div>
    </div>
    <div class="form-group col-md-3">
        <label for="dk_check_out">Tgl <em>Check-out</em></label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_check_out" class="form-control pull-right" id="dk_check_out" value="<?php echo date('Y-m-d',strtotime($data->dk_check_out));?>">
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="dk_check_out_jam">Jam <em>Check-out</em></label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" name="dk_check_out_jam" class="form-control pull-right" id="dk_check_out_jam" value="<?php echo date('H:i',strtotime($data->dk_check_out));?>">
        </div>
    </div>
    <div class="form-group col-md-3">
        <label for="dk_pembukaan">Tgl Pembukaan</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_pembukaan" class="form-control pull-right" id="dk_pembukaan" value="<?php echo date('Y-m-d',strtotime($data->dk_pembukaan));?>">
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="dk_pembukaan_jam">Jam Pembukaan</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" name="dk_pembukaan_jam" class="form-control pull-right" id="dk_pembukaan_jam" value="<?php echo date('H:i',strtotime($data->dk_pembukaan));?>">
        </div>
    </div>
    <div class="form-group col-md-3">
        <label for="dk_penutupan">Tgl Penutupan</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" name="dk_penutupan" class="form-control pull-right" id="dk_penutupan" value="<?php echo date('Y-m-d',strtotime($data->dk_penutupan));?>">
        </div>
    </div>
    <div class="form-group col-md-2">
        <label for="dk_penutupan_jam">Jam Penutupan</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-clock-o"></i>
            </div>
            <input type="text" name="dk_penutupan_jam" class="form-control pull-right" id="dk_penutupan_jam" value="<?php echo date('H:i',strtotime($data->dk_penutupan));?>">
        </div>
    </div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#dk_date_start,#dk_date_end,#dk_check_in,#dk_check_out,#dk_penutupan,#dk_pembukaan').datepicker({
        autoclose: true,
        format : 'yyyy-mm-dd'
    });
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'diklat/edit_data_submit',
            type    : 'POST',
            dataType: 'JSON',
            data    : $(this).serialize(),
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('#modalForm .btn-submit').prop({'disabled':false}).html('<i class="fa fa-floppy-o"></i> Submit');
                } else {
                    show_msg(dt.msg);
                    load_table();
                    $('#MyModal').modal('hide');
                }
            }
        })
        return false;
    })
</script>