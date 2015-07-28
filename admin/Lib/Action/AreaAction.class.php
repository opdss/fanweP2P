<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class AreaAction extends CommonAction{
	public function index()
	{
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');			
		}
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	public function area_list()
	{
		$id =  intval($_REQUEST['id']);
		$area_list = M("Area")->where("city_id=".intval($_REQUEST['city_id'])." and pid = 0")->findAll();
		$this->assign("vo",M("Area")->getById($id));
		$this->assign("area_list",$area_list);
		$this->display();
	}

	public function add()
	{
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);		

		$this->assign("new_sort", M("Area")->max("sort")+1);
		$this->display();
	}
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		$this->display ();
	}
	public function foreverdelete() {
	//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				if(M("Area")->where(array ('pid' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("SUB_AREA_EXIST"),$ajax);
				}
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					M("DealAreaLink")->where(array ('area_id' => array ('in', explode ( ',', $id ) )))->delete();
					save_log($info.l("DELETE_SUCCESS"),1);
					clear_auto_cache("deal_quan_ids");
								clear_auto_cache("byouhui_filter_nav_cache");
			clear_auto_cache("fyouhui_filter_nav_cache");
			clear_auto_cache("tuan_filter_nav_cache");
			clear_auto_cache("ytuan_filter_nav_cache");
			clear_auto_cache("store_filter_nav_cache");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
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
		if(!check_empty($data['name']))
		{
			$this->error(L("AREA_NAME_EMPTY_TIP"));
		}	
		
		
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			clear_auto_cache("deal_quan_ids");
						clear_auto_cache("byouhui_filter_nav_cache");
			clear_auto_cache("fyouhui_filter_nav_cache");
			clear_auto_cache("tuan_filter_nav_cache");
			clear_auto_cache("ytuan_filter_nav_cache");
			clear_auto_cache("store_filter_nav_cache");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log($log_info.L("INSERT_FAILED").$DBerr,0);
			$this->error(L("INSERT_FAILED").$DBerr);
		}
	}	
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
	
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error(L("AREA_NAME_EMPTY_TIP"));
		}	
	// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			clear_auto_cache("deal_quan_ids");
						clear_auto_cache("byouhui_filter_nav_cache");
			clear_auto_cache("fyouhui_filter_nav_cache");
			clear_auto_cache("tuan_filter_nav_cache");
			clear_auto_cache("ytuan_filter_nav_cache");
			clear_auto_cache("store_filter_nav_cache");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log($log_info.L("UPDATE_FAILED").$DBerr,0);
			$this->error(L("UPDATE_FAILED").$DBerr,0);
		}
	}
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
					clear_auto_cache("byouhui_filter_nav_cache");
			clear_auto_cache("fyouhui_filter_nav_cache");
			clear_auto_cache("tuan_filter_nav_cache");
			clear_auto_cache("ytuan_filter_nav_cache");
			clear_auto_cache("store_filter_nav_cache");
		$this->success(l("SORT_SUCCESS"),1);
	}	
	
}
?>