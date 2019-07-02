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
        echo '<tr class="row_'.$val->dh_id.'">
                <td><input type="checkbox" name="dh_id[]" value="'.$val->dh_id.'"></td>
                <td>'.$val->kel_name.'</td>
                <td>'.$tgl.'</td>
                <td align="center">'.$val->cnt.'</td>
                <td align="center">'.$upload.'</td>
                <td align="center">
                    <a data-toggle="tooltip" title="Daftar pembelajaran" onclick="load_page(this);return false" href="'.base_url('pengajar/pembelajaran/'.$val->kel_id.'/'.$val->dh_date).'" data-target="kelas" class="btn-flat btn btn-xs btn-primary"><i class="fa fa-calendar-check-o"></i></a>
                    <a data-toggle="tooltip" title="Hapus tanggal" onclick="delete_data(this);return false" href="javascript:;" data-id="'.$val->dh_id.'" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></a>
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
