<?php
if ($data){
    foreach ($data as $val){
        $tgl = $this->conv->hariIndo(date('N',strtotime($val->dh_date))).', '.
            $this->conv->tglIndo($val->dh_date);
        $jam_1 = date('H:i',strtotime($val->dm_jam));
        $jam_2 = '';
        $date = new DateTime($val->dh_date.' '.$val->dm_jam);
        $date->modify('+45 minutes');
        $jam_2 = $date->format('H:i');
        echo '<tr class="row_'.$val->dm_id.'">
                <td><input type="checkbox" name="dm_id[]" value="'.$val->dm_id.'"></td>
                <td>'.$tgl.'</td>
                <td align="center">'.$val->dm_jam_ke.'</td>
                <td align="center">'.$jam_1.' s/d '.$jam_2.'</td>
                <td>'.$val->dm_materi.'</td>
                <td>'.$val->tutor_1.'</td>
                <td>'.$val->tutor_2.'</td>
                <td>'.$val->tutor_3.'</td>
                <td>'.$val->tutor_4.'</td>
                <td align="center">
                    <a data-toggle="tooltip" title="Edit pembelajaran" onclick="show_modal(this);return false" href="'.base_url('pengajar/edit_pembelajaran/'.$val->dm_id).'" class="btn btn-xs btn-primary btn-flat"><i class="fa fa-pencil"></i></a>
                    <a data-toggle="tooltip" title="Hapus pembelajaran" onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->dm_id.'" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></a>
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
