<?php
class Web_book extends MY_Controller {
	/* Index */
	public function index(){
		$this->load->library('inc');
		$this->load->helper('my');
		$data = $this->inc->Page($this,array('url'=>'web_book/index.html','model'=>'web_book_m'));
		$data['js'] = array('web/web_book.js');
		$data['Menus'] = $this->inc->getMenuAdmin($this);
		if($this->IsMobile) {
			$this->inc->adminView($this,'web/book/index_mo',$data);
		}else {
			$this->inc->adminView($this,'web/book/index',$data);
		}
	}
	/* Search */
	public function search(){
		$this->load->view('web/book/sea');
	}
	/* Edit */
	public function edit(){
		$this->load->model('web_book_m');
		$data['edit'] = $this->web_book_m->getOne();
		$this->load->view('web/book/edit',$data);
	}
	public function editData(){
		$this->load->model('web_book_m');
		echo $this->web_book_m->update()?'{"status":"y"}':'{"status":"n"}';
	}
	/* Delete */
	public function delData(){
		$this->load->model('web_book_m');
		echo $this->web_book_m->del();
	}
	/* View */
	public function show(){
		$this->load->model('web_book_m');
		$data['show'] = $this->web_book_m->getOne();
		$this->load->view('web/book/show',$data);
	}
}
?>