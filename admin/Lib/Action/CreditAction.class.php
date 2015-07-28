<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class CreditAction extends CommonAction{
	public function index()
	{
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("UserCreditType");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		$this->display ();
		return;
	}
	
	
	public function add()
	{
		$this->assign("newsort",M("UserCreditType")->where("is_delete=0")->max("sort")+1);
		$this->display();
	}
	
	public function insert() {
		B('FilterString');
		$data = M("UserCreditType")->create ();
		//开始验证有效性
		$this->assign("jumpUrl",u("Credit"."/add"));
		if(!check_empty($data['type_name']))
		{
			$this->error(L("DEALCATE_NAME_EMPTY_TIP"));
		}	

		// 更新数据
		$log_info = $data['name'];
		$list=M("UserCreditType")->add($data);
		if (false !== $list) {
			
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			rm_auto_cache("credit_type");
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
		$vo = M("UserCreditType")->where($condition)->find();
		$this->assign ( 'vo', $vo );		
		
		$this->display ();
	}

	
    public function set_effect()
	{
		$id = intval($_REQUEST['id']);
		$ajax = intval($_REQUEST['ajax']);
		$info = M("UserCreditType")->where("id=".$id)->getField("type_name");
		$c_is_effect = M("UserCreditType")->where("id=".$id)->getField("is_effect");  //当前状态
		$n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
		M("UserCreditType")->where("id=".$id)->setField("is_effect",$n_is_effect);	
		save_log($info.l("SET_EFFECT_".$n_is_effect),1);
		rm_auto_cache("credit_type");
		$this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1)	;	
	}
	
	public function set_sort()
	{
		$id = intval($_REQUEST['id']);
		$sort = intval($_REQUEST['sort']);
		$log_info = M("UserCreditType")->where("id=".$id)->getField("type_name");
		if(!check_sort($sort))
		{
			$this->error(l("SORT_FAILED"),1);
		}
		M("UserCreditType")->where("id=".$id)->setField("sort",$sort);
		save_log($log_info.l("SORT_SUCCESS"),1);
		rm_auto_cache("credit_type");
		$this->success(l("SORT_SUCCESS"),1);
	}
	
	public function update() {
		B('FilterString');
		$data = M("UserCreditType")->create ();
		$log_info = M("UserCreditType")->where("id=".intval($data['id']))->getField("type_name");
		//开始验证有效性
		$this->assign("jumpUrl",u("Credit"."/edit",array("id"=>$data['id'])));
		// 更新数据
		$list=M("UserCreditType")->save ($data);
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("UPDATE_SUCCESS"),1);
			rm_auto_cache("credit_type");
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$log_info.L("UPDATE_FAILED"));
		}
	}

	
	public function delete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) ,'status'=>array('eq',0) );
				
				$rel_data = M("UserCreditType")->where($condition)->findAll();
				foreach($rel_data as $data)
				{
					$info[] = $data['type_name'];
				}
				
				if($info) $info = implode(",",$info);
				
				$list = M("UserCreditType")->where ( $condition )->delete();

				if ($list!==false) {
					save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
					rm_auto_cache("credit_type");
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	public function user(){
		$this->user_get_list();
	}
	
	public function user_wait(){
		$this->user_get_list(0);
	}
	
	public function user_success(){
		$this->user_get_list(1);
	}
	
	public function user_bad(){
		$this->user_get_list(2);
	}
	
	private function user_get_list($status = -1){
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if(intval($_REQUEST['uuser_id']) > 0){
			$map['user_id'] = array("eq",intval($_REQUEST['uuser_id']));
		}
		
		if($status > -1){
			$map['passed'] = array("eq",$status);
		}
		
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("UserCreditFile");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->assign ("main_title","认证列表");
		$this->display ("user");
		return;
	}
	
	function op_passed(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			echo "认证信息不存在";
			exit();
		}
		$credit = D ("UserCreditFile")->where("id=".$id)->find();
		if($credit==0){
			echo "认证信息不存在";
			exit();
		}
		
		
		if($credit['file']){
			$credit['file_list'] = unserialize($credit['file']);
		}
		$credit_type= load_auto_cache("credit_type");
		
		$this->assign ("credit_type",$credit_type['list'][$credit['type']]);
		$this->assign ("credit",$credit);
		
		$user_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$credit['user_id']);
		//籍贯
		$user_info['n_province'] = M("RegionConf")->where("id=".$user_info['n_province_id'])->getField("name");
		$user_info['n_city'] = M("RegionConf")->where("id=".$user_info['n_city_id'])->getField("name");
		
		//户口所在地
		$user_info['province'] = M("RegionConf")->where("id=".$user_info['province_id'])->getField("name");
		$user_info['city'] = M("RegionConf")->where("id=".$user_info['city_id'])->getField("name");
		
		$this->assign ("user_info",$user_info);
		$this->display ();
		return;
	}
	
	public function modify_passed(){
		$id = intval($_REQUEST['id']);
		if($id==0){
			echo "认证信息不存在";
			exit();
		}
		$credit = D ("UserCreditFile")->where("id=".$id)->find();
		if($credit==0){
			echo "认证信息不存在";
			exit();
		}
		
		
		$ispassed = intval($_REQUEST["passed"]);
		$field_array = array(
			"credit_identificationscanning"=>"idcardpassed",
			"credit_contact"=>"workpassed",
			"credit_credit"=>"creditpassed",
			"credit_incomeduty"=>"incomepassed",
			"credit_house"=>"housepassed",
			"credit_car"=>"carpassed",
			"credit_marriage"=>"marrypassed",
			"credit_titles"=>"skillpassed",
			"credit_videoauth"=>"videopassed",
			"credit_mobilereceipt"=>"mobiletruepassed",
			"credit_residence"=>"residencepassed",
			"credit_seal"=>"sealpassed",
		);
		
		
		$credit_type= load_auto_cache("credit_type");
		
		$typeinfo = $credit_type['list'][$credit['type']];
		
		if($field_array[$credit['type']]){
			$data[$field_array[$credit['type']]] = $ispassed;
			if($ispassed==1){
				$data[$field_array[$credit['type']].'_time'] = TIME_UTC;
			}
			else{
				$data[$field_array[$credit['type']].'_time'] = 0;
			}
			
			M('User')->where('id='.$credit['user_id'])->save($data);
		}
		
		$u_info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user WHERE id=".$credit['user_id']);
		
		if($ispassed > 0){
			require_once APP_ROOT_PATH."/system/libs/user.php";
			if($ispassed==1 && $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_point_log WHERE user_id='".intval($credit['user_id'])."' and memo='%".$typeinfo['type_name']."%' and `type`= 8 ")==0){
				modify_account(array('point'=>$typeinfo['point']),$credit['user_id'],$typeinfo['type_name'],8);
				
			}
			
			if($ispassed==1){
				$user_current_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($u_info['level_id']));
				$user_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where point <=".intval($u_info['point'])." order by point desc");
				if($user_current_level['point']<=$user_level['point']&& $u_info['level_id']!=$user_level['id'] && $user_level['id'] > 0)
				{
					$u_info['level_id'] = intval($user_level['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$u_info['level_id']." where id = ".$u_info['id']);
					require_once APP_ROOT_PATH ."/app/Lib/common.php";
					$notice['level_name']=$user_level['name'];
					$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_LEVEL_ADD'",false);
					$GLOBALS['tmpl']->assign("notice",$notice);
					$pm_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
					
					send_user_msg("",$pm_content,0,$u_info['id'],TIME_UTC,0,true,true);
					
					$user_current_level['name'] = $user_level['name'];
				}
				$sh_notice['time'] = to_date($credit['create_time'],"Y年m月d日");		//提交审核时间
				$sh_notice['shop_title'] = app_conf('SHOP_TITLE');					//站点名称
				$sh_notice['type_name'] = $typeinfo['type_name'];					//审核类型名
				$sh_notice['point'] = $u_info['point'];								//信用分数
				$sh_notice['dengji'] = $user_current_level['name'];					//信用等级名
				$sh_notice['quota'] = $u_info['quota'];								//信用额度
				$sh_notice['msg'] = $_REQUEST['msg'];								//未能通过原因
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sh_succeed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_SUCCESS_SHEN_HE'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sh_succeed_content['content']);
			}
			else{
				$sh_notice['time'] = to_date($credit['create_time'],"Y年m月d日");		//提交审核时间
				$sh_notice['shop_title'] = app_conf('SHOP_TITLE');					//站点名称
				$sh_notice['type_name'] = $typeinfo['type_name'];					//审核类型名
				$sh_notice['point'] = $u_info['point'];								//信用分数
				$sh_notice['quota'] = $u_info['quota'];								//信用额度
				$sh_notice['msg'] = $_REQUEST['msg'];								//未能通过原因
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
					
				$tmpl_sh_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_FAILED_SHEN_HE'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sh_failed_content['content']);
			}
			
			$group_arr = array(0,$credit['user_id']);
			sort($group_arr);
			$group_arr[] =  intval($ispassed + 1);
			
			$msg_data['content'] = $sh_content;
			$msg_data['to_user_id'] = $credit['user_id'];
			$msg_data['create_time'] = TIME_UTC;
			$msg_data['type'] = 0;
			$msg_data['group_key'] = implode("_",$group_arr);
			$msg_data['is_notice'] = intval($ispassed + 1);
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
			$id = $GLOBALS['db']->insert_id();
			$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);
			
			
			$credit_data['status'] = 1;
			$credit_data['passed'] = $ispassed;
			$credit_data['passed_time'] = TIME_UTC;
			$credit_data['msg'] = $_REQUEST['msg'];
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_credit_file",$credit_data,"UPDATE","id = ".$credit['id']);
			
			save_log(l("ADMIN_MODIFY_CREDIT").":".$u_info['user_name']." ".$typeinfo['type_name'],1);
		}
		$this->success(L("UPDATE_SUCCESS")); 
	}
}
?>