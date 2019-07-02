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
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    foreach ($data as $valH){
        $page = 1;
        ?>
        <div class="page">
            <div style="margin-left: 50px">
                <table width="100%" class="it-grid">
                    <tr>
                        <td align="center" valign="middle" colspan="2">
                            <img src="<?php echo base_url('assets/logo-tutwuri.png');?>" style="width:50px;margin: 10px;float: left;">

                            <div style="margin:auto;float: left;font-weight: bold;display: flex;height:70px;width:600px">
                        <span style="margin: auto;font-size:10pt !important;">PUSAT PENGEMBANGAN DAN PEMBERDAYAAN PENDIDIK DAN TENAGA KEPENDIDIKAN<br>
                            BIDANG MESIN DAN TEKNIK INDUSTRI</span>
                            </div>

                        </td>
                    </tr>
                    <tr style="height: 40px">
                        <td width="100px" align="center">FORMULIR</td>
                        <td align="center"><strong>DAFTAR HADIR PENGAJAR</strong></td>
                    </tr>
                </table>
                <?php
                if ($page == 1){
                    ?>
                    <table width="100%" style="margin-top: 10px">
                        <tr>
                            <td width="150px" valign="top">Nama Diklat</td>
                            <td width="10px" valign="top">:</td>
                            <td valign="top"><strong><?php echo $valH->dk_name;?></strong></td>
                        </tr>
                        <tr>
                            <td>Waktu Pelaksanaan</td>
                            <td>:</td>
                            <td><strong><?php
                                    echo $this->conv->tglIndo($valH->kel_periode_a).' s/d '.
                                        $this->conv->tglIndo($valH->kel_periode_b);
                                    ?></strong>

                            </td>
                        </tr>
                        <tr>
                            <td>Tempat Pelaksanaan</td>
                            <td>:</td>
                            <td><strong><?php echo $valH->pb_name;?></strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal Kehadiran</td>
                            <td>:</td>
                            <td><strong><?php echo $this->conv->hariIndo(date('N',strtotime($valH->dh_date))).', '.$this->conv->tglIndo($valH->dh_date);?></strong></td>
                        </tr>
                    </table>
                    <?php
                }
                ?>
                <table width="100%" class="it-grid it-cetak" style="margin-top: 10px">
                    <thead>
                    <tr>
                        <th width="100px">Waktu</th>
                        <th width="300px">Materi</th>
                        <th width="200px">Pengajar</th>
                        <th width="100px" colspan="2">Tanda Tangan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($valH->jam){
                        foreach ($valH->jam as $valJam){
                            $jam_1 = date('H:i',strtotime($valJam->dm_jam));
                            $jam_2 = '';
                            $date = new DateTime($valH->dh_date.' '.$valJam->dm_jam);
                            $date->modify('+45 minutes');
                            $jam_2 = $date->format('H:i');
                            ?>
                            <tr>
                                <td align="center"><?php echo $jam_1.' s/d '.$jam_2;?></td>
                                <td><?php echo $valJam->dm_materi;?></td>
                                <td>
                                    <?php
                                    $tutor[0] = $tutor[1] = '';
                                    if (strlen($valJam->tutor_1) > 0){ $tutor[0] = $valJam->tutor_1; }
                                    if (strlen($valJam->tutor_2) > 0){ $tutor[1] = $valJam->tutor_2; }
                                    if (count($tutor) > 0){
                                        echo '<ol type="1">';
                                        for($i = 0; $i <= 1; $i++){
                                            echo '<li>'.$tutor[$i].'</li>';
                                        }
                                        echo '</ol>';
                                    } else {
                                        echo '<ol type="1"><li></li><li></li></ol>';
                                    }
                                    ?>
                                </td>
                                <td valign="top" style="font-size:8pt !important;">1</td>
                                <td valign="bottom" style="font-size:8pt !important;">2</td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                    </tbody>
                </table>
                <div style="float: right;width: 200px;text-align: center;margin-top:20px">
                    Ketua Panitia,
                    <div style="height:50px"></div>
                    <?php
                    if ($valH->penjab){
                        if (strlen($valH->penjab->pes_gelar_depan) > 0){
                            $penjab = $valH->penjab->pes_gelar_depan.'. '.$valH->penjab->pes_fullname;
                        } else {
                            $penjab = $valH->penjab->pes_fullname;
                        }
                        if (strlen($valH->penjab->pes_gelar_blk) > 0){ $penjab .= ', '.$valH->penjab->pes_gelar_blk; }
                    } else {
                        $penjab = '( ........................................ )';
                    }
                    echo '<strong>'.$penjab.'</strong>';
                    ?>
                </div>
            </div>
            <div style="position: absolute;bottom:10px;right: 30px; font-size:8pt !important;font-style: italic">
                Daftar Hadir Pengajar Tanggal <?php echo $this->conv->tglIndo($valH->kel_periode_a); ?>
            </div>
        </div>
        <?php


        //var_dump($valDik);
        ?>
        <?php
    }
}
?>

</body>
</html>