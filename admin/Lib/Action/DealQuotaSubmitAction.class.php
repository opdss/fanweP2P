<?php

class DealQuotaSubmitAction extends CommonAction{

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
		$model = D ("DealQuotaSubmit");
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
		
		if($vo['deal_status'] ==0){
			$level_list = load_auto_cache("level");
			$u_level = M("User")->where("id=".$vo['user_id'])->getField("level_id");
			$vo['services_fee'] = $level_list['services_fee'][$u_level];
		}
		
		$user_info = M("User") -> getById($vo['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);
	
		foreach($old_imgdata_str as $k=>$v){
			$old_imgdata_str[$k]['key'] = $k;  /*+一个key*/
		}
		$this->assign("user_info",$user_info);
		$this->assign("old_imgdata_str",$old_imgdata_str);
		

		$vo['view_info'] = unserialize($vo['view_info']);
		
		
		if($vo['status'] == 0){
			
			if($vo['manage_fee'] ==""){
				// VIP状态处于有效 、采用 VIP借款管理费 比例 计算
				$vip_id = M("User")->where("vip_state='1' and id=".$vo['user_id'])->getField("vip_id");
				//echo $vip_id;
				$load_mfee = M("VipSetting")->where("vip_id='$vip_id'")->getField("load_mfee");
				if($load_mfee){
					$vo['manage_fee']=$load_mfee;
				}else{
					$vo['manage_fee'] = app_conf("MANAGE_FEE");
				}
			}
			if($vo['user_loan_manage_fee'] =="")
				$vo['user_loan_manage_fee'] = app_conf("USER_LOAN_MANAGE_FEE");
			if($vo['manage_impose_fee_day1'] =="")
				$vo['manage_impose_fee_day1'] = app_conf("MANAGE_IMPOSE_FEE_DAY1");
			if($vo['manage_impose_fee_day2'] =="")
				$vo['manage_impose_fee_day2'] = app_conf("MANAGE_IMPOSE_FEE_DAY2");
			if($vo['impose_fee_day1'] =="")
				$vo['impose_fee_day1'] = app_conf("IMPOSE_FEE_DAY1");
			if($vo['impose_fee_day2'] =="")
				$vo['impose_fee_day2'] = app_conf("IMPOSE_FEE_DAY2");
			if($vo['user_load_transfer_fee'] =="")
				$vo['user_load_transfer_fee'] = app_conf("USER_LOAD_TRANSFER_FEE");
			if($vo['compensate_fee'] =="")
				$vo['compensate_fee'] = app_conf("COMPENSATE_FEE");
			if($vo['user_bid_rebate'] =="")
				$vo['user_bid_rebate'] = app_conf("USER_BID_REBATE");
			if($vo['user_loan_interest_manage_fee'] =="")
				$vo['user_loan_interest_manage_fee'] = app_conf("USER_LOAN_INTEREST_MANAGE_FEE");
			if($vo['generation_position'] =="")
				$vo['generation_position'] = 100;
			if($vo['user_bid_score_fee']=="")
				$vo['user_bid_score_fee'] = app_conf("USER_BID_SCORE_FEE");
		}
		
		
		$this->assign("vo",$vo);
		
		$citys = M("DealCity")->where('is_delete= 0 and is_effect=1 ')->findAll();
		$citys_link = unserialize($vo['citys']);
		foreach($citys as $k=>$v){
			foreach($citys_link as $kk=>$vv){
				if($vv == $v['id'])
					$citys[$k]['is_selected'] = 1;
			}
		}
		
		$this->assign ( 'citys', $citys );
		
		$deal_cate_tree = M("DealCate")->where('is_delete = 0')->findAll();
		$deal_cate_tree = D("DealCate")->toFormatTree($deal_cate_tree,'name');
		$this->assign("deal_cate_tree",$deal_cate_tree);
		
		$deal_agency = M("User")->where('is_effect = 1 and user_type =2')->order('sort DESC')->findAll();
		$this->assign("deal_agency",$deal_agency);
		
		$deal_type_tree = M("DealLoanType")->findAll();
		$deal_type_tree = D("DealLoanType")->toFormatTree($deal_type_tree,'name');
		$this->assign("deal_type_tree",$deal_type_tree);
    	
    	$contract_list = load_auto_cache("contract_cache");
    	$this->assign("contract_list",$contract_list);
    	
		$this->display ();
	}
	
	public function update(){
		
		$data = M(MODULE_NAME)->create();
		
		$point = 0;
		if($data['status'])
			$point = intval($_REQUEST ['point']);
		
		$this->assign("jumpUrl","javascript:history.back(-1);");
		
		if($data['status']==1){
			if(!check_empty($data['name']))
			{
				$this->error(L("DEAL_NAME_EMPTY_TIP"));
			}	
			if(!check_empty($data['sub_name']))
			{
				$this->error(L("DEAL_SUB_NAME_EMPTY_TIP"));
			}		
			if($data['cate_id']==0)
			{
				$this->error(L("DEAL_CATE_EMPTY_TIP"));
			}
			
		}
		
		if($point>0){
			$msg="授信额度申请成功，增加信用额度".$point;
			require_once APP_ROOT_PATH."system/libs/user.php";
			modify_account(array('point'=>$point),$data['user_id'],$msg,8);
			
		}
		
		$user_info = M("User") -> getById($data['user_id']);
		$old_imgdata_str = unserialize($user_info['view_info']);

		
		$data['view_info'] = array();
		foreach($_REQUEST['key'] as $k=>$v){
			if(isset($old_imgdata_str[$v])){
				$data['view_info'][$v] = $old_imgdata_str[$v];
			}
		}
		$data['view_info'] = serialize($data['view_info']);
		
		$data['citys'] = serialize($_REQUEST['city_id']);
		
		// 更新数据
		$list=M(MODULE_NAME)->save($data);
		
		if ($list > 0) {
			
			$sdata['update_time'] = TIME_UTC;
			$sdata['id'] = $data['id'];
			M(MODULE_NAME)->save($sdata);
			
			//成功提示
			$vo = M(MODULE_NAME)->where("id=".$data['id'])->find();
			$user_id = $vo['user_id'];
			$user_info = M("User")->where("id=".$user_id)->find();
			require_once APP_ROOT_PATH."/system/libs/user.php";
			if($data['status']==1){
				//$content = "您于".to_date($vo['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($vo['borrow_amount'])."授信额度申请成功，请查看您的申请记录。";
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  21;
				
				$sh_notice['point'] = "并增加信用积分".$point;
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");		//提交时间
				$sh_notice['quota'] = format_price($vo['borrow_amount']);				//授信额度
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_SXQUORA_SUCCESS_SMS'",false);
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
				//$content = "您于".to_date($vo['create_time'],"Y年m月d日 H:i:s")."提交的".format_price($vo['money'])."授信额度申请申请被我们驳回，驳回原因\"".$data['bad_msg']."\"";
				
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  22;
				
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");		//提交时间
				$sh_notice['quota'] = format_price($vo['money']);						//授信额度
				$sh_notice['msg'] = $data['bad_msg'];									//驳回原因
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_SXQUORA_FAILED_SMS'",false);
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
					$notice['quota_money'] = $vo['borrow_amount'];
					$notice['msg'] = $data['bad_msg'];
					$notice['site_name'] = app_conf("SHOP_TITLE");
						
					$GLOBALS['tmpl']->assign("notice",$notice);
						
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
						
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "授信额度申请失败";
					$msg_data['content'] = addslashes($msg);;
					$msg_data['send_time'] = 0;
					$msg_data['is_send'] = 0;
					$msg_data['create_time'] = TIME_UTC;
					$msg_data['user_id'] = $user_info['id'];
					$msg_data['is_html'] = $tmpl['is_html'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_msg_list",$msg_data); //插入
				}
			}
			
			save_log("编号为".$data['id']."的授信额度申请".L("UPDATE_SUCCESS"),1);
			$this->assign("jumpUrl",u(MODULE_NAME."/index",array("status"=>$data['status'])));
			$this->success(L("UPDATE_SUCCESS"));
		}else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log("编号为".$data['id']."的授信额度申请".L("UPDATE_FAILED").$DBerr,0);
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