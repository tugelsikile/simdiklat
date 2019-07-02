<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Diklat extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    //start diklat segment
    public function index()	{
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat') < 1){
            $data['body']   = 'errors/403';
        } else {
            $data['body']   = 'diklat/home';
            $data['diklat']   = 'diklat';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_table(){
        $json['t']  = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $dtUser     = $this->dbase->dataResult('diklat',array('dk_status'=>1),'*','dk_date_start','DESC','','',array('dk_name'=>$keyword));
        /*$dtUser     = $this->dbase->sqlResult("SELECT d.*
                                    FROM      tb_diklat AS d
                                    WHERE     d.dk_name LIKE '%".$keyword."%' AND d.dk_status = 1
                                    GROUP BY  d.dk_id
                                    ORDER BY  d.dk_date_start DESC ");*/
        if ($this->session->userdata('lvl_diklat') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dtUser){
            $json['msg'] = 'Data tidak ditemukan';
        } else {
            $i = 0;
            foreach ($dtUser as $val){
                $dtUser[$i] = $val;
                $dtUser[$i]->cnt = 0;
                $cnt = $this->dbase->dataRow('diklat_kelas',array('dk_id'=>$val->dk_id,'kel_status'=>1),'COUNT(kel_id) AS cnt');
                if ($cnt){
                    $dtUser[$i]->cnt = $cnt->cnt;
                }
                $i++;
            }
            $data['data']   = $dtUser;
            $json['t']      = 1;
            $json['html']   = $this->load->view('diklat/data_table',$data,TRUE);
        }
        die(json_encode($json));
    }
    function add_data(){
        if ($this->session->userdata('lvl_diklat') < 2){
            die('Forbidden');
        } else {
            $this->load->view('diklat/add_data');
        }
    }
    function add_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $dk_name            = $this->input->post('dk_name');
        $dk_date_start      = $this->input->post('dk_date_start');
        $dk_date_end        = $this->input->post('dk_date_end');
        $date_start         = explode("-",$dk_date_start);
        $date_end           = explode('-',$dk_date_end);
        $dk_place           = $this->input->post('dk_place');
        $dk_place_address   = $this->input->post('dk_place_address');
        $dk_keterangan      = $this->input->post('dk_keterangan');
        $dk_check_in        = $this->input->post('dk_check_in');
        $dk_check_out       = $this->input->post('dk_check_out');
        $dk_check_in_jam    = $this->input->post('dk_check_in_jam');
        $dk_check_out_jam   = $this->input->post('dk_check_out_jam');
        $dk_pembukaan       = $this->input->post('dk_pembukaan');
        $dk_penutupan       = $this->input->post('dk_penutupan');
        $dk_pembukaan_jam   = $this->input->post('dk_pembukaan_jam');
        $dk_penutupan_jam   = $this->input->post('dk_penutupan_jam');
        $tgcheckin          = explode('-',$dk_check_in);
        $tgcheckout         = explode('-',$dk_check_out);
        $jamcheckin         = explode(':',$dk_check_in_jam);
        $jamcheckout        = explode(':',$dk_check_out_jam);
        $tgbuka             = explode('-',$dk_pembukaan);
        $tgtutup            = explode('-',$dk_penutupan);
        $jambuka            = explode(':',$dk_pembukaan_jam);
        $jamtutup           = explode(':',$dk_penutupan_jam);
        $dk_nomor_surat     = $this->input->post('dk_nomor_surat');

        if ($this->session->userdata('lvl_diklat') < 2){
            $json['msg'] = 'Forbidden';
        } elseif (strlen(trim($dk_name)) == 0) {
            $json['msg'] = 'Nama diklat belum diisi';
        } elseif (strlen(trim($dk_nomor_surat)) == 0){
            $json['msg'] = 'Nomor surat belum diisi';
        } elseif (strlen(trim($dk_date_start)) != 10) {
            $json['msg'] = 'Tanggal diklat dimulai belum diisi';
        } elseif (count($date_start) != 3) {
            $json['msg'] = 'Tanggal diklat dimulai tidak valid';
        } elseif (strlen(trim($dk_date_end)) != 10) {
            $json['msg'] = 'Tanggal diklat selesai belum diisi';
        } elseif (count($date_end) != 3) {
            $json['msg'] = 'Tanggal diklat selesai tidak valid';
        } elseif (strlen(trim($dk_check_in)) != 10) {
            $json['msg'] = 'Tanggal check-in belum diisi';
        } elseif (count($tgcheckin) != 3) {
            $json['msg'] = 'Tanggal check-in tidak valid';
        } elseif (strlen(trim($dk_check_in_jam)) != 5) {
            $json['msg'] = 'Jam check-in belum diisi';
        } elseif (count($jamcheckin) != 2) {
            $json['msg'] = 'Jam check-in tidak valid';
        } elseif (strlen(trim($dk_check_out)) != 10) {
            $json['msg'] = 'Tanggal check-out belum diisi';
        } elseif (count($tgcheckout) != 3) {
            $json['msg'] = 'Tanggal check-out tidak valid';
        } elseif (strlen(trim($dk_check_out_jam)) != 5) {
            $json['msg'] = 'Jam check-out belum diisi';
        } elseif (count($jamcheckout) != 2) {
            $json['msg'] = 'Jam check-out tidak valid';
        } elseif (strlen(trim($dk_pembukaan)) != 10) {
            $json['msg'] = 'Tanggal pembukaan belum diisi';
        } elseif (count($tgbuka) != 3) {
            $json['msg'] = 'Tanggal pembukaan tidak valid';
        } elseif (strlen(trim($dk_pembukaan_jam)) != 5) {
            $json['msg'] = 'Jam pembukaan belum diisi';
        } elseif (count($jambuka) != 2){
            $json['msg'] = 'Jam pembukaan tidak valid';
        } elseif (strlen(trim($dk_penutupan)) != 10) {
            $json['msg'] = 'Tanggal penutupan belum diisi';
        } elseif (count($tgtutup) != 3) {
            $json['msg'] = 'Tanggal penutupan tidak valid';
        } elseif (strlen(trim($dk_penutupan_jam)) != 5) {
            $json['msg'] = 'Jam penutupan belum diisi';
        } elseif (count($jamtutup) != 2){
            $json['msg'] = 'Jam penutupan tidak valid';
        } else {
            $arr = array(
                'dk_name'=>$dk_name, 'dk_date_start'=>$dk_date_start, 'dk_place'=>$dk_place, 'dk_place_address'=>$dk_place_address,
                'dk_keterangan'=>$dk_keterangan, 'dk_date_end' => $dk_date_end, 'dk_check_in' => $dk_check_in.' '.$dk_check_in_jam.':00',
                'dk_check_out' => $dk_check_out.' '.$dk_check_out_jam.':00', 'dk_pembukaan' => $dk_pembukaan.' '.$dk_pembukaan_jam.':00',
                'dk_penutupan' => $dk_penutupan.' '.$dk_penutupan_jam.':00', 'dk_nomor_surat' => $dk_nomor_surat
            );
            $user_id = $this->dbase->dataInsert('diklat',$arr);
            if (!$user_id){
                $json['msg'] = 'DB Error';
            } else {
                $json['t'] = 1;
                $data['data'] = $this->dbase->sqlResult("SELECT
                                    d.*,Count(dk.kel_id) AS cnt
                                    FROM      tb_diklat AS d
                                    LEFT JOIN tb_diklat_kelas AS dk ON dk.dk_id = d.dk_id
                                    WHERE d.dk_id = '".$user_id."' ");
                $json['html'] = $this->load->view('diklat/data_table',$data,TRUE);
                $json['msg'] = 'Diklat berhasil ditambahkan';
            }
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('dk_id');
        if ($this->session->userdata('lvl_diklat') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('diklat',array('dk_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('diklat',array('dk_id'=>$val),array('dk_status'=>0));
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
        $dtUser     = $this->dbase->dataRow('diklat',array('dk_id'=>$user_id));
        if ($this->session->userdata('lvl_diklat') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('diklat',array('dk_id'=>$user_id),array('dk_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Diklat berhasil dihapus';
        }
        die(json_encode($json));
    }
    function edit_data(){
        $user_id    = $this->uri->segment(3);
        $dtUser     = $this->dbase->dataRow('diklat',array('dk_id'=>$user_id));
        if ($this->session->userdata('lvl_diklat') < 3){
            die('Forbidden');
        } elseif (!$user_id || !$dtUser){
            die('Invalid data');
        } else {
            $data['data']   = $dtUser;
            $this->load->view('diklat/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $dk_id              = $this->input->post('dk_id');
        $dtDK               = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id));
        $dk_name            = $this->input->post('dk_name');
        $dk_date_start      = $this->input->post('dk_date_start');
        $dk_date_end        = $this->input->post('dk_date_end');
        $date_start         = explode("-",$dk_date_start);
        $date_end           = explode('-',$dk_date_end);
        $dk_place           = $this->input->post('dk_place');
        $dk_place_address   = $this->input->post('dk_place_address');
        $dk_keterangan      = $this->input->post('dk_keterangan');
        $dk_check_in        = $this->input->post('dk_check_in');
        $dk_check_out       = $this->input->post('dk_check_out');
        $dk_check_in_jam    = $this->input->post('dk_check_in_jam');
        $dk_check_out_jam   = $this->input->post('dk_check_out_jam');
        $dk_pembukaan       = $this->input->post('dk_pembukaan');
        $dk_penutupan       = $this->input->post('dk_penutupan');
        $dk_pembukaan_jam   = $this->input->post('dk_pembukaan_jam');
        $dk_penutupan_jam   = $this->input->post('dk_penutupan_jam');
        $tgcheckin          = explode('-',$dk_check_in);
        $tgcheckout         = explode('-',$dk_check_out);
        $jamcheckin         = explode(':',$dk_check_in_jam);
        $jamcheckout        = explode(':',$dk_check_out_jam);
        $tgbuka             = explode('-',$dk_pembukaan);
        $tgtutup            = explode('-',$dk_penutupan);
        $jambuka            = explode(':',$dk_pembukaan_jam);
        $jamtutup           = explode(':',$dk_penutupan_jam);
        $dk_nomor_surat     = $this->input->post('dk_nomor_surat');

        if ($this->session->userdata('lvl_diklat') < 3) {
            $json['msg'] = 'Forbidden';
        } elseif (!$dk_id || !$dtDK){
            $json['msg'] = 'Invalid data diklat';
        } elseif (strlen(trim($dk_name)) == 0) {
            $json['msg'] = 'Nama diklat belum diisi';
        } elseif (strlen(trim($dk_nomor_surat)) == 0){
            $json['msg'] = 'Nomor surat belum diisi';
        } elseif (strlen(trim($dk_date_start)) != 10) {
            $json['msg'] = 'Tanggal diklat dimulai belum diisi';
        } elseif (count($date_start) != 3) {
            $json['msg'] = 'Tanggal diklat dimulai tidak valid';
        } elseif (strlen(trim($dk_date_end)) != 10) {
            $json['msg'] = 'Tanggal diklat selesai belum diisi';
        } elseif (count($date_end) != 3) {
            $json['msg'] = 'Tanggal diklat selesai tidak valid';
        } elseif (strlen(trim($dk_check_in)) != 10) {
            $json['msg'] = 'Tanggal check-in belum diisi';
        } elseif (count($tgcheckin) != 3) {
            $json['msg'] = 'Tanggal check-in tidak valid';
        } elseif (strlen(trim($dk_check_in_jam)) != 5) {
            $json['msg'] = 'Jam check-in belum diisi';
        } elseif (count($jamcheckin) != 2) {
            $json['msg'] = 'Jam check-in tidak valid';
        } elseif (strlen(trim($dk_check_out)) != 10) {
            $json['msg'] = 'Tanggal check-out belum diisi';
        } elseif (count($tgcheckout) != 3) {
            $json['msg'] = 'Tanggal check-out tidak valid';
        } elseif (strlen(trim($dk_check_out_jam)) != 5) {
            $json['msg'] = 'Jam check-out belum diisi';
        } elseif (count($jamcheckout) != 2) {
            $json['msg'] = 'Jam check-out tidak valid';
        } elseif (strlen(trim($dk_pembukaan)) != 10) {
            $json['msg'] = 'Tanggal pembukaan belum diisi';
        } elseif (count($tgbuka) != 3) {
            $json['msg'] = 'Tanggal pembukaan tidak valid';
        } elseif (strlen(trim($dk_pembukaan_jam)) != 5) {
            $json['msg'] = 'Jam pembukaan belum diisi';
        } elseif (count($jambuka) != 2){
            $json['msg'] = 'Jam pembukaan tidak valid';
        } elseif (strlen(trim($dk_penutupan)) != 10) {
            $json['msg'] = 'Tanggal penutupan belum diisi';
        } elseif (count($tgtutup) != 3) {
            $json['msg'] = 'Tanggal penutupan tidak valid';
        } elseif (strlen(trim($dk_penutupan_jam)) != 5) {
            $json['msg'] = 'Jam penutupan belum diisi';
        } elseif (count($jamtutup) != 2){
            $json['msg'] = 'Jam penutupan tidak valid';
        } else {
            $arr = array(
                'dk_name'=>$dk_name, 'dk_date_start'=>$dk_date_start, 'dk_place'=>$dk_place, 'dk_place_address'=>$dk_place_address,
                'dk_keterangan'=>$dk_keterangan, 'dk_date_end' => $dk_date_end, 'dk_check_in' => $dk_check_in.' '.$dk_check_in_jam.':00',
                'dk_check_out' => $dk_check_out.' '.$dk_check_out_jam.':00', 'dk_pembukaan' => $dk_pembukaan.' '.$dk_pembukaan_jam.':00',
                'dk_penutupan' => $dk_penutupan.' '.$dk_penutupan_jam.':00', 'dk_nomor_surat' => $dk_nomor_surat
            );
            //$arr = array('dk_name'=>$dk_name,'dk_date_start'=>$dk_date_start,'dk_place'=>$dk_place,'dk_place_address'=>$dk_place_address,'dk_keterangan'=>$dk_keterangan);
            $this->dbase->dataUpdate('diklat',array('dk_id'=>$dk_id),$arr);
            $json['t'] = 1;
            $json['msg'] = 'Data diklat berhasil dirubah';
        }
        die(json_encode($json));
    }
    // end diklat

    // start kelas
    function kelas(){
        $dk_id = $this->uri->segment(3);
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_diklat_kelas') < 1) {
            $data['body'] = 'errors/403';
        } elseif (!$dk_id){
            $data['body'] = 'errors/500';
        } else {
            $dtDik          = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id));
            if (!$dtDik){
                $data['body']   = 'errors/404';
            } else {
                $data['pb']     = $this->dbase->dataResult('pusat_belajar',array('pb_status'=>1),'pb_id,pb_name');
                $data['data']   = $dtDik;
                $data['body']   = 'diklat/kelas_home';
                $data['diklat'] = 'diklat';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_kelas(){
        $json['t'] = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $dk_id      = $this->input->post('dk_id');
        $pb_id      = $this->input->post('pb_id');
        $dtPB       = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id),'pb_id');
        $dtDK       = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id,'dk_status'=>1));
        if ($this->session->userdata('lvl_diklat_kelas') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dk_id || !$dtDK) {
            $json['msg'] = 'Invalid data diklat';
        } elseif (strlen(trim($pb_id)) > 0 && !$dtPB){
            $json['msg'] = 'Invalid Pusat Belajar';
        } else {
            $sql_pb = "";
            if (strlen(trim($pb_id)) > 0 && $dtPB){ $sql_pb = " AND dk.pb_id = '".$pb_id."' "; }
            $dtKelas = $this->dbase->sqlResult("SELECT  dk.*,pb.pb_name,Count(dkm.km_id) AS cnt
                                FROM      tb_diklat_kelas AS dk
                                LEFT JOIN tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                                LEFT JOIN tb_diklat_kelas_member AS dkm ON dkm.kel_id = dk.kel_id AND dkm.km_status = 1 AND dkm.km_type = 'peserta'
                                WHERE     (dk.kel_name LIKE '%".$keyword."%' OR pb.pb_name LIKE '%".$keyword."%' )
                                          AND dk.kel_status = 1 AND dk.dk_id = '".$dk_id."' ".$sql_pb."
                                GROUP BY  dk.kel_id
                                ORDER BY  dk.kel_name ASC ");
            if (!$dtKelas){
                $json['msg'] = 'Tidak ada data';
            } else {
                $this->load->library('conv');
                $json['t']      = 1;
                $data['data']   = $dtKelas;
                $json['html']   = $this->load->view('diklat/data_kelas',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_kelas(){
        if ($this->session->userdata('lvl_diklat_kelas') < 2){
            die('Forbidden');
        } else {
            $dk_id  = $this->uri->segment(3);
            $dtDK   = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id));
            if (!$dk_id || !$dtDK){
                die('Invalid data diklat');
            } else {
                $data['pb']     = $this->dbase->dataResult('pusat_belajar',array('pb_status'=>1));
                $data['data']   = $dtDK;
                $this->load->view('diklat/add_kelas',$data);
            }
        }
    }
    function add_kelas_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_kelas') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            $dk_id          = $this->input->post('dk_id');
            $dtDK           = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id));
            $kel_name       = $this->input->post('kel_name');
            $kel_periode_a  = $this->input->post('kel_periode_a');
            $dateA          = explode("-",$kel_periode_a);
            $kel_periode_b  = $this->input->post('kel_periode_b');
            $dateB          = explode("-",$kel_periode_b);
            $pb_id          = $this->input->post('pb_id');
            $dtPB           = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
            if (!$dk_id || !$dtDK){
                $json['msg'] = 'Invalid data diklat';
            } elseif (strlen(trim($kel_name)) == 0){
                $json['msg'] = 'Nama kelas harus diisi';
            } elseif (strlen(trim($kel_periode_a)) != 10){
                $json['msg'] = 'Periode Mulai tidak valid';
            } elseif (count($dateA) != 3){
                $json['msg'] = 'Periode Mulai tidak valid';
            } elseif (strlen(trim($kel_periode_b)) != 10){
                $json['msg'] = 'Periode Selesai tidak valid';
            } elseif (count($dateB) != 3) {
                $json['msg'] = 'Periode Selesai tidak valid';
            } elseif (!$pb_id || !$dtPB){
                $json['msg'] = 'Pilih pusat belajar';
            } else {
                $arr = array(
                    'dk_id' => $dk_id, 'pb_id' => $pb_id, 'kel_name' => $kel_name, 'kel_periode_a' => $kel_periode_a,
                    'kel_periode_b' => $kel_periode_b
                );
                $kel_id = $this->dbase->dataInsert('diklat_kelas',$arr);
                if (!$kel_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $this->load->library('conv');
                    $json['t'] = 1;
                    $data['data'] = $this->dbase->sqlResult("SELECT  dk.*,pb.pb_name,Count(dkm.km_id) AS cnt
                                FROM      tb_diklat_kelas AS dk
                                LEFT JOIN tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                                LEFT JOIN tb_diklat_kelas_member AS dkm ON dkm.kel_id = dk.kel_id
                                WHERE     dk.kel_id = '".$kel_id."'
                                GROUP BY  pb.pb_name
                                ORDER BY  dk.kel_name ASC ");
                    $json['html'] = $this->load->view('diklat/data_kelas',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
    function edit_kelas(){
        if ($this->session->userdata('lvl_diklat_kelas') < 3){
            die('Forbidden');
        } else {
            $kel_id = $this->uri->segment(3);
            $dtKel  = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            if (!$kel_id || !$dtKel){
                die('Invalid data Kelas');
            } else {
                $data['data']   = $dtKel;
                $data['pb']     = $this->dbase->dataResult('pusat_belajar',array('pb_status'=>1));
                $this->load->view('diklat/edit_kelas',$data);
            }
        }
    }
    function edit_kelas_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_diklat_kelas') < 3){
            $json['msg'] = 'Forbidden';
        } else {
            $kel_id         = $this->input->post('kel_id');
            $dtKel          = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$kel_id));
            $kel_name       = $this->input->post('kel_name');
            $kel_periode_a  = $this->input->post('kel_periode_a');
            $dateA          = explode("-",$kel_periode_a);
            $kel_periode_b  = $this->input->post('kel_periode_b');
            $dateB          = explode("-",$kel_periode_b);
            $pb_id          = $this->input->post('pb_id');
            $dtPB           = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
            if (!$kel_id || !$dtKel){
                $json['msg'] = 'Invalid data kelas';
            } elseif (strlen(trim($kel_name)) == 0){
                $json['msg'] = 'Nama kelas harus diisi';
            } elseif (strlen(trim($kel_periode_a)) != 10){
                $json['msg'] = 'Periode Mulai tidak valid';
            } elseif (count($dateA) != 3){
                $json['msg'] = 'Periode Mulai tidak valid';
            } elseif (strlen(trim($kel_periode_b)) != 10){
                $json['msg'] = 'Periode Selesai tidak valid';
            } elseif (count($dateB) != 3) {
                $json['msg'] = 'Periode Selesai tidak valid';
            } elseif (!$pb_id || !$dtPB){
                $json['msg'] = 'Pilih pusat belajar';
            } else {
                $arr = array(
                    'pb_id' => $pb_id, 'kel_name' => $kel_name, 'kel_periode_a' => $kel_periode_a,
                    'kel_periode_b' => $kel_periode_b
                );
                $this->dbase->dataUpdate('diklat_kelas',array('kel_id'=>$kel_id),$arr);
                $json['t']      = 1;
                $json['msg']    = 'Data kelas berhasil dirubah';
            }
        }
        die(json_encode($json));
    }
    function bulk_kelas_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('kel_id');
        if ($this->session->userdata('lvl_diklat_kelas') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('diklat_kelas',array('kel_id'=>$val),array('kel_status'=>0));
                }
            }
            $json['t'] = 1;
            $json['data'] = $user_id;
            $json['msg'] = count($user_id).' data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_kelas(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('id');
        $dtUser     = $this->dbase->dataRow('diklat_kelas',array('kel_id'=>$user_id));
        if ($this->session->userdata('lvl_diklat') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('diklat_kelas',array('kel_id'=>$user_id),array('kel_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Kelas berhasil dihapus';
        }
        die(json_encode($json));
    }
    //end kelas

    function pb(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_pb') < 1) {
            $data['body'] = 'errors/403';
        } else {
            $data['body']   = 'diklat/pb_home';
            $data['diklat'] = 'diklat';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_pb(){
        $json['t'] = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        if ($this->session->userdata('lvl_pb') < 1) {
            $json['msg'] = 'Forbidden';
        } else {
            $dtPB   = $this->dbase->sqlResult("SELECT pb.*
                                FROM    tb_pusat_belajar AS pb
                                WHERE   (
                                        pb.pb_name LIKE '%".$keyword."%' OR
                                        pb.pb_address LIKE '%".$keyword."%' OR
                                        pb.pb_phone LIKE '%".$keyword."%'
                                        )
                                        AND pb.pb_status = 1 
                                ORDER BY pb.pb_name ASC ");
            if (!$dtPB){
                $json['msg'] = 'Tidak ada data';
            } else {
                $i = 0;
                foreach ($dtPB as $val){
                    $dtPB[$i]       = $val;
                    $dtPB[$i]->cnt  = 0;
                    $cnt = $this->dbase->sqlRow("SELECT  Count(DISTINCT(dk.dk_id)) AS cnt
                                    FROM      tb_diklat_kelas AS dk
                                    WHERE     dk.pb_id = '".$val->pb_id."' AND dk.kel_status = 1 ");
                    if ($cnt){
                        $dtPB[$i]->cnt  = $cnt->cnt;
                    }
                    $i++;
                }
                $data['data']   = $dtPB;
                $json['t']      = 1;
                $json['html']   = $this->load->view('diklat/data_pb',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_pb(){
        if ($this->session->userdata('lvl_pb') < 2){
            die('Forbidden');
        } else {
            $data['prov']   = $this->dbase->dataResult('wil_prov',array(),'*','name','asc');
            $this->load->view('diklat/add_pb',$data);
        }
    }
    function add_pb_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_pb') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            $pb_name    = $this->input->post('pb_name');
            $chName     = $this->dbase->dataRow('pusat_belajar',array('pb_name'=>$pb_name,'pb_status'=>1));
            $pb_phone   = $this->input->post('pb_phone');
            $pb_email   = $this->input->post('pb_email');
            $pb_address = $this->input->post('pb_address');
            $kec_id     = $this->input->post('kec_id');
            $dtKec      = $this->dbase->dataRow('wil_kec',array('kec_id'=>$kec_id));
            if (strlen(trim($pb_name)) == 0){
                $json['msg'] = 'Masukkan nama Pusat Belajar';
            } elseif ($chName){
                $json['msg'] = 'Pusat belajar dengan nama ini sudah ada';
            } elseif (strlen(trim($pb_phone)) == 0){
                $json['msg'] = 'No. Telp belum diisi';
            } elseif (strlen(trim($pb_email)) == 0){
                $json['msg'] = 'Alamat email belum diisi';
            } elseif (strlen(trim($pb_address)) == 0){
                $json['msg'] = 'Alamat pusat belajar belum diisi';
            } elseif (!$kec_id || !$dtKec){
                $json['msg'] = 'Pilih Kecamatan';
            } else {
                $arr = array(
                    'pb_name' => $pb_name, 'pb_address' => $pb_address, 'pb_phone' => $pb_phone, 'pb_email' => $pb_email, 'kec_id' => $kec_id
                );
                $pb_id = $this->dbase->dataInsert('pusat_belajar',$arr);
                if (!$pb_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t']      = 1;
                    $json['msg']    = 'Pusat belajar berhasil ditambahkan';
                    $data['data']   = $this->dbase->sqlResult("SELECT pb.*
                                FROM    tb_pusat_belajar AS pb
                                WHERE   pb.pb_id = '".$pb_id."' ");
                    $data['data'][0]->cnt = 0;
                    $json['html']   = $this->load->view('diklat/data_pb',$data,TRUE);
                }
            }

        }
        die(json_encode($json));
    }
    function edit_pb(){
        if ($this->session->userdata('lvl_pb') < 3){
            die('Forbidden');
        } else {
            $pb_id  = $this->uri->segment(3);
            $dtPB   = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
            if (!$pb_id || !$dtPB){
                die('Invalid data Pusat Belajar');
            } else {
                $data['data']   = $dtPB;
                $data['prov']   = $this->dbase->dataResult('wil_prov',array(),'*','name','asc');
                $kec            = $this->dbase->dataRow('wil_kec',array('kec_id'=>$dtPB->kec_id));
                $kab            = $this->dbase->dataRow('wil_kab',array('kab_id'=>$kec->kab_id));
                $prov           = $this->dbase->dataRow('wil_prov',array('prov_id'=>$kab->prov_id));
                $data['kab']    = $this->dbase->dataResult('wil_kab',array('prov_id'=>$prov->prov_id));
                $data['kec']    = $this->dbase->dataResult('wil_kec',array('kab_id'=>$kec->kab_id));
                $data['kab_id'] = $kec->kab_id;
                $data['prov_id']= $kab->prov_id;
                $this->load->view('diklat/edit_pb',$data);
            }
        }
    }
    function edit_pb_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_pb') < 3){
            $json['msg'] = 'Forbidden';
        } else {
            $pb_id      = $this->input->post('pb_id');
            $dtPB       = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$pb_id));
            $pb_name    = $this->input->post('pb_name');
            $chName     = $this->dbase->dataRow('pusat_belajar',array('pb_id !='=>$pb_id,'pb_name'=>$pb_name,'pb_status'=>1));
            $pb_phone   = $this->input->post('pb_phone');
            $pb_email   = $this->input->post('pb_email');
            $pb_address = $this->input->post('pb_address');
            $kec_id     = $this->input->post('kec_id');
            $dtKec      = $this->dbase->dataRow('wil_kec',array('kec_id'=>$kec_id));
            if (!$pb_id || !$dtPB){
                $json['msg'] = 'Invalid data Pusat Belajar';
            } elseif (strlen(trim($pb_name)) == 0){
                $json['msg'] = 'Masukkan nama Pusat Belajar';
            } elseif ($chName){
                $json['msg'] = 'Pusat belajar dengan nama ini sudah ada';
            } elseif (strlen(trim($pb_phone)) == 0){
                $json['msg'] = 'No. Telp belum diisi';
            } elseif (strlen(trim($pb_email)) == 0){
                $json['msg'] = 'Alamat email belum diisi';
            } elseif (strlen(trim($pb_address)) == 0){
                $json['msg'] = 'Alamat pusat belajar belum diisi';
            } elseif (!$kec_id || !$dtKec){
                $json['msg'] = 'Pilih Kecamatan';
            } else {
                $arr = array(
                    'pb_name' => $pb_name, 'pb_address' => $pb_address, 'pb_phone' => $pb_phone, 'pb_email' => $pb_email, 'kec_id' => $kec_id
                );
                $this->dbase->dataUpdate('pusat_belajar',array('pb_id'=>$pb_id),$arr);
                $json['t']  = 1;
                $json['msg']= 'Data pusat belajar berhasil dirubah';
            }

        }
        die(json_encode($json));
    }
    function bulk_pb_delete(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('pb_id');
        if ($this->session->userdata('lvl_pb') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($user_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($user_id as $val){
                $chk = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('pusat_belajar',array('pb_id'=>$val),array('pb_status'=>0));
                }
            }
            $json['t'] = 1;
            $json['data'] = $user_id;
            $json['msg'] = count($user_id).' data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_pb(){
        $json['t'] = 1; $json['msg'] = '';
        $user_id    = $this->input->post('id');
        $dtUser     = $this->dbase->dataRow('pusat_belajar',array('pb_id'=>$user_id));
        if ($this->session->userdata('lvl_pb') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid data';
        } else {
            $this->dbase->dataUpdate('pusat_belajar',array('pb_id'=>$user_id),array('pb_status'=>0));
            $json['t'] = 1;
            $json['msg'] = 'Pusat Belajar berhasil dihapus';
        }
        die(json_encode($json));
    }
    function surat_pemanggilan(){
        $dk_id  = $this->uri->segment(3);
        $dk_id  = substr($dk_id,0,strlen($dk_id)-1);
        $dk_id  = explode("-",$dk_id);
        if (count($dk_id) == 0){
            die('Pilih data');
        } else {
            $dtDK = array();
            $i = 0;
            foreach ($dk_id as $valDK){
                $chDk = $this->dbase->dataResult('diklat',array('dk_id'=>$valDK));
                if ($chDk){
                    $dtDK[$i]   = $chDk;
                    $i++;
                }
            }
            if (!$dtDK){
                die('Tidak ada data diklat');
            } else {
                $this->load->library('conv');
                $data['data'] = $dtDK;
                $this->load->view('diklat/surat_pemanggilan',$data);
            }
        }
    }
}
