<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Log extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
	
	private $cols="hostname,name,addr,city,prov,org,net,iplan,ipwan,status,lastping";
	private $table="custom_masterdata";
	
	public function in(){
		$retval=array("code"=>"404","ttl"=>"Gagal","msgs"=>"User/Password salah");
		$usr=trim($this->input->post("usr"));
		$pwd=trim($this->input->post("pwd"));
		$this->db->where('userid',$usr);
		$this->db->where('userpwd',md5($pwd));
		$acc=$this->db->get("custom_users")->result_array();
		if(count($acc)>0){
			$token=md5(uniqid(rand(), true)).md5(uniqid(rand(), true));
			$this->session->set_userdata('user_token',$token);
			$retval=array("code"=>"200","ttl"=>"OK","msgs"=>$token);
		}
		
		echo json_encode($retval);
	}
	public function status($s=''){
		$user=$this->session->userdata('user_token');
		$auth=$this->input->get_request_header('X-token', TRUE);
		if(isset($user)){
			if($auth==$user){
				$where=array();
				if($s!='') { $where=array("status"=>$s); }
				$data=$this->db->select($this->cols)->where($where)->get($this->table)->result_array();
				$retval=array('code'=>"200",'ttl'=>"OK",'msgs'=>$data);
				echo json_encode($retval);
			}else{
				$retval=array('code'=>"403",'ttl'=>"Invalid session",'msgs'=>"Invalid token");
				echo json_encode($retval);
			}
		}else{
			$retval=array('code'=>"403",'ttl'=>"Session closed",'msgs'=>"Please login");
			echo json_encode($retval);
		}
	}
	
}
