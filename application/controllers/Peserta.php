<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta extends CI_Controller {
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
        } elseif ($this->session->userdata('lvl_diklat_pes') < 1){
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
                $data['body']   = 'peserta/home';
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
        $keyword    = $this->input->post('keyword',TRUE);
        $kel_id     = $this->input->post('kel_id',TRUE);
        //$dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
        /*$dtUser     = $this->dbase->dataResult(
            'diklat_kelas_member',
            array('diklat_kelas_member.kel_id'=>$kel_id,'diklat_kelas_member.km_status'=>1,'tb_peserta.pes_status'=>1,'diklat_kelas_member.km_type'=>'peserta'),
            'tb_peserta.pes_id,diklat_kelas_member.km_id,diklat_kelas_member.km_name,tb_peserta.pes_nopesukg,tb_peserta.pes_gelar_depan,tb_peserta.pes_fullname,tb_peserta.pes_gelar_blk,tb_school.sch_name,tb_wil_kab.`name` AS kab_name,tb_wil_prov.`name` AS prov_name',
            'tb_peserta.pes_fullname',
            'ASC',
            '',
            '',
            array('tb_peserta.pes_fullname'=>$keyword,'tb_peserta.pes_nopesukg'=>$keyword,'tb_school.sch_name'=>$keyword,'tb_wil_kab.`name`'=>$keyword,'tb_wil_prov.`name`'=>$keyword),
            array(
                0 => array('tb_name' => 'tb_peserta', 'tb_val' => 'diklat_kelas_member.pes_id = tb_peserta.pes_id'),
                1 => array('tb_name' => 'tb_school', 'tb_val' => 'tb_peserta.sch_id = tb_school.sch_id'),
                2 => array('tb_name' => 'tb_wil_kab', 'tb_val' => 'tb_school.kab_id = tb_wil_kab.kab_id'),
                3 => array('tb_name' => 'tb_wil_prov', 'tb_val' => 'tb_wil_kab.prov_id = tb_wil_prov.prov_id')
            ),
            'diklat_kelas_member.km_id');*/
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
                      AND dkm.kel_id = '".$kel_id."' AND dkm.km_status = 1 AND p.pes_status = 1 AND dkm.km_type = 'peserta'
            GROUP BY  dkm.km_id 
            ORDER BY  p.pes_fullname ASC");
        if ($this->session->userdata('lvl_diklat_pes') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dtUser){
            $json['msg'] = 'Data tidak ditemukan';
        } else {
            $this->load->library('conv');
            $data['data']   = $dtUser;
            $json['t']      = 1;
            $json['html']   = $this->load->view('peserta/data_home',$data,TRUE);
        }
        die(json_encode($json));
    }
    function add_data(){
        if ($this->session->userdata('lvl_diklat_pes') < 2){
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
                $this->load->view('peserta/add_data',$data);
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

        if ($this->session->userdata('lvl_diklat_pes') < 2) {
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
                    'user_phone' => $pes_phone, 'user_email' => $pes_email, 'user_npwp' => $pes_npwp, 'user_level' => 4
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
                $km_id = $this->dbase->dataInsert('diklat_kelas_member',array('pes_id'=>$pes_id,'kel_id'=>$kel_id,'km_type'=>'peserta'));
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
                    $json['html'] = $this->load->view('peserta/data_home',$data,TRUE);
                    $json['msg'] = 'Peserta berhasil ditambahkan';
                }
            }
        }
        die(json_encode($json));
    }
    function edit_data(){
        $pes_id     = $this->uri->segment(3);
        $dtPes      = $this->dbase->dataRow('peserta',array('pes_id'=>$pes_id));
        if ($this->session->userdata('lvl_diklat_pes') < 3){
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

        if ($this->session->userdata('lvl_diklat_pes') < 3) {
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
        if ($this->session->userdata('lvl_diklat_pes') < 4){
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
        if ($this->session->userdata('lvl_diklat_pes') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('diklat_kelas_member',array('km_id'=>$user_id),array('km_status'=>0));
            $this->dbase->dataUpdate('peserta',array('pes_id'=>$dtUser->pes_id),array('pes_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Peserta berhasil dihapus';
        }
        die(json_encode($json));
    }
    function import(){
        if ($this->session->userdata('lvl_diklat_pes') < 2){
            die('Forbidden');
        } else {
            $kel_id     = $this->uri->segment(3);
            $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            if (!$dtKel || !$kel_id){
                die('Invalid data kelas');
            } else {
                $data['data']   = $dtKel;
                $this->load->view('peserta/import',$data);
            }
        }
    }
    function import_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pes') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            $kel_id     = $this->input->post('kel_id');
            $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            if (!$kel_id || !$dtKel){
                $json['msg'] = 'Invalid data kelas';
            } else {
                if (!$_FILES['file']['name']) {
                    $json['msg'] = 'Mohon pilih filenya';
                } elseif ($_FILES['file']['error']) {
                    $json['msg'] = 'Ada error pada filenya';
                } else {
                    $filename   = $_FILES['file']['name'];
                    $ext        = explode('.',$filename);
                    $ext        = end($ext);
                    $ext        = strtolower($ext);
                    if ($ext != 'xlsx'){
                        $json['msg'] = 'Tipe file import peserta tidak valid';
                    } else {
                        ini_set('max_execution_time', 100000);
                        $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
                        $inputFileName = $_FILES["file"]["tmp_name"];
                        $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
                        $cacheSettings = array( 'memoryCacheSize' => '2GB');
                        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                        $inputFileType 	= IOFactory::identify($inputFileName);
                        $objReader 		= IOFactory::createReader($inputFileType);
                        $objPHPExcel 	= $objReader->load($inputFileName);
                        try {
                            $inputFileType  = IOFactory::identify($inputFileName);
                            $objReader      = IOFactory::createReader($inputFileType);
                            $objPHPExcel    = $objReader->load($inputFileName);
                        } catch(Exception $e) {
                            $json['msg'] = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
                        }//end try

                        //  Get worksheet dimensions
                        //$sheet 			= $objPHPExcel->getSheet(0);
                        $sheet          = $objPHPExcel->setActiveSheetIndex(0);
                        $highestRow 	= $sheet->getHighestRow();
                        $n = 0; $data = array();
                        for ($row = 3; $row <= $highestRow; $row++) {
                            $pes_nopesukg       = $sheet->getCell('B' . $row)->getValue();
                            $pes_gelar_depan    = $sheet->getCell('C' . $row)->getValue();
                            $pes_fullname       = $sheet->getCell('D' . $row)->getValue();
                            $pes_gelar_blk      = $sheet->getCell('E' . $row)->getValue();
                            $pes_sex            = $sheet->getCell('F' . $row)->getValue();
                            $pes_bplace         = $sheet->getCell('G' . $row)->getValue();
                            $pes_bdate          = $sheet->getCell('H' . $row)->getValue();
                            $pes_agama          = $sheet->getCell('I' . $row)->getValue();
                            $pes_nuptk          = $sheet->getCell('J' . $row)->getValue();
                            $pes_nip            = $sheet->getCell('K' . $row)->getValue();
                            $pangkat            = $sheet->getCell('L' . $row)->getValue();
                            $pes_jabatan        = $sheet->getCell('M' . $row)->getValue();
                            $pes_didik          = $sheet->getCell('N' . $row)->getValue();
                            $pes_jurusan        = $sheet->getCell('O' . $row)->getValue();
                            $pes_address        = $sheet->getCell('P' . $row)->getValue();
                            $pes_phone          = $sheet->getCell('Q' . $row)->getValue();
                            $pes_email          = $sheet->getCell('R' . $row)->getValue();
                            $pes_npwp           = $sheet->getCell('S' . $row)->getValue();
                            $sch_name           = $sheet->getCell('T' . $row)->getValue();
                            $sch_negeri         = $sheet->getCell('U' . $row)->getValue();
                            $sch_nss            = $sheet->getCell('V' . $row)->getValue();
                            $sch_npsn           = $sheet->getCell('W' . $row)->getValue();
                            $sch_address        = $sheet->getCell('X' . $row)->getValue();
                            $kab_name           = $sheet->getCell('Y' . $row)->getValue();
                            $prov_name          = $sheet->getCell('Z' . $row)->getValue();
                            $sch_phone          = $sheet->getCell('AA' . $row)->getValue();
                            $sch_fax            = $sheet->getCell('AB' . $row)->getValue();
                            $sch_email          = $sheet->getCell('AC' . $row)->getValue();

                            if (strlen(trim($pes_fullname)) > 0){
                                $data[$n]   = new stdClass();
                                $data[$n]->kel_id   = $kel_id;
                                //start data peserta
                                $chkubynopesukg = $this->dbase->dataRow('user',array('user_nopesukg'=>$pes_nopesukg));
                                $data[$n]->pangkat_id       = NULL;
                                $data[$n]->user_id          = NULL;
                                if ($chkubynopesukg){
                                    $data[$n]->user_id          = $chkubynopesukg->user_id;
                                    $data[$n]->pes_nopesukg     = $chkubynopesukg->user_nopesukg;
                                    $data[$n]->pes_fullname     = $chkubynopesukg->user_fullname;
                                    $data[$n]->pes_gelar_blk    = $chkubynopesukg->user_gelar_blk;
                                    $data[$n]->pes_gelar_depan  = $chkubynopesukg->user_gelar_depan;
                                    $data[$n]->pes_sex          = $chkubynopesukg->user_sex;
                                    $data[$n]->pes_bplace       = $chkubynopesukg->user_bplace;
                                    $data[$n]->pes_bdate        = $chkubynopesukg->user_bdate;
                                    $data[$n]->pes_agama        = $chkubynopesukg->user_agama;
                                    $data[$n]->pes_nuptk        = $chkubynopesukg->user_nuptk;
                                    $data[$n]->pes_nip          = $chkubynopesukg->user_nip;
                                    $data[$n]->pes_jabatan      = $chkubynopesukg->user_jabatan;
                                    $data[$n]->pes_didik        = $chkubynopesukg->user_didik;
                                    $data[$n]->pes_jurusan      = $chkubynopesukg->user_jurusan;
                                    $data[$n]->pes_address      = $chkubynopesukg->user_address;
                                    $data[$n]->pes_phone        = $chkubynopesukg->user_phone;
                                    $data[$n]->pes_email        = $chkubynopesukg->user_email;
                                    $data[$n]->pes_npwp         = $chkubynopesukg->user_npwp;
                                } else {
                                    $data[$n]->pes_nopesukg     = $pes_nopesukg;
                                    $data[$n]->pes_fullname     = $pes_fullname;
                                    $data[$n]->pes_gelar_blk    = $pes_gelar_blk;
                                    $data[$n]->pes_gelar_depan  = $pes_gelar_depan;
                                    $data[$n]->pes_sex          = $pes_sex;
                                    $data[$n]->pes_bplace       = $pes_bplace;
                                    $data[$n]->pes_bdate        = $pes_bdate;
                                    $data[$n]->pes_agama        = $pes_agama;
                                    $data[$n]->pes_nuptk        = $pes_nuptk;
                                    $data[$n]->pes_nip          = $pes_nip;
                                    $data[$n]->pes_jabatan      = $pes_jabatan;
                                    $data[$n]->pes_didik        = $pes_didik;
                                    $data[$n]->pes_jurusan      = $pes_jurusan;
                                    $data[$n]->pes_address      = $pes_address;
                                    $data[$n]->pes_phone        = $pes_phone;
                                    $data[$n]->pes_email        = $pes_email;
                                    $data[$n]->pes_npwp         = $pes_npwp;
                                }
                                if (strlen(trim($pangkat)) > 0){
                                    $pang_id = $this->dbase->sqlRow("SELECT pangkat_id FROM tb_pangkat WHERE pangkat_name LIKE '%".$pangkat."%' ");
                                    if ($pang_id){
                                        $data[$n]->pangkat_id = $pang_id->pangkat_id;
                                    }
                                }
                                //end data peserta

                                //start data sekolah
                                $chknamasch = $this->dbase->sqlRow("SELECT * FROM tb_school WHERE sch_name LIKE '%".$sch_name."%' ");
                                $data[$n]->sch_id     = NULL;
                                if ($chknamasch){
                                    $data[$n]->sch_id       = $chknamasch->sch_id;
                                } else {
                                    $data[$n]->sch_name     = $sch_name;
                                    $data[$n]->sch_negeri   = $sch_negeri;
                                    $data[$n]->sch_nss      = $sch_nss;
                                    $data[$n]->sch_npsn     = $sch_npsn;
                                    $data[$n]->sch_address  = $sch_address;
                                    $data[$n]->sch_phone    = $sch_phone;
                                    $data[$n]->sch_fax      = $sch_fax;
                                    $data[$n]->sch_email    = $sch_email;
                                    $data[$n]->kab_id       = 3204;
                                    $dtKab = $this->dbase->sqlRow("SELECT  kab.kab_id
                                                FROM      tb_wil_kab AS kab
                                                LEFT JOIN tb_wil_prov AS prov ON kab.prov_id = prov.prov_id
                                                WHERE     kab.`name` LIKE '%".$kab_name."%' AND prov.`name` LIKE '%".$prov_name."%' ");
                                    if ($dtKab){ $data[$n]->kab_id   = $dtKab->kab_id; }
                                }
                                //end data sekolah
                                $n++;
                            }
                        }
                        //die(var_dump($data));
                        if ($n > 0){
                            $json['dataLength'] = count($data);
                            $json['t'] = 1;
                            $json['data']   = $data;
                        } else {
                            $json['msg'] = 'Tidak ada data pada file ini';
                        }
                    }
                }
            }
        }
        die(json_encode($json));
    }
    function import_proses(){
        $json['t'] = 1; $json['msg'] = '';
        $data   = $this->input->post('data');
        if ($this->session->userdata('lvl_diklat_pes') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            //die(var_dump($data['sch_id']));
            //start school
			$sch_id 	= $data['sch_id'];
			if (!$sch_id){
                $dataSch    = $this->dbase->sqlRow("SELECT sch_id FROM tb_school WHERE sch_name LIKE '%".$data['sch_name']."%' ");
                if (!$dataSch){
                    $arsch = array(
                        'sch_name' => $data['sch_name'], 'sch_negeri' => $data['sch_negeri'], 'sch_address' => $data['sch_address'],
                        'sch_phone' => $data['sch_phone'], 'sch_fax' => $data['sch_fax'], 'sch_email' => $data['sch_email'], 'kab_id' => $data['kab_id'],
                        'sch_nss' => $data['sch_nss'], 'sch_npsn' => $data['sch_npsn']
                    );
                    $sch_id     = $this->dbase->dataInsert('school',$arsch);
                }
            }
            //end school

            //start user
            if (!$data['user_id']){
                $aruser = array(
                    'sch_id' => $sch_id, 'pangkat_id' => $data['pangkat_id'], 'user_fullname' => strtoupper($data['pes_fullname']), 'user_gelar_depan' => $data['pes_gelar_depan'],
                    'user_gelar_blk' => $data['pes_gelar_blk'], 'user_bplace' => $data['pes_bplace'], 'user_bdate' => $data['pes_bdate'], 'user_sex' => $data['pes_sex'],
                    'user_nip' => $data['pes_nip'], 'user_nuptk' => $data['pes_nuptk'], 'user_nopesukg' => $data['pes_nopesukg'], 'user_jabatan' => $data['pes_jabatan'],
                    'user_didik' => $data['pes_didik'], 'user_jurusan' => $data['pes_jurusan'], 'user_agama' => $data['pes_agama'], 'user_address' => $data['pes_address'],
                    'user_phone' => $data['pes_phone'], 'user_email' => $data['pes_email'], 'user_npwp' => $data['pes_npwp'], 'user_level' => 4
                );
                $user_name  = str_replace(" ","",strtolower($data['pes_fullname']));
                $aruser['user_name']    = $user_name;
                $aruser['user_password']= password_hash('123456',PASSWORD_DEFAULT);
                if (strlen(trim($data['pangkat_id'])) == 0){ $aruser['pangkat_id'] = NULL; }
                $user_id = $this->dbase->dataInsert('user',$aruser);
            }
            //end user

            $arpes = array(
                'sch_id' => $sch_id, 'pangkat_id' => $data['pangkat_id'], 'pes_fullname' => strtoupper($data['pes_fullname']), 'pes_gelar_depan' => $data['pes_gelar_depan'],
                'pes_gelar_blk' => $data['pes_gelar_blk'], 'pes_bplace' => $data['pes_bplace'], 'pes_bdate' => $data['pes_bdate'], 'pes_sex' => $data['pes_sex'],
                'pes_nip' => $data['pes_nip'], 'pes_nuptk' => $data['pes_nuptk'], 'pes_nopesukg' => $data['pes_nopesukg'], 'pes_jabatan' => $data['pes_jabatan'],
                'pes_didik' => $data['pes_didik'], 'pes_jurusan' => $data['pes_jurusan'], 'pes_agama' => $data['pes_agama'], 'pes_address' => $data['pes_address'],
                'pes_phone' => $data['pes_phone'], 'pes_email' => $data['pes_email'], 'pes_npwp' => $data['pes_npwp']
            );
            if (strlen(trim($data['pangkat_id'])) == 0){ $arpes['pangkat_id'] = NULL; }
            $pes_id = $this->dbase->dataInsert('peserta',$arpes);
            if (!$pes_id){
                $json['msg'] = 'DB Error';
            } else {
                $km_id = $this->dbase->dataInsert('diklat_kelas_member',array('pes_id'=>$pes_id,'kel_id'=>$data['kel_id']));
                if (!$km_id){
                    $json['msg'] = 'DB Kelas ERROR';
                } else {
                    $json['t'] = 1;
                }
            }
        }
        die(json_encode($json));
    }
    function hadir(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_pes') < 1){
            $data['body']   = 'errors/403';
        } else {
            $kel_id         = $this->uri->segment(3);
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,kel_name,dk_id');
            if (!$kel_id || !$dtKel){
                $data['body'] = 'errors/404';
            } else {
                $data['kel']    = $this->dbase->dataResult('diklat_kelas',array('kel_status'=>1,'pb_id'=>$this->session->userdata('pb_id'),'dk_id'=>$dtKel->dk_id),'kel_id,kel_name,dk_id');
                $data['data']   = $dtKel;
                $data['body']   = 'peserta/hadir';
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
                SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1,dh.dh_format
                FROM      tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE     dh.dh_status = 1 AND dh.kel_id = '".$kel_id."' AND dh.dh_type = 'pes'
                ORDER BY  dh.dh_date ASC
            ");
            if (!$dtHadir){
                $json['msg'] = 'Data tidak ditemukan';
            } else {
                $this->load->library('conv');
                $data['data']   = $dtHadir;
                $json['t']      = 1;
                $json['html']   = $this->load->view('peserta/data_hadir',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_hadir(){
        $kel_id     = $this->uri->segment(3);
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        if ($this->session->userdata('lvl_diklat_pes') < 2){
            die('Forbidden');
        } elseif (!$kel_id || !$dtKel) {
            die('Invalid data kelas');
        } else {
            $tglCount       = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pes'),'COUNT(dh_id) AS cnt')->cnt;
            //if ($tglCount > 0){ $tglCount = $tglCount + 1; }
            $tgl            = new DateTime($dtKel->kel_periode_a);
            $tgl            = $tgl->modify('+'.$tglCount.' day');
            $data['tgl']    = $tgl;
            $data['data']   = $dtKel;
            $this->load->view('peserta/add_hadir',$data);
        }
    }
    function add_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $dh_format  = $this->input->post('dh_format');
        $kel_id     = $this->input->post('kel_id');
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        $dh_date    = $this->input->post('dh_date');
        $chkDate    = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$dh_date,'dh_status'=>1,'dh_type'=>'pes'));
        if ($this->session->userdata('lvl_diklat_pes') < 2){
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
                    'kel_id' => $kel_id, 'dh_date' => $dh_date, 'dh_format' => $dh_format, 'dh_type' => 'pes'
                ));
                if (!$dh_id){
                    $json['msg'] = 'DB Error';
                } else {
                    //insert dh ke tutor dan panitia
                    $chk_dh_tutor = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$dh_date,'dh_type'=>'tut','dh_status'=>1),'dh_id');
                    $tutor_cnt    = $this->dbase->dataRow('diklat_kelas_member',array('kel_id'=>$kel_id,'km_status'=>1,'km_type'=>'pengajar'),'km_id');
                    $chk_dh_pan   = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$dh_date,'dh_type'=>'pan','dh_status'=>1),'dh_id');
                    if (!$chk_dh_pan){
                        $this->dbase->dataInsert('diklat_kehadiran',array(
                            'kel_id' => $kel_id, 'dh_date' => $dh_date, 'dh_type' => 'pan', 'dh_format' => $dh_format
                        ));
                    }
                    if (!$chk_dh_tutor && $tutor_cnt){
                        $dh_idtut = $this->dbase->dataInsert('diklat_kehadiran',array(
                            'kel_id' => $kel_id, 'dh_date' => $dh_date, 'dh_type' => 'tut'
                        ));
                        // insert jam pembelajaran
                        if (date('N',strtotime($dh_date)) != 5){ //jika bukan hari jum'at
                            $this->insertJam($dh_idtut,'08:00:00',$kel_id);        //1
                            $this->insertJam($dh_idtut,'08:45:00',$kel_id);        //2
                            // 09:15 - 09:30 istirahat
                            $this->insertJam($dh_idtut,'09:30:00',$kel_id);        //3
                            $this->insertJam($dh_idtut,'10:15:00',$kel_id);        //4
                            $this->insertJam($dh_idtut,'11:00:00',$kel_id);        //5
                            // 11:45 - 12:30 isoma + dzuhur
                            $this->insertJam($dh_idtut,'12:30:00',$kel_id);        //6
                            $this->insertJam($dh_idtut,'13:15:00',$kel_id);        //7
                            $this->insertJam($dh_idtut,'14:00:00',$kel_id);        //8
                            $this->insertJam($dh_idtut,'14:45:00',$kel_id);        //9
                            // 15:30 - 16:00 isoma + ashar
                            $this->insertJam($dh_idtut,'16:00:00',$kel_id);        //10
                            $this->insertJam($dh_idtut,'16:45:00',$kel_id);        //11
                            // 17:15 - 20:00 isoma shalat maghrib + isya
                            $this->insertJam($dh_idtut,'20:00:00',$kel_id);        //12
                            $this->insertJam($dh_idtut,'20:45:00',$kel_id);        //13
                        } else {
                            $this->insertJam($dh_idtut,'08:00:00',$kel_id);        //1
                            $this->insertJam($dh_idtut,'08:45:00',$kel_id);        //2
                            // 09:15 - 09:30 istirahat
                            $this->insertJam($dh_idtut,'09:30:00',$kel_id);        //3
                            $this->insertJam($dh_idtut,'10:15:00',$kel_id);        //4
                            $this->insertJam($dh_idtut,'11:00:00',$kel_id);        //5
                            // 11:00 - 13:00 isoma + shalat jum'at
                            $this->insertJam($dh_idtut,'13:00:00',$kel_id);        //6
                            $this->insertJam($dh_idtut,'13:45:00',$kel_id);        //7
                            $this->insertJam($dh_idtut,'14:15:00',$kel_id);        //8
                            // 15:00 - 16:30 isoma + ashar
                            $this->insertJam($dh_idtut,'15:30:00',$kel_id);        //9
                            $this->insertJam($dh_idtut,'16:15:00',$kel_id);        //10
                            $this->insertJam($dh_idtut,'17:00:00',$kel_id);        //11
                            // 17:00 - 20:00 isoma shalat maghrib + isya
                            $this->insertJam($dh_idtut,'20:00:00',$kel_id);        //12
                            $this->insertJam($dh_idtut,'20:45:00',$kel_id);        //13
                        }
                    }
                    //end insert
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("
                        SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1,dh.dh_format
                        FROM      tb_diklat_kehadiran AS dh
                        LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                        WHERE     dh.dh_id = '".$dh_id."'
                    ");
                    $this->load->library('conv');
                    $json['html'] = $this->load->view('peserta/data_hadir',$data,TRUE);
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
        if ($this->session->userdata('lvl_diklat_pes') < 4){
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
        if ($this->session->userdata('lvl_diklat_pes') < 4){
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
                WHERE     dh.dh_id IN (".$tgl_id.") AND dh.dh_status = 1 AND dh.dh_type = 'pes'
            ");
                if (!$dtHadir){
                    die('Tidak ada data');
                } else {
                    $data['data']   = $dtHadir;
                    $this->load->view('peserta/cetak_gambar_hadir',$data);
                }
            }
        }
    }
    function cetak_hadir(){
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
                SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,d.dk_name,pb.pb_name,dk.kel_periode_a,dk.kel_periode_b,dh.dh_format
                FROM      tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                LEFT JOIN tb_diklat AS d ON dk.dk_id = d.dk_id
                LEFT JOIN tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                WHERE     dh.dh_id IN (".$tgl_id.") AND dh.dh_status = 1 AND dh.dh_type = 'pes'
            ");
                if (!$dtHadir){
                    die('Tidak ada data');
                } else {
                    $i = 0;
                    foreach ($dtHadir as $valH){
                        $dtHadir[$i]    = $valH;
                        $dtHadir[$i]->peserta = $this->dbase->sqlResult("
                        SELECT    p.pes_nopesukg,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,s.sch_name
                        FROM      tb_diklat_kelas_member AS dkm
                        LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                        LEFT JOIN tb_school AS s ON p.sch_id = s.sch_id
                        WHERE     dkm.kel_id = '".$valH->kel_id."' AND dkm.km_status = 1 AND dkm.km_type = 'peserta'
                        ORDER BY  p.pes_fullname ASC
                    ");
                        $dtHadir[$i]->penjab = $this->dbase->sqlRow("
                        SELECT    p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk
                        FROM      tb_diklat_kelas_member AS dkm
                        LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                        WHERE     dkm.kel_id = '".$valH->kel_id."' AND dkm.km_status = 1 AND dkm.km_name = 'Penanggung Jawab'
                    ");
                        $dtHadir[$i]->tutor = $this->dbase->sqlResult("
                        SELECT    p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk
                        FROM      tb_diklat_kelas_member AS dkm
                        LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                        WHERE     dkm.km_status = 1 AND dkm.km_type = 'pengajar' AND dkm.kel_id = '".$valH->kel_id."'
                    ");
                        $i++;
                    }
                    $data['data']   = $dtHadir;
                    $this->load->library('conv');
                    $this->load->view('peserta/cetak_hadir',$data);
                }
            }
        }
    }
    function upload_hadir(){
        if ($this->session->userdata('lvl_diklat_pes') < 1) {
            die('Forbidden');
        } else {
            $dh_id  = $this->uri->segment(3);
            $dtKel  = $this->dbase->dataRow('diklat_kehadiran',array('dh_id'=>$dh_id));
            if (!$dh_id || !$dtKel){
                die('Invalid data kelas');
            } else {
                $data['data']   = $dtKel;
                $this->load->view('peserta/upload_hadir',$data);
            }
        }
    }
    function upload_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pes') < 1) {
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
                            $file_name      = 'hadir_'.$dh_id.'_'.$page.'.'.$ext;
                            $target_path    = $target.$file_name;
                            $src_path       = $_FILES['file']['tmp_name'][$i];
                            @move_uploaded_file($src_path,$target_path);
                            @chmod($target_path,0777);
                            $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_page_'.$page => $file_name));
                        }
                    } else {
                        $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_page_'.$page => NULL));
                        @unlink($target.'hadir_'.$dh_id.'_'.$page.'.jpg');
                        @unlink($target.'hadir_'.$dh_id.'_'.$page.'.png');
                        @unlink($target.'hadir_'.$dh_id.'_'.$page.'.bmp');
                    }
                    $page++;
                }
                $json['t'] = 1;
                $json['msg'] = 'Daftar hadir berhasil diupload';
            }
        }
        die(json_encode($json));
    }

}
