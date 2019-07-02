<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wilayah extends CI_Controller {
    function __construct(){
        parent::__construct();
    }
    function prov_selected(){
        $json['t']  = 0; $json['msg'] = '';
        $prov_id    = $this->input->post('prov_id');
        $dtProv     = $this->dbase->dataRow('wil_prov',array('prov_id'=>$prov_id));
        if (!$prov_id || !$dtProv){
            $json['msg'] = 'Invalid data provinsi';
        } else {
            $dtKab  = $this->dbase->dataResult('wil_kab',array('prov_id'=>$prov_id),'*','name','asc');
            if (!$dtKab){
                $json['msg'] = 'Tidak ada data';
            } else {
                $json['t'] = 1;
                $json['data'] = $dtKab;
            }
        }
        die(json_encode($json));
    }
    function kab_selected(){
        $json['t']  = 0; $json['msg'] = '';
        $prov_id    = $this->input->post('kab_id');
        $dtProv     = $this->dbase->dataRow('wil_kab',array('kab_id'=>$prov_id));
        if (!$prov_id || !$dtProv){
            $json['msg'] = 'Invalid data kabupaten';
        } else {
            $dtKab  = $this->dbase->dataResult('wil_kec',array('kab_id'=>$prov_id),'*','name','asc');
            if (!$dtKab){
                $json['msg'] = 'Tidak ada data';
            } else {
                $json['t'] = 1;
                $json['data'] = $dtKab;
            }
        }
        die(json_encode($json));
    }
}
