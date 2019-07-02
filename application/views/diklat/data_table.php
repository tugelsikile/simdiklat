<?php
if ($data){
    foreach ($data as $val){
        echo '<tr class="row_'.$val->dk_id.'">
                <td><input type="checkbox" name="dk_id[]" value="'.$val->dk_id.'"></td>
                <td align="center">
                    <div class="btn-group btn-group-xs">
                        <button type="button" class="btn btn-info">Action</button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a onclick="show_modal(this);return false" href="'.base_url('diklat/edit_data/'.$val->dk_id).'"><i class="fa fa-pencil"></i> Edit data</a></li>
                            <li><a onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->dk_id.'"><i class="fa fa-trash"></i> Hapus data</a></li>
                            <li class="divider"></li>
                            <li><a data-target="diklat" href="'.base_url('diklat/kelas/'.$val->dk_id).'" onclick="load_page(this);return false"><i class="fa fa-mortar-board"></i> Data Kelas</a></li>
                        </ul>
                    </div>
                </td>
                <td>'.$val->dk_name.'<br><em>'.$val->dk_keterangan.'</em></td>
                <td>'.$val->dk_place.'<br><em>'.$val->dk_place_address.'</em></td>
                <td align="center">'.$val->cnt.'</td>
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
