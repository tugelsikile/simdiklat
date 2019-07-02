<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajar extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    //start diklat segment
    function index(){

    }
    public function daftar()	{
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $data['body']   = 'errors/403';
        } else {
            $kel_id         = $this->uri->segment(3);
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,kel_name,dk_id');
            if (!$kel_id || !$dtKel){
                $data['body'] = 'errors/404';
            } else {
                $data['kel']  = $this->dbase->dataResult('diklat_kelas',array('kel_status'=>1,'pb_id'=>$this->session->userdata('pb_id'),'dk_id'=>$dtKel->dk_id),'kel_id,kel_name,dk_id');
                $data['data']   = $dtKel;
                //die(var_dump($data['kelas']));
                $data['body']   = 'pengajar/home';
                $data['kelas']  = 'kelas';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_home(){
        $json['t']  = 0; $json['msg'] = '';
        $keyword    = html_escape($this->input->post('keyword',TRUE));
        $kel_id     = $this->input->post('kel_id',TRUE);
        //$dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
        $dtUser     = $this->dbase->sqlResult("
            SELECT  dkm.*,p.pes_nopesukg,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,
                    s.sch_name,wk.`name` AS kab_name,wp.`name` AS prov_name
            FROM    tb_diklat_kelas_member AS dkm
            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
            LEFT JOIN tb_school AS s ON p.sch_id = s.sch_id
            LEFT JOIN tb_wil_kab AS wk ON s.kab_id = wk.kab_id
            LEFT JOIN tb_wil_prov AS wp ON wk.prov_id = wp.prov_id
            WHERE     (
                        p.pes_fullname LIKE '%".$keyword."%' OR
                        p.pes_nopesukg LIKE '%".$keyword."%' OR
                        s.sch_name LIKE '%".$keyword."%' OR
                        wk.`name` LIKE '%".$keyword."%' OR
                        wp.`name` LIKE '%".$keyword."%'
                      )
                      AND dkm.kel_id = '".$kel_id."' AND dkm.km_status = 1 AND p.pes_status = 1 AND dkm.km_type = 'pengajar'
            GROUP BY  dkm.km_id 
            ORDER BY  dkm.km_id ASC");
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dtUser){
            $json['msg'] = 'Data tidak ditemukan';
        } else {
            $this->load->library('conv');
            $data['data']   = $dtUser;
            $json['t']      = 1;
            $json['html']   = $this->load->view('pengajar/data_home',$data,TRUE);
        }
        die(json_encode($json));
    }
    function add_data(){
        if ($this->session->userdata('lvl_diklat_pengajar') < 2){
            die('Forbidden');
        } else {
            $kel_id = $this->uri->segment(3);
            $dtKel  = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            if (!$kel_id || !$dtKel){
                die('Invalid data kelas');
            } else {
                $data['data']   = $dtKel;
                $data['user']   = $this->dbase->sqlResult("SELECT  u.user_id,u.user_gelar_depan,u.user_fullname,u.user_gelar_blk,s.sch_name,u.user_nopesukg
                                                           FROM    tb_user AS u
                                                           LEFT JOIN tb_school AS s ON u.sch_id = s.sch_id
                                                           WHERE   u.user_level > 2 AND u.user_status = 1
                                                           ORDER BY u.user_fullname ASC ");
                //$data['user']   = $this->dbase->dataResult('user',array('user_level'=>4,'user_status'=>1),'user_id,user_fullname,user_gelar_depan,user_gelar_blk,user_nopesukg');
                $data['prov']   = $this->dbase->dataResult('wil_prov',array(),'*','name','ASC');
                $data['sch']    = $this->dbase->dataResult('school',array('sch_status'=>1),'*','sch_name','ASC');
                $data['pang']   = $this->dbase->dataResult('pangkat',array('pangkat_status'=>1));
                $this->load->view('pengajar/add_data',$data);
            }
        }
    }
    function add_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $kel_id         = $this->input->post('kel_id');
        $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        $user_id        = $this->input->post('user_id');
        $dtUser         = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        $pes_nopesukg   = $this->input->post('pes_nopesukg');
        $chkNopes       = $this->dbase->dataRow('user',array('user_nopesukg'=>$pes_nopesukg,'user_status'=>1,'user_id !='=>$user_id));
        $pes_nuptk      = $this->input->post('pes_nuptk');
        $pes_nip        = $this->input->post('pes_nip');
        $pes_gelar_depan= $this->input->post('pes_gelar_depan');
        $pes_fullname   = $this->input->post('pes_fullname');
        $pes_gelar_blk  = $this->input->post('pes_gelar_blk');
        $pes_bplace     = $this->input->post('pes_bplace');
        $pes_bdate      = $this->input->post('pes_bdate');
        $bdate          = explode("-",$pes_bdate);
        $pes_agama      = $this->input->post('pes_agama');
        $pes_sex        = $this->input->post('pes_sex');
        $pangkat_id     = $this->input->post('pangkat_id');
        $pes_jabatan    = $this->input->post('pes_jabatan');
        $pes_didik      = $this->input->post('pes_didik');
        $pes_jurusan    = $this->input->post('pes_jurusan');
        $pes_address    = $this->input->post('pes_address');
        $pes_phone      = $this->input->post('pes_phone');
        $pes_email      = $this->input->post('pes_email');
        $pes_npwp       = $this->input->post('pes_npwp');
        $sch_id         = (int)$this->input->post('sch_id');
        if ($sch_id == 0){ $sch_id = NULL; }
        $dtSch          = $this->dbase->dataRow('school',array('sch_id'=>$sch_id));
        $sch_name       = $this->input->post('sch_name');
        $sch_nss        = $this->input->post('sch_nss');
        $sch_npsn       = $this->input->post('sch_npsn');
        $sch_address    = $this->input->post('sch_address');
        $kab_id         = $this->input->post('kab_id');
        $dtKab          = $this->dbase->dataRow('wil_kab',array('kab_id'=>$kab_id));
        $sch_phone      = $this->input->post('sch_phone');
        $sch_fax        = $this->input->post('sch_fax');
        $sch_email      = $this->input->post('sch_email');
        $sch_negeri     = $this->input->post('sch_negeri');

        if ($this->session->userdata('lvl_diklat_pengajar') < 2) {
            $json['msg'] = 'Forbidden';
        } elseif (!$kel_id || !$dtKel) {
            $json['msg'] = 'Invalid data kelas';
        } elseif (strlen(trim($user_id)) > 0 && $user_id != 'x' && !$dtUser) {
            $json['msg'] = 'Invalid data pengguna';
        } elseif (strlen(trim($user_id)) && strlen(trim($pes_nopesukg)) && $chkNopes){
            $json['msg'] = 'Nomor Peserta sudah dipakai oleh orang lain';
        } elseif (!$dtUser && strlen(trim($pes_fullname)) == 0) {
            $json['msg'] = 'Nama peserta belum diisi';
        } elseif (!$dtUser && strlen(trim($pes_bplace)) == 0){
            $json['msg'] = 'Tempat lahir belum diisi';
        } elseif (!$dtUser && strlen(trim($pes_bdate)) == 0) {
            $json['msg'] = 'Tanggal lahir belum diisi';
        } elseif (!$dtUser && count($bdate) != 3) {
            $json['msg'] = 'Tanggal lahir tidak valid';
        } elseif (strlen(trim($sch_id)) > 0 && !$dtSch) {
            $json['msg'] = 'Asal institusi tidak valid atau belum diisi';
        } elseif (strlen(trim($sch_id)) == 0 && strlen(trim($sch_name)) == 0){
            $json['msg'] = 'Masukkan nama Institusi';
        } else {
            if (strlen(trim($sch_name)) > 0){
                $arsch = array(
                    'sch_name' => $sch_name, 'sch_negeri' => $sch_negeri, 'sch_address' => $sch_address,
                    'sch_phone' => $sch_phone, 'sch_fax' => $sch_fax, 'sch_email' => $sch_email, 'kab_id' => $kab_id,
                    'sch_nss' => $sch_nss, 'sch_npsn' => $sch_npsn
                );
                $sch_id     = $this->dbase->dataInsert('school',$arsch);
                $prov       = $this->dbase->dataRow('wil_prov',array('prov_id'=>$dtKab->prov_id));
                $schname    = $sch_name;
                $schkab     = $dtKab->name;
                $schprov    = $prov->name;
            } else {
                $schname    = $dtSch->sch_name;
                $dtKab      = $this->dbase->dataRow('wil_kab',array('kab_id'=>$dtSch->kab_id));
                $schkab     = $dtKab->name;
                $schprov    = $this->dbase->dataRow('wil_prov',array('prov_id'=>$dtKab->prov_id))->name;
            }
            if (!$dtUser){
                $aruser = array(
                    'sch_id' => $sch_id, 'pangkat_id' => $pangkat_id, 'user_fullname' => strtoupper($pes_fullname), 'user_gelar_depan' => $pes_gelar_depan,
                    'user_gelar_blk' => $pes_gelar_blk, 'user_bplace' => $pes_bplace, 'user_bdate' => $pes_bdate, 'user_sex' => $pes_sex,
                    'user_nip' => $pes_nip, 'user_nuptk' => $pes_nuptk, 'user_nopesukg' => $pes_nopesukg, 'user_jabatan' => $pes_jabatan,
                    'user_didik' => $pes_didik, 'user_jurusan' => $pes_jurusan, 'user_agama' => $pes_agama, 'user_address' => $pes_address,
                    'user_phone' => $pes_phone, 'user_email' => $pes_email, 'user_npwp' => $pes_npwp, 'user_level' => 3
                );
                $user_name  = str_replace(" ","",$pes_fullname);
                $aruser['user_name']    = $user_name;
                $aruser['user_password']= password_hash('123456',PASSWORD_DEFAULT);
                if (strlen(trim($pangkat_id)) == 0){ $aruser['pangkat_id'] = NULL; }
                $user_id = $this->dbase->dataInsert('user',$aruser);
            } else {
                $pes_fullname       = $dtUser->user_fullname;
                $pes_gelar_blk      = $dtUser->user_gelar_blk;
                $pes_gelar_depan    = $dtUser->user_gelar_depan;
                $pangkat_id         = $dtUser->pangkat_id;
                $pes_nopesukg       = $dtUser->user_nopesukg;
                $pes_nuptk          = $dtUser->user_nuptk;
                $pes_nip            = $dtUser->user_nip;
                $pes_bplace         = $dtUser->user_bplace;
                $pes_bdate          = $dtUser->user_bdate;
                $pes_agama          = $dtUser->user_agama;
                $pes_sex            = $dtUser->user_sex;
                $pes_jabatan        = $dtUser->user_jabatan;
                $pes_didik          = $dtUser->user_didik;
                $pes_jurusan        = $dtUser->user_jurusan;
                $pes_address        = $dtUser->user_address;
                $pes_phone          = $dtUser->user_phone;
                $pes_email          = $dtUser->user_email;
                $pes_npwp           = $dtUser->user_npwp;
            }
            $arpes = array(
                'sch_id' => $sch_id, 'pangkat_id' => $pangkat_id, 'pes_fullname' => strtoupper($pes_fullname), 'pes_gelar_depan' => $pes_gelar_depan,
                'pes_gelar_blk' => $pes_gelar_blk, 'pes_bplace' => $pes_bplace, 'pes_bdate' => $pes_bdate, 'pes_sex' => $pes_sex,
                'pes_nip' => $pes_nip, 'pes_nuptk' => $pes_nuptk, 'pes_nopesukg' => $pes_nopesukg, 'pes_jabatan' => $pes_jabatan,
                'pes_didik' => $pes_didik, 'pes_jurusan' => $pes_jurusan, 'pes_agama' => $pes_agama, 'pes_address' => $pes_address,
                'pes_phone' => $pes_phone, 'pes_email' => $pes_email, 'pes_npwp' => $pes_npwp
            );
            if (strlen(trim($pangkat_id)) == 0){ $arpes['pangkat_id'] = NULL; }
            $pes_id = $this->dbase->dataInsert('peserta',$arpes);
            if (!$pes_id){
                $json['msg'] = 'DB Error';
            } else {
                $km_id = $this->dbase->dataInsert('diklat_kelas_member',array('pes_id'=>$pes_id,'kel_id'=>$kel_id,'km_type'=>'pengajar'));
                if (!$km_id){
                    $json['msg'] = 'DB Kelas member error';
                } else {
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("
                            SELECT  dkm.*,p.pes_nopesukg,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,
                                    s.sch_name,wk.`name` AS kab_name,wp.`name` AS prov_name
                            FROM    tb_diklat_kelas_member AS dkm
                            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                            LEFT JOIN tb_school AS s ON p.sch_id = s.sch_id
                            LEFT JOIN tb_wil_kab AS wk ON s.kab_id = wk.kab_id
                            LEFT JOIN tb_wil_prov AS wp ON wk.prov_id = wp.prov_id
                            WHERE     dkm.km_id = '".$km_id."'
                            ");
                    $data['data'][0]->sch_name  = $schname;
                    $data['data'][0]->kab_name  = $schkab;
                    $data['data'][0]->prov_name = $schprov;
                    $json['html'] = $this->load->view('pengajar/data_home',$data,TRUE);
                    $json['msg'] = 'Pengajar berhasil ditambahkan';
                }
            }
        }
        die(json_encode($json));
    }
    function edit_data(){
        $pes_id     = $this->uri->segment(3);
        $dtPes      = $this->dbase->dataRow('peserta',array('pes_id'=>$pes_id));
        if ($this->session->userdata('lvl_diklat_pengajar') < 3){
            die('Forbidden');
        } elseif (!$pes_id || !$dtPes){
            die('Invalid data');
        } else {
            $data['prov']   = $this->dbase->dataResult('wil_prov',array(),'*','name','ASC');
            $data['sch']    = $this->dbase->dataResult('school',array('sch_status'=>1),'*','sch_name','ASC');
            $data['pang']   = $this->dbase->dataResult('pangkat',array('pangkat_status'=>1));
            $data['data']   = $dtPes;
            $this->load->view('peserta/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $pes_id         = $this->input->post('pes_id');
        $dtPes          = $this->dbase->dataRow('peserta',array('pes_id'=>$pes_id));

        $pes_nopesukg   = $this->input->post('pes_nopesukg');
        $pes_nuptk      = $this->input->post('pes_nuptk');
        $pes_nip        = $this->input->post('pes_nip');
        $pes_gelar_depan= $this->input->post('pes_gelar_depan');
        $pes_fullname   = $this->input->post('pes_fullname');
        $pes_gelar_blk  = $this->input->post('pes_gelar_blk');
        $pes_bplace     = $this->input->post('pes_bplace');
        $pes_bdate      = $this->input->post('pes_bdate');
        $bdate          = explode("-",$pes_bdate);
        $pes_agama      = $this->input->post('pes_agama');
        $pes_sex        = $this->input->post('pes_sex');
        $pangkat_id     = $this->input->post('pangkat_id');
        $pes_jabatan    = $this->input->post('pes_jabatan');
        $pes_didik      = $this->input->post('pes_didik');
        $pes_jurusan    = $this->input->post('pes_jurusan');
        $pes_address    = $this->input->post('pes_address');
        $pes_phone      = $this->input->post('pes_phone');
        $pes_email      = $this->input->post('pes_email');
        $pes_npwp       = $this->input->post('pes_npwp');
        $sch_id         = (int)$this->input->post('sch_id');
        if ($sch_id == 0){ $sch_id = NULL; }
        $dtSch          = $this->dbase->dataRow('school',array('sch_id'=>$sch_id));
        $sch_name       = $this->input->post('sch_name');
        $sch_nss        = $this->input->post('sch_nss');
        $sch_npsn       = $this->input->post('sch_npsn');
        $sch_address    = $this->input->post('sch_address');
        $kab_id         = $this->input->post('kab_id');
        $dtKab          = $this->dbase->dataRow('wil_kab',array('kab_id'=>$kab_id));
        $sch_phone      = $this->input->post('sch_phone');
        $sch_fax        = $this->input->post('sch_fax');
        $sch_email      = $this->input->post('sch_email');
        $sch_negeri     = $this->input->post('sch_negeri');

        if ($this->session->userdata('lvl_diklat_pengajar') < 3) {
            $json['msg'] = 'Forbidden';
        } elseif (!$pes_id || !$dtPes){
            $json['msg'] = 'Invalid data kelas';
        } elseif (strlen(trim($pes_fullname)) == 0) {
            $json['msg'] = 'Nama peserta belum diisi';
        } elseif (strlen(trim($pes_bplace)) == 0){
            $json['msg'] = 'Tempat lahir belum diisi';
        } elseif (strlen(trim($pes_bdate)) == 0) {
            $json['msg'] = 'Tanggal lahir belum diisi';
        } elseif (count($bdate) != 3) {
            $json['msg'] = 'Tanggal lahir tidak valid';
        } elseif (strlen(trim($sch_id)) > 0 && !$dtSch) {
            $json['msg'] = 'Asal institusi tidak valid atau belum diisi';
        } elseif (strlen(trim($sch_id)) == 0 && strlen(trim($sch_name)) == 0){
            $json['msg'] = 'Masukkan nama Institusi';
        } else {
            if (strlen(trim($sch_name)) > 0){
                $arsch = array(
                    'sch_name' => $sch_name, 'sch_negeri' => $sch_negeri, 'sch_address' => $sch_address,
                    'sch_phone' => $sch_phone, 'sch_fax' => $sch_fax, 'sch_email' => $sch_email, 'kab_id' => $kab_id,
                    'sch_nss' => $sch_nss, 'sch_npsn' => $sch_npsn
                );
                $sch_id     = $this->dbase->dataInsert('school',$arsch);
                $prov       = $this->dbase->dataRow('wil_prov',array('prov_id'=>$dtKab->prov_id));
                $schname    = $sch_name;
                $schkab     = $dtKab->name;
                $schprov    = $prov->name;
            }
            $arpes = array(
                'sch_id' => $sch_id, 'pangkat_id' => $pangkat_id, 'pes_fullname' => $pes_fullname, 'pes_gelar_depan' => $pes_gelar_depan,
                'pes_gelar_blk' => $pes_gelar_blk, 'pes_bplace' => $pes_bplace, 'pes_bdate' => $pes_bdate, 'pes_sex' => $pes_sex,
                'pes_nip' => $pes_nip, 'pes_nuptk' => $pes_nuptk, 'pes_nopesukg' => $pes_nopesukg, 'pes_jabatan' => $pes_jabatan,
                'pes_didik' => $pes_didik, 'pes_jurusan' => $pes_jurusan, 'pes_agama' => $pes_agama, 'pes_address' => $pes_address,
                'pes_phone' => $pes_phone, 'pes_email' => $pes_email, 'pes_npwp' => $pes_npwp
            );
            if (strlen(trim($pangkat_id)) == 0){ $arpes['pangkat_id'] = NULL; }
            $this->dbase->dataUpdate('peserta',array('pes_id'=>$pes_id),$arpes);
            $json['t']      = 1;
            $json['msg']    = 'Peserta berhasil diupdate';
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('km_id');
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('diklat_kelas_member',array('km_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('diklat_kelas_member',array('km_id'=>$val),array('km_status'=>0));
                    $this->dbase->dataUpdate('peserta',array('pes_id'=>$chk->pes_id),array('pes_status'=>0));
                }
            }
            $json['t'] = 1;
            $json['data'] = $user_id;
            $json['msg'] = count($user_id).' data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_data(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('id');
        $dtUser     = $this->dbase->dataRow('diklat_kelas_member',array('km_id'=>$user_id));
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('diklat_kelas_member',array('km_id'=>$user_id),array('km_status'=>0));
            $this->dbase->dataUpdate('peserta',array('pes_id'=>$dtUser->pes_id),array('pes_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Pengajar berhasil dihapus';
        }
        die(json_encode($json));
    }
    function hadir(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $data['body']   = 'errors/403';
        } else {
            $kel_id         = $this->uri->segment(3);
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,kel_name,dk_id');
            if (!$kel_id || !$dtKel){
                $data['body'] = 'errors/404';
            } else {
                $data['kel']    = $this->dbase->dataResult('diklat_kelas',array('kel_status'=>1,'pb_id'=>$this->session->userdata('pb_id'),'dk_id'=>$dtKel->dk_id),'kel_id,kel_name,dk_id');
                $data['data']   = $dtKel;
                $data['body']   = 'pengajar/hadir';
                $data['kelas']  = 'kelas';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_hadir(){
        $json['t'] = 0; $json['msg'] = 'Tidak ada data';
        $kel_id     = $this->input->post('kel_id');
        $keyword    = $this->input->post('keyword');
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        if (!$kel_id || !$dtKel){
            $json['msg'] = 'Invalid data kelas';
        } else {
            $dtHadir = $this->dbase->sqlResult("
                SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1,Count(dp.dm_id) AS cnt
                FROM      tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                LEFT JOIN tb_diklat_pembelajaran AS dp ON dp.dh_id = dh.dh_id AND dp.dm_status = 1
                WHERE     dh.dh_status = 1 AND dh.kel_id = '".$kel_id."' AND dh.dh_type = 'tut'
                GROUP BY  dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1
                ORDER BY  dh.dh_date ASC
            ");
            if (!$dtHadir){
                $json['msg'] = 'Data tidak ditemukan';
            } else {
                $this->load->library('conv');
                $data['data']   = $dtHadir;
                $json['t']      = 1;
                $json['html']   = $this->load->view('pengajar/data_hadir',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_hadir(){
        $kel_id     = $this->uri->segment(3);
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        $tutor_cnt    = $this->dbase->dataRow('diklat_kelas_member',array('kel_id'=>$kel_id,'km_status'=>1,'km_type'=>'pengajar'),'km_id');
        if ($this->session->userdata('lvl_diklat_pengajar') < 2){
            die('Forbidden');
        } elseif (!$kel_id || !$dtKel) {
            die('Invalid data kelas');
        } elseif (!$tutor_cnt){
            die('Masukkan data pengajar lebih dulu');
        } else {
            $tglCount       = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'tut'),'COUNT(dh_id) AS cnt')->cnt;
            $tgl            = new DateTime($dtKel->kel_periode_a);
            $tgl            = $tgl->modify('+'.$tglCount.' day');
            $data['tgl']    = $tgl;
            $data['data']   = $dtKel;

            $this->load->view('pengajar/add_hadir',$data);
        }
    }
    function add_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $kel_id     = $this->input->post('kel_id');
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        $dh_date    = $this->input->post('dh_date');
        $chkDate    = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$dh_date,'dh_status'=>1,'dh_type'=>'tut'));
        if ($this->session->userdata('lvl_diklat_pengajar') < 2){
            $json['msg'] = 'Forbidden';
        } elseif (!$kel_id || !$dtKel) {
            $json['msg'] = 'Invalid data kelas';
        } elseif (strlen(trim($dh_date)) != 10) {
            $json['msg'] = 'Tanggal kehadiran tidak valid atau belum diisi';
        } elseif ($chkDate){
            $json['msg'] = 'Tanggal kehadiran sudah ada';
        } else {
            $tg_target  = new DateTime($dh_date);
            $tg_max     = new DateTime($dtKel->kel_periode_b);
            $tg_min     = new DateTime($dtKel->kel_periode_a);
            if ($tg_target < $tg_min){
                $json['msg'] = 'Tanggal kehadiran tidak boleh kurang dari tanggal mulai diklat';
            } elseif ($tg_target > $tg_max){
                $json['msg'] = 'Tanggal kehadiran tidak boleh lebih dari tanggal berakhir diklat';
            } else {
                $dh_id = $this->dbase->dataInsert('diklat_kehadiran',array(
                    'kel_id' => $kel_id, 'dh_date' => $dh_date, 'dh_type' => 'tut'
                ));
                if (!$dh_id){
                    $json['msg'] = 'DB Error';
                } else {
                    //$tutor_cnt    = $this->dbase->dataRow('diklat_kelas_member',array('kel_id'=>$kel_id,'km_status'=>1,'km_type'=>'pengajar'),'km_id');
                    if (date('N',strtotime($dh_date)) != 5){ //jika bukan hari jum'at
                        $this->insertJam($dh_id,'08:00:00',$kel_id);        //1
                        $this->insertJam($dh_id,'08:45:00',$kel_id);        //2
                        // 09:15 - 09:30 istirahat
                        $this->insertJam($dh_id,'09:30:00',$kel_id);        //3
                        $this->insertJam($dh_id,'10:15:00',$kel_id);        //4
                        $this->insertJam($dh_id,'11:00:00',$kel_id);        //5
                        // 11:45 - 12:30 isoma + dzuhur
                        $this->insertJam($dh_id,'12:30:00',$kel_id);        //6
                        $this->insertJam($dh_id,'13:15:00',$kel_id);        //7
                        $this->insertJam($dh_id,'14:00:00',$kel_id);        //8
                        $this->insertJam($dh_id,'14:45:00',$kel_id);        //9
                        // 15:30 - 16:00 isoma + ashar
                        $this->insertJam($dh_id,'16:00:00',$kel_id);        //10
                        $this->insertJam($dh_id,'16:45:00',$kel_id);        //11
                        // 17:15 - 20:00 isoma shalat maghrib + isya
                        $this->insertJam($dh_id,'20:00:00',$kel_id);        //12
                        $this->insertJam($dh_id,'20:45:00',$kel_id);        //13
                    } else {
                        $this->insertJam($dh_id,'08:00:00',$kel_id);        //1
                        $this->insertJam($dh_id,'08:45:00',$kel_id);        //2
                        // 09:15 - 09:30 istirahat
                        $this->insertJam($dh_id,'09:30:00',$kel_id);        //3
                        $this->insertJam($dh_id,'10:15:00',$kel_id);        //4
                        $this->insertJam($dh_id,'11:00:00',$kel_id);        //5
                        // 11:00 - 13:00 isoma + shalat jum'at
                        $this->insertJam($dh_id,'13:00:00',$kel_id);        //6
                        $this->insertJam($dh_id,'13:45:00',$kel_id);        //7
                        $this->insertJam($dh_id,'14:15:00',$kel_id);        //8
                        // 15:00 - 16:30 isoma + ashar
                        $this->insertJam($dh_id,'15:30:00',$kel_id);        //9
                        $this->insertJam($dh_id,'16:15:00',$kel_id);        //10
                        $this->insertJam($dh_id,'17:00:00',$kel_id);        //11
                        // 17:00 - 20:00 isoma shalat maghrib + isya
                        $this->insertJam($dh_id,'20:00:00',$kel_id);        //12
                        $this->insertJam($dh_id,'20:45:00',$kel_id);        //13
                    }
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("
                        SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1,Count(dp.dm_id) AS cnt
                        FROM      tb_diklat_kehadiran AS dh
                        LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                        LEFT JOIN tb_diklat_pembelajaran AS dp ON dp.dh_id = dh.dh_id AND dh.dh_status = 1
                        WHERE     dh.dh_id = '".$dh_id."'
                    ");
                    $this->load->library('conv');
                    $json['html'] = $this->load->view('pengajar/data_hadir',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
    function insertJam($dh_id,$jam,$kel_id=FALSE){
        $jamke = $this->dbase->dataRow('diklat_pembelajaran',array('dh_id'=>$dh_id,'dm_status'=>1),'COUNT(dm_id) AS cnt')->cnt;
        $jamke = $jamke + 1;
        $tutor_1 = $tutor_2 = NULL;
        if ($kel_id){
            $tutor = $this->dbase->dataResult('diklat_kelas_member',array('kel_id'=>$kel_id,'km_status'=>1,'km_type'=>'pengajar'),'pes_id');
            if ($tutor){ $tutor_1 = $tutor[0]->pes_id; }
            if (count($tutor) > 1){ $tutor_2 = $tutor[1]->pes_id; }
        }
        $this->dbase->dataInsert('diklat_pembelajaran',array('dh_id'=>$dh_id,'dm_jam_ke'=>$jamke,'dm_jam'=>$jam,'dm_tutor_1'=>$tutor_1,'dm_tutor_2'=>$tutor_2));
    }
    function bulk_delete_hadir(){
        $json['t'] = 0; $json['msg'] = '';
        $dh_id      = $this->input->post('dh_id');
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dh_id){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } elseif (count($dh_id) == 0){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            foreach ($dh_id as $val){
                $chk = $this->dbase->sqlRow("
                SELECT  dh.dh_id,dh.dh_page_1,dh.dh_page_2,dh.dh_page_3,dh.dh_page_4,dh.dh_page_5,dh.dh_page_6,dh.dh_page_7,
                        dh.dh_page_8,dk.pb_id
                FROM    tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE   dh.dh_id = '".$val."'
                ");
                if ($chk){
                    $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$val),
                        array(
                            'dh_status'=>0, 'dh_page_1' => NULL, 'dh_page_2' => NULL, 'dh_page_3' => NULL, 'dh_page_4' => NULL,
                            'dh_page_5' => NULL, 'dh_page_6' => NULL, 'dh_page_7' => NULL, 'dh_page_8' => NULL
                        ));
                    if (strlen($chk->dh_page_1) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_1); }
                    if (strlen($chk->dh_page_2) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_2); }
                    if (strlen($chk->dh_page_3) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_3); }
                    if (strlen($chk->dh_page_4) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_4); }
                    if (strlen($chk->dh_page_5) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_5); }
                    if (strlen($chk->dh_page_6) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_6); }
                    if (strlen($chk->dh_page_7) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_7); }
                    if (strlen($chk->dh_page_8) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$chk->pb_id.'/'.$chk->dh_page_8); }
                }
            }
            $json['t'] = 1;
            $json['data']   = $dh_id;
            $json['msg'] = count($dh_id).' data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_hadir_file($target){
        @unlink($target);
    }
    function delete_hadir(){
        $json['t'] = 0; $json['msg'] = '';
        $dh_id      = $this->input->post('id');
        $dtDh       = $this->dbase->sqlRow("
                SELECT  dh.dh_id,dh.dh_page_1,dh.dh_page_2,dh.dh_page_3,dh.dh_page_4,dh.dh_page_5,dh.dh_page_6,dh.dh_page_7,
                        dh.dh_page_8,dk.pb_id
                FROM    tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE   dh.dh_id = '".$dh_id."'
                ");
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dh_id || !$dtDh){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            if (strlen($dtDh->dh_page_1) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_1); }
            if (strlen($dtDh->dh_page_2) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_2); }
            if (strlen($dtDh->dh_page_3) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_3); }
            if (strlen($dtDh->dh_page_4) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_4); }
            if (strlen($dtDh->dh_page_5) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_5); }
            if (strlen($dtDh->dh_page_6) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_6); }
            if (strlen($dtDh->dh_page_7) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_7); }
            if (strlen($dtDh->dh_page_8) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_8); }
            $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_status'=>0, 'dh_page_1' => NULL, 'dh_page_2' => NULL, 'dh_page_3' => NULL, 'dh_page_4' => NULL,
                'dh_page_5' => NULL, 'dh_page_6' => NULL, 'dh_page_7' => NULL, 'dh_page_8' => NULL));
            $json['t'] = 1;
            $json['msg'] = 'data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function pembelajaran(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $data['body']   = 'errors/403';
        } else {
            $kel_id         = $this->uri->segment(3);
            $tgl            = $this->uri->segment(4);
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,kel_name,dk_id');
            $dtHadir        = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$tgl,'dh_status'=>1,'dh_type'=>'tut'));
            if (!$kel_id || !$dtKel || !$tgl || !$dtHadir){
                $data['body'] = 'errors/404';
            } else {
                $data['kel']    = $this->dbase->dataResult('diklat_kelas',array('kel_status'=>1,'pb_id'=>$this->session->userdata('pb_id'),'dk_id'=>$dtKel->dk_id),'kel_id,kel_name,dk_id');
                $data['dh_date']= $tgl;
                $data['kel_id'] = $kel_id;
                $data['data']   = $this->dbase->dataResult('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'tut'),'dh_date','dh_date','ASC');
                $data['body']   = 'pengajar/pembelajaran';
                $data['kelas']  = 'kelas';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_pembelajaran(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $json['msg'] = 'Forbidden';
        } else {
            $keyword    = $this->input->post('keyword');
            $dh_date    = $this->input->post('dh_date');
            $kel_id     = $this->input->post('kel_id');
            $dtDh       = $this->dbase->dataRow('diklat_kehadiran',array('dh_status'=>1,'dh_date'=>$dh_date,'kel_id'=>$kel_id,'dh_type'=>'tut'),'dh_id');
            if (!$kel_id || !$dh_date || !$dtDh){
                $json['msg'] = 'Tidak ada data tanggal';
            } else {
                $dtPem = $this->dbase->sqlResult("
                    SELECT      dp.dm_id,dp.dm_jam,dh.dh_date,dh.dh_id,dp.dm_jam_ke,dp.dm_materi,
                                CONCAT(t1.pes_fullname,', ',t1.pes_gelar_blk) AS tutor_1,
                                CONCAT(t2.pes_fullname,', ',t2.pes_gelar_blk) AS tutor_2,
                                CONCAT(t3.pes_fullname,', ',t3.pes_gelar_blk) AS tutor_3,
                                CONCAT(t4.pes_fullname,', ',t4.pes_gelar_blk) AS tutor_4
                    FROM        tb_diklat_pembelajaran AS dp
                    LEFT JOIN   tb_peserta AS t1 ON dp.dm_tutor_1 = t1.pes_id 
                    LEFT JOIN   tb_peserta AS t2 ON dp.dm_tutor_2 = t2.pes_id 
                    LEFT JOIN   tb_peserta AS t3 ON dp.dm_tutor_3 = t3.pes_id 
                    LEFT JOIN   tb_peserta AS t4 ON dp.dm_tutor_4 = t4.pes_id 
                    LEFT JOIN   tb_diklat_kehadiran AS dh ON dp.dh_id = dh.dh_id AND dh.dh_status = 1 
                    WHERE       dp.dm_status = 1 AND dh.dh_date = DATE('".$dh_date."')
                                AND dh.kel_id = '".$kel_id."'
                                AND (
                                    t1.pes_fullname LIKE '%".$keyword."%' OR
                                    t2.pes_fullname LIKE '%".$keyword."%' OR
                                    t3.pes_fullname LIKE '%".$keyword."%' OR
                                    t4.pes_fullname LIKE '%".$keyword."%' OR
                                    dp.dm_materi LIKE '%".$keyword."%'
                                )
                    ORDER BY    dp.dm_jam_ke ASC
                ");
                if (!$dtPem){
                    $json['msg'] = 'Tidak ada data';
                } else {
                    $this->load->library('conv');
                    $data['data']   = $dtPem;
                    $json['t']      = 1;
                    $json['html']   = $this->load->view('pengajar/data_pembelajaran',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
    function kelas_select(){
        $json['t'] = 0; $json['msg'] = '';
        $kel_id     = $this->input->post('kel_id');
        $dtKelas    = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
        if (!$kel_id || !$dtKelas){
            $json['msg'] = 'Invalid data kelas';
        } else {
            $dtDate = $this->dbase->dataResult('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'tut'),'dh_date');
            if (!$dtDate){
                $json['msg'] = 'Tidak ada data';
            } else {
                $json['t'] = 1;
                $json['data'] = $dtDate;
            }
        }
        die(json_encode($json));
    }
    function add_pembelajaran(){
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            die('Forbidden');
        } else {
            $kel_id     = $this->uri->segment(3);
            $dh_date    = $this->uri->segment(4);
            $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
            $dtDH       = $this->dbase->dataRow('diklat_kehadiran',array('dh_type'=>'tut','dh_status'=>1,'kel_id'=>$kel_id,'dh_date'=>$dh_date),'dh_id');
            if (!$kel_id || !$dtKel){
                die('Invalid data kelas');
            } elseif (!$dtDH || !$dh_date){
                die('Tanggal kehadiran belum dibuat');
            } else {
                $data['tutor']      = $this->dbase->sqlResult("
                    SELECT      p.pes_id,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,s.sch_name
                    FROM        tb_diklat_kelas_member AS dkm
                    LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                    LEFT JOIN tb_school AS s ON p.sch_id = s.sch_id
                    WHERE       dkm.km_type = 'pengajar' AND dkm.km_status = 1 AND dkm.kel_id = '".$kel_id."'
                    ORDER BY    p.pes_fullname ASC
                ");
                $data['kelas']      = $dtKel;
                $data['dh']         = $dtDH;
                $this->load->view('pengajar/add_pembelajaran',$data);
            }
        }
    }
    function add_pembelajaran_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $json['msg'] = 'Forbidden';
        } else {
            $dh_id      = $this->input->post('dh_id');
            $dtDH       = $this->dbase->dataRow('diklat_kehadiran',array('dh_id'=>$dh_id),'dh_id');
            $tutor_1    = $this->input->post('tutor_1');
            $tutor_2    = $this->input->post('tutor_2');
            $tutor_3    = $this->input->post('tutor_3');
            $tutor_4    = $this->input->post('tutor_4');
            $dt1        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_1,'km_type'=>'pengajar'),'pes_id');
            $dt2        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_2,'km_type'=>'pengajar'),'pes_id');
            $dt3        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_3,'km_type'=>'pengajar'),'pes_id');
            $dt4        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_4,'km_type'=>'pengajar'),'pes_id');
            $dm_jam     = $this->input->post('dm_jam');
            $jam        = explode(':',$dm_jam);
            $dm_materi  = $this->input->post('dm_materi');
            if (!$dh_id || !$dtDH) {
                $json['msg'] = 'Invalid tanggal kehadiran';
            } elseif (strlen($dm_jam) != 5) {
                $json['msg'] = 'Jam pembelajaran belum diisi atau formatnya tidak valid';
            } elseif (count($jam) != 2) {
                $json['msg'] = 'Format jam tidak valid, gunakan format JJ:MM';
            } elseif (strlen(trim($dm_materi)) == 0){
                $json['msg'] = 'Materi belum diisi';
            } elseif (!$tutor_1){
                $json['msg'] = 'Pengajar harus diisi';
            } elseif (strlen($tutor_1) > 0 && !$dt1){
                $json['msg'] = 'Invalid data Pengajar 1';
            } elseif (strlen($tutor_2) > 0 && !$dt2){
                $json['msg'] = 'Invalid data Pengajar 2';
            } elseif (strlen($tutor_3) > 0 && !$dt3) {
                $json['msg'] = 'Invalid data Pengajar 3';
            } elseif (strlen($tutor_4) > 0 && !$dt4) {
                $json['msg'] = 'Invalid data Pengajar 4';
            } else {
                $jamKe  = $this->dbase->dataRow('diklat_pembelajaran',array('dh_id'=>$dh_id,'dm_status'=>1),'COUNT(dm_id) AS cnt')->cnt;
                $jamKe  = $jamKe+1;
                if (strlen($tutor_2) == 0){ $tutor_2 = NULL; }
                if (strlen($tutor_3) == 0){ $tutor_3 = NULL; }
                if (strlen($tutor_4) == 0){ $tutor_4 = NULL; }
                $dm_id  = $this->dbase->dataInsert('diklat_pembelajaran',array(
                    'dh_id'=>$dh_id, 'dm_jam' => $dm_jam, 'dm_jam_ke' => $jamKe, 'dm_materi' => $dm_materi,
                    'dm_tutor_1' => $tutor_1, 'dm_tutor_2' => $tutor_2, 'dm_tutor_3' => $tutor_3, 'dm_tutor_4' => $tutor_4
                ));
                if (!$dm_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t'] = 1;
                    $data['data']   = $this->dbase->sqlResult("
                    SELECT      dp.dm_id,dp.dm_jam,dh.dh_date,dh.dh_id,dp.dm_jam_ke,dp.dm_materi,
                                CONCAT(t1.pes_fullname,', ',t1.pes_gelar_blk) AS tutor_1,
                                CONCAT(t2.pes_fullname,', ',t2.pes_gelar_blk) AS tutor_2,
                                CONCAT(t3.pes_fullname,', ',t3.pes_gelar_blk) AS tutor_3,
                                CONCAT(t4.pes_fullname,', ',t4.pes_gelar_blk) AS tutor_4
                    FROM        tb_diklat_pembelajaran AS dp
                    LEFT JOIN   tb_peserta AS t1 ON dp.dm_tutor_1 = t1.pes_id 
                    LEFT JOIN   tb_peserta AS t2 ON dp.dm_tutor_2 = t2.pes_id 
                    LEFT JOIN   tb_peserta AS t3 ON dp.dm_tutor_3 = t3.pes_id 
                    LEFT JOIN   tb_peserta AS t4 ON dp.dm_tutor_4 = t4.pes_id 
                    LEFT JOIN   tb_diklat_kehadiran AS dh ON dp.dh_id = dh.dh_id AND dh.dh_status = 1 
                    WHERE       dp.dm_id = '".$dm_id."'
                    ");
                    $this->load->library('conv');
                    $json['html']   = $this->load->view('pengajar/data_pembelajaran',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
    function edit_pembelajaran(){
        if ($this->session->userdata('lvl_diklat_pengajar') < 1) {
            die('Forbidden');
        } else {
            $dm_id  = $this->uri->segment(3);
            $dtDM   = $this->dbase->dataRow('diklat_pembelajaran',array('dm_id'=>$dm_id));
            if (!$dm_id || !$dtDM){
                die('Invalid data pembelajaran');
            } else {
                $kel_id   = $this->dbase->dataRow('diklat_kehadiran',array('dh_id'=>$dtDM->dh_id),'kel_id')->kel_id;
                $data['tutor']      = $this->dbase->sqlResult("
                    SELECT      p.pes_id,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,s.sch_name
                    FROM        tb_diklat_kelas_member AS dkm
                    LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                    LEFT JOIN tb_school AS s ON p.sch_id = s.sch_id
                    WHERE       dkm.km_type = 'pengajar' AND dkm.km_status = 1 AND dkm.kel_id = '".$kel_id."'
                    ORDER BY    p.pes_fullname ASC
                ");
                $data['data']   = $dtDM;
                $this->load->view('pengajar/edit_pembelajaran',$data);
            }
        }
    }
    function edit_pembelajaran_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            $json['msg'] = 'Forbidden';
        } else {
            $dm_id      = $this->input->post('dm_id');
            $dtDM       = $this->dbase->dataRow('diklat_pembelajaran',array('dm_id'=>$dm_id),'dm_id');
            $tutor_1    = $this->input->post('tutor_1');
            $tutor_2    = $this->input->post('tutor_2');
            $tutor_3    = $this->input->post('tutor_3');
            $tutor_4    = $this->input->post('tutor_4');
            $dt1        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_1,'km_type'=>'pengajar'),'pes_id');
            $dt2        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_2,'km_type'=>'pengajar'),'pes_id');
            $dt3        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_3,'km_type'=>'pengajar'),'pes_id');
            $dt4        = $this->dbase->dataRow('diklat_kelas_member',array('pes_id'=>$tutor_4,'km_type'=>'pengajar'),'pes_id');
            $dm_jam     = $this->input->post('dm_jam');
            $jam        = explode(':',$dm_jam);
            $dm_materi  = $this->input->post('dm_materi');
            if (!$dm_id || !$dtDM) {
                $json['msg'] = 'Invalid tanggal kehadiran';
            } elseif (strlen($dm_jam) != 5) {
                $json['msg'] = 'Jam pembelajaran belum diisi atau formatnya tidak valid';
            } elseif (count($jam) != 2) {
                $json['msg'] = 'Format jam tidak valid, gunakan format JJ:MM';
            } elseif (strlen(trim($dm_materi)) == 0){
                $json['msg'] = 'Materi belum diisi';
            } elseif (!$tutor_1){
                $json['msg'] = 'Pengajar harus diisi';
            } elseif (strlen($tutor_1) > 0 && !$dt1){
                $json['msg'] = 'Invalid data Pengajar 1';
            } elseif (strlen($tutor_2) > 0 && !$dt2){
                $json['msg'] = 'Invalid data Pengajar 2';
            } elseif (strlen($tutor_3) > 0 && !$dt3) {
                $json['msg'] = 'Invalid data Pengajar 3';
            } elseif (strlen($tutor_4) > 0 && !$dt4) {
                $json['msg'] = 'Invalid data Pengajar 4';
            } else {
                $this->dbase->dataUpdate('diklat_pembelajaran',array('dm_id'=>$dm_id),array(
                    'dm_jam' => $dm_jam, 'dm_materi' => $dm_materi,
                    'dm_tutor_1' => $tutor_1, 'dm_tutor_2' => $tutor_2, 'dm_tutor_3' => $tutor_3, 'dm_tutor_4' => $tutor_4
                ));
                $json['t'] = 1;
                $json['msg'] = 'Pembelajaran berhasil dirubah';
            }
        }
        die(json_encode($json));
    }
    function bulk_delete_pembelajaran(){
        $json['t'] = 0; $json['msg'] = '';
        $dh_id      = $this->input->post('dm_id');
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dh_id){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } elseif (count($dh_id) == 0){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            foreach ($dh_id as $val){
                $chk = $this->dbase->dataRow('diklat_pembelajaran',array('dm_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('diklat_pembelajaran',array('dm_id'=>$val),array('dm_status'=>0));
                }
            }
            $json['t'] = 1;
            $json['data']   = $dh_id;
            $json['msg'] = count($dh_id).' data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_pembelajaran(){
        $json['t'] = 0; $json['msg'] = '';
        $dm_id      = $this->input->post('id');
        $dtDM       = $this->dbase->dataRow('diklat_pembelajaran',array('dm_id'=>$dm_id));
        if ($this->session->userdata('lvl_diklat_pengajar') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dm_id || !$dtDM){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            $this->dbase->dataUpdate('diklat_pembelajaran',array('dm_id'=>$dm_id),array('dm_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function cetak_hadir(){
        if ($this->session->userdata('lvl_diklat_pengajar') < 1){
            die('Forbidden');
        } else {
            $tgl     = $this->uri->segment(3);
            $tgl     = explode('-',$tgl);
            if (!$tgl){
                die('Pilih data lebih dulu');
            } elseif (count($tgl) == 0){
                die('Pilih data lebih dulu');
            } else {
                $tgl_id = ''; $i = 0;
                foreach ($tgl as $val){
                    if (strlen($val) > 0){
                        $tgl_id .= $val;
                        $i++;
                        if ($i + 1 < count($tgl)){
                            $tgl_id .= ',';
                        }
                    }
                }
                //die(var_dump($tgl_id));
                $dtHadir = $this->dbase->sqlResult("
                    SELECT      pb.pb_name,dk.kel_periode_a,dk.kel_periode_b,dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,d.dk_name
                    FROM        tb_diklat_kehadiran AS dh
                    LEFT JOIN   tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                    LEFT JOIN   tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                    LEFT JOIN   tb_diklat AS d ON dk.dk_id = d.dk_id
                    WHERE       dh.dh_id IN (".$tgl_id.")
                    ORDER BY    dh.dh_date ASC
                ");
                if (!$dtHadir){
                    die('Tidak ada data');
                } else {
                    $i = 0;
                    foreach ($dtHadir as $valH){
                        $dtHadir[$i]        = $valH;
                        $dtHadir[$i]->jam   = $this->dbase->sqlResult("
                            SELECT      CONCAT(t1.pes_fullname,', ',t1.pes_gelar_blk) AS tutor_1,
                                        CONCAT(t2.pes_fullname,', ',t2.pes_gelar_blk) AS tutor_2,
                                        dp.dm_jam,dp.dm_materi
                            FROM        tb_diklat_pembelajaran AS dp
                            LEFT JOIN   tb_peserta AS t1 ON dp.dm_tutor_1 = t1.pes_id
                            LEFT JOIN   tb_peserta AS t2 ON dp.dm_tutor_2 = t2.pes_id
                            WHERE       dp.dh_id = '".$valH->dh_id."' AND dp.dm_status = 1
                            ORDER BY    dp.dm_jam_ke ASC
                        ");
                        $dtHadir[$i]->penjab = $this->dbase->sqlRow("
                            SELECT    p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk
                            FROM      tb_diklat_kelas_member AS dkm
                            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                            WHERE     dkm.kel_id = '".$valH->kel_id."' AND dkm.km_status = 1 AND dkm.km_name = 'Penanggung Jawab'
                        ");
                        $i++;
                    }
                    $data['data']   = $dtHadir;
                    $this->load->library('conv');
                    $this->load->view('pengajar/cetak_hadir',$data);
                }
            }
        }
    }
    function upload_hadir(){
        $dh_id  = $this->uri->segment(3);
        $dtDH   = $this->dbase->dataRow('diklat_kehadiran',array('dh_id'=>$dh_id),'dh_id');
        if ($this->session->userdata('lvl_diklat_pengajar') < 2){
            die('Forbidden');
        } elseif (!$dh_id || !$dtDH){
            die('Invalid data kehadiran');
        } else {
            $data['data']   = $dtDH;
            $this->load->view('pengajar/upload_hadir',$data);
        }
    }
    function upload_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pengajar') < 2) {
            $json['msg'] = 'Forbidden';
        } else {
            $dh_id     = $this->input->post('dh_id');
            $dtDh       = $this->dbase->dataRow('diklat_kehadiran',array('dh_id'=>$dh_id));
            $file_data  = $_FILES;
            if (!$dh_id || !$dtDh){
                $json['msg'] = 'Invalid data Kehadiran';
            } elseif (count($file_data) == 0){
                $json['msg'] = 'Pilih file yang akan diupload';
            } else {
                $this->load->library('conv');
                $target = FCPATH . 'assets/upload/'.$this->session->userdata('pb_id').'/';
                if (!file_exists($target)){ @mkdir($target,7777,true); } else { @chmod($target,0777);}
                $page = 1; $allow = array('jpg','png','bmp');
                for($i = 0; $i < count($_FILES['file']['name']); $i++){
                    $file_name      = $_FILES['file']['name'][$i];
                    if (strlen($file_name) > 0){
                        $ext            = explode('.',$file_name);
                        $ext            = end($ext);
                        $ext            = strtolower($ext);
                        if (in_array($ext,$allow)){
                            $file_name      = 'hadir_tutor_'.$dh_id.'_'.$page.'.'.$ext;
                            $target_path    = $target.$file_name;
                            $src_path       = $_FILES['file']['tmp_name'][$i];
                            @move_uploaded_file($src_path,$target_path);
                            @chmod($target_path,0777);
                            $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_page_'.$page => $file_name));
                        }
                    } else {
                        $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_page_'.$page => NULL));
                        @unlink($target.'hadir_tutor_'.$dh_id.'_'.$page.'.jpg');
                        @unlink($target.'hadir_tutor_'.$dh_id.'_'.$page.'.png');
                        @unlink($target.'hadir_tutor_'.$dh_id.'_'.$page.'.bmp');
                    }
                    $page++;
                }
                $json['t'] = 1;
                $json['msg'] = 'Daftar hadir berhasil diupload';
            }
        }
        die(json_encode($json));
    }
    function cetak_gambar_hadir(){
        if ($this->session->userdata('lvl_diklat_pes') < 1){
            die('Forbidden');
        } else {
            $tgl     = $this->uri->segment(3);
            $tgl     = explode('-',$tgl);
            if (!$tgl){
                die('Pilih data lebih dulu');
            } elseif (count($tgl) == 0){
                die('Pilih data lebih dulu');
            } else {
                $tgl_id = ''; $i = 0;
                foreach ($tgl as $val){
                    if (strlen($val) > 0){
                        $tgl_id .= $val;
                        $i++;
                        if ($i + 1 < count($tgl)){
                            $tgl_id .= ',';
                        }
                    }
                }
                //die(var_dump($tgl_id));
                $dtHadir = $this->dbase->sqlResult("
                SELECT    dh.*,dk.pb_id
                FROM      tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE     dh.dh_id IN (".$tgl_id.") AND dh.dh_status = 1 AND dh.dh_type = 'tut'
            ");
                if (!$dtHadir){
                    die('Tidak ada data');
                } else {
                    $data['data']   = $dtHadir;
                    $this->load->view('pengajar/cetak_gambar_hadir',$data);
                }
            }
        }
    }
}
