<?php
if ($data){
    foreach ($data as $val){
        $periode = $this->conv->tglIndo($val->kel_periode_a).' s/d '.$this->conv->tglIndo($val->kel_periode_b);
        echo '<tr class="row_'.$val->kel_id.'">
                <td align="center"><input type="checkbox" name="kel_id[]" value="'.$val->kel_id.'"></td>
                <td>'.$val->kel_name.'</td>
                <td>'.$periode.'</td>
                <td>'.$val->pb_name.'</td>
                <td align="center">'.$val->cnt.'</td>
                <td>
                    <div class="btn-group btn-group-xs" style="margin-bottom: 5px">
                        <button type="button" class="btn btn-info btn-flat">Data</button>
                            <button type="button" class="btn-flat btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-target="kelas" href="'.base_url('peserta/daftar/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-user-circle"></i> Daftar Peserta</a></li>
                            <li><a data-target="kelas" href="'.base_url('pengajar/daftar/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-user-circle"></i> Daftar Pengajar</a></li>
                            <li><a data-target="kelas" href="'.base_url('panitia/daftar/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-user-circle"></i> Daftar Panitia</a></li>
                        </ul>
                    </div>
                    <div class="btn-group btn-group-xs" style="margin-bottom: 5px">
                        <button type="button" class="btn btn-info btn-flat">Daftar Hadir</button>
                            <button type="button" class="btn-flat btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-target="kelas" href="'.base_url('peserta/hadir/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-calendar-check-o"></i> Daftar Hadir Peserta</a></li>
                            <li><a data-target="kelas" href="'.base_url('pengajar/hadir/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-calendar-check-o"></i> Daftar Hadir Pengajar</a></li>
                            <li><a data-target="kelas" href="'.base_url('panitia/hadir/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-calendar-check-o"></i> Daftar Hadir Panitia</a></li>
                        </ul>
                    </div>
                    <a style="margin-bottom: 5px" class="btn btn-xs btn-info btn-flat" data-target="kelas" href="'.base_url('kelas/cetak/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-print"></i> Halaman Cetak</a>
                    <div class="btn-group btn-group-xs" style="margin-bottom: 5px">
                        <button type="button" class="btn btn-info btn-flat">Jawaban</button>
                            <button type="button" class="btn-flat btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-target="kelas" href="'.base_url('jawaban/pre_test/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-square-o"></i> Jawaban Pre Test</a></li>
                            <li><a data-target="kelas" href="'.base_url('jawaban/post_test/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-square-o"></i> Jawaban Post Test</a></li>
                        </ul>
                    </div>
                    <div class="btn-group btn-group-xs" style="margin-bottom: 5px">
                        <button type="button" class="btn btn-info btn-flat">Nilai</button>
                            <button type="button" class="btn-flat btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a data-target="kelas" href="'.base_url('nilai/sikap/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-circle"></i> Nilai Sikap</a></li>
                            <li><a data-target="kelas" href="'.base_url('nilai/keterampilan/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-circle"></i> Nilai Keterampilan</a></li>
                            <li><a data-target="kelas" href="'.base_url('nilai/prepost/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-circle"></i> Nilai Pre / Post Test</a></li>
                            <li><a data-target="kelas" href="'.base_url('nilai/akhir/'.$val->kel_id).'" onclick="load_page(this);return false"><i class="fa fa-check-circle"></i> Nilai Akhir</a></li>
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
