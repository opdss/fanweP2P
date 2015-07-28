<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class DealShowAction extends CommonAction{
	public function index()
	{
		if(trim($_REQUEST['deal_name'])!='')
		{
			$condition['deal_name'] = array('like','%'.trim($_REQUEST['deal_name']).'%');			
		}
		//$condition['is_delete'] = 0;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign('vo', $vo);
		$this->display ();
	}
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					clear_auto_cache("get_help_cache");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_SUCCESS"),0);
					$this->error (l("DELETE_SUCCESS"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['deal_name']))
		{
			$this->error("贷款名称不能为空");
		}	
		if(!check_empty($data['user_name']))
		{
			$this->error("名字不能为空");
		}
		if(!check_empty($data['school']))
		{
			$this->error("学校不能为空");
		}
		if(floatval($data['amount'])<=0)
		{
			$this->error("请输入正确的借款金额");
		}			
		// 更新数据
		$log_info = $data['deal_name'];
		$data['deal_name'] = strim($data['deal_name']);
		$data['user_name'] = strim($data['user_name']);
		$data['school'] = strim($data['school']);
		$data['sort'] = intval($data['sort']);
		$data["create_time"] = strtotime($data["create_time"]);		
		$data["img"] = strim($data["img"]);		

		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();	
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("deal_name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['deal_name']))
		{
			$this->error("贷款名称不能为空");
		}	
		if(!check_empty($data['user_name']))
		{
			$this->error("名字不能为空");
		}
		if(!check_empty($data['school']))
		{
			$this->error("学校不能为空");
		}
		if(floatval($data['amount'])<=0)
		{
			$this->error("请输入正确的借款金额");
		}		
		// 更新数据
		$log_info = $data['deal_name'];
		$data['deal_name'] = strim($data['deal_name']);
		$data['user_name'] = strim($data['user_name']);
		$data['school'] = strim($data['school']);
		$data['sort'] = intval($data['sort']);
		$data["create_time"] = strtotime($data["create_time"]);		
		$data["img"] = strim($data["img"]);	
		if(strim($data["img"])=="")
		{
			unset($data["img"]);
		}
		$list=M(MODULE_NAME)->save($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			clear_auto_cache("get_help_cache");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("Article")->where("id=".$id)->getField("title");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Article")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		clear_auto_cache("get_help_cache");
		$this->success(l("SORT_SUCCESS"),1);
	}
	public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("title");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		clear_auto_cache("get_help_cache");
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
}
?>