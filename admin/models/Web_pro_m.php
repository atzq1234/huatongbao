<?php
class Web_pro_m extends CI_Model {
	var $table = 'web_pro';
	/* Page */
	function page($num, $offset, $like='',$where=''){
		if($like) {$this->db->like($like);}
		if($where) {$this->db->where_in('state', $where['in']);}
		$db = clone($this->db);
		$this->db->order_by('id desc');
		$query = $this->db->get($this->table,$num,$offset);
		$data = $query->result();
		$total = $db->count_all_results($this->table);
		return array('data'=>$data,'total'=>$total);
	}

	/* Get One */
	function getOne($select='',$id=''){
		$id = $id?$id:$this->input->post('id');
		if($id){
			$this->db->select($select);
			$query = $this->db->get_where($this->table, array('id' => $id));
			$data = $query->row();
			return $data;
		}
	}
	/* Add */
	function add(){
		$title = trim($this->input->post('title'));
		if($title){
			$data['class'] = $this->input->post('fid');
			$data['title'] = $title;
			$data['uname'] = $_SESSION['uinfo']['uname'];
			$data['ctime'] = trim($this->input->post('ctime'));
			$data['img'] = trim($this->input->post('img'));
			$data['key'] = trim($this->input->post('key'));
			$data['summary'] = $this->input->post('summary');
			$data['content'] = $this->input->post('content');
			
			return $this->db->insert($this->table,$data)?true:false;
		}
	}
	/* Update */
	function update(){
		$id = $this->input->post('id');
		if($id){
			$data['class'] = $this->input->post('fid');
			$data['title'] = trim($this->input->post('title'));
			$data['ctime'] = trim($this->input->post('ctime'));
			$data['img'] = trim($this->input->post('img'));
			$data['key'] = trim($this->input->post('key'));
			$data['summary'] = $this->input->post('summary');
			$data['content'] = $this->input->post('content');
			
			$this->db->where('id', $id);
			return $this->db->update($this->table, $data)?true:false;
		}
	}
	/* 更新图片 */
	function updateImg($data){
		$id = $this->input->post('id');
		if($id){
			$this->db->where('id', $id);
			return $this->db->update($this->table, $data)?true:false;
		}
	}
	/* Delete */
	function del(){
		$id = trim($this->input->post('id'));
		if($id){
			$this->db->trans_start();
			$arr = array_filter(explode(' ', $id));
			foreach($arr as $val){
				//删除图片
				$file = $this->getOne('upload',$val);
				$this->DelIMG($file->upload);
				//删除数据
				$this->db->delete($this->table,array('id'=>$val));
			}
			$this->db->trans_complete();
			return $this->db->trans_status();
		}else{return FALSE;}
	}
	//删除图片
	private function DelIMG($url=''){
		$path = '../upload/images/pro/';
		$arr = array_filter(explode(',', $url));
		foreach ($arr as $val){
			@unlink($path.$val);
		}
	}
	/* Audit */
	function audit(){
		$id = trim($this->input->post('id'));
		if($id){
			$this->db->trans_start();
			$arr = array_filter(explode(' ', $id));
			foreach($arr as $val){
				$data['state'] = $this->input->post('state');
				$this->db->update($this->table,$data,array('id'=>$val));
			}
			$this->db->trans_complete();
			return $this->db->trans_status();
		}else{return FALSE;}
	}
}