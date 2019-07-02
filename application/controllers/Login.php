<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    function __construct(){
        parent::__construct();
    }
    public function index()	{
		if ($this->session->userdata('login')){
		    redirect(base_url(''));
        }
        $this->load->view('login');
	}
	function submit(){
        $json['t'] = 0; $json['msg'] = '';
        $user_name  = $this->input->post('user_name');
        $user_pass  = $this->input->post('user_pass');
        $dtUser     = $this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1));
        if (strlen(trim($user_name)) == 0){
            $json['msg'] = 'Mohon masukkan nama pengguna';
        } elseif (!$dtUser){
            $json['msg'] = 'Nama pengguna tidak terdaftar ';//.password_hash($user_pass,PASSWORD_DEFAULT);
        } elseif (strlen(trim($user_pass)) == 0){
            $json['msg'] = 'Masukkan kata sandi';
        } elseif (!password_verify($user_pass,$dtUser->user_password)){
            $json['msg'] = 'Kombinasi nama pengguna dan kata sandinya tidak valid';
        } else {
            $dtLevel    = $this->dbase->dataRow('user_level',array('lvl_id'=>$dtUser->user_level));
            $ar = array(
                'user_id'   => $dtUser->user_id,    'user_name' => $dtUser->user_name,
                'login'     => true,                'lvl_pb'    => $dtLevel->lvl_pb,
                'lvl_diklat'=> $dtLevel->lvl_diklat, 'lvl_diklat_kelas' => $dtLevel->lvl_diklat_kelas,
                'lvl_diklat_pes' => $dtLevel->lvl_diklat_pes, 'lvl_diklat_pengajar' => $dtLevel->lvl_diklat_pengajar,
                'lvl_diklat_panitia' => $dtLevel->lvl_diklat_panitia, 'lvl_diklat_postpretest' => $dtLevel->lvl_diklat_postpretest,
                'lvl_diklat_sikap' => $dtLevel->lvl_diklat_sikap, 'lvl_diklat_keterampilan' => $dtLevel->lvl_diklat_keterampilan,
                'lvl_diklat_nilai' => $dtLevel->lvl_diklat_nilai, 'lvl_diklat_sertifikat' => $dtLevel->lvl_diklat_sertifikat,
                'lvl_sch' => $dtLevel->lvl_sch, 'lvl_user' => $dtLevel->lvl_user, 'lvl_name' => $dtLevel->lvl_name,
                'lvl_soal' => $dtLevel->lvl_soal, 'lvl_soal_jawab' => $dtLevel->lvl_soal_jawab
            );
            if (strlen(trim($dtUser->pb_id)) > 0){
                $chPB = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$dtUser->pb_id),'pb_id,pb_name');
                if ($chPB){
                    $ar['pb_id']    = $dtUser->pb_id;
                    $ar['pb_name']  = $chPB->pb_name;
                }
            }
            $this->session->set_userdata($ar);
            $json['t'] = 1;
            $json['msg'] = 'Login sukses';
        }
        die(json_encode($json));
    }
}
