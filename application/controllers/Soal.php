<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soal extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    public function index()	{
        redirect(base_url('soal/paket'));
    }
    function paket(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_soal') < 1){
            $data['body'] = 'errors/403';
        } else {
            $data['diklat'] = $this->dbase->dataResult('diklat',array('dk_status'=>1),'dk_id,dk_name');
            $data['body']   = 'soal/paket';
            $data['soal']   = 'soal';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_paket(){
        $json['t']  = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_soal') < 1){
            $json['msg'] = 'Forbidden';
        } else {
            $dk_id      = $this->input->post('dk_id');
            $sql_dk     = "";
            if (strlen($dk_id)){ $sql_dk = " AND d.dk_id = '".$dk_id."' "; }
            $keyword    = $this->input->post('keyword');
            $dtPaket    = $this->dbase->sqlResult("
                SELECT      dsp.*,Count(ds.soal_id) AS cnt,d.dk_name
                FROM        tb_diklat_soal_paket AS dsp
                LEFT JOIN   tb_diklat_soal AS ds ON ds.pk_id = dsp.pk_id AND ds.soal_status = 1
                LEFT JOIN   tb_diklat AS d ON dsp.dk_id = d.dk_id
                WHERE       (
                            dsp.pk_name LIKE '%" . $keyword . "%' OR
                            d.dk_name LIKE '%".$keyword."%'
                            ) 
                            AND dsp.pk_status = 1 ".$sql_dk." 
                GROUP BY    dsp.pk_id
                ORDER BY    dsp.pk_name ASC
            ");
            if (!$dtPaket){
                $json['msg'] = 'Tidak ada data';
            } else {
                $json['t']      = 1;
                $data['data']   = $dtPaket;
                $json['html']   = $this->load->view('soal/data_paket',$data,true);
            }
        }
        die(json_encode($json));
    }
    function add_paket(){
        if ($this->session->userdata('lvl_soal') < 2){
            die('Forbidden');
        } else {
            $data['diklat']  = $this->dbase->dataResult('diklat',array('dk_status'=>1),'dk_id,dk_name');
            $this->load->view('soal/add_paket',$data);
        }
    }
    function add_paket_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_soal') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            $dk_id      = $this->input->post('dk_id');
            $dtDK       = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id),'dk_id');
            $pk_name    = $this->input->post('pk_name');
            $chPK       = $this->dbase->dataRow('diklat_soal_paket',array('pk_name'=>$pk_name,'pk_status'=>1,'dk_id'=>$dk_id),'pk_id');
            if (strlen(trim($pk_name)) == 0){
                $json['msg'] = 'Nama / Kode paket soal belum diisi';
            } elseif ($chPK) {
                $json['msg'] = 'Nama / Kode paket soal sudah ada';
            } elseif (!$dk_id || !$dtDK){
                $json['msg'] = 'Nama diklat tidak valid';
            } else {
                $pk_id = $this->dbase->dataInsert('diklat_soal_paket',array('pk_name'=>$pk_name,'dk_id'=>$dk_id));
                if (!$pk_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t'] = 1;
                    $data['data']   = $this->dbase->sqlResult("
                        SELECT      dsp.*,Count(ds.soal_id) AS cnt,d.dk_name
                        FROM        tb_diklat_soal_paket AS dsp
                        LEFT JOIN   tb_diklat_soal AS ds ON ds.pk_id = dsp.pk_id AND ds.soal_status = 1
                        LEFT JOIN   tb_diklat AS d ON dsp.dk_id = d.dk_id
                        WHERE       dsp.pk_id = '".$pk_id."'
                    ");
                    $json['html']   = $this->load->view('soal/data_paket',$data,true);
                    $json['msg']    = 'Paket soal berhasil ditambahkan';
                }
            }
        }
        die(json_encode($json));
    }
    function edit_paket(){
        $pk_id = $this->uri->segment(3);
        if ($this->session->userdata('lvl_soal') < 3){
            die('Forbidden');
        } else {
            $dtPK   = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            if (!$dtPK || !$pk_id){
                die('Invalid data paket soal');
            } else {
                $data['diklat']  = $this->dbase->dataResult('diklat',array('dk_status'=>1),'dk_id,dk_name');
                $data['data']    = $dtPK;
                $this->load->view('soal/edit_paket',$data);
            }
        }
    }
    function edit_paket_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_soal') < 3){
            $json['msg'] = 'Forbidden';
        } else {
            $pk_id      = $this->input->post('pk_id');
            $pk_name    = $this->input->post('pk_name');
            $dk_id      = $this->input->post('dk_id');
            $dtDK       = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id),'dk_id');
            $dtPK       = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            $chkPK      = $this->dbase->dataRow('diklat_soal_paket',array('pk_id !='=>$pk_id,'pk_name'=>$pk_name,'pk_status'=>1,'dk_id'=>$dk_id));
            if (!$pk_id || !$dtPK){
                $json['msg'] = 'Invalid paket soal';
            } elseif ($chkPK) {
                $json['msg'] = 'Nama / Kode paket soal sudah ada';
            } elseif (!$dk_id || !$dtDK){
                $json['msg'] = 'Pilih diklat';
            } else {
                $this->dbase->dataUpdate('diklat_soal_paket',array('pk_id'=>$pk_id),array('pk_name'=>$pk_name,'dk_id'=>$dk_id));
                $json['msg']    = 'Data berhasil dirubah';
                $json['t']      = 1;
            }
        }
        die(json_encode($json));
    }
    function bulk_delete_paket(){
        $json['t'] = 0; $json['msg'] = '';
        $pk_id      = $this->input->post('pk_id');
        if ($this->session->userdata('lvl_soal') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$pk_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($pk_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($pk_id as $val){
                $this->dbase->dataUpdate('diklat_soal_paket',array('pk_id'=>$val),array('pk_status'=>0));
            }
            $json['t'] = 1;
            $json['data'] = $pk_id;
            $json['msg'] = count($pk_id).' berhasil dihapus';
        }
        die(json_encode($json));
    }
    function delete_paket(){
        $json['t'] = 0; $json['msg'] = '';
        $pk_id      = $this->input->post('id');
        if ($this->session->userdata('lvl_soal') < 4){
            $json['msg'] = 'Forbidden';
        } elseif (!$pk_id){
            $json['msg'] = 'Invalid data paket soal';
        } else {
            $dtPk = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            if (!$dtPk){
                $json['msg'] = 'Tidak ada paket soal';
            } else {
                $this->dbase->dataUpdate('diklat_soal_paket',array('pk_id'=>$pk_id),array('pk_status'=>0));
                $json['msg'] = 'Paket soal berhasil dihapus';
                $json['t'] = 1;
            }
        }
        die(json_encode($json));
    }
    function data_soal(){
        if (!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('lvl_soal') < 1){
            $data['body'] = 'errors/403';
        } else {
            $pk_id          = $this->uri->segment(3);
            $dtPaket        = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            if ($dtPaket){
                $data['pk_id']  = $pk_id;
                $data['dk_id']  = $dtPaket->dk_id;
                $data['paket']   = $this->dbase->dataResult('diklat_soal_paket',array('pk_status'=>1,'dk_id'=>$dtPaket->dk_id),'pk_id,pk_name');
            } else {
                $data['pk_id']  = '';
                $data['dk_id']  = '';
                $data['paket']   = $this->dbase->dataResult('diklat_soal_paket',array('pk_status'=>1,'dk_id'=>$data['dk_id']),'pk_id,pk_name');
            }
            $data['diklat']  = $this->dbase->dataResult('diklat',array('dk_status'=>1),'dk_id,dk_name');
            $data['body']   = 'soal/data_soal';
            $data['soal']   = 'soal';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_data_soal(){
        $json['t'] = 0; $json['msg'] = 'Tidak ada data';
        $pk_id      = $this->input->post('pk_id');
        $keyword    = $this->input->post('keyword');
        $dtPK       = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
        if ($this->session->userdata('lvl_soal') < 1){
            $json['msg'] = 'Forbidden';
        } elseif (strlen($pk_id) > 0 && !$dtPK){
            $json['msg'] = 'Invalid paket soal';
        } else {
            $sql_pk = "";
            if (strlen($pk_id) > 0 && $dtPK){ $sql_pk = " AND ds.pk_id = '".$pk_id."' "; }
            $dtSoal = $this->dbase->sqlResult("
                SELECT      ds.*,dsp.pk_name
                FROM        tb_diklat_soal AS ds
                LEFT JOIN   tb_diklat_soal_paket AS dsp ON ds.pk_id = dsp.pk_id
                WHERE       ds.soal_status = 1
                            AND ( dsp.pk_name LIKE '%".$keyword."%' OR ds.soal_nomor LIKE '%".$keyword."%' ) 
                            ".$sql_pk."
                GROUP BY    ds.soal_id
                ORDER BY    ds.soal_nomor ASC
            ");
            if (!$dtSoal){
                $json['msg'] = 'Tidak ada data';
            } else {
                $data['data']   = $dtSoal;
                $json['t']      = 1;
                $json['html']   = $this->load->view('soal/data_data_soal',$data,true);
            }
        }
        die(json_encode($json));
    }
    function dk_select(){
        $json['t'] = 0; $json['msg'] = '';
        $dk_id  = $this->input->post('dk_id');
        $dtDK   = $this->dbase->dataRow('diklat',array('dk_id'=>$dk_id),'dk_id');
        if (!$dk_id || !$dtDK){
            $json['msg'] = 'Invalid diklat';
        } else {
            $dtPaket    = $this->dbase->dataResult('diklat_soal_paket',array('dk_id'=>$dk_id,'pk_status'=>1),'pk_id,pk_name');
            if (!$dtPaket){
                $json['msg'] = 'Tidak ada Paket Soal';
            } else {
                $json['t']  = 1;
                $json['data'] = $dtPaket;
            }
        }
        die(json_encode($json));
    }
    function add_soal(){
        if ($this->session->userdata('lvl_soal') < 2){
            die('Forbidden');
        } else {
            $pk_id  = $this->uri->segment(3);
            $dtPK   = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            if (!$pk_id || !$dtPK){
                die('Invalid paket soal');
            } else {
                $data['paket']  = $dtPK;
                $data['diklat'] = $this->dbase->dataRow('diklat',array('dk_id'=>$dtPK->dk_id),'dk_name')->dk_name;
                $data['urut']   = $this->dbase->dataRow('diklat_soal',array('soal_status'=>1,'pk_id'=>$pk_id),'COUNT(soal_id) AS cnt')->cnt + 1;
                $this->load->view('soal/add_soal',$data);
            }
        }
    }
    function add_soal_submit(){
        $json['t'] = 0; $json['msg'] = '';
        if ($this->session->userdata('lvl_soal') < 2){
            $json['msg'] = 'Forbidden';
        } else {
            $pk_id      = $this->input->post('pk_id');
            $dtPK       = $this->dbase->dataRow('diklat_soal_paket',array('pk_id'=>$pk_id));
            $soal_nomor = (int)$this->input->post('soal_nomor');
            $soal_jawab = $this->input->post('soal_jawab');
            $chkUrut    = $this->dbase->dataRow('diklat_soal',array('pk_id'=>$pk_id,'soal_nomor'=>$soal_nomor,'soal_status'=>1));
            if (!$pk_id || !$dtPK){
                $json['msg'] = 'Invalid paket soal';
            } elseif (!$soal_nomor){
                $json['msg'] = 'Nomor soal belum diisi';
            } elseif ($chkUrut){
                $json['msg'] = 'Nomor soal sudah ada';
            } elseif (strlen($soal_jawab) == 0){
                $json['msg'] = 'Jawaban belum dipilih';
            } else {
                $soal_id = $this->dbase->dataInsert('diklat_soal',array('pk_id'=>$pk_id,'soal_nomor'=>$soal_nomor,'soal_jawab'=>$soal_jawab));
                if (!$soal_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t'] = 1;
                    $data['data']   = $this->dbase->sqlResult("
                        SELECT      ds.*,dsp.pk_name
                        FROM        tb_diklat_soal AS ds
                        LEFT JOIN   tb_diklat_soal_paket AS dsp ON ds.pk_id = dsp.pk_id
                        WHERE       ds.soal_id = '".$soal_id."'
                        GROUP BY    ds.soal_id
                    ");
                    $json['html']   = $this->load->view('soal/data_data_soal',$data,TRUE);
                }
            }
        }
        die(json_encode($json));
    }
}
