<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    function sch_selected(){
        $json['t']  = 0; $json['msg'] = '';
        $sch_id     = $this->input->post('sch_id');
        $dtSch      = $this->dbase->dataRow('school',array('sch_id'=>$sch_id));
        if (!$sch_id || !$dtSch){
            $json['msg'] = 'Invalid data sekolah';
        } else {
            $kab    = $this->dbase->dataRow('wil_kab',array('kab_id'=>$dtSch->kab_id));
            $json['sch_negeri']     = $dtSch->sch_negeri;
            $json['sch_nss']        = $dtSch->sch_nss;
            $json['sch_npsn']       = $dtSch->sch_npsn;
            $json['sch_address']    = $dtSch->sch_address;
            $json['sch_phone']      = $dtSch->sch_phone;
            $json['sch_fax']        = $dtSch->sch_fax;
            $json['sch_email']      = $dtSch->sch_email;
            $json['kab_id']         = $dtSch->kab_id;
            $json['prov_id']        = $kab->prov_id;
            $json['dtkab']          = $this->dbase->dataResult('wil_kab',array('prov_id'=>$kab->prov_id));
            $json['t']              = 1;
        }
        die(json_encode($json));
    }
}
