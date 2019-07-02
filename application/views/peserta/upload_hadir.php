<style>

</style>
<form class="form form-horizontal" id="uploadForm">
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 1</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files1">
                <label class="custom-file-label" for="files1">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 2</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files2">
                <label class="custom-file-label" for="files2">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 3</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files3">
                <label class="custom-file-label" for="files3">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 4</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files4">
                <label class="custom-file-label" for="files4">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 5</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files5">
                <label class="custom-file-label" for="files5">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 6</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files6">
                <label class="custom-file-label" for="files6">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 7</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files7">
                <label class="custom-file-label" for="files7">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 fileinputwrapper">
        <label>Halaman 8</label>
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file[]" type="file" class="custom-file-input" id="files8">
                <label class="custom-file-label" for="files8">Choose file</label>
            </div>
        </div>
    </div>
    <div class="col-md-12 progresswrapper">
        <div class="progress">
            <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only">0%</span>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="info alert alert-info text-left" style="display:none"></div>
        <div class="error alert alert-danger text-left" style="display:none"></div>
    </div>
    <div class="col-md-12">
        <button class="btn btn-primary input-group-text" id=""><i class="fa fa-upload"></i> Upload</button>
    </div>
   <div class="clearfix"></div>
</form>


<style>
    .custom-file-label{
        overflow: hidden;
    }
</style>
<script>

    $('.custom-file-input').change(function (e) {
        var file_name   = $(this).val();
        $(this).next('.custom-file-label').text(file_name);

    });

    $('.progresswrapper').hide();
    $('#uploadForm').on('submit', function(event){
        event.preventDefault();
        if ($('#uploadForm .custom-file-input').val().length == 0){
            show_msg('Pilih file yang akan diupload','error');
        } else {
            var formdata        = new FormData($('#uploadForm')[0]);
            formdata.append('dh_id','<?php echo $data->dh_id;?>');
            $('.fileinputwrapper').hide();
            $('.progresswrapper').show();
            $.ajax({
                xhr     : function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e){
                        if(e.lengthComputable){
                            console.log('Bytes Loaded : ' + e.loaded);
                            console.log('Total Size : ' + e.total);
                            console.log('Persen : ' + (e.loaded / e.total));
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('#progressBar').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                type        : 'POST',
                url         : base_url + 'peserta/upload_hadir_submit',
                data        : formdata,
                processData : false,
                contentType : false,
                dataType    : 'JSON',
                success     : function(dt){
                    if (dt.t == 0){
                        $('.fileinputwrapper').show();
                        $('.progresswrapper').hide();
                        show_msg(dt.msg,'error');
                    } else {
                        $('.progresswrapper').hide();
                        $('#MyModal').modal('hide');
                        show_msg(dt.msg);
                        load_table();
                    }
                }
            });
        }
        return false;
    });
</script>