<form id="modalForm" class="form" autocomplete="off">
    <div class="form-group col-md-12">
        <label for="pb_name">Nama Pusat Belajar</label>
        <input type="text" id="pb_name" name="pb_name" class="form-control" autocomplete="nope">
    </div>
    <div class="form-group col-md-6">
        <label for="pb_phone">No. Telp</label>
        <input type="text" id="pb_phone" name="pb_phone" class="form-control" autocomplete="nope">
    </div>
    <div class="form-group col-md-6">
        <label for="pb_email">Email</label>
        <input type="text" id="pb_email" name="pb_email" class="form-control" autocomplete="nope">
    </div>
    <div class="form-group col-md-12">
        <label for="pb_address">Alamat Pusat Belajar</label>
        <textarea name="pb_address" id="pb_address" class="form-control"></textarea>
    </div>
    <div class="form-group col-md-4">
        <label for="kec_id">Kecamatan</label>
        <select name="kec_id" id="kec_id" style="width: 100%" class="form-control">
            <option value="">Kecamatan</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="kab_id">Kabupaten</label>
        <select name="kab_id" id="kab_id" style="width: 100%" class="form-control" onchange="kab_selected()">
            <option value="">Kabupaten</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label for="prov_id">Provinsi</label>
        <select name="prov_id" id="prov_id" style="width: 100%" class="form-control" onchange="prov_selected()">
            <?php
            foreach ($prov as $val){
                echo '<option value="'.$val->prov_id.'">'.$val->name.'</option>';
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
    $('#kec_id,#kab_id,#prov_id').select2();
    prov_selected();
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
                        if (i == dtlen){
                            kab_selected();
                        }
                    });
                }
            }
        });
    }
    function kab_selected() {
        var kab_id = $('#kab_id').val();
        $.ajax({
            url     : base_url + 'wilayah/kab_selected',
            type    : 'POST',
            dataType: 'JSON',
            data    : { kab_id : kab_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#kec_id').html('<option value="">Kecamatan</option>');
                } else {
                    $('#kec_id').html('');
                    $.each(dt.data,function (i,v) {
                        $('#kec_id').append('<option value="'+v.kec_id+'">'+v.name+'</option>');
                    });
                }
            }
        })
    }
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'diklat/add_pb_submit',
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