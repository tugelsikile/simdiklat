<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {
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
        } elseif ($this->session->userdata('lvl_diklat_kelas') < 1){
            $data['body']   = 'errors/403';
        } else {
            $data['diklat'] = $this->dbase->sqlResult("SELECT  d.*
                                            FROM      tb_diklat AS d
                                            LEFT JOIN tb_diklat_kelas AS dk ON dk.dk_id = d.dk_id AND d.dk_status = 1
                                            WHERE     dk.pb_id = '".$this->session->userdata('pb_id')."' AND dk.kel_status = 1
                                            GROUP BY  d.dk_id
                                            ORDER BY  d.dk_date_start DESC
                                            ");
            $data['body']   = 'kelas/home';
            $data['kelas']  = 'kelas';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_home(){
        $json['t']  = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $dk_id      = $this->input->post('dk_id');
        $dtUser     = $this->dbase->sqlResult("SELECT dk.*,Count(dkm.km_id) AS cnt,pb.pb_name
                                    FROM      tb_diklat_kelas AS dk
                                    LEFT JOIN tb_diklat_kelas_member AS dkm ON dkm.kel_id = dk.kel_id AND dkm.km_status = 1 AND dkm.km_type = 'peserta'
                                    LEFT JOIN tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                                    WHERE     dk.kel_name LIKE '%".$keyword."%' AND dk.kel_status = 1
                                              AND dk.dk_id = '".$dk_id."' AND dk.pb_id = '".$this->session->userdata('pb_id')."'
                                    GROUP BY  dk.kel_id
                                    ORDER BY  dk.kel_name ASC ");
        if ($this->session->userdata('lvl_diklat_kelas') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (!$dtUser){
            $json['msg'] = 'Data tidak ditemukan';
        } else {
            $this->load->library('conv');
            $data['data']   = $dtUser;
            $json['t']      = 1;
            $json['html']   = $this->load->view('kelas/data_table',$data,TRUE);
        }
        die(json_encode($json));
    }
    function cetak_pemanggilan(){
        $form_kelas     = $this->uri->segment(3);
        $form_kelas     = explode("-",$form_kelas);
        if ($this->session->userdata('lvl_diklat_kelas') < 1){
            die('Forbidden');
        } elseif (!$form_kelas){
            die('Pilih data lebih dulu');
        } elseif (count($form_kelas) == 0){
            die('Pilih data lebih dulu');
        } else {
            $kel_id = ''; $i = 0;
            foreach ($form_kelas as $val){
                if (strlen($val) > 0){
                    $kel_id .= $val;
                    $i++;
                    if ($i + 1 < count($form_kelas)){
                        $kel_id .= ',';
                    }
                }
            }
            //var_dump($form_kelas);
            $dtKelas    = $this->dbase->sqlResult("
                SELECT    dk.kel_name,d.dk_name,d.dk_date_start,d.dk_date_end,d.dk_check_in,d.dk_check_out,d.dk_nomor_surat,
                          d.dk_pembukaan,d.dk_penutupan,pb.pb_name,pb.pb_address,dk.kel_id,d.dk_nomor_surat,d.dk_titimangsa,
                          kec.`name` AS kec_name,kab.`name` AS kab_name,prov.`name` AS prov_name
                FROM      tb_diklat_kelas AS dk
                LEFT JOIN tb_diklat AS d ON dk.dk_id = d.dk_id
                LEFT JOIN tb_pusat_belajar AS pb ON dk.pb_id = pb.pb_id
                LEFT JOIN tb_wil_kec AS kec ON pb.kec_id = kec.kec_id
                LEFT JOIN tb_wil_kab AS kab ON kec.kab_id = kab.kab_id
                LEFT JOIN tb_wil_prov AS prov ON kab.prov_id = prov.prov_id
                WHERE     dk.kel_id IN (".$kel_id.")
            ");
            if (!$dtKelas){
                die('Tidak ada data');
            } else {
                $i = 0;
                foreach ($dtKelas as $valK){
                    $dtKelas[$i]    = $valK;
                    $dtKelas[$i]->info = $this->dbase->sqlResult("
                        SELECT      p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,p.pes_phone
                        FROM        tb_diklat_kelas_member AS dkm
                        LEFT JOIN   tb_peserta AS p ON dkm.pes_id = p.pes_id
                        WHERE       dkm.kel_id = '".$valK->kel_id."' AND dkm.km_status = 1 AND dkm.km_type = 'panitia' 
                                    AND ( dkm.km_name = 'Ketua Kelas' OR dkm.km_name = 'Bendahara' )
                    ");
                    $dtKelas[$i]->konf = $this->dbase->sqlResult("
                        SELECT      p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,p.pes_phone
                        FROM        tb_diklat_kelas_member AS dkm
                        LEFT JOIN   tb_peserta AS p ON dkm.pes_id = p.pes_id
                        WHERE       dkm.kel_id = '".$valK->kel_id."' AND dkm.km_status = 1 AND dkm.km_type = 'panitia' 
                                    AND ( dkm.km_name = 'Admin Kelas' OR dkm.km_name = 'Sekretaris' )
                    ");
                    $dtKelas[$i]->peserta = $this->dbase->sqlResult("
                        SELECT      p.pes_gelar_depan,p.pes_fullname,p.pes_gelar_blk,p.pes_phone,s.sch_name,kab.`name` AS kab_name,
                                    prov.`name` AS prov_name,p.pes_nopesukg
                        FROM        tb_diklat_kelas_member AS dkm
                        LEFT JOIN   tb_peserta AS p ON dkm.pes_id = p.pes_id
                        LEFT JOIN   tb_school AS s ON p.sch_id = s.sch_id
                        LEFT JOIN   tb_wil_kab AS kab ON s.kab_id = kab.kab_id
                        LEFT JOIN   tb_wil_prov AS prov ON kab.prov_id = prov.prov_id
                        WHERE       dkm.kel_id = '".$valK->kel_id."' AND dkm.km_status = 1 AND dkm.km_type = 'peserta'
                        ORDER BY    p.pes_fullname ASC
                    ");
                    $i++;
                }
                $this->load->library('conv');
                $data['data']   = $dtKelas;
                $this->load->view('kelas/cetak_pemanggilan',$data);
            }
        }
    }
}
