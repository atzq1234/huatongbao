<?php
class Inc{
	//Web 三层视图
	function adminView($APP, $url='', $data=''){
		/* View */
		if($APP->IsMobile) {
			$APP->load->view('../../themes/admin/'.$APP->config->config['admin_themes'].'/inc/top_mo',$data);
			$APP->load->view($url);
			$APP->load->view('../../themes/admin/'.$APP->config->config['admin_themes'].'/inc/bottom_mo');
		}else{
			$APP->load->view('../../themes/admin/'.$APP->config->config['admin_themes'].'/inc/top',$data);
			$APP->load->view($url);
			$APP->load->view('../../themes/admin/'.$APP->config->config['admin_themes'].'/inc/bottom');
		}
	}
	function getMenuAdmin($APP){
		$permArr = $_SESSION['AdminInfo']['permArr'];
		$Cname = $APP->router->class;
		$FID = $this->getFID($APP,$Cname);
		$M1 = 0; $M2 = 0;
		$one = $this->getMenus($APP, 0);
		foreach ($one as $val1) {
			if(isset($permArr[$val1->id])){
				$data[$M1] = array('id'=>$val1->id,'title'=>$val1->title,'url'=>$val1->url,'ico'=>$val1->ico);
				$two = $this->getMenus($APP, $val1->id);
				foreach ($two as $val2) {
					if(isset($permArr[$val2->id])){
						if($val1->id==$FID['FID1']){
							$data[$M1]['menus'][$M2] = array('id'=>$val2->id,'title'=>$val2->title,'url'=>$val2->url,'ico'=>$val2->ico);
							$three = $this->getMenus($APP, $val2->id);
							foreach ($three as $val3){
								if(isset($permArr[$val3->id])){
								$data[$M1]['menus'][$M2]['menus'][] = array('id'=>$val3->id,'title'=>$val3->title,'url'=>$val3->url,'ico'=>$val3->ico);
								}
							}
						}else{$data[$M1]['menus'] = FALSE;}
						$M2++;
					}
				}
				$M1++;
			}
		}
		//用户导航
		$actionHtml=$this->actionHtml($APP);
		$userHtml = $this->userHtml();
		
		return array('Date'=>$data,'FID'=>$FID,'Ctitle'=>$FID['Ctitle'],'userHtml'=>$userHtml,'actionHtml'=>$actionHtml);
	}
	/* GetMenu */
	private function getMenus($APP, $fid=''){
		$APP->load->model('sys_menus_m');
		return $APP->sys_menus_m->getMenus($fid);
	}
	private function getFID($APP,$Cname){
		$FID1=''; $FID2=''; $FID3='';
		$G1 = $APP->sys_menus_m->getID($Cname);
		$Title = $G1->title;
		if($G1->fid==0){
			$FID1 = $G1->id;
		}else{
			$G2 = $APP->sys_menus_m->getMenu($G1->fid);
			if($G2->fid==0){
				$FID1 = $G1->fid;
				$FID2 = $G1->id;
			}else{
				$FID1 = $G2->fid;
				$FID2 = $G1->fid;
				$FID3 = $G1->id;
			}
		}
		return array('Ctitle'=>$Title,'FID1'=>$FID1,'FID2'=>$FID2,'FID3'=>$FID3);
	}
	/* 动作菜单 */
	private function actionHtml($APP){
		$APP->load->model('sys_menus_action_m');
		$permArr = $_SESSION['AdminInfo']['permArr'];
		$action = $APP->sys_menus_action_m->getAll();
		$i = 1;
		$html = '';
		foreach($action as $val){
			if(intval($permArr[$APP->Cid])&intval($val->perm)){
				if($i == 1){
					$html .= '<li><a href="'.base_url().$APP->config->config['index_url'].$APP->uri->segment(1).'.html"><em class="'.$val->ico.'"></em>'.$val->name.'</a></li>';
				}else{
					$html .= '<li><a href="#" id="'.$val->ico.'"><em class="'.$val->ico.'"></em>'.$val->name.'</a></li>';
				}
			}
			$i++;
		}
		return $html;
	}
	/* User Html */
	private function userHtml(){
		@session_start();
		$UserLogin = @$_SESSION['AdminInfo']['logged_in'];
		if($UserLogin == TRUE){
			$_SESSION['AdminInfo']['ltime'] = time()+1800;
			$html = '<a href="#"><b>'.$_SESSION['AdminInfo']['uname'].'</b></a>&nbsp;['.$_SESSION['AdminInfo']['department'].'-'.$_SESSION['AdminInfo']['name'].']&nbsp;|&nbsp;<a href="'.base_url('index_c/loginOut.html').'"><b>注销</b></a>';
		}else{
			$html = '<a href="'.base_url('../').'"><b>请登陆</b></a>';
		}
		return $html;
	}
	
	//分页
	function page($APP,$arr=''){
		$APP->load->library('pagination');
		$APP->load->model($arr['model']);
		//默认值
		if(!array_key_exists('page',$arr)){$arr['page']=15;}
		if(!array_key_exists('name',$arr)){$arr['name']='page';}
		if(!array_key_exists('where',$arr)){$arr['where']='';}
		/* Config */
		$config['page_query_string'] = TRUE;
		$config['first_tag_open'] = '<span>';
		$config ['first_link'] = '首页';
		$config['first_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span>';
		$config['prev_link'] = '上一页';
		$config['prev_tag_close'] = '</span>';
		$config['next_tag_open'] = '<span>';
		$config['next_link'] = '下一页';
		$config['next_tag_close'] = '</span>';
		$config['last_tag_open'] = '<span>';
		$config ['last_link'] = '末页';
		$config['last_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="page_cur">';
		$config['cur_tag_close'] = '</span>';
		$config['num_tag_open'] = '<span>';
		$config['num_tag_close'] = '</span>';
		/* Search */
		$get_url = '?';
		if(isset($_GET['search'])){
			$like = $APP->input->get();
			unset($like['per_page']);
			/* Url */
			foreach($like as $key=>$val){
				if($val==''){
					unset($like[$key]);
				}else{
					$get_url .= $key.'='.$val.'&';
				}
			}
			/* Remove Search and Null */
			unset($like['search']);
		}else{$like = array();}
		/* Data */
		$per_page = $APP->input->get('per_page');
		$d = $APP->$arr['model']->$arr['name']($arr['page'],$per_page,$like,$arr['where']);
		$config['total_rows'] = $d['total'];
		$config['per_page'] = $arr['page'];
		$config['base_url'] = base_url().$APP->config->config['index_url'].$arr['url'].$get_url;
		$APP->pagination->initialize($config);
		/* Other */
		$data['list'] = $d['data'];
		$data['page'] = $APP->pagination->create_links();
		$data['total'] = '共<b> '.$config['total_rows'].' </b>条';
		$data['key'] = $like;
		$data['get_url'] = $get_url.'per_page='.$per_page;
		
		return $data;
	}
}