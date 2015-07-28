<?php

class VipFestivalsAction extends CommonAction {

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
    
    public function festivals_trash()
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
			$this->error("节日名称不能为空！");
		}	
		
		// 更新数据
		$log_info = $data['name'];
		$list=M(MODULE_NAME)->add($data);
		if (false !== $list) {
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("cache_vip_festivals");
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
		rm_auto_cache("cache_vip_festivals");
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
		rm_auto_cache("cache_vip_festivals");
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
			rm_auto_cache("cache_vip_festivals");
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
				
				
				if(M("GivenRecord")->where(array ('gift_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("存在关联节日积分 "),$ajax);
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
					rm_auto_cache("cache_vip_festivals");
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
				
				if(M("GivenRecord")->where(array ('gift_id' => array ('in', explode ( ',', $id ) )))->count()>0)
				{
					$this->error (l("存在关联节日积分"),$ajax);
				}
				
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['name'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					save_log($info.l("RESTORE_SUCCESS"),1);
					rm_auto_cache("cache_vip_festivals");
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
					rm_auto_cache("cache_vip_festivals");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function send_gift(){
		$today=to_date(TIME_UTC,"Y-m-d");
		$this_year=date("Y");
		$id = intval($_REQUEST ['id']);
		$condition['is_delete'] = 0;
		$condition['id'] = $id;		
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ( 'vo', $vo );		
		
		$sqlscore = " SELECT vs.holiday_score,vt.vip_grade  FROM  ".DB_PREFIX."vip_setting vs LEFT JOIN ".DB_PREFIX."vip_type vt ON vs.vip_id=vt.id and vt.is_delete=0 and vt.is_effect=1  ";
	
		$vip_score = $GLOBALS['db']->getAll($sqlscore);

		$this->assign ( 'vip_score', $vip_score );
		
		$vslist = M("VipSetting")->where("is_effect='1'")->findAll();
		$this->assign ( 'vslist', $vslist );
		
		$this->display ();
		
	}
	
	public function send_update_gift(){
		require_once(APP_ROOT_PATH."system/libs/user.php");
		$id = intval($_REQUEST ['id']);
		$vip_festivalsinfo = $GLOBALS['db']->getRow("select * FROM ".DB_PREFIX."vip_festivals WHERE id='".$id."' ");
		if(trim($_REQUEST['is_send']==1)){
			$info['given_name_type'] = 2;
			$info['given_type'] = 2;
			$info['send_date'] = to_date(TIME_UTC,"Y-m-d");
			$info['given_num'] = trim($_REQUEST['number']);
			$info['send_state'] = trim($_REQUEST['is_send']);
			$info['gift_id'] = $id;
			$info['brief'] = trim($_REQUEST['brief']);
			$user_data = M("User")->where("vip_state='1' and vip_id!='0' and user_type!='3' ")->findAll();
			foreach($user_data as $data)
			{
					$vip_id=$data['vip_id'];
					$holiday_score=M("VipSetting")->where("vip_id='$vip_id'")->getField("holiday_score");
					$info['holiday_score'] = $holiday_score;	
					$info['user_id'] = $data['id'];	
					$info['vip_id'] = $vip_id;
					$info['given_value'] =$info['given_num']*$holiday_score;	
					$GLOBALS['db']->autoExecute(DB_PREFIX."given_record",$info,"INSERT");
					$score=$info['given_num']*$holiday_score;
					$msg = '[<a href="#" target="_blank">'.$vip_festivalsinfo['name'].'</a>],发送积分';
					modify_account(array('score'=>$score),$data['id'],$msg,29);
			}
			
			$GLOBALS['db']->query("update ".DB_PREFIX."vip_festivals SET number='".$_REQUEST['number']."',is_send='".$_REQUEST['is_send']."' WHERE id=".$id);
			
			$this->success(L("礼品发放成功！")); 
		}else{
			$this->error(L("礼品发放失败！"));
		}
		
	}
	
}
?>