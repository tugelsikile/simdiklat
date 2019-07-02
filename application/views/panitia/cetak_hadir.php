<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Cetak</title>


    <!-- Custom styles for this template -->
    <link href="<?php echo base_url('assets/cetak.min.css');?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        @import url('https://fonts.googleapis.com/css?family=Tinos');
        .page{
            max-height: 29.5cm;
        }
        *{
            font-size: 10pt !important; font-family: 'Tinos', serif !important;
        }
        ol li{
            margin: 0; padding: 0;
        }
        ul{
            margin-left: 20px;
        }
        .it-grid td{
            background: none !important;
        }
        .tbdata tbody td{
            height: 80px !important;
        }
        .it-grid tbody td,.it-grid tbody tr{
            background:none !important;
        }
        .it-grid{
            background: none !important;
        }
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    $page       = array_chunk($data->hadir,8,true);
    $no_page    = 1; $nomor = 1;
    foreach ($page as $valPage) {
        ?>
        <div class="page">
            <div style="margin-left: 50px">
                <table width="100%" class="it-grid">
                    <tr>
                        <td align="center" valign="middle" colspan="2">
                            <img src="<?php echo base_url('assets/logo-tutwuri.png'); ?>"
                                 style="width:50px;margin: 10px;float: left;">

                            <div style="margin:auto;float: left;font-weight: bold;display: flex;height:70px;width:600px">
                        <span style="margin: auto;font-size:10pt !important;">PUSAT PENGEMBANGAN DAN PEMBERDAYAAN PENDIDIK DAN TENAGA KEPENDIDIKAN<br>
                            BIDANG MESIN DAN TEKNIK INDUSTRI</span>
                            </div>

                        </td>
                    </tr>
                    <tr style="height: 40px">
                        <td width="100px" align="center">FORMULIR</td>
                        <td align="center"><strong>DAFTAR HADIR PANITIA</strong></td>
                    </tr>
                </table>
                <?php
                if ($no_page == 1){
                    ?>
                    <table width="100%" style="margin-top: 10px">
                        <tr>
                            <td width="150px" valign="top">Nama Diklat</td>
                            <td width="10px" valign="top">:</td>
                            <td valign="top"><strong><?php echo $data->dk_name; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Waktu Pelaksanaan</td>
                            <td>:</td>
                            <td><strong><?php
                                    echo $this->conv->tglIndo($data->kel_periode_a) . ' s/d ' .
                                        $this->conv->tglIndo($data->kel_periode_b);
                                    ?></strong>

                            </td>
                        </tr>
                        <tr>
                            <td>Tempat Pelaksanaan</td>
                            <td>:</td>
                            <td><strong><?php echo $data->pb_name; ?></strong></td>
                        </tr>
                    </table>
                    <?php
                }
                $colspan = count($data->pes);
                ?>
                <table width="100%" class="it-grid it-cetak tbdata" style="margin-top:20px">
                    <thead>
                    <tr>
                        <th width="30px" rowspan="2">No</th>
                        <th width="200px" rowspan="2">Hari, Tanggal</th>
                        <th colspan="<?php echo $colspan; ?>">Nama Panitia</th>
                    </tr>
                    <tr>
                        <?php
                        if ($data->pes) {
                            foreach ($data->pes as $valP) {
                                $fullname = '';
                                if (strlen($valP->pes_gelar_depan) > 0) {
                                    $fullname .= $valP->pes_gelar_depan . '. ';
                                }
                                $fullname .= $valP->pes_fullname;
                                if (strlen($valP->pes_gelar_blk) > 0) {
                                    $fullname .= ', ' . $valP->pes_gelar_blk;
                                }
                                echo '<th>' . $fullname . '</th>';
                            }
                        }
                        ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($valPage) {
                        foreach ($valPage as $valH) {
                            echo '<tr>
                                    <td align="center" valign="middle">' . $nomor . '</td>
                                    <td valign="middle">
                                        ' . $this->conv->hariIndo(date('N', strtotime($valH->dh_date))) . ', 
                                        ' . $this->conv->tglIndo($valH->dh_date) . '
                                    </td>';
                            for($x = 0; $x < count($data->pes); $x++){
                                echo '<td></td>';
                            }
                            echo '</tr>';
                            $nomor++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <?php
                if ($no_page >= count($page)){
                    //var_dump($data);
                    $penjab = '';
                    if ($data->penjab){
                        if (strlen($data->penjab->pes_gelar_depan) > 0){ $penjab .= $data->penjab->pes_gelar_depan.'. '; }
                        $penjab .= $data->penjab->pes_fullname;
                        if (strlen($data->penjab->pes_gelar_blk) > 0){ $penjab .= ', '.$data->penjab->pes_gelar_blk; }
                    } else {
                        $penjab = '(........................................)';
                    }
                    ?>
                    <div style="float: right; width: 200px;margin-top: 20px;text-align: center">
                        Ketua Panitia,
                        <div style="height: 70px"></div>
                        <strong><?php echo $penjab; ?></strong>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div style="position: absolute;bottom:10px;right: 30px; font-size:8pt !important;font-style: italic">
                Daftar Hadir Panitia -
                Halaman <?php echo $no_page;?>
            </div>
        </div>
        <?php
        $no_page++;
    }
}
?>

</body>
</html>