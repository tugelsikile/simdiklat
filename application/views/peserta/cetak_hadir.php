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
        $data_pes   = array_chunk($valH->peserta,19,TRUE);
        $page       = 1; $nomor = 1;
        //var_dump($data_pes);
        foreach ($data_pes as $valPage){
            //var_dump(count($data_pes));
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
                            <td align="center"><strong>DAFTAR HADIR PESERTA</strong></td>
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
                        if ($valH->dh_format == 'pagi'){
                            $colspan    = 2;
                            $width      = '210px';
                            $th         = '<th width="105px">Pagi</th><th width="105px">Siang</th>';
                        } elseif ($valH->dh_format == 'siang') {
                            $colspan    = 3;
                            $width      = '210px';
                            $th         = '<th width="70px">Pagi</th><th width="70px">Siang</th><th width="70px">Malam</th>';
                        } else {
                            $colspan    = 2;
                            $width      = '210px';
                            $th         = '<th width="105px">Siang</th><th width="105px">Malam</th>';
                        }
                    }
                    ?>
                    <table width="100%" class="it-grid it-cetak" style="margin-top: 10px">
                        <thead>
                        <tr>
                            <th rowspan="2" width="30px">No</th>
                            <th rowspan="2" width="100px">Nopes UKG</th>
                            <th rowspan="2">Nama Peserta</th>
                            <th rowspan="2" width="200px">Instansi</th>
                            <th width="<?php echo $width;?>" colspan="<?php echo $colspan;?>">Kehadiran</th>
                        </tr>
                        <tr>
                            <?php echo $th; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($valPage as $valPes){
                            if ($valH->dh_format == 'pagi' || $valH->dh_format == 'malam'){
                                $td = '<td></td><td></td>';
                            } else {
                                $td = '<td></td><td></td><td></td>';
                            }
                            if (strlen($valPes->sch_name) > 30){
                                $sch_name = substr($valPes->sch_name,0,30).'..';
                            } else {
                                $sch_name = $valPes->sch_name;
                            }
                            echo '<tr>
                                <td align="center">'.$nomor.'</td>
                                <td align="center">'.$valPes->pes_nopesukg.'</td>
                                <td>'.$valPes->pes_fullname.'</td>
                                <td>'.$sch_name.'</td>
                                '.$td.'
                              </tr>';
                            $nomor++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                    if ($page >= count($data_pes)){
                        $penjab = $tutor1 = $tutor2 = '( ........................................ )';
                        if ($valH->penjab){
                            if (strlen($valH->penjab->pes_gelar_depan) > 0){
                                $penjab = $valH->penjab->pes_gelar_depan.'. '.$valH->penjab->pes_fullname;
                            } else {
                                $penjab = $valH->penjab->pes_fullname;
                            }
                            if (strlen($valH->penjab->pes_gelar_blk) > 0){ $penjab .= ', '.$valH->penjab->pes_gelar_blk; }
                        }
                        if ($valH->tutor){
                            if (count($valH->tutor) == 1){
                                if (strlen($valH->tutor[0]->pes_gelar_depan) > 0){
                                    $tutor1 = $valH->tutor[0]->pes_gelar_depan.'. '.$valH->tutor[0]->pes_fullname;
                                } else {
                                    $tutor1 = $valH->tutor[0]->pes_fullname;
                                }
                                if (strlen($valH->tutor[0]->pes_gelar_blk) > 0){ $tutor1 .= ', '.$valH->tutor[0]->pes_gelar_blk; }
                            } else {
                                if (strlen($valH->tutor[0]->pes_gelar_depan) > 0){
                                    $tutor1 = $valH->tutor[0]->pes_gelar_depan.'. '.$valH->tutor[0]->pes_fullname;
                                } else {
                                    $tutor1 = $valH->tutor[0]->pes_fullname;
                                }
                                if (strlen($valH->tutor[0]->pes_gelar_blk) > 0){ $tutor1 .= ', '.$valH->tutor[0]->pes_gelar_blk; }
                                if (strlen($valH->tutor[1]->pes_gelar_depan) > 0){
                                    $tutor2 = $valH->tutor[1]->pes_gelar_depan.'. '.$valH->tutor[1]->pes_fullname;
                                } else {
                                    $tutor2 = $valH->tutor[1]->pes_fullname;
                                }
                                if (strlen($valH->tutor[1]->pes_gelar_blk) > 0){ $tutor2 .= ', '.$valH->tutor[1]->pes_gelar_blk; }
                            }
                        }


                        ?>
                        <table width="100%" style="margin-top:20px" class="it-grid">
                            <tr>
                                <td width="33%" align="center" valign="top">
                                    Pengajar 1,
                                    <div style="height:50px"></div>
                                    <strong><?php echo $tutor1; ?></strong>
                                </td>
                                <td width="33%" align="center" valign="top">
                                    Pengajar 2,
                                    <div style="height:50px"></div>
                                    <strong><?php echo $tutor2; ?></strong>
                                </td>
                                <td align="center" valign="top">
                                    Ketua Panitia,
                                    <div style="height:50px"></div>
                                    <strong><?php echo $penjab;?></strong>
                                </td>
                            </tr>
                        </table>
                        <?php
                    }
                    ?>
                </div>
                <div style="position: absolute;bottom:10px;right: 30px; font-size:8pt !important;font-style: italic">
                    Daftar Hadir Peserta Tanggal <?php echo $this->conv->tglIndo($valH->kel_periode_a); ?>
                    - Halaman <?php echo $page; ?>
                </div>
            </div>
            <?php
            $page++;
        }
        //var_dump($valDik);
        ?>
        <?php
    }
}
?>

</body>
</html>