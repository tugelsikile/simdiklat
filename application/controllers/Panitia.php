<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Panitia extends CI_Controller {
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
        } elseif ($this->session->userdata('lvl_diklat_panitia') < 1){
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
                $data['body']   = 'panitia/home';
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
            SELECT  dkm.*,p.pes_nopesukg,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,dkm.km_name
            FROM    tb_diklat_kelas_member AS dkm
            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
            WHERE     (
                        p.pes_fullname LIKE '%".$keyword."%' OR
                        dkm.km_name LIKE '%".$keyword."%'
                      )
                      AND dkm.kel_id = '".$kel_id."' AND dkm.km_status = 1 AND p.pes_status = 1 AND dkm.km_type = 'panitia'
            GROUP BY  dkm.km_id 
            ORDER BY  dkm.km_id ASC");
        if ($this->session->userdata('lvl_diklat_panitia') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dtUser){
            $json['msg'] = 'Data tidak ditemukan';
        } else {
            $this->load->library('conv');
            $data['data']   = $dtUser;
            $json['t']      = 1;
            $json['html']   = $this->load->view('panitia/data_home',$data,TRUE);
        }
        die(json_encode($json));
    }
    function add_data(){
        if ($this->session->userdata('lvl_diklat_panitia') < 2){
            die('Forbidden');
        } else {
            $kel_id = $this->uri->segment(3);
            $dtKel  = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            $panCnt = $this->dbase->dataRow('diklat_kelas_member',array('kel_id'=>$kel_id,'km_type'=>'panitia','km_status'=>1),'COUNT(km_id) AS cnt')->cnt;
            if (!$kel_id || !$dtKel) {
                die('Invalid data kelas');
            } elseif ($panCnt >= 8){
                die('Jumlah Panitia terlalu banyak (max 8), hapus atau rubah salah satu panitia agar bisa menambahkan lagi.');
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
                $this->load->view('panitia/add_data',$data);
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
        $km_name        = $this->input->post('km_name');
        $chkPenjab      = $this->dbase->dataRow('diklat_kelas_member',array('km_status'=>1,'kel_id'=>$kel_id,'km_type'=>'panitia','km_name'=>'Penanggung Jawab'));

        if ($this->session->userdata('lvl_diklat_panitia') < 2) {
            $json['msg'] = 'Forbidden';
        } elseif (!$kel_id || !$dtKel) {
            $json['msg'] = 'Invalid data kelas';
        } elseif ($km_name == 'Penanggung Jawab' && $chkPenjab){
            $json['msg'] = 'Penanggungjawab tidak boleh lebih dari 1 orang';
        } elseif (strlen(trim($user_id)) > 0 && $user_id != 'x' && !$dtUser) {
            $json['msg'] = 'Invalid data pengguna';
        } elseif (strlen(trim($user_id)) && strlen(trim($pes_nopesukg)) && $chkNopes){
            $json['msg'] = 'Nomor Panitia sudah dipakai oleh orang lain';
        } elseif (!$dtUser && strlen(trim($pes_fullname)) == 0) {
            $json['msg'] = 'Nama Panitia belum diisi';
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
                    'user_phone' => $pes_phone, 'user_email' => $pes_email, 'user_npwp' => $pes_npwp, 'user_level' => 5
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
                $km_id = $this->dbase->dataInsert('diklat_kelas_member',array('pes_id'=>$pes_id,'kel_id'=>$kel_id,'km_type'=>'panitia','km_name'=>$km_name));
                if (!$km_id){
                    $json['msg'] = 'DB Kelas member error';
                } else {
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("
                            SELECT  dkm.*,p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,dkm.km_name
                            FROM    tb_diklat_kelas_member AS dkm
                            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                            WHERE     dkm.km_id = '".$km_id."'
                            ");
                    $json['html'] = $this->load->view('panitia/data_home',$data,TRUE);
                    $json['msg'] = 'Panitia berhasil ditambahkan';
                }
            }
        }
        die(json_encode($json));
    }
    function edit_data(){
        $pes_id     = $this->uri->segment(3);
        $dtPes      = $this->dbase->dataRow('peserta',array('pes_id'=>$pes_id));
        $km_id      = $this->uri->segment(4);
        $dtKM       = $this->dbase->dataRow('diklat_kelas_member',array('km_id'=>$km_id));
        if ($this->session->userdata('lvl_diklat_panitia') < 3){
            die('Forbidden');
        } elseif (!$pes_id || !$dtPes) {
            die('Invalid data panitia');
        } elseif (!$km_id || !$dtKM){
            die('Invalid data kelas member');
        } else {
            $data['prov']   = $this->dbase->dataResult('wil_prov',array(),'*','name','ASC');
            $data['sch']    = $this->dbase->dataResult('school',array('sch_status'=>1),'*','sch_name','ASC');
            $data['pang']   = $this->dbase->dataResult('pangkat',array('pangkat_status'=>1));
            $data['data']   = $dtPes;
            $data['kelas']  = $dtKM;
            $this->load->view('panitia/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $pes_id         = $this->input->post('pes_id');
        $dtPes          = $this->dbase->dataRow('peserta',array('pes_id'=>$pes_id),'pes_id');

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
        $km_name        = $this->input->post('km_name');
        $kel_id         = $this->input->post('kel_id');
        $km_id          = $this->input->post('km_id');
        $chKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
        $chKM           = $this->dbase->dataRow('diklat_kelas_member',array('km_id'=>$km_id),'km_id');
        $chPenjab       = $this->dbase->dataRow('diklat_kelas_member',array('km_id !='=>$km_id,'kel_id'=>$kel_id,'km_status'=>1,'km_name'=>'Penanggung Jawab'),'km_id');


        if ($this->session->userdata('lvl_diklat_panitia') < 3) {
            $json['msg'] = 'Forbidden';
        } elseif (!$pes_id || !$dtPes) {
            $json['msg'] = 'Invalid data panitia';
        } elseif (!$km_id || !$chKM) {
            $json['msg'] = 'Invalid data kelas member';
        } elseif (!$kel_id || !$chKel){
            $json['msg'] = 'Invalid data kelas';
        } elseif ($km_name == 'Penanggung Jawab' && $chPenjab){
            $json['msg'] = 'Penanggung Jawab tidak boleh lebih dari 1';
        } elseif (strlen(trim($pes_fullname)) == 0) {
            $json['msg'] = 'Nama panitia belum diisi';
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
            $this->dbase->dataUpdate('diklat_kelas_member',array('km_id'=>$km_id),array('km_name'=>$km_name));
            $json['t']      = 1;
            $json['msg']    = 'Panitia berhasil diupdate';
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('km_id');
        if ($this->session->userdata('lvl_diklat_panitia') < 4){
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
        if ($this->session->userdata('lvl_diklat_panitia') < 4){
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
    function hadir(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_panitia') < 1){
            $data['body']   = 'errors/403';
        } else {
            $kel_id         = $this->uri->segment(3);
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,kel_name,dk_id');
            if (!$kel_id || !$dtKel){
                $data['body'] = 'errors/404';
            } else {
                $data['kel']    = $this->dbase->dataResult('diklat_kelas',array('kel_status'=>1,'pb_id'=>$this->session->userdata('pb_id'),'dk_id'=>$dtKel->dk_id),'kel_id,kel_name,dk_id');
                $data['data']   = $dtKel;
                $data['body']   = 'panitia/hadir';
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
                SELECT      dh.dh_id,dh.dh_date,dh.dh_format,dh.dh_page_1,dk.kel_name
                FROM        tb_diklat_kehadiran AS dh
                LEFT JOIN   tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE       dh.dh_status = 1 AND dh.dh_type = 'pan' AND dh.kel_id = '".$kel_id."'
                ORDER BY    dh.dh_date ASC 
            ");
            if (!$dtHadir){
                $json['msg'] = 'Data tidak ditemukan';
            } else {
                $this->load->library('conv');
                $data['data']   = $dtHadir;
                $json['t']      = 1;
                $json['html']   = $this->load->view('panitia/data_hadir',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_hadir(){
        $kel_id     = $this->uri->segment(3);
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        if ($this->session->userdata('lvl_diklat_panitia') < 2){
            die('Forbidden');
        } elseif (!$kel_id || !$dtKel) {
            die('Invalid data kelas');
        } else {
            $tglCount       = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pan'),'COUNT(dh_id) AS cnt')->cnt;
            $tgl            = new DateTime($dtKel->kel_periode_a);
            $tgl            = $tgl->modify('+'.$tglCount.' day');
            $data['tgl']    = $tgl;
            $data['data']   = $dtKel;
            $this->load->view('panitia/add_hadir',$data);
        }
    }
    function add_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $kel_id     = $this->input->post('kel_id');
        $dtKel      = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
        $dh_date    = $this->input->post('dh_date');
        $chkDate    = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_date'=>$dh_date,'dh_status'=>1,'dh_type'=>'pan'));
        if ($this->session->userdata('lvl_diklat_panitia') < 2){
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
                    'kel_id' => $kel_id, 'dh_date' => $dh_date, 'dh_type' => 'pan'
                ));
                if (!$dh_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("
                        SELECT    dk.kel_name,dh.dh_date,dh.dh_id,dh.kel_id,dh.dh_page_1,dh.dh_format
                        FROM      tb_diklat_kehadiran AS dh
                        LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                        WHERE     dh.dh_id = '".$dh_id."'
                    ");
                    $this->load->library('conv');
                    $json['html'] = $this->load->view('panitia/data_hadir',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
    function cetak_hadir(){
        if ($this->session->userdata('lvl_diklat_panitia') < 1){
            die('Forbidden');
        } else {
            $kel_id     = $this->uri->segment(3);
            $dtKelas = $this->dbase->sqlRow("
                    SELECT      d.dk_name,dk.kel_periode_a,dk.kel_periode_b,dk.kel_name,pb.pb_name,dk.kel_id
                    FROM        tb_diklat_kelas AS dk
                    LEFT JOIN   tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                    LEFT JOIN   tb_diklat AS d ON dk.dk_id = d.dk_id
                    WHERE       dk.kel_id = '".$kel_id."'
            ");
            if (!$kel_id || !$dtKelas){
                die('Invalid data kelas');
            } else {
                $dtHadir = $this->dbase->dataResult('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pan'));
                if (!$dtHadir){
                    die('Tidak ada data tanggal');
                } else {
                    $dtKelas->hadir = $dtHadir;
                    $dtKelas->pes   = $this->dbase->sqlResult("
                            SELECT    p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk
                            FROM      tb_diklat_kelas_member AS dkm
                            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                            WHERE     dkm.kel_id = '".$kel_id."' AND dkm.km_status = 1 AND dkm.km_type = 'panitia'
                            ORDER BY  p.pes_fullname ASC
                        ");
                    $dtKelas->penjab= $this->dbase->sqlRow("
                            SELECT    p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk
                            FROM      tb_diklat_kelas_member AS dkm
                            LEFT JOIN tb_peserta AS p ON dkm.pes_id = p.pes_id
                            WHERE     dkm.kel_id = '".$kel_id."' AND dkm.km_status = 1 AND dkm.km_name = 'Penanggung Jawab'
                        ");
                    $data['data']   = $dtKelas;
                    $this->load->library('conv');
                    $this->load->view('panitia/cetak_hadir',$data);
                }
            }
        }
    }
    function upload_hadir(){
        $kel_id     = $this->uri->segment(3);
        $dtKelas    = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
        if (!$kel_id || !$dtKelas){
            die('Invalid data kelas');
        } elseif ($this->session->userdata('lvl_diklat_panitia') < 1){
            die('Forbidden');
        } else {
            $data['data']   = $dtKelas;
            $this->load->view('panitia/upload_hadir',$data);
        }
    }
    function upload_hadir_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_pes') < 1) {
            $json['msg'] = 'Forbidden';
        } else {
            $kel_id     = $this->input->post('kel_id');
            $dtKelas    = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id');
            $dtHadir    = $this->dbase->dataResult('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1),'dh_id');
            if ($this->session->userdata('lvl_diklat_panitia') < 1){
                $json['msg'] = 'Forbidden';
            } elseif (!$kel_id || !$dtKelas){
                $json['msg'] = 'Invalid data kelas';
            } elseif (!$dtHadir){
                $json['msg'] = 'Tidak ada tanggal kehadiran';
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
                            $file_name      = 'hadir_panitia_'.$kel_id.'_'.$page.'.'.$ext;
                            $target_path    = $target.$file_name;
                            $src_path       = $_FILES['file']['tmp_name'][$i];
                            @move_uploaded_file($src_path,$target_path);
                            @chmod($target_path,0777);
                            $this->dbase->dataUpdate('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pan'),array('dh_page_'.$page => $file_name));
                        }
                    } else {
                        $this->dbase->dataUpdate('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pan'),array('dh_page_'.$page => NULL));
                        @unlink($target.'hadir_panitia_'.$kel_id.'_'.$page.'.jpg');
                        @unlink($target.'hadir_panitia_'.$kel_id.'_'.$page.'.png');
                        @unlink($target.'hadir_panitia_'.$kel_id.'_'.$page.'.bmp');
                    }
                    $page++;
                }
                $json['t']  = 1;
                $json['msg'] = 'Daftar hadir berhasil diupload';
            }
        }
        die(json_encode($json));
    }
    function bulk_delete_hadir(){
        $json['t'] = 0; $json['msg'] = '';
        $dh_id      = $this->input->post('dh_id');
        if ($this->session->userdata('lvl_diklat_panitia') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dh_id){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } elseif (count($dh_id) == 0){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            foreach ($dh_id as $val){
                $chk = $this->dbase->sqlRow("
                    SELECT  dh.dh_id,dh.dh_page_1,dh.dh_page_2,dh.dh_page_3,dh.dh_page_4,dh.dh_page_5,dh.dh_page_6,dh.dh_page_7,
                            dh.dh_page_8,dk.pb_id,dh.kel_id
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
                    $chkHadir = $this->dbase->dataRow('diklat_kehadiran',array('dh_type'=>'pan','kel_id'=>$chk->kel_id,'dh_status'=>1),'COUNT(dh_id) AS cnt')->cnt;
                    if (!$chkHadir){
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
                        dh.dh_page_8,dk.pb_id,dh.kel_id
                FROM    tb_diklat_kehadiran AS dh
                LEFT JOIN tb_diklat_kelas AS dk ON dh.kel_id = dk.kel_id
                WHERE   dh.dh_id = '".$dh_id."'
                ");
        if ($this->session->userdata('lvl_diklat_panitia') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$dh_id || !$dtDh){
            $json['msg'] = 'Pilih data yang akan dihapus';
        } else {
            $this->dbase->dataUpdate('diklat_kehadiran',array('dh_id'=>$dh_id),array('dh_status'=>0, 'dh_page_1' => NULL, 'dh_page_2' => NULL, 'dh_page_3' => NULL, 'dh_page_4' => NULL,
                'dh_page_5' => NULL, 'dh_page_6' => NULL, 'dh_page_7' => NULL, 'dh_page_8' => NULL));
            $chkHadir = $this->dbase->dataRow('diklat_kehadiran',array('dh_type'=>'pan','kel_id'=>$dtDh->kel_id,'dh_status'=>1),'COUNT(dh_id) AS cnt')->cnt;
            if (!$chkHadir){
                if (strlen($dtDh->dh_page_1) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_1); }
                if (strlen($dtDh->dh_page_2) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_2); }
                if (strlen($dtDh->dh_page_3) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_3); }
                if (strlen($dtDh->dh_page_4) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_4); }
                if (strlen($dtDh->dh_page_5) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_5); }
                if (strlen($dtDh->dh_page_6) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_6); }
                if (strlen($dtDh->dh_page_7) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_7); }
                if (strlen($dtDh->dh_page_8) > 0){ $this->delete_hadir_file(FCPATH.'assets/upload/'.$dtDh->pb_id.'/'.$dtDh->dh_page_8); }
            }
            $json['t'] = 1;
            $json['msg'] = 'data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function cetak_gambar_hadir(){
        if ($this->session->userdata('lvl_diklat_panitia') < 1){
            die('Forbidden');
        } else {
            $kel_id     = $this->uri->segment(3);
            $dtKelas    = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id),'kel_id,pb_id');
            $dtHadir    = $this->dbase->dataRow('diklat_kehadiran',array('kel_id'=>$kel_id,'dh_status'=>1,'dh_type'=>'pan'),'dh_id,dh_page_1,dh_page_2,dh_page_3,dh_page_4,dh_page_5,dh_page_6,dh_page_7,dh_page_8');
            if (!$kel_id || !$dtKelas){
                die('Tidak ada data kelas');
            } elseif (!$dtHadir){
                die('Tidak ada data tanggal');
            } else {
                $data['data']   = $dtHadir;
                $data['kelas']  = $dtKelas;
                $this->load->view('panitia/cetak_gambar_hadir',$data);
            }
        }
    }
}
