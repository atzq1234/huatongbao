<?php
class Sys_db_restore extends MY_Controller {
	/* Index */
	public function index(){
		$this->load->library('inc');
		$this->load->helper('my');
		$this->load->helper('file');
		$this->load->model('sys_db_m');
		
		$data['js'] = array('system/sys_db_restore.js',);
		$data['Menus'] = $this->inc->getMenuAdmin($this);
		$data['file'] = get_dir_file_info($this->config->config['backup'],false);
		$this->inc->adminView($this,'system/db/restore/index',$data);
	}
	/* Download */
	public function down(){
		$this->load->helper('download');
		/* Read File */
		$fname = $this->input->get('name');
		$data = file_get_contents($this->config->config['backup'].'/'.$fname);

		force_download($fname, $data); 
	}
	/* Delete */
	public function delData(){
		
		$file = array_filter(explode(',', $this->input->post('id')));
		foreach($file as $val){
			if(unlink($this->config->config['backup'].'/'.$val)){
				$data = true;
			}else{
				$data = false;
				break;
			}
		}
		echo $data;
	}
	/* Import */
	public function imp(){
		/* Config */
		$data['file'] = $this->config->config['backup'].'/'.$this->input->post('file');
		$this->load->view('system/db/restore/imp',$data);
	}
	public function impData(){
		$this->load->helper('file');
		
		$file = $this->input->post('file');
		$data = '{"status":"n"}';
		/* Remove Notes */
		$content = read_file($file);
		$content = preg_replace("/#\n# TABLE(.*)\s#\n/i","",$content);
		$sqls = array_filter(explode(";\n",$content));
		foreach($sqls as $sql){
			$sql = trim($sql);
			if(!empty($sql)){
				if(@$this->db->query($sql)){
					$data = '{"status":"y"}';
				}else{
					break;
				}
			}
		}
		echo $data;
	}
}