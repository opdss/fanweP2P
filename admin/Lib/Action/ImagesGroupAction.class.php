<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +-----------------------------------------
class ImagesGroupAction extends CommonAction
{
	public function insert() {
		B('FilterString');
		$data = M("ImagesGroup")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		
		if(!check_empty($data['name']))
		{
			$this->error(L("POINTNAME_EMPTY_TIP"));
		}
		
		if(M(MODULE_NAME)->where("`name`='".$data['name']."'")->count()>0){
			$this->error(L("HAD_POINTGROUP"));
		}	

		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			clear_auto_cache("store_image_group_list");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );
		
		$this->display ();
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit"));
		
		if(!check_empty($data['name']))
		{
			$this->error(L("POINTNAME_EMPTY_TIP"));
		}

		if(M(MODULE_NAME)->where("id<>".$data['id']." and `name`='".$data['name']."'")->count()>0){
			$this->error(L("HAD_POINTGROUP"));
		}
		
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		$log_info = $data['name'];
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			clear_auto_cache("store_image_group_list");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function foreverdelete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST['id'];
		if(!empty($id))
		{
			$name=$this->getActionName();
			$model = D($name);
			$pk = $model->getPk ();
			$ids =  explode ( ',', $id );
			$condition = array ($pk => array ('in', $ids) );
			$link_condition = array ("images_group_id" => array ('in', $ids ) );
			
			if(false !== $model->where ( $condition )->delete ())
			{
				M("ImagesGroupLink")->where($link_condition)->delete();				
				save_log($ids.l("FOREVER_DELETE_SUCCESS"),1);
				clear_auto_cache("store_image_group_list");
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			}
			else
			{
				save_log($ids.l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		}
		else
		{
			$this->error (l("INVALID_OPERATION"),$ajax);
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
		clear_auto_cache("store_image_group_list");
		$this->success(l("SORT_SUCCESS"),1);
	}	
}
?>