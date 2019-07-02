<?php
if ($data){
    foreach ($data as $val){
        echo '<tr class="row_'.$val->pk_id.'">
                <td><input type="checkbox" name="pk_id[]" value="'.$val->pk_id.'"></td>
                <td>'.$val->pk_name.'</td>
                <td>'.$val->dk_name.'</td>
                <td align="center">'.$val->cnt.'</td>
                <td align="center">
                    <a data-toggle="tooltip" title="Edit data" onclick="show_modal(this);return false" href="'.base_url('soal/edit_paket/'.$val->pk_id).'" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-pencil"></i></a>
                    <a data-toggle="tooltip" title="Data soal" onclick="load_page(this);return false" data-target="soal" href="'.base_url('soal/data_soal/'.$val->pk_id).'" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-bookmark-o"></i></a>
                    <a data-toggle="tooltip" title="Hapus data" onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->pk_id.'" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></a>
                </td>
              </tr>';
    }
}
?>
<script>
    $('#DataTable tbody input:checkbox').click(function () {
        if ($('#DataTable tbody input:checkbox:checked').length > 0){
            $('.btn-delete').removeClass('disabled');
        } else {
            $('.btn-delete').addClass('disabled');
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
</script>
