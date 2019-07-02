<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    public function index()	{
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_user') < 1){
            $data['body'] = 'errors/403';
        } else {
            $data['body']   = 'user/home';
            $data['user']   = 'user';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_table(){
        $json['t']  = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_user') < 1){
            $json['msg'] = 'Forbidden';
        } else {
            $keyword    = $this->input->post('keyword');
            $dtUser     = $this->dbase->sqlResult("SELECT  ul.lvl_name,u.user_id,u.user_name,pb.pb_name
                            FROM      tb_user AS u
                            LEFT JOIN tb_user_level AS ul ON u.user_level = ul.lvl_id
                            LEFT JOIN tb_pusat_belajar AS pb ON u.pb_id = pb.pb_id
                            WHERE     (u.user_name LIKE '%".$keyword."%') AND u.user_status = 1
                            ORDER BY  u.user_name ASC ");
            if (!$dtUser){
                $json['msg'] = 'Data tidak ditemukan';
            } else {
                $data['data']   = $dtUser;
                $json['t']      = 1;
                $json['html']   = $this->load->view('user/data_table',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_user(){
        if ($this->session->userdata('lvl_user') < 2){
            die('Forbidden');
        } else {
            $data['pb']     = $this->dbase->dataResult('pusat_belajar',array('pb_status'=>1));
            $data['level']  = $this->dbase->dataResult('user_level',array('lvl_status'=>1));
            $this->load->view('user/add_user',$data);
        }
    }
    function add_user_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $user_name      = $this->input->post('user_name');
        $user_password  = $this->input->post('user_password');
        $lvl_id         = $this->input->post('lvl_id');
        $pb_id          = $this->input->post('pb_id');
        $dtPB           = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
        $dtLevel        = $this->dbase->dataRow('user_level',array('lvl_id'=>$lvl_id));
        $dtUser         = $this->dbase->dataRow('user',array('user_name'=>$user_name,'user_status'=>1));
        if ($this->session->userdata('lvl_user') < 2){
            $json['msg'] = 'Forbidden';
        } elseif (strlen(trim($user_name)) == 0){
            $json['msg'] = 'Nama pengguna belum diisi';
        } elseif ($dtUser){
            $json['msg'] = 'Nama pengguna sudah dipakai';
        } elseif (strlen(trim($user_password)) == 0) {
            $json['msg'] = 'Password belum diisi';
        } elseif (!$lvl_id || !$dtLevel) {
            $json['msg'] = 'Level pengguna tidak valid';
        } elseif ($lvl_id == 2 && !$dtPB){
            $json['msg'] = 'Pilih pusat belajar';
        } else {
            $arr = array('user_level'=>$lvl_id,'user_name'=>$user_name,'user_password'=>password_hash($user_password,PASSWORD_DEFAULT));
            if ($lvl_id == 2 && $dtPB){
                $arr['pb_id'] = $pb_id;
            }
            $user_id = $this->dbase->dataInsert('user',$arr);
            if (!$user_id){
                $json['msg'] = 'DB Error';
            } else {
                $json['t'] = 1;
                $data['data'] = $this->dbase->sqlResult("SELECT  ul.lvl_name,u.user_id,u.user_name,pb.pb_name
                            FROM      tb_user AS u
                            LEFT JOIN tb_user_level AS ul ON u.user_level = ul.lvl_id
                            LEFT JOIN tb_pusat_belajar AS pb ON u.pb_id = pb.pb_id
                            WHERE     u.user_id = '".$user_id."' ");
                $json['html'] = $this->load->view('user/data_table',$data,TRUE);
                $json['msg'] = 'Pengguna berhasil ditambahkan';
            }
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('user_id');
        if ($this->session->userdata('lvl_user') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('user',array('user_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('user',array('user_id'=>$val),array('user_status'=>0));
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
        $dtUser     = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        if ($this->session->userdata('lvl_user') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('user',array('user_id'=>$user_id),array('user_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Pengguna berhasil dihapus';
        }
        die(json_encode($json));
    }
    function bulk_reset(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('user_id');
        if ($this->session->userdata('lvl_user') < 3){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('user',array('user_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('user',array('user_id'=>$val),array('user_password'=>password_hash('123456',PASSWORD_DEFAULT)));
                }
            }
            $json['t'] = 1;
            $json['data'] = $user_id;
            $json['msg'] = count($user_id).' data berhasil direset kata sandinya menjadi 123456';
        }
        die(json_encode($json));
    }
    function reset_pass_data(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('id');
        $dtUser     = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        if ($this->session->userdata('lvl_user') < 3){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('user',array('user_id'=>$user_id),array('user_password'=>password_hash('123456',PASSWORD_DEFAULT)));
            $json['t'] = 1;
            $json['msg'] = 'Pengguna berhasil direset kata sandinya menjadi 123456';
        }
        die(json_encode($json));
    }
    function edit_data(){
        $user_id    = $this->uri->segment(3);
        $dtUser     = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        if ($this->session->userdata('lvl_user') < 3){
            die('Forbidden');
        } elseif (!$user_id || !$dtUser){
            die('Invalid data');
        } else {
            $data['pb']     = $this->dbase->dataResult('pusat_belajar',array('pb_status'=>1));
            $data['level']  = $this->dbase->dataResult('user_level',array('lvl_status'=>1));
            $data['data']   = $dtUser;
            $this->load->view('user/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $user_id        = $this->input->post('user_id');
        $chkUser        = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        $user_name      = $this->input->post('user_name');
        $user_password  = $this->input->post('user_password');
        $lvl_id         = $this->input->post('lvl_id');
        $pb_id          = $this->input->post('pb_id');
        $dtPB           = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
        $dtLevel        = $this->dbase->dataRow('user_level',array('lvl_id'=>$lvl_id));
        $dtUser         = $this->dbase->dataRow('user',array('user_id !='=>$user_id,'user_name'=>$user_name,'user_status'=>1));
        if ($this->session->userdata('lvl_user') < 3) {
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$chkUser){
            $json['msg'] = 'Invalid data pengguna';
        } elseif (strlen(trim($user_name)) == 0){
            $json['msg'] = 'Nama pengguna belum diisi';
        } elseif ($dtUser){
            $json['msg'] = 'Nama pengguna sudah dipakai';
        } elseif (!$lvl_id || !$dtLevel) {
            $json['msg'] = 'Level pengguna tidak valid';
        } elseif ($lvl_id == 2 && !$dtPB){
            $json['msg'] = 'Pilih pusat belajar';
        } else {
            $arr = array('user_level'=>$lvl_id,'user_name'=>$user_name);
            if ($lvl_id == 2 && $dtPB){
                $arr['pb_id'] = $pb_id;
            }
            if (strlen(trim($user_password)) > 0){
                $arr['user_password'] = password_hash($user_password,PASSWORD_DEFAULT);
            }
            $this->dbase->dataUpdate('user',array('user_id'=>$user_id),$arr);
            $json['t']      = 1;
            $json['msg']    = 'Pengguna berhasil dirubah';
        }
        die(json_encode($json));
    }
    function user_selected(){
        $json['t'] = 0; $json['msg'] = '';
        $user_id    = $this->input->post('user_id');
        $dtUser     = $this->dbase->dataRow('user',array('user_id'=>$user_id));
        if ($user_id && $dtUser){
            $json['t'] = 1;
            $json['data']   = $dtUser;
        }
        die(json_encode($json));
    }
}
