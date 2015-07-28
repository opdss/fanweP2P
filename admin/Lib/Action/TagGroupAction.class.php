<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: awfigq <awfigq@qq.com>
// +----------------------------------------------------------------------
/**
 +------------------------------------------------------------------------------
 +------------------------------------------------------------------------------
 */
class TagGroupAction extends CommonAction
{
	
	public function add()
	{
		$deal_cates = M("DealCate")->where("is_delete=0 and is_effect=1")->findAll();
		$this->assign("deal_cates",$deal_cates);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M("TagGroup")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		
		if(!check_empty($data['name']))
		{
			$this->error(L("TAGNAME_EMPTY_TIP"));
		}
		

		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			//成功提示
			foreach($_REQUEST['cate_id'] as $cate_id)
			{
				if(intval($cate_id)>0)
				{
					$link_data=  array();
					$link_data['category_id'] = intval($cate_id);
					$link_data['tag_group_id'] = $list;
					M("TagGroupLink")->add($link_data);
				}
			}			
			clear_auto_cache("store_filter_nav_cache");
			save_log($log_info.L("INSERT_SUCCESS"),1);
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
		
		$deal_cates = M("DealCate")->where("is_delete=0 and is_effect=1")->findAll();
		foreach($deal_cates as $k=>$v)
		{
			$deal_cates[$k]['checked'] = M("TagGroupLink")->where("category_id=".$v['id']." and tag_group_id = ".$vo['id'])->count();
		}
		$this->assign("deal_cates",$deal_cates);
		
		$this->display ();
	}
	
	public function update()
	{
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		
		if(!check_empty($data['name']))
		{
			$this->error(L("TAGNAME_EMPTY_TIP"));
		}

		
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		$log_info = $data['name'];
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示			
			M("TagGroupLink")->where("tag_group_id=".$data['id'])->delete();
			foreach($_REQUEST['cate_id'] as $cate_id)
			{
				if(intval($cate_id)>0)
				{
					$link_data=  array();
					$link_data['category_id'] = intval($cate_id);
					$link_data['tag_group_id'] = $data['id'];
					M("TagGroupLink")->add($link_data);
				}
			}	
			clear_auto_cache("store_filter_nav_cache");
			save_log($log_info.L("UPDATE_SUCCESS"),1);
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
			$ids = explode ( ',', $id );
			$condition = array ($pk => array ('in',  $ids) );
			$link_condition = array ("tag_group_id" => array ('in', $ids ) );
			
			if(false !== $model->where ( $condition )->delete ())
			{
				M("TagGroupLink")->where($link_condition)->delete();	

				M("SupplierLocationDpTagResult")->where(array ("group_id" => array ('in', $ids ) ))->delete();
				M("SupplierTag")->where(array ("group_id" => array ('in', $ids ) ))->delete();
				M("SupplierTagGroupPreset")->where(array ("group_id" => array ('in', $ids ) ))->delete();
				M("TagUserVote")->where(array ("group_id" => array ('in', $ids ) ))->delete();
				
				
				save_log($ids.l("FOREVER_DELETE_SUCCESS"),1);
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
		$this->success(l("SORT_SUCCESS"),1);
	}	
}
?>