<form id="modalForm" class="form" autocomplete="off">
    <input type="hidden" name="pk_id" value="<?php echo $paket->pk_id;?>">
    <div class="form-group col-md-12">
        <label for="pk_name">Nama Diklat</label>
        <input type="text" id="pk_name" name="pk_name" class="form-control" disabled autocomplete="nope" value="<?php echo $diklat;?>">
    </div>
    <div class="form-group col-md-12">
        <label for="pk_name">Nama / Kode Paket Soal</label>
        <input type="text" id="pk_name" name="pk_name" class="form-control" disabled autocomplete="nope" value="<?php echo $paket->pk_name;?>">
    </div>
    <div class="form-group col-md-3">
        <label for="soal_nomor">Nomor Soal</label>
        <input type="number" name="soal_nomor" id="soal_nomor" value="<?php echo $urut; ?>" min="1" max="999" class="form-control">
    </div>
    <div class="form-group col-md-3">
        <label for="soal_jawab">Jawaban</label>
        <select name="soal_jawab" id="soal_jawab" style="width: 100%">
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
        </select>
    </div>
    <div class="clearfix"></div>
    <div class="form-group col-md-6">
        <button type="submit" class="btn-submit btn btn-primary btn-sm"><i class="fa fa-floppy-o"></i> Submit</button>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#soal_jawab').select2();
    $('#modalForm').submit(function () {
        $('#modalForm .btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'soal/add_soal_submit',
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