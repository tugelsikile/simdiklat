<form class="form form-horizontal" id="uploadForm">
    <div class="col-md-12 fileinputwrapper">
        <div class="input-group mb-3 col-md-12" style="margin-bottom: 20px">
            <div class="custom-file">
                <input name="file" type="file" class="custom-file-input" id="files">
                <label class="custom-file-label" for="files">Choose file</label>
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
        <a class="btn btn-primary" href="<?php echo base_url('assets/format/upload_peserta.xlsx');?>" target="_blank"><i class="fa fa-download"></i> Download Format</a>
    </div>
   <div class="clearfix"></div>
</form>


<style>
    .custom-file-label{
        overflow: hidden;
    }
</style>
<script>

    $('#uploadForm #files').change(function (e) {
        var file_name = $("#uploadForm #files").val();
        $('#uploadForm .custom-file-label').text(file_name);
    });
    $('.progresswrapper').hide();
    $('#uploadForm').on('submit', function(event){
        event.preventDefault();
        if ($('#uploadForm #files').val().length == 0){
            show_msg('Pilih file yang akan diupload','error');
        } else {
            var formdata        = new FormData($('#uploadForm')[0]);
            formdata.append('kel_id','<?php echo $data->kel_id;?>');
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
                url         : base_url + 'peserta/import_submit',
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
                        var dtlen = dt.data.length;
                        $.each(dt.data,function (i,v) {
                            $('.info').html('Memproses data '+v.pes_fullname).show();
                            $.ajax({
                                async   : false,
                                cache   : false,
                                type    : 'POST',
                                dataType: 'JSON',
                                data    : { data : dt.data[i] },
                                url     : base_url + 'peserta/import_proses',
                                success : function (dts) {
                                    if (dts.t == 0){
                                        $('.error').append(dt.msg).show();
                                    } else {
                                        if ((i+1) >= dtlen){
                                            load_table();
                                            $('#MyModal').modal('hide');
                                        }
                                    }
                                }
                            })
                        })

                        //$('#uploadForm')[0].reset();
                        //$('.custom-file-label').html('Choose file');
                        //$('.fileinputwrapper').show();
                        //$('.progresswrapper').hide();
                        //show_msg(dt.msg);
                        //$('#MyModal').modal('hide');
                        //load_table();
                    }
                }
            });
        }
        return false;
    });
</script>