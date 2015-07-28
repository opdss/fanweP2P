<?php

class QuotaSubmitAction extends CommonAction{

    //提现申请列表
	public function index(){
		
		if(trim($_REQUEST['user_name'])!='')
		{
			$map['user_id'] = D("User")->where("user_name='".trim($_REQUEST['user_name'])."'")->getField('id');
		}
		if(trim($_REQUEST['status'])!='')
		{
			$map['status'] = intval($_REQUEST['status']);
		}
		$model = D ("QuotaSubmit");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
	}
	
	//提现申请列表
	public function edit(){
		$id = intval($_REQUEST ['id']);
		$condition['id'] = $id;	
		$vo = M(MODULE_NAME)->where($condition)->find();
		$this->assign("vo",$vo);
		$this->display ();
	}
	
	public function update(){
		
		$data = M(MODULE_NAME)->create();
		
		$data['status'] = strim(intval($_REQUEST['status']));
		$data['bad_msg'] = strim($_REQUEST['msg']);
		$data['note'] = strim($_REQUEST['note']);
		// 更新数据
		$list=M(MODULE_NAME)->save($data);
		
		if ($list >0) {
			
			$sdata['op_time'] = TIME_UTC;
			$sdata['id'] = $data['id'];
			M(MODULE_NAME)->save($sdata);
			
			//成功提示
			$vo = M(MODULE_NAME)->where("id=".$data['id'])->find();
			$user_id = $vo['user_id'];
			$user_info = M("User")->where("id=".$user_id)->find();
			require_once APP_ROOT_PATH."/system/libs/user.php";
			if($data['status']==1){
				//$content = "您于".to_date($vo['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($vo['money'])."信用额度申请成功，请查看您的申请记录。";
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  21;
				
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");		//申请时间
				$sh_notice['quota'] = format_price($vo['money']);						//申请额度
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_QUORA_SUCCESS'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sz_failed_content['content']);
				$msg_data['content'] = $sh_content;
				$msg_data['to_user_id'] = $user_id;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['type'] = 0;
				$msg_data['group_key'] = implode("_",$group_arr);
				$msg_data['is_notice'] = 21;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
				$id = $GLOBALS['db']->insert_id();
				
				
				$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);
				
				//更改用户额度
				if($vo['money'] > $user_info['quota']){
					modify_account(array("quota"=>($vo['money'] - $user_info['quota'])),$user_info['id'],"申请额度",13);
				}
				
				//短信通知
				if(app_conf("SMS_ON")==1)
				{
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_QUOTA_SUCCESS_SMS'",false);				
					$tmpl_content = $tmpl['content'];
									
					$notice['user_name'] = $user_info["user_name"];
					$notice['quota_money'] = $vo['money'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
					
					$GLOBALS['tmpl']->assign("notice",$notice);
					
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "额度申请成功短信提醒";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入				
				}
			}
			else{
				//驳回
				//$content = "您于".to_date($vo['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($vo['money'])."信用额度申请申请被我们驳回，驳回原因\"".$data['bad_msg']."\"";
				
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  22;
				
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");		//申请时间
				$sh_notice['quota'] = format_price($vo['money']);						//申请额度	
				$sh_notice['msg'] = $data['bad_msg'];									//驳回原因	
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_QUORA_FAILED'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sz_failed_content['content']);
				
				$msg_data['content'] = $sh_content;
				$msg_data['to_user_id'] = $user_id;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['type'] = 0;
				$msg_data['group_key'] = implode("_",$group_arr);
				$msg_data['is_notice'] = 22;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
				$id = $GLOBALS['db']->insert_id();
				$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);

				//短信通知
				if(app_conf("SMS_ON")==1)
				{
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_QUOTA_FAILED_SMS'",false);
					$tmpl_content = $tmpl['content'];
						
					$notice['user_name'] = $user_info["user_name"];
					$notice['quota_money'] = $vo['money'];
					$notice['msg'] = $data['bad_msg'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
						
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
						
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "额度申请失败";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
			
			save_log("编号为".$data['id']."的信用额度申请".L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log("编号为".$data['id']."的信用额度申请".L("UPDATE_FAILED").$DBerr,0);
			$this->error(L("UPDATE_FAILED").$DBerr,0);
		}
	}
	
	

	
	public function delete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
		
				if ($list!==false) {					
					save_log(l("FOREVER_DELETE_SUCCESS"),1);
					$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
				} else {
					save_log(l("FOREVER_DELETE_FAILED"),0);
					$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}	
}
?>