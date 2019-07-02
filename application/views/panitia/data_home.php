<?php
if ($data){
    foreach ($data as $val){
        $fullname = '';
        if (strlen($val->pes_gelar_depan) > 0){ $fullname = $val->pes_gelar_depan.'. '; }
        $fullname .= $val->pes_fullname;
        if (strlen(trim($val->pes_gelar_blk)) > 0){ $fullname .= ', '.$val->pes_gelar_blk; }
        echo '<tr class="row_'.$val->km_id.'">
                <td><input type="checkbox" name="km_id[]" value="'.$val->km_id.'"></td>
                <td>'.$fullname.'</em></td>
                <td>'.$val->km_name.'</td>
                <td align="center">
                    <div class="btn-group btn-group-xs">
                        <button type="button" class="btn btn-info">Action</button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a onclick="show_modal(this);return false" href="'.base_url('panitia/edit_data/'.$val->pes_id.'/'.$val->km_id).'"><i class="fa fa-pencil"></i> Edit data</a></li>
                            <li><a onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->km_id.'"><i class="fa fa-trash"></i> Hapus data</a></li>
                        </ul>
                    </div>
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
