<?php
if ($data){
    foreach ($data as $val){
        $periode = $this->conv->tglIndo($val->kel_periode_a).' s/d '.$this->conv->tglIndo($val->kel_periode_b);
        echo '<tr class="row_'.$val->kel_id.'">
                <td><input type="checkbox" name="kel_id[]" value="'.$val->kel_id.'"></td>
                <td align="center">
                    <div class="btn-group btn-group-xs">
                        <button type="button" class="btn btn-info">Action</button>
                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a onclick="show_modal(this);return false" href="'.base_url('diklat/edit_kelas/'.$val->kel_id).'"><i class="fa fa-pencil"></i> Edit data</a></li>
                            <li><a onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->kel_id.'"><i class="fa fa-trash"></i> Hapus data</a></li>
                            <li class="divider"></li>
                            <li><a data-target="diklat" href="'.base_url('peserta/daftar/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-user-circle"></i> Data Peserta</a></li>
                        </ul>
                    </div>
                </td>
                <td>'.$val->kel_name.'</td>
                <td>'.$periode.'</td>
                <td>'.$val->pb_name.'</td>
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
