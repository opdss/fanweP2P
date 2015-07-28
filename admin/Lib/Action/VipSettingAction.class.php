<?php

class VipSettingAction extends CommonAction {

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
    
    public function setting_trash()
	{
		$condition['is_delete'] = 1;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function add()
	{
		$this->assign("newsort",M(MODULE_NAME)->where("is_delete=0")->max("sort")+1);
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$this->assign("redenvelope_list",M("VipRedEnvelope")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$this->assign("gift_list",M("VipGift")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/add"));
		//print_r($_REQUEST["gift_records"]);die;
		
		$giftlist = $_REQUEST['gift_records'];
		$gift=implode(",",$giftlist);
		$data['gift']= $gift;	
		$red_envelopelist = $_REQUEST['red_envelopes'];
		$red_envelope=implode(",",$red_envelopelist);
		$data['red_envelope']= $red_envelope;
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
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign ('vo',$vo);
		$set_list = M("VipSetting")->where("id='$id'")->find();
		$this->assign ('set_list', $set_list );
		
		$this->assign("redenvelope_list",M("VipRedEnvelope")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$this->assign("gift_list",M("VipGift")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		
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
		$giftlist = $_REQUEST['gift_records'];
		$gift=implode(",",$giftlist);
		$data['gift']= $gift;	
		$red_envelopelist = $_REQUEST['red_envelopes'];
		$red_envelope=implode(",",$red_envelopelist);
		$data['red_envelope']= $red_envelope;
		
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
	
	public function config(){
		$vip_id = intval($_REQUEST ['vip_id']);
		if($vip_id>0) {
			$list = M()->query("SELECT * FROM ".DB_PREFIX."user_carry_config WHERE vip_id='$vip_id' ORDER BY id ASC");
		}else{
			$list = M()->query("SELECT * FROM ".DB_PREFIX."user_carry_config ORDER BY id ASC");
		}
		
		$this->assign("vip_list",M("VipType")->where('is_effect = 1 and is_delete = 0 order by sort ')->findAll());
		$this->assign("list",$list);
		$this->display();
	}
	
	public function saveconfig(){
		$config = $_POST['config'];
		$has_ids = null;
		foreach($config['id'] as $k=>$v){
			if(intval($v) > 0){
				$has_ids[] = $v;
			}
		}
		M()->query("DELETE FROM ".DB_PREFIX."user_carry_config WHERE id not in (".implode(",",$has_ids).")");
		
		foreach($config['id'] as $k=>$v){
			if(intval($v) > 0){
				$config_data =array();
				$config_data['id'] = $v;
				$config_data['name'] = trim($config['name'][$k]);
				$config_data['min_price'] = floatval($config['min_price'][$k]);
				$config_data['max_price'] = floatval($config['max_price'][$k]);
				$config_data['fee'] = floatval($config['fee'][$k]);
				$config_data['vip_id'] = intval($config['vip_id'][$k]);
				$config_data['fee_type'] = intval($config['fee_type'][$k]);
				M("UserCarryConfig")->save($config_data);
			}
		}
		
		$aconfig = $_POST['aconfig'];
		foreach($aconfig['name'] as $k=>$v){
			if(trim($v)!=""){
				$config_data =array();
				$config_data['name'] = trim($v);
				$config_data['min_price'] = floatval($aconfig['min_price'][$k]);
				$config_data['max_price'] = floatval($aconfig['max_price'][$k]);
				$config_data['fee'] = floatval($aconfig['fee'][$k]);
				$config_data['vip_id'] = intval($aconfig['vip_id'][$k]);
				$config_data['fee_type'] = intval($aconfig['fee_type'][$k]);
				M("UserCarryConfig")->add($config_data);
			}
		}
		rm_auto_cache("user_carry_config");
		parent::success(L("UPDATE_SUCCESS"));
	}
	
}
?>