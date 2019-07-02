<?php
if ($data){
    foreach ($data as $val){
        if (strlen($val->dh_page_1) == 0){
            $upload = '<i class="fa fa-close text-danger"></i>';
        } else {
            $upload = '<i class="fa fa-check text-success"></i>';
        }
        $tgl = $this->conv->hariIndo(date('N',strtotime($val->dh_date))).', '.
            $this->conv->tglIndo($val->dh_date);
        switch($val->dh_format){
            case 'pagi' : $format = 'Pagi - Siang'; break;
            case 'siang' : $format = 'Pagi - Siang - Malam'; break;
            case 'malam' : $format = 'Siang - Malam'; break;
        }
        echo '<tr class="row_'.$val->dh_id.'">
                <td><input type="checkbox" name="dh_id[]" value="'.$val->dh_id.'"></td>
                <td>'.$val->kel_name.'</td>
                <td>'.$tgl.'</td>
                <td>'.$format.'</td>
                <td align="center">'.$upload.'</td>
                <td align="center">
                    <a onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->dh_id.'" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></a>
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
