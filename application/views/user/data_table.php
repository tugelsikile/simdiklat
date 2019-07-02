<?php
if ($data){
    foreach ($data as $val){
        echo '<tr class="row_'.$val->user_id.'">
                <td><input type="checkbox" name="user_id[]" value="'.$val->user_id.'"></td>
                <td>'.$val->user_name.'</td>
                <td>'.$val->lvl_name.'</td>
                <td>'.$val->pb_name.'</td>
                <td align="center">
                    <a data-toggle="tooltip" title="Edit data" onclick="show_modal(this);return false" href="'.base_url('user/edit_data/'.$val->user_id).'" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>
                    <a data-toggle="tooltip" title="Hapus data" onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->user_id.'" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
                    <a data-toggle="tooltip" title="Reset Password" onclick="reset_pass(this);return false" href="javascript:;" data-id="'.$val->user_id.'" class="btn btn-warning btn-xs"><i class="fa fa-refresh"></i></a>
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
