<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="pes_id" value="<?php echo $data->pes_id;?>">
    <input type="hidden" name="kel_id" value="<?php echo $kelas->kel_id;?>">
    <input type="hidden" name="km_id" value="<?php echo $kelas->km_id;?>">
    <div class="form-group col-md-6 km_name">
        <label for="km_name" class="">Jabatan dalam Kepanitiaan</label>
        <select name="km_name" id="km_name" class="form-control" style="width: 100%">
            <option value="Penanggung Jawab">Penanggung Jawab</option>
            <option value="Bendahara">Bendahara</option>
            <option value="Sekretaris">Sekretaris</option>
            <option value="Ketua Kelas">Ketua Kelas</option>
            <option value="Admin Kelas">Admin Kelas</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4">
        <label for="pes_nopesukg">Nomor Peserta UKG</label>
        <input type="text" id="pes_nopesukg" name="pes_nopesukg" class="form-control" autocomplete="nope" value="<?php echo $data->pes_nopesukg;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="pes_nuptk">NUPTK</label>
        <input type="text" id="pes_nuptk" name="pes_nuptk" class="form-control" autocomplete="nope" value="<?php echo $data->pes_nuptk;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="pes_nip">NIP</label>
        <input type="text" id="pes_nip" name="pes_nip" class="form-control" autocomplete="nope" value="<?php echo $data->pes_nip;?>">
    </div>
    <div class="form-group col-md-2">
        <label for="pes_gelar_depan">Gelar Depan</label>
        <input type="text" id="pes_gelar_depan" name="pes_gelar_depan" class="form-control" autocomplete="nope" value="<?php echo $data->pes_gelar_depan;?>">
    </div>
    <div class="form-group col-md-7">
        <label for="pes_fullname">Nama Lengkap</label>
        <input type="text" required id="pes_fullname" name="pes_fullname" class="form-control" autocomplete="nope" value="<?php echo $data->pes_fullname;?>">
    </div>
    <div class="form-group col-md-3">
        <label for="pes_gelar_blk">Gelar Belakang</label>
        <input type="text" id="pes_gelar_blk" name="pes_gelar_blk" class="form-control" autocomplete="nope" value="<?php echo $data->pes_gelar_blk;?>">
    </div>
    <div class="form-group col-md-6">
        <label for="pes_bplace">Tempat Lahir</label>
        <input type="text" required id="pes_bplace" name="pes_bplace" class="form-control" autocomplete="nope" value="<?php echo $data->pes_bplace;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="pes_bdate">Tgl Lahir</label>
        <div class="input-group date">
            <div class="input-group-addon">
                <i class="fa fa-calendar"></i>
            </div>
            <input type="text" required name="pes_bdate" class="form-control pull-right" id="pes_bdate" value="<?php echo $data->pes_bdate;?>">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4">
        <label for="pes_agama">Agama</label>
        <select name="pes_agama" id="pes_agama" class="form-control">
            <option value="Islam">Islam</option>
            <option value="Kristen">Kristen</option>
            <option value="Protestan">Protestan</option>
            <option value="Hindu">Hindu</option>
            <option value="Buddha">Buddha</option>
        </select>
    </div>
    <div class="form-group col-md-2">
        <label for="pes_sex">Jenis Kelamin</label>
        <select name="pes_sex" id="pes_sex" class="form-control">
            <option value="L">Laki-Laki</option>
            <option value="P">Perempuan</option>
        </select>
    </div>
    <div class="form-group col-md-3">
        <label for="pangkat_id">Pangkat/Golongan</label>
        <select id="pangkat_id" name="pangkat_id" class="form-control" style="width: 100%">
            <option value="">Pangkat/Golongan</option>
            <?php
            foreach ($pang as $val){
                echo '<option value="'.$val->pangkat_id.'">'.$val->pangkat_kode.' - '.$val->pangkat_name.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <label for="pes_jabatan">Jabatan</label>
        <input type="text" required id="pes_jabatan" name="pes_jabatan" class="form-control" autocomplete="nope" value="<?php echo $data->pes_jabatan;?>">
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <label for="pes_didik">Pendidikan Terakhir</label>
        <input type="text" required id="pes_didik" name="pes_didik" class="form-control" autocomplete="nope" value="<?php echo $data->pes_didik;?>">
    </div>
    <div class="form-group col-md-6">
        <label for="pes_jurusan">Jurusan</label>
        <input type="text" required id="pes_jurusan" name="pes_jurusan" class="form-control" autocomplete="nope" value="<?php echo $data->pes_jurusan;?>">
    </div>
    <div class="form-group col-md-12">
        <label for="pes_address">Alamat Rumah</label>
        <textarea name="pes_address" id="pes_address" class="form-control"><?php echo $data->pes_address;?></textarea>
    </div>
    <div class="form-group col-md-4">
        <label for="pes_phone">No. HP</label>
        <input type="text" id="pes_phone" name="pes_phone" class="form-control" autocomplete="nope" value="<?php echo $data->pes_phone;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="pes_email">Email</label>
        <input type="text" id="pes_email" name="pes_email" class="form-control" autocomplete="nope" value="<?php echo $data->pes_email;?>">
    </div>
    <div class="form-group col-md-4">
        <label for="pes_npwp">NPWP</label>
        <input type="text" id="pes_npwp" name="pes_npwp" class="form-control" autocomplete="nope" value="<?php echo $data->pes_npwp;?>">
    </div>
    <div class="form-group col-md-6 sch_name" style="display:none">
        <label for="sch_name">Asal Institusi / Sekolah</label>
        <div class="input-group date">
            <input type="text" name="sch_name" class="form-control pull-right" id="sch_name">
            <div class="input-group-addon">
                <a href="javascript:;" onclick="cancel_addsch();"><i class="fa fa-close"></i></a>
            </div>
        </div>
    </div>
    <div class="form-group col-md-6 sch_id">
        <label for="sch_id">Asal Institusi / Sekolah</label>
        <select id="sch_id" name="sch_id" class="form-control" style="width: 100%" onchange="sch_selected()">
            <option value="">Asal Institusi / Sekolah</option>
            <option value="x">Lainnya</option>
            <?php
            foreach ($sch as $val){
                echo '<option value="'.$val->sch_id.'">'.$val->sch_name.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-4">
        <label for="sch_negeri">Status Sekolah</label>
        <select name="sch_negeri" id="sch_negeri" class="form-control sch-data" style="width: 100%" disabled>
            <option value="NEGERI">NEGERI</option>
            <option value="SWASTA">SWASTA</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="sch_nss">NSS</label>
        <input type="text" id="sch_nss" name="sch_nss" disabled class="form-control sch-data" autocomplete="nope">
    </div>
    <div class="form-group col-md-4">
        <label for="sch_npsn">NPSN</label>
        <input type="text" id="sch_npsn" name="sch_npsn" disabled class="form-control sch-data" autocomplete="nope">
    </div>
    <div class="form-group col-md-12">
        <label for="sch_address">Alamat Sekolah / Institusi</label>
        <textarea name="sch_address" id="sch_address" disabled class="form-control sch-data"></textarea>
    </div>
    <div class="form-group col-md-6">
        <label for="kab_id">Kabupaten</label>
        <select name="kab_id" id="kab_id" class="form-control sch-data" style="width: 100%" disabled>

        </select>
    </div>
    <div class="form-group col-md-6">
        <label for="prov_id">Provinsi</label>
        <select name="prov_id" id="prov_id" class="form-control sch-data" style="width: 100%" disabled onchange="prov_selected()">
            <?php
            foreach ($prov as $val){
                echo '<option value="'.$val->prov_id.'">'.$val->name.'</option>';
            }
            ?>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="sch_phone">No. Telp</label>
        <input type="text" id="sch_phone" name="sch_phone" disabled class="form-control sch-data" autocomplete="nope">
    </div>
    <div class="form-group col-md-4">
        <label for="sch_fax">No. Fax</label>
        <input type="text" id="sch_fax" name="sch_fax" disabled class="form-control sch-data" autocomplete="nope">
    </div>
    <div class="form-group col-md-4">
        <label for="sch_email">Email</label>
        <input type="text" id="sch_email" name="sch_email" disabled class="form-control sch-data" autocomplete="nope">
    </div>

    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#pes_agama').val('<?php echo $data->pes_agama;?>');
    $('#pes_sex').val('<?php echo $data->pes_sex;?>');
    $('#pangkat_id').val('<?php echo $data->pangkat_id;?>');
    $('#sch_id').val('<?php echo $data->sch_id;?>');
    $('#km_name').val('<?php echo $kelas->km_name;?>');
    sch_selected();
    $('#sch_id,#pangkat_id,#prov_id,#kab_id,#km_name').select2();
    $('#pes_bdate').datepicker({
        autoclose: true,
        format : 'yyyy-mm-dd'
    });
    function cancel_addsch() {
        $('#sch_id').val(0).select2();
        $('.sch_id').show();
        $('.sch_name').hide();
        $('#sch_name').val('');
        $('.sch-data').prop({'disabled':true});
    }
    function sch_selected() {
        var sch_id  = $('#sch_id').val();
        if (sch_id == 'x'){
            $('.sch_id').hide();
            $('.sch_name').show();
            $('.sch-data').prop({'disabled':false});
        } else {
            $.ajax({
                url     : base_url + 'school/sch_selected',
                type    : 'POST',
                data    : { sch_id : sch_id },
                dataType: 'JSON',
                success : function (dt) {
                    if (dt.t == 0){
                        //show_msg(dt.msg,'error');
                        $('#sch_nss,#sch_npsn,#sch_address,#sch_phone,#sch_fax,#sch_email').val('');
                    } else {
                        //$('#prov_id,#kab_id').select2({'destroy'});
                        $('#prov_id').val(dt.prov_id);
                        $('#kab_id').html('');
                        $.each(dt.dtkab,function (i,v) {
                            $('#kab_id').append('<option value="'+v.kab_id+'">'+v.name+'</option>');
                            if(i + 1 >= dt.dtkab.length){
                                $('#kab_id').val(dt.kab_id);
                            }
                        });
                        $('#sch_negeri').val(dt.sch_negeri);
                        $('#sch_nss').val(dt.sch_nss);
                        $('#sch_npsn').val(dt.sch_npsn);
                        $('#sch_address').val(dt.sch_address);
                        $('#sch_phone').val(dt.sch_phone);
                        $('#sch_fax').val(dt.sch_fax);
                        $('#sch_email').val(dt.sch_email);
                        $('#prov_id,#kab_id').select2();
                    }
                }
            })
        }
    }
    function prov_selected() {
        var prov_id = $('#prov_id').val();
        $.ajax({
            url     : base_url + 'wilayah/prov_selected',
            type    : 'POST',
            dataType: 'JSON',
            data    : { prov_id : prov_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#kab_id').html('<option value="">Kabupaten</option>');
                } else {
                    $('#kab_id').html('');
                    var dtlen = dt.data.length - 1;
                    $.each(dt.data,function (i,v) {
                        $('#kab_id').append('<option value="'+v.kab_id+'">'+v.name+'</option>');
                    });
                }
            }
        });
    }
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'panitia/edit_data_submit',
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