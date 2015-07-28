<?php
// +----------------------------------------------------------------------
// | Fanwe 方维众筹商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
require_once APP_ROOT_PATH."/app/lib/crowd_func.php";
class CrowdAction extends CommonAction{
	public function online_index()
	{	
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['time_status'])==1)
		{
			$map['_string'] = '(begin_time > '.get_gmtime().')';			
		}
		
		if(intval($_REQUEST['time_status'])==2)
		{
			$map['_string'] = "(begin_time < '".get_gmtime()."') and ((end_time > '".get_gmtime()."') or (end_time = 0))";
		}
		
		if(intval($_REQUEST['time_status'])==3)
		{
			$map['_string'] = '(end_time < '.get_gmtime().') and (end_time <> 0)';	
		}
		if($_REQUEST['type']=='NULL'){
			unset($_REQUEST['type']);
		}
		if($_REQUEST['type']!=NULL){
			$map['type']=intval($_REQUEST['type']);
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		$map['is_effect'] = 1;		
		$map['is_delete'] = 0;		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("CrowdCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function submit_index()
	{
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		
		if($_REQUEST['type']=='NULL'){
			unset($_REQUEST['type']);
		}
		
		if($_REQUEST['type']!=NULL){
 			$map['type']=intval($_REQUEST['type']);
		}
		$map['is_effect'] = array("in",array(0,2));
		$map['is_delete'] = 0;		

		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("CrowdCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function delete_index()
	{
		if(trim($_REQUEST['name'])!='')
		{
			$map['name'] = array('like','%'.trim($_REQUEST['name']).'%');
		}
		
		if(intval($_REQUEST['cate_id'])>0)
		{
			$map['cate_id'] = intval($_REQUEST['cate_id']);
		}
		
		if(intval($_REQUEST['user_id'])>0)
		{
			$map['user_id'] = intval($_REQUEST['user_id']);
		}
		

		$map['is_delete'] = 1;		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$cate_list = M("CrowdCate")->findAll();
		$this->assign("cate_list",$cate_list);
		$this->display ();
	}
	
	public function add()
	{
		$cate_list = M("CrowdCate")->findAll();
		$cate_list = D("CrowdCate")->toFormatTree($cate_list,'name');
		$this->assign("cate_list",$cate_list);
		
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		$this->assign("region_lv2",$region_lv2);
		//项目等级
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_level order by level ASC");
		$this->assign("user_level",$user_level);
		
		$this->assign("new_sort", M("Crowd")->max("sort")+1);
		$this->display();
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		if($vo['user_id']==0)$vo['user_id']  = '';
		$vo['begin_time'] = $vo['begin_time']!=0?to_date($vo['begin_time']):'';
		$vo['end_time'] = $vo['end_time']!=0?to_date($vo['end_time']):'';
 		$this->assign ( 'vo', $vo );
		
		$cate_list = M("CrowdCate")->findAll();
		$cate_list = D("CrowdCate")->toFormatTree($cate_list,'name');
		$this->assign("cate_list",$cate_list);
		
		$region_pid = 0;
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2 order by py asc");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['name'] == $vo['province'])
			{
				$region_lv2[$k]['selected'] = 1;
				$region_pid = $region_lv2[$k]['id'];
				break;
			}
		}
		$this->assign("region_lv2",$region_lv2);
		
		
		if($region_pid>0)
		{
			$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".$region_pid." order by py asc");  //三级地址
			foreach($region_lv3 as $k=>$v)
			{
				if($v['name'] == $vo['city'])
				{
					$region_lv3[$k]['selected'] = 1;
					break;
				}
			}
			$this->assign("region_lv3",$region_lv3);
		}
		
		$qa_list = M("CrowdFaq")->where("deal_id=".$vo['id'])->order("sort asc")->findAll();
		$this->assign("faq_list",$qa_list);
		
		$user_level = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_level order by level ASC");
		$this->assign("user_level",$user_level);
		
		$this->display ();
	}
	
	
	
	
	public function insert() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M(MODULE_NAME)->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}	
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
			
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
 		$data['create_time'] = get_gmtime();
		$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		
		// 更新数据
		$log_info = $data['name'];
		
		$list=M(MODULE_NAME)->add($data);

		if (false !== $list) {
			//成功提示
			
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Crowd")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $list;
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("CrowdFaq")->add($qa);
				}
			}
			
			syn_deal($list);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}	
	public function insert_investor(){
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
 		$data = M(MODULE_NAME)->create ();
 		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add_investor"));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}	
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
		$history_info=deal_investor_info($data['history'],'history');
   		if($history_info['status']){
			$data['history']=serialize(array_filter($history_info['data']));
		}else{
			$this->error($history_info['info']);
		}
		if($data['stock']){
			$stock_info=deal_investor_info($data['stock'],'stock');
			if($stock_info['status']){
				$data['stock']=serialize(array_filter($stock_info['data']));
			}else{
	 			$this->error($stock_info['info']);
			}
		}
	 		
 		$unstock_info=deal_investor_info($data['unstock'],'unstock');
		if($unstock_info['status']){
			$data['unstock']=serialize(array_filter($unstock_info['data']));
		}else{
			$this->error($unstock_info['info']);
		}
 		$plan_info=deal_investor_info($data['plan'],'plan');
		if($plan_info['status']){
			$data['plan']=serialize(array_filter($plan_info['data']));
		}else{
			$this->error($plan_info['info']);
		}
   		$attach_info=deal_investor_info($data['attach'],'attach');
 		if($attach_info['status']){
			$data['attach']=serialize(array_filter($attach_info['data']));
		}else{
			$this->error($attach_info['info']);
		}
		
 		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		$data['pay_end_time'] = trim($data['pay_end_time'])==''?0:to_timespan($data['pay_end_time']);
		$data['business_create_time'] = trim($data['business_create_time'])==''?0:to_timespan($data['business_create_time']);
		
		$data['create_time'] = get_gmtime();
		$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		
		// 更新数据
		$log_info = $data['name'];
		
		$list=M(MODULE_NAME)->add($data);

		if (false !== $list) {
			//成功提示
			
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Crowd")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $list;
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("CrowdFaq")->add($qa);
				}
			}
			
			syn_deal($list);
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		
		$this->deal_update(intval($data['id']));
 		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
		
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		$data['create_time'] = get_gmtime();
		$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		else
		{
			$data['source_vedio'] = "";
		}

		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Crowd")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			//成功提示			
			M("CrowdFaq")->where("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $data['id'];
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("CrowdFaq")->add($qa);
				}
			}
			M("Crowd")->where("id=".$data['id'])->setField("deal_extra_cache","");
			M("CrowdLog")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			M("CrowdComment")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			syn_deal($data['id']);
			syn_deal_status($data['id']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}
	public function update_all(){
		$re=$GLOBALS['db']->getAll("select * from  ".DB_PREFIX."deal where  is_effect = 1 and is_delete=0 ");
		foreach($re as $k=>$v){
			syn_deal($v['id']);
			syn_deal_status($v['id']);
		}
		ajax_return(array('status'=>1));
	}
	public function update_investor() {
		B('FilterString');
 		$data = M(MODULE_NAME)->create();
		
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("name");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit_investor",array("id"=>$data['id'])));
		if(!check_empty($data['name']))
		{
			$this->error("请输入名称");
		}	
		if(intval($data['cate_id'])==0)
		{
			$this->error("请选择分类");
		}
		if(floatval($data['limit_price'])<=0){
			$this->error("目标金额要大于0");
		}
		$this->deal_update(intval($data['id']));
		
    	$history_info=deal_investor_info($data['history'],'history');
   		if($history_info['status']){
			$data['history']=serialize(array_filter($history_info['data']));
		}else{
			$this->error($history_info['info']);
		}
  		$stock_info=deal_investor_info($data['stock'],'stock');
		if($stock_info['status']){
			$data['stock']=serialize(array_filter($stock_info['data']));
		}else{
 			$this->error($stock_info['info']);
		}
 		$unstock_info=deal_investor_info($data['unstock'],'unstock');
		if($unstock_info['status']){
			$data['unstock']=serialize(array_filter($unstock_info['data']));
		}else{
			$this->error($unstock_info['info']);
		}
 		$plan_info=deal_investor_info($data['plan'],'plan');
		if($plan_info['status']){
			$data['plan']=serialize(array_filter($plan_info['data']));
		}else{
			$this->error($plan_info['info']);
		}
   		$attach_info=deal_investor_info($data['attach'],'attach');
 		if($attach_info['status']){
			$data['attach']=serialize(array_filter($attach_info['data']));
		}else{
			$this->error($attach_info['info']);
		}
		
		$data['begin_time'] = trim($data['begin_time'])==''?0:to_timespan($data['begin_time']);
		$data['end_time'] = trim($data['end_time'])==''?0:to_timespan($data['end_time']);
		$data['pay_end_time'] = trim($data['pay_end_time'])==''?0:to_timespan($data['pay_end_time']);
		
		$data['business_create_time'] = trim($data['business_create_time'])==''?0:to_timespan($data['business_create_time']);
		$data['create_time'] = get_gmtime();
		$data['user_name'] = M("User")->where("id=".intval($data['user_id']))->getField("user_name");
		if(!$data['user_name'] )$data['user_name'] ="";
		if($data['vedio']!="")
		{
			require_once APP_ROOT_PATH."system/utils/vedio.php";
			$vedio = fetch_vedio_url($data['vedio']);		
			if($vedio!="")
			{
				$data['source_vedio'] =  $vedio;
			}
			else
			{
				$this->error("非法的视频地址");
			}
		}
		else
		{
			$data['source_vedio'] = "";
		}

		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			if($data['is_effect']==1&&$data['user_id']>0)
			{
				$deal_count = M("Crowd")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
				M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);
			}
			//成功提示			
			M("CrowdFaq")->where("deal_id=".$data['id'])->delete();
			foreach($_REQUEST['question'] as $k=>$v)
			{
				if(trim($v)!=""||trim($_REQUEST['answer'][$k])!='')
				{
					$qa = array();
					$qa['deal_id'] = $data['id'];
					$qa['question'] = trim($v);
					$qa['answer'] = trim($_REQUEST['answer'][$k]);
					$qa['sort'] = intval($k)+1;
					M("CrowdFaq")->add($qa);
				}
			}
			M("Crowd")->where("id=".$data['id'])->setField("deal_extra_cache","");
			M("CrowdLog")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			M("CrowdComment")->where("deal_id=".$data['id'])->setField("deal_info_cache","");
			//syn_deal($data['id']);
			//syn_deal_status($data['id']);
			save_log($log_info.L("UPDATE_SUCCESS"),1);
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
		$log_info = M("Crowd")->where("id=".$id)->getField("name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("Crowd")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function delete() {
		//彻底删除指定记录
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
				$list = M(MODULE_NAME)->where ( $condition )->setField("is_delete",1);		
						
				if ($list!==false) {
					foreach($rel_data as $data)
					{						
						$deal_count = M("Crowd")->where("user_id=".$data['user_id']." and is_effect = 1 and is_delete = 0")->count();
						M("User")->where("id=".$data['user_id'])->setField("build_count",$deal_count);						
					}
					save_log($info."成功移到回收站",1);
					$this->success ("成功移到回收站",$ajax);
				} else {
					save_log($info."移到回收站出错",0);					
					$this->error ("移到回收站出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function restore() {
		//彻底删除指定记录
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
				$list = M(MODULE_NAME)->where ( $condition )->setField("is_delete",0);				
				if ($list!==false) {
					save_log($info."恢复成功",1);
					$this->success ("恢复成功",$ajax);
				} else {
					save_log($info."恢复出错",0);
					$this->error ("恢复出错",$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$link_condition = array ('deal_id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();				
				if ($list!==false) {					
					M("CrowdFaq")->where($link_condition)->delete();
					M("CrowdComment")->where($link_condition)->delete();
					M("CrowdFocusLog")->where($link_condition)->delete();
					M("CrowdItem")->where($link_condition)->delete();
					M("CrowdItemImage")->where($link_condition)->delete();
					M("CrowdOrder")->where($link_condition)->delete();
					M("CrowdPayLog")->where($link_condition)->delete();
					M("CrowdSupportLog")->where($link_condition)->delete();
					M("CrowdVisitLog")->where($link_condition)->delete();
					M("CrowdLog")->where($link_condition)->delete();
					M("UserCrowdNotify")->where($link_condition)->delete();
					M("CrowdNotify")->where($link_condition)->delete();
					
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function add_faq()
	{
		$this->display();
	}
	
	public function deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
			if (method_exists ( $this, '_filter' )) {
				$this->_filter ( $map );
			}
			$name=$this->getActionName();
			$model = D ("CrowdItem");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function add_deal_item()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		$this->display();
	}
	
	
	public function insert_deal_item() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M("CrowdItem")->create ();

		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add_deal_item",array("id"=>$data['deal_id'])));
		if(!check_empty($data['price']))
		{
			$this->error("请输入价格");
		}	
		
		// 更新数据
		
		$list=M("CrowdItem")->add($data);
		$log_info =  "项目ID".$data['deal_id'].":".format_price($data['price']);	
		if (false !== $list) {
			//成功提示
			
			$imgs = $_REQUEST['image'];
			foreach($imgs as $k=>$v)
			{
				if($v!='')
				{
					$img_data['deal_id'] = $data['deal_id'];
					$img_data['deal_item_id'] = $list;
					$img_data['image'] = $v;
					M("CrowdItemImage")->add($img_data);
				}
			}
			M("Crowd")->where("id=".$data['deal_id'])->setField("deal_extra_cache","");
			save_log($log_info.L("INSERT_SUCCESS"),1);
			syn_deal($data['deal_id']);
			syn_deal_status($data['deal_id']);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit_deal_item()
	{
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;		
		$vo = M("CrowdItem")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		//输出图片集
		$img_list = M("CrowdItemImage")->where("deal_item_id=".$vo['id'])->findAll();
		$imgs = array();
		foreach($img_list as $k=>$v)
		{
			$imgs[$k] = $v['image']; 
		}
		$this->assign("img_list",$imgs);
		
		$this->display();
	}
	
	public function update_deal_item() {
		B('FilterString');
		$ajax = intval($_REQUEST['ajax']);
		$data = M("CrowdItem")->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit_deal_item",array("id"=>$data['id'])));
		if(!check_empty($data['price']))
		{
			$this->error("请输入价格");
		}	
		
		// 更新数据
		$this->deal_update(intval($data['deal_id']));
		$list=M("CrowdItem")->save($data);
		$log_info =  "项目ID".$data['deal_id'].":".format_price($data['price']);	
		if (false !== $list) {
			if($data['virtual_person']>0){
				
			}
			//成功提示
			//开始处理图片
			M("CrowdItemImage")->where("deal_item_id=".$data['id'])->delete();
			$imgs=array($_REQUEST['img0'],$_REQUEST['img1'],$_REQUEST['img2'],$_REQUEST['img3']);
			//$imgs = $_REQUEST['image'];
			foreach($imgs as $k=>$v)
			{
				if($v!='')
				{
					$img_data['deal_item_id'] = $data['id'];
					$img_data['deal_id'] = $data['deal_id'];
					$img_data['image'] = $v;
					M("CrowdItemImage")->add($img_data);
				}
			}
			M("Crowd")->where("id=".$data['deal_id'])->setField("deal_extra_cache","");
			M("CrowdLog")->where("deal_id=".$data['deal_id'])->setField("deal_info_cache","");
			//end 处理图片
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			syn_deal($data['deal_id']);
			syn_deal_status($data['deal_id']);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"));
		}
	}
	
	public function del_deal_item()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("CrowdItem")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = format_price($data['price']);	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id.":".$info;
				$list = M("CrowdItem")->where ( $condition )->delete();				
				if ($list!==false) {					
					M("Crowd")->where("id=".$deal_id)->setField("deal_extra_cache","");
					syn_deal($deal_id);
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	
	
	//pay_log 放款日志
	public function pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		//当项目佣金比例大于0时
		if($deal_info['pay_radio']> 0)
		{
			$deal_info['commission'] = $deal_info['support_amount']*$deal_info['pay_radio'] + $deal_info['delivery_fee_amount'];
			$deal_info['pay_amount'] =$deal_info['support_amount'] -$deal_info['commission'];
		}
		//当项目佣金比例等于0时
		else{
			$deal_info['commission'] = $deal_info['support_amount'] - $deal_info['pay_amount'] + $deal_info['delivery_fee_amount'];
		}
	
	//	$deal_info['commission'] = $deal_info['support_amount'] - $deal_info['pay_amount'] + $deal_info['delivery_fee_amount'];
		
	
		
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		

			$model = D ("CrowdPayLog");
			$paid_money = $model->where($map)->sum("money");
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			$this->assign("remain_money",$remain_money);
			$this->assign("paid_money",$paid_money);
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function add_pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		$deal_info['commission'] = $deal_info['support_amount'] - $deal_info['pay_amount'] + $deal_info['delivery_fee_amount'];
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
	
			$model = D ("CrowdPayLog");
			$paid_money = $model->where($map)->sum("money");
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			$this->assign("paid_money",$paid_money);
			$this->assign("remain_money",$remain_money);
		}
		
		$this->display();
	}
	
	public function save_pay_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];		
		
			$model = D ("CrowdPayLog");
			$paid_money = $model->where($map)->sum("money");
			$remain_money = $deal_info['pay_amount'] - $paid_money;
			
			$money = doubleval($_REQUEST['money']);
			$log_info = strim($_REQUEST['log_info']);
			
			if($money<=0||$money>$remain_money)
			{
				$this->error("金额出错");
			}
			else
			{
				if($deal_info['user_id']>0)
				{
					require_once APP_ROOT_PATH."system/libs/user.php";
					if($log_info=="")$log_info = $deal_info['name']."项目筹款发放";
					modify_account(array("money"=>$money),$deal_info['user_id'],$log_info);
					$log['deal_id'] = $deal_info['id'];
					$log['money'] = $money;
					$log['create_time'] = get_gmtime();
					$log['log_info'] = $log_info;
					$model->add($log);
					save_log($log_info.$money,1);
					send_pay_success($log_info);
					
					$this->success("筹款发放成功");
				}
				else
				{
					$this->error("管理员创建项目，无需发放筹款");
				}
			}
			
		}
		else
		{
			$this->error("项目不存在");
		}
	}
	
	public function del_pay_log()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("CrowdPayLog")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = format_price($data['money']);	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id.":".$info;
				$list = M("CrowdPayLog")->where ( $condition )->delete();				
				if ($list!==false) {					
					
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	//项目日志
	public function deal_log()
	{
		$deal_id = intval($_REQUEST['id']);
		$deal_info = M("Crowd")->getById($deal_id);
		$this->assign("deal_info",$deal_info);
		
		if($deal_info)
		{
			$map['deal_id'] = $deal_info['id'];	
			$model = D ("CrowdLog");
			if (! empty ( $model )) {
				$this->_list ( $model, $map );
			}
		}
		
		$this->display();
	}
	
	public function del_deal_log()
	{
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );		
				$condition_log = array ('log_id' => array ('in', explode ( ',', $id ) ) );				
				$rel_data = M("CrowdLog")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$deal_id = $data['deal_id'];
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$info = "项目ID".$deal_id."的日志:".$info;
				$list = M("CrowdLog")->where ( $condition )->delete();	
							
				if ($list!==false) {		
					$GLOBALS['db']->query("update ".DB_PREFIX."deal set log_count = log_count - ".intval($list)." where id = ".$deal_id);			
					M("CrowdComment")->where($condition_log)->delete();
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function batch_refund()
	{
		$page = intval($_REQUEST['page']);

		$page=($page<=0)?1:$page;

		$page_size = 100;
		$deal_id = intval($_REQUEST['id']);
		
		$limit = (($page-1)*$page_size).",".$page_size;
		
		$deal_info = M("Crowd")->where("id=".$deal_id." and is_delete = 0 and is_effect = 1 and is_success = 0 and end_time <>0 and end_time <".get_gmtime())->find();
		if(!$deal_info)
		{
			$this->error("该项目不能批量退款");
		}
		else
		{
			require_once APP_ROOT_PATH."system/libs/user.php";
			$refund_order_list = M("CrowdOrder")->where("deal_id=".$deal_id." and is_refund = 0 and order_status = 3")->limit($limit)->findAll();
			foreach($refund_order_list as $k=>$v)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set is_refund = 1 where id = ".$v['id']);
				if($GLOBALS['db']->affected_rows()>0)
				{
					modify_account(array("money"=>$v['total_price']),$v['user_id'],$v['deal_name']."项目失败退款");					
				}
			}
			$remain = M("CrowdOrder")->where("deal_id=".$deal_id." and is_refund = 0 and order_status = 3")->count();
			if($remain==0)
			{
				$jump_url = u("Crowd/online_index");
				$this->assign("jumpUrl",$jump_url);
				M("Crowd")->where("id=".$deal_info['id'])->setField("deal_extra_cache","");
				M("CrowdLog")->where("deal_id=".$deal_info['id'])->setField("deal_info_cache","");
				$this->success("批量退款成功");
			}
			else
			{
				$jump_url = u("Crowd/batch_refund",array("id"=>$deal_id,"page"=>$page+1));
				$this->assign("jumpUrl",$jump_url);
				$this->success("批量退款中，请勿刷新页面，剩余".$remain."条订单未退款");
			}
			
		}
		
	}
	function deal_update($deal_id){
		$deal=$GLOBALS['db']->getRow("select * from  ".DB_PREFIX."deal where id=$deal_id");
 		$now_time=get_gmtime();
 		if(($deal['begin_time']<$now_time||$deal['end_time']<$now_time)&&($deal['invote_money']>0||$deal['virtual_price']>0||$deal['support_amount']>0)){
 			// $this->error("项目已经开始无法编辑");
		} 
	}
	 
}
?>