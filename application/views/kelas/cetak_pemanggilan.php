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
        *{
            font-size: 11pt !important; font-family:'Times New Roman', Times, serif !important;
        }
        ol li{
            margin: 0; padding: 0;
        }
        ul{
            margin-left: 20px;
        }
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    foreach ($data as $valKelas){
        //var_dump($valDik);
        ?>
        <div class="page" style="">
            <table width="100%">
                <tr>
                    <td align="center" valign="middle">
                        <img src="<?php echo base_url('assets/logo-tut-wuri-handayani-hitam-putih.png');?>" style="width:80px;float: left;margin:10px">
                        <strong style="">
                            <span style="font-size: 16pt !important;">KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</span><br>
                            <span style="font-size: 10pt !important;">
                                PUSAT PENGEMBANGAN DAN PEMBERDAYAAN PENDIDIK DAN TENAGA KEPENDIDIKAN
                            <br>BIDANG MESIN DAN TEKNIK INDUSTRI
                            </span><br>
                            <span style="font-size: 10pt !important;">
                                Jl. Pasantren KM 2, Cimahi 40513 ~ Telp. (022) 6652326; Fax. (022) 6654698, 6650540<br>
                                www.tedcbandung.com ~ email : tedc@tedcbandung.com
                            </span>
                        </strong>
                    </td>
                </tr>
            </table>
            <div style="border:solid 1px #000;margin:10px 0"></div>
            <table width="100%">
                <tr>
                    <td width="80px">Nomor</td>
                    <td width="10px">:</td>
                    <td><?php echo $valKelas->dk_nomor_surat;?></td>
                    <td width="150px" align="right">
                        <?php echo $this->conv->tglIndo($valKelas->dk_titimangsa);?>
                    </td>
                </tr>
                <tr>
                    <td>Lampiran</td>
                    <td>:</td>
                    <td>satu Set</td>
                </tr>
                <tr>
                    <td valign="top">Hal</td>
                    <td valign="top">:</td>
                    <td colspan="2" style="font-weight: bold">
                        Pemanggilan <?php echo $valKelas->dk_name; ?>
                    </td>
                </tr>
            </table>
            <table width="100%" style="margin-top:20px;margin-bottom: 20px">
                <tr>
                    <td valign="top" width="50px" rowspan="2">Yth.</td>
                    <td>1. Kepala Apa</td>
                </tr>
                <tr>
                    <td>2. Kepala Apa</td>
                </tr>
            </table>

            <div style="margin:20px 0; text-align: justify">
                Kementerian Pendidikan dan Kebudayaan melalui Pusat Pengembangan dan Pemberdayaan Pendidik dan Tenaga Kependidikan
                Bidang Mesin dan Teknik Industri (PPPPTK BMTI), akan menyelenggarakan <?php echo $valKelas->dk_name; ?>.
            </div>
            <div style="margin:20px 0; text-align: justify">
            Sehubungan dengan hal tersebut mohon bantuan saudara untuk menugaskan nama-nama terlampir menjadi peserta kegiatan dimaksud,
            dengan penjelasan sebagai berikut :
            </div>
            <table width="100%" class="it-grid">
                <tr>
                    <td width="200px" valign="top">Nama Kegiatan</td>
                    <td style="font-weight: bold"><?php echo $valKelas->dk_name;?></td>
                </tr>
                <tr>
                    <td valign="top">Periode</td>
                    <td><?php echo $this->conv->tglIndo($valKelas->dk_date_start).' s/d '.$this->conv->tglIndo($valKelas->dk_date_end);?></td>
                </tr>
                <tr>
                    <td valign="top">Tempat</td>
                    <td>
                        <?php
                        echo $valKelas->pb_name.'.<br>'.$valKelas->pb_address.'. Kec. '.
                             ucwords(strtolower($valKelas->kec_name)).' '.
                             ucwords(strtolower($valKelas->kab_name)).' - '.
                             ucwords(strtolower($valKelas->prov_name));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><em>Check-In</em></td>
                    <td>
                        <?php
                        echo $this->conv->hariIndo(date('N',strtotime($valKelas->dk_check_in))).', '.
                        $this->conv->tglIndo(date('Y-m-d',strtotime($valKelas->dk_check_in))).', mulai pukul '.
                        date('H:i',strtotime($valKelas->dk_check_in)). 'WIB';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top">Registrasi dan Pembukaan</td>
                    <td>
                        <?php
                        echo $this->conv->hariIndo(date('N',strtotime($valKelas->dk_pembukaan))).', '.
                            $this->conv->tglIndo(date('Y-m-d',strtotime($valKelas->dk_pembukaan))).', '.
                            date('H:i',strtotime($valKelas->dk_pembukaan)). 'WIB';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top">Penutupan</td>
                    <td>
                        <?php
                        echo $this->conv->hariIndo(date('N',strtotime($valKelas->dk_penutupan))).', '.
                            $this->conv->tglIndo(date('Y-m-d',strtotime($valKelas->dk_penutupan))).', '.
                            date('H:i',strtotime($valKelas->dk_penutupan)). 'WIB';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top"><em>Check-out</em></td>
                    <td>
                        <?php
                        echo $this->conv->hariIndo(date('N',strtotime($valKelas->dk_check_out))).', '.
                            $this->conv->tglIndo(date('Y-m-d',strtotime($valKelas->dk_check_out))).', selambat-lambatnya pukul '.
                            date('H:i',strtotime($valKelas->dk_check_out)). 'WIB';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top">Persyaratan Peserta</td>
                    <td>
                        <ol type="a">
                            <li>Peserta sesuai lampiran dan tidak dapat diganti;</li>
                            <li>Tidak diperkenankan membawa anak/keluarga;</li>
                            <li>
                                Khusus bagi peserta wanita yang sedang hamil, dimohon untuk memastikan kesehatannya dan mampu
                                mengikuti kegiatan, apabila terjadi sesuatu terkait dengan kehamilannya, maka menjadi resiko
                                yang bersangkutan.
                            </li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td valign="top">Konfirmasi</td>
                    <td>
                        <?php
                        $konfir = $info = '';
                        foreach ($valKelas->info as $valInfo){
                            if (strlen($valInfo->pes_gelar_depan) > 0){ $info .= $valInfo->pes_gelar_depan.'. '; }
                            $info .= ucwords(strtolower($valInfo->pes_fullname));
                            if (strlen($valInfo->pes_gelar_blk) > 0){ $info .= ', '.$valInfo->pes_gelar_blk; }
                            if (strlen($valInfo->pes_phone) > 0){ $info .= ' ('.$valInfo->pes_phone.')'; }
                            $info .= ', ';
                        }
                        foreach ($valKelas->konf as $valKonf){
                            if (strlen($valKonf->pes_gelar_depan) > 0){ $konfir .= $valKonf->pes_gelar_depan.'. '; }
                            $konfir .= ucwords(strtolower($valKonf->pes_fullname));
                            if (strlen($valKonf->pes_gelar_blk) > 0){ $konfir .= ', '.$valKonf->pes_gelar_blk; }
                            if (strlen($valKonf->pes_phone) > 0){ $konfir .= ' ('.$valKonf->pes_phone.')'; }
                            $konfir .= ', ';
                        }
                        ?>
                        Konfirmasi terkait data peserta melalui contact person : <strong><?php echo $konfir;?></strong>.
                        Informasi terkait diklat bisa melalui contact person : <strong><?php echo $info; ?></strong>.
                        Informasi terkait penyelenggaraan diklat dapat dilakukan setiap hari kerja (Senin s/d Jum'at)
                    </td>
                </tr>
                <tr>
                    <td valign="top">Kehadiran Peserta</td>
                    <td>
                        <ol type="a">
                            <li>Peserta wajib mengikuti seluruh rangkaian kegiatan sampai dengan akhir kegiatan, tidak ada toleransi keterlambatan kegiatan;</li>
                            <li>Apabila tidak mengikuti kegiatan dengan alasan sakit, harus menginformasikan kepada Instruktur atau Petugas Administrasi Kelas;</li>
                            <li>Bersedia mengikuti aturan yang ditetapkan.</li>
                        </ol>
                    </td>
                </tr>
                <tr>
                    <td valign="top">Kelengkapan yang harus dibawa Peserta</td>
                    <td>
                        <ol type="a">
                            <li>Peserta wajib membawa softcopy dokumen yang berkaitan dengan Kurikulum;</li>
                            <li>Membawa laptop;</li>
                            <li>Surat Tugas dari Dinas Pendidikan setempat;</li>
                            <li>Pas Foto berwarna ukuran 3 x 4 cm sebanyak 3 lembar;</li>
                            <li>Surat Keterangan Sehat dari Dokter (bagi peserta yang hamil/sedang dalam masa pengobatan);</li>
                            <li>Kartu Askes/BPJS/Asuransi lain (bagi yang memiliki) yang akan digunakan oleh peserta untuk
                            Pemeriksaan dan/atau pengobatan di Klinik atau Rumah Sakit Umum terdekat;</li>
                            <li>Membawa baju batik dan obat-obatan pribadi;</li>
                            <li>Membawa SPPD dari PPPPTK BMTI (terlampir), yang telah ditandatangani &amp; distempel (sesuai tanda v pada SPPD)
                            oleh pejabat yang berwenang. Nama pejabat dan NIP (bila ada) ditulis dengan pensil atau ditulis pada kertas terpisah.
                            Guna keseragaman dan mencegah kesalahan, pengetikan akan dilakukan oleh PPPPTK BMTI.</li>
                        </ol>
                    </td>
                </tr>
            </table>
        </div>
        <div class="page">
            <table width="100%" class="it-grid">
                <tr>
                    <td width="200px" valign="top">Pembiayaan</td>
                    <td>
                        <ol type="a">
                            <li>Biaya Perjalanan :
                                <ul type="-">
                                    <li>Biaya perjalanan dan uang saku peserta akan dibayarkan sesuai peraturan dan ketersediaan anggaran dalam DIPA PPPPTK BMTI <?php echo date('Y',strtotime($valKelas->dk_date_start));?>;</li>
                                    <li>Peserta menggunakan pesawat kelas ekonomi, dibuktikan dengan tiket, <em>boarding pass</em>, <em>airport tax</em> dan bukti pengeluaran lainnya, lalu diserahkan ke panitia. Peserta wajib membeli tiket pesawat pulang pergi;</li>
                                    <li>Sesuai surat edaran dari Kemendikbud No. 75502/A.A2/KU/2017 tertanggal 28 November 2017, panitia tidak akan mengganti tiket kepulangan peserta dengan referensi tiket kedatangan (tidak dibayarkan sesuai tiket kedatangan);</li>
                                    <li>Panitia tidak dapat mengganti biaya perjalanan bagi peserta yang tidak menyerahkan karcis, tiket, <em>boarding pass</em>, <em>airport tax</em> serta bukti pengeluaran lainnya; </li>
                                    <li>Bukti pertanggungjawaban perjalanan (tiket, <em>boarding pass</em>, <em>airport tax</em>, dsb) harus sesuai dengan nama yang bersangkutan dan dipastikan dengan nama yang bersangkutan telah terdaftar dalam <em>database</em> maskapai penerbangan (karena dalam pemeriksaan, Badan Pemeriksaan Keuangan RI akan melakukan <em>cross check</em> bukti perjalanan).</li>
                                </ul>
                            </li>
                            <li>Akomodasi dan Konsumsi :
                                <ul>
                                    <li>Akomodasi dan Konsumsi disesuaikan dengan standar pelayanan yang ditetapkan PPPPTK BMTI.</li>
                                </ul>
                            </li>
                        </ol>
                        Pembiayaan tersebut akan dibayarkan 1 (satu) hari sebelum penutupan diklat.
                    </td>
                </tr>
            </table>
            <div style="margin: 10px"></div>
            Demikian, atas bantuan dan kerjasama yang baik, kami ucapkan terima kasih.
            <div style="float: right;margin-top:80px">
                Kepala,
                <div style="height: 80px"></div>
                <strong>Drs. Marthen Patte Patiung, M.M.</strong><br>
                NIP. 195904161986031002
            </div>
            <div class="clearfix" style="clear: both"></div>
            Tembusan :
            <ol type="1">
                <li>Direktur Jendral Guru dan Tenaga Kependidikan;</li>
                <li>PPK PPPPTK BMTI.</li>
            </ol>
        </div>
        <?php
        if ($valKelas->peserta){
            $pagePes    = array_chunk($valKelas->peserta,20,true);
            $page = $nomor  = 1;
            foreach ($pagePes as $valPagepes){
                ?>
                <div class="page">
                    <?php
                    if ($page == 1){
                        ?>
                        Lampiran Surat Kepala PPPPTK BMTI<br>
                        <table width="100%">
                            <tr>
                                <td width="100px">Nomor</td>
                                <td width="10px">:</td>
                                <td><?php echo $valKelas->dk_nomor_surat;?></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>:</td>
                                <td><?php echo $this->conv->tglIndo($valKelas->dk_titimangsa);?></td>
                            </tr>
                        </table>
                        <div style="text-align: center">
                            <strong style="font-size:11pt">
                                DAFTAR PESERTA<br>
                                <?php echo strtoupper($valKelas->kel_name);?><br>
                                <?php echo strtoupper($valKelas->pb_name);?><br><br>
                                PERIODE <?php echo strtoupper($this->conv->tglIndo($valKelas->dk_date_start).' s/d '.$this->conv->tglIndo($valKelas->dk_date_end)); ?><br>
                            </strong>
                        </div>
                        <?php
                    }
                    ?>
                    <table width="100%" class="it-grid it-cetak" style="margin-top:10px">
                        <thead>
                        <tr>
                            <th width="50px">No</th>
                            <th>Nama Peserta</th>
                            <th>Sekolah / Instansi</th>
                            <th>Kab / Kota</th>
                            <th>Propinsi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($valPagepes as $valPes){
                            $fullname = '';
                            if (strlen($valPes->pes_gelar_depan) > 0){ $fullname .= $valPes->pes_gelar_depan.'. '; }
                            $fullname .= ucwords(strtolower($valPes->pes_fullname));
                            if (strlen($valPes->pes_gelar_blk) > 0){ $fullname .= ', '.$valPes->pes_gelar_blk; }
                            echo '<tr>
                                    <td align="center">'.$nomor.'</td>
                                    <td>'.$fullname.'</td>
                                    <td>'.$valPes->sch_name.'</td>
                                    <td>'.$valPes->kab_name.'</td>
                                    <td>'.$valPes->prov_name.'</td>
                                  </tr>';
                            $nomor++;
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php
                    if ($page >= count($pagePes)){
                        ?>
                        <div style="width: 300px;float: right;margin-top:20px;text-align: center">
                            Kepala PPPPTK BTMI,
                            <div style="height: 80px"></div>
                            <strong>Drs. Marthen Patte Patiung, M.M.</strong><br>
                            NIP. 195904161986031002
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
                $page++;
            }
        }
    }
}
?>

</body>
</html>