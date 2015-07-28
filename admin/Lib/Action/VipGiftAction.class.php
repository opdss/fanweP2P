<?php

class VipGiftAction extends CommonAction {

    public function index() {
    	$condition['is_delete'] = 0;
		//$condition['pid'] = 0;
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
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
		$list = $this->get("list");
		
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			//$v['level'] = -1;
			$v['name'] = $v['name'];
			$result[$row] = $v;
			$row++;

		}
		//dump($result);exit;
		$this->assign("list",$result);
		
		$this->display ();
		return;
    }
    
    public function gift_trash()
	{
		$condition['is_delete'] = 1;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$this->assign("newsort",M(MODULE_NAME)->where("is_delete=0")->max("sort")+1);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		if(!check_empty($data['name']))
		{
			$this->error("礼品名称不能为空！");
		}	

		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("cache_vip_gift");
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function edit() {		
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );		
		
		$this->display ();
	}

	//状态 变更
    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M(MODULE_NAME)->where("id=".$id)->getField("name");
		$c_is_effect = M(MODULE_NAME)->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M(MODULE_NAME)->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		rm_auto_cache("cache_vip_gift");
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
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
		rm_auto_cache("cache_vip_gift");
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function update() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		$log_info = M(MODULE_NAME)->where("id=".intval($data['id']))->getField("title");
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("cache_vip_gift");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				
				if(M("GiftRecord")->where(array ('gift_value' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("存在关联礼品 ID"),$ajax);
				}
				
				
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					rm_auto_cache("cache_vip_gift");
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	public function restore() {
		//恢复指定记录
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
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					save_log($info.l("RESTORE_SUCCESS"),1);
					rm_auto_cache("cache_vip_gift");
					$this->success (l("RESTORE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("RESTORE_FAILED"),0);
					$this->error (l("RESTORE_FAILED"),$ajax);
				
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

				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();

				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("cache_vip_gift");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	//获得礼品
	public function vip_gift_record(){
		
		$sql=" select gr.* from ".DB_PREFIX."gift_record gr " .
			 "left join ".DB_PREFIX."deal_load_repay dlr on dlr.load_id = gr.load_id  " .
			 " where 1=1 AND ((gr.gift_type = 2 AND dlr.true_reward_money >0 ) OR (gr.gift_type <> 2)) ";
			 
		if(trim($_REQUEST['user_name'])!='')
		{
			$sqluid  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sqluid);
			$sql .= "and gr.user_id in ($ids)";
			
		}
		
		if(trim($_REQUEST['name'])!='')
		{	
			$sqldid  ="select group_concat(id) from ".DB_PREFIX."deal where name like '%".trim($_REQUEST['name'])."%'";
			$idss = $GLOBALS['db']->getOne($sqldid);
			$sql .= "and gr.deal_id in ($idss)";
		}
		
		
		if(isset($_REQUEST['gift_type']))
		{
			if(intval($_REQUEST['gift_type'])==-1){
			}elseif(intval($_REQUEST['gift_type'])==1){
				$sql .= "and gr.gift_type = 1 ";
			}elseif(intval($_REQUEST['gift_type'])==2){
				$sql .= "and gr.gift_type = 2 ";
			}elseif(intval($_REQUEST['gift_type'])==3){
				$sql .= "and gr.gift_type = 3 ";
			}elseif(intval($_REQUEST['gift_type'])==4){
				$sql .= "and gr.gift_type = 4 ";
			}
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'gift_record.release_date'] = array('egt',$begin_time);
				$sql .= "and gr.release_date > '$begin_time' ";
			}
			else{
				$map[DB_PREFIX.'gift_record.release_date']= array("between",array($begin_time,$end_time));
				$sql .= "and gr.release_date between ('$begin_time','$end_time') ";
			}
				
		}
		$sql .= " GROUP BY gr.load_id ";
		
		$model = D();
		$voList = $this->_Sql_list($model, $sql, false);
		$this->display();	
		
	}
	
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
	
		//定义条件
		if(trim($_REQUEST['user_name'])!='')
		{
			$sql  ="select group_concat(id) from ".DB_PREFIX."user where user_name like '%".trim($_REQUEST['user_name'])."%'";
			$ids = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'gift_record.user_id'] = array("in",$ids);
		}
		
		if(trim($_REQUEST['name'])!='')
		{	
			$sql  ="select group_concat(id) from ".DB_PREFIX."deal where name like '%".trim($_REQUEST['name'])."%'";
			$idss = $GLOBALS['db']->getOne($sql);
			$map[DB_PREFIX.'gift_record.deal_id'] = array("in",$idss);
		}
		
		
		if(isset($_REQUEST['gift_type']))
		{
			if(intval($_REQUEST['gift_type'])==-1){
			}elseif(intval($_REQUEST['gift_type'])==1){
				$map[DB_PREFIX.'gift_record.gift_type'] = array("eq",1);
			}elseif(intval($_REQUEST['gift_type'])==2){
				$map[DB_PREFIX.'gift_record.gift_type'] = array("eq",2);
			}elseif(intval($_REQUEST['gift_type'])==3){
				$map[DB_PREFIX.'gift_record.gift_type'] = array("eq",3);
			}elseif(intval($_REQUEST['gift_type'])==4){
				$map[DB_PREFIX.'gift_record.gift_type'] = array("eq",4);
			}
		}
		
		$begin_time  = trim($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time']);
		if($begin_time !='' || $end_time !=''){
			if($end_time==0)
			{
				$map[DB_PREFIX.'gift_record.release_date'] = array('egt',$begin_time);
			}
			else
				$map[DB_PREFIX.'gift_record.release_date']= array("between",array($begin_time,$end_time));
				
		}
	
		$list = M("GiftRecord")
		->where($map)
		->limit($limit)->findAll();
	
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			$gift_value = array('id'=>'""','deal_id'=>'""','user_id'=>'""','gift_type'=>'""','gift_value'=>'""','status'=>'""','release_date'=>'""');
			if($page == 1)
				$content = iconv("utf-8","gbk","编号,标题,用户名,收益类型,收益值,发放状态,发放时间");
			if($page==1)
				$content = $content . "\n";
	
			foreach($list as $k=>$v)
			{
				
				if($list[$k]['gift_type']==1){
					$list[$k]['gift_value'] =  $v['gift_value']." 元";
				}elseif($list[$k]['gift_type']==2){
					if($list[$k]['reward_money']!=0){
						$list[$k]['gift_value'] = $v['gift_value']."% (金额".$list[$k]['reward_money']." 元)";
					}else{
						$reward_money=M("DealLoadRepay")->where(" load_id=".$list[$k]['load_id'])->getField("reward_money");
						if($reward_money!=0){
							$list[$k]['gift_value'] =  $list[$k]['gift_value']."% (金额".$reward_money." 元)";
						}else{
							$list[$k]['gift_value'] =  $list[$k]['gift_value']."%";
						}
				
					}
				
				}elseif($list[$k]['gift_type']==3){
					$list[$k]['gift_value'] = $v['gift_value']." 积分";
				}elseif($list[$k]['gift_type']==4){
					$name=M("VipGift")->where(" id=".$list[$k]['gift_value'])->getField("name");
					$list[$k]['gift_value'] =  $name;
				}
				
				if($list[$k]['gift_type']==1){
					$list[$k]['gift_type'] =  "红包";
				}elseif($list[$k]['gift_type']==2){
					$list[$k]['gift_type'] = "收益率";
				}elseif($list[$k]['gift_type']==3){
					$list[$k]['gift_type'] = "积分";
				}elseif($list[$k]['gift_type']==4){
					$list[$k]['gift_type'] = "礼品";
				}
				if($list[$k]['status']==1)
					$list[$k]['status'] =  "已发放";
				else
					$list[$k]['status'] =  "未发放";
				
				$deal_name=M("Deal")->where(" id=".$list[$k]['deal_id'])->getField("name");
				
				$gift_value = array();
				$gift_value['id'] = iconv('utf-8','gbk','"' . $v['id'] . '"');
				$gift_value['deal_id'] = iconv('utf-8','gbk','"' . $deal_name . '"');
				$gift_value['user_id'] = iconv('utf-8','gbk','"' . get_user_name_reals($list[$k]['user_id']) . '"');
				$gift_value['gift_type'] = iconv('utf-8','gbk','"' . $list[$k]['gift_type'] . '"');
				$gift_value['gift_value'] = iconv('utf-8','gbk','"' . $list[$k]['gift_value']  . '"');
				$gift_value['status'] = iconv('utf-8','gbk','"' . $list[$k]['status'] . '"');
				$gift_value['release_date'] = iconv('utf-8','gbk','"' . $v['release_date'] . '"');
				
				$content .= implode(",", $gift_value) . "\n";
			}
			header("Content-Disposition: attachment; filename=gift_list.csv");
			echo $content;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}
	
	}
	
	
}
?>