<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }
    public function index()	{
		if (!$this->session->userdata('login')){
		    redirect(base_url('login'));
        }
        $data['dashboard']= 'dashboard';
        $data['body']   = 'dashboard';
		if ($this->input->is_ajax_request()){
		    $this->load->view($data['body'],$data);
        } else {
		    $this->load->view('home',$data);
        }
	}
}
