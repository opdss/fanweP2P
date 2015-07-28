<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class EventAction extends CommonAction{
	
	
	public function index()
	{		
		$map['publish_wait'] = 0;
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');			
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	public function add()
	{
		$cate_tree = M("EventCate")->findAll();
		$this->assign("cate_tree",$cate_tree);
		$this->assign("new_sort", M(MODULE_NAME)->max("sort")+1);
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		$this->display();
	}
	
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$vo['event_begin_time'] = $vo['event_begin_time']!=0?to_date($vo['event_begin_time']):'';
		$vo['event_end_time'] = $vo['event_end_time']!=0?to_date($vo['event_end_time']):'';
		$vo['submit_begin_time'] = $vo['submit_begin_time']!=0?to_date($vo['submit_begin_time']):'';
		$vo['submit_end_time'] = $vo['submit_end_time']!=0?to_date($vo['submit_end_time']):'';
		$this->assign ( 'vo', $vo );
		
		$supplier_info = M("Supplier")->where("id=".$vo['supplier_id'])->find();
		$this->assign("supplier_info",$supplier_info);
		
		$cate_tree = M("EventCate")->findAll();
		$this->assign("cate_tree",$cate_tree);
		
		//输出团购城市
		$city_list = M("DealCity")->where('is_delete = 0')->findAll();
		$city_list = D("DealCity")->toFormatTree($city_list,'name');
		$this->assign("city_list",$city_list);
		
		$field_list = M("EventField")->where("event_id=".$id)->order("sort asc")->findAll();
		$this->assign("field_list",$field_list);
		
		$this->display();
	}
	
	public function insert()
	{
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("活动名称不能为空");
		}		
		// 更新数据
		$data['event_begin_time'] = trim($data['event_begin_time'])==''?0:to_timespan($data['event_begin_time']);
		$data['event_end_time'] = trim($data['event_end_time'])==''?0:to_timespan($data['event_end_time']);
		$data['submit_begin_time'] = trim($data['submit_begin_time'])==''?0:to_timespan($data['submit_begin_time']);
		$data['submit_end_time'] = trim($data['submit_end_time'])==''?0:to_timespan($data['submit_end_time']);
		
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			
			$area_ids = $_REQUEST['area_id'];
			foreach($area_ids as $area_id)
			{
				$area_data['area_id'] = $area_id;
				$area_data['event_id'] = $list;
				M("EventAreaLink")->add($area_data);
			}
			
			$location_ids = $_REQUEST['location_id'];
			foreach($location_ids as $location_id)
			{
				$link_data = array();
				$link_data['location_id'] = $location_id;
				$link_data['event_id'] = $list;
				M("EventLocationLink")->add($link_data);
				recount_supplier_data_count($location_id,"event");
			}
			
			foreach($_REQUEST['field_id'] as $k=>$field_id)
			{
				$event_field = array();
				$event_field['event_id'] = $list;
				$event_field['field_show_name'] = $_REQUEST['field_show_name'][$k];
				$event_field['field_type'] = $_REQUEST['field_type'][$k];
				$event_field['value_scope'] = $_REQUEST['value_scope'][$k];
				$event_field['sort'] = $k;
				M("EventField")->add($event_field);
			}
			M("EventCate")->where("id=".$data['cate_id'])->setField("count",M("Event")->where("cate_id=".$data['cate_id'])->count());
			syn_event_match($list);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	
	public function update()
	{
		
		B('FilterString');
		$data = M(MODULE_NAME)->create ();

		$data['publish_wait'] = 0;
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error("活动名称不能为空");
		}	
		$data['event_begin_time'] = trim($data['event_begin_time'])==''?0:to_timespan($data['event_begin_time']);
		$data['event_end_time'] = trim($data['event_end_time'])==''?0:to_timespan($data['event_end_time']);
		$data['submit_begin_time'] = trim($data['submit_begin_time'])==''?0:to_timespan($data['submit_begin_time']);
		$data['submit_end_time'] = trim($data['submit_end_time'])==''?0:to_timespan($data['submit_end_time']);
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			
			M("EventAreaLink")->where("event_id=".$data['id'])->delete();
			$area_ids = $_REQUEST['area_id'];
			foreach($area_ids as $area_id)
			{
				$area_data['area_id'] = $area_id;
				$area_data['event_id'] = $data['id'];
				M("EventAreaLink")->add($area_data);
			}		

			M("EventLocationLink")->where("event_id=".$data['id'])->delete();
			$location_ids = $_REQUEST['location_id'];
			foreach($location_ids as $location_id)
			{
				$link_data = array();
				$link_data['location_id'] = $location_id;
				$link_data['event_id'] = $data['id'];
				M("EventLocationLink")->add($link_data);
				recount_supplier_data_count($location_id,"event");
			}
			
			$submit_ids = array(0);
			foreach($_REQUEST['field_id'] as $k=>$field_id)
			{
				$submit_ids[] = intval($field_id);
				$event_field = M("EventField")->getById(intval($field_id));
				if($event_field)
				{
					$event_field['event_id'] = $data['id'];
					$event_field['field_show_name'] = $_REQUEST['field_show_name'][$k];
					$event_field['field_type'] = $_REQUEST['field_type'][$k];
					$event_field['value_scope'] = $_REQUEST['value_scope'][$k];
					$event_field['sort'] = $k;
					M("EventField")->save($event_field);
				}
				else
				{		
					$event_field = array();		
					$event_field['event_id'] = $data['id'];
					$event_field['field_show_name'] = $_REQUEST['field_show_name'][$k];
					$event_field['field_type'] = $_REQUEST['field_type'][$k];
					$event_field['value_scope'] = $_REQUEST['value_scope'][$k];
					$event_field['sort'] = $k;
					$submit_ids[] = M("EventField")->add($event_field);
				}
			}
			M("EventField")->where(array("event_id"=>$data['id'],"id"=>array("not in",$submit_ids)))->delete();
			M("EventSubmitField")->where(array("field_id"=>array("not in",$submit_ids),"event_id"=>$data['id']))->delete();
			
			M("EventCate")->where("id=".$data['cate_id'])->setField("count",M("Event")->where("cate_id=".$data['cate_id'])->count());
			syn_event_match($data['id']);
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	
	public function area_list()
	{
		$id =  intval($_REQUEST['id']); //活动ID
		$area_list = M("Area")->where("city_id=".intval($_REQUEST['city_id']))->findAll();
		foreach($area_list as $k=>$v)
		{
			if(M("EventAreaLink")->where("area_id=".$v['id']." and event_id = ".$id)->count())
			{
				$area_list[$k]['checked'] = true;
			}	
		}
		$this->assign("area_list",$area_list);
		$this->display();		
	}

	
	function add_submit_item()
	{
		$event_id = intval($_REQUEST['event_id']);
		$this->assign("event_id",$event_id);
		$this->display();
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M(MODULE_NAME)->where("id=".$id)->getField('name');
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function foreverdelete() {
	//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();
				if ($list!==false) {
					$locations = M("EventLocationLink")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->findAll();					
					M("EventAreaLink")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("EventLocationLink")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("EventDealCateTypeLink")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("EventField")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("EventSubmit")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					M("EventSubmitField")->where(array ('event_id' => array ('in', explode ( ',', $id ) ) ))->delete();
					foreach($locations as $location)
					{
						recount_supplier_data_count($location['location_id'],"event");
					}
					foreach($rel_data as $data)
					{
						M("EventCate")->where("id=".$data['cate_id'])->setField("count",M("Event")->where("cate_id=".$data['cate_id'])->count());
					}					
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}	
	}
	
	function load_supplier_location()
	{
		$supplier_id = intval($_REQUEST['supplier_id']);
		$event_id = intval($_REQUEST['event_id']);
		
		$supplier_location_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier_location where supplier_id = ".$supplier_id);
		foreach($supplier_location_list as $k=>$v)
		{
			if($event_id>0)
			$supplier_location_list[$k]['checked'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."event_location_link where location_id = ".$v['id']." and event_id = ".$event_id);
			else 
			$supplier_location_list[$k]['checked'] = true;
			
		}
		$this->assign("supplier_location_list",$supplier_location_list);
		
		if($supplier_location_list)
		$result['status'] = 1;
		else
		$result['status'] = 0;
		$result['html'] = $this->fetch();
		$this->ajaxReturn($result['html'],"",$result['status']);
	}
	
	
	
	
	public function publish()
	{
		$map['publish_wait'] = 1;
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
	
	public function toogle_status()
	{
		
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$field = $_REQUEST['field'];
		$info = $id."_".$field;
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField($field);  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField($field,$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		$locations = M("EventLocationLink")->where(array ('event_id' =>$id ))->findAll();	
		foreach($locations as $location)
		{
			recount_supplier_data_count($location['location_id'],"event");
		}
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
}
?>