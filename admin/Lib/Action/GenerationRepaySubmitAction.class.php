<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class GenerationRepaySubmitAction extends CommonAction{
	function index(){
		if(trim($_REQUEST['user_name'])!='')
		{
			$map['user_id'] = D("User")->where("user_name='".trim($_REQUEST['user_name'])."'")->getField('id');
		}
		
		if(trim($_REQUEST['status'])!='')
		{
			$map['status'] = intval($_REQUEST['status']);
		}
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$model = D ("GenerationRepaySubmit");
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
		B('FilterString');
		$data = M(MODULE_NAME)->create ();
		
		//开始验证有效性
		$this->assign("jumpUrl",u(MODULE_NAME."/index",array("status"=>$_REQUEST['status'])));
		if(intval($data['status'])==0)
		{
			$this->success(L("UPDATE_SUCCESS"));
		}
		$data['update_time'] = TIME_UTC;
		// 更新数据
		$list=M(MODULE_NAME)->save ($data);
		if (false !== $list) {
			//成功提示
			$vo = M(MODULE_NAME)->where("id=".$data['id'])->find();
			$user_id = $vo['user_id'];
			$user_info = M("User")->where("id=".$user_id)->find();
			if($data['status']==1){
				$deal_name = D("Deal")->where("id=".$vo['deal_id'])->getField("name");
				//成功
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  19;
				
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");		//提交时间
				$sh_notice['deal_name'] = $deal_name;				//贷款名称
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_XY_SUCCESS_SMS'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sz_failed_content['content']);
				$msg_data['content'] = $sh_content;
				$msg_data['to_user_id'] = $user_id;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['type'] = 0;
				$msg_data['group_key'] = implode("_",$group_arr);
				$msg_data['is_notice'] = 19;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
				$id = $GLOBALS['db']->insert_id();
				$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);
				
				//短信通知
				if(app_conf("SMS_ON")==1)
				{
					$tmpl = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_GEN_SUCCESS_SMS'");				
					$tmpl_content = $tmpl['content'];
									
					$notice['user_name'] = $user_info["user_name"];
					$notice['deal_name'] = $deal_name;
					$notice['site_name'] = app_conf("SHOP_TITLE");
					
					$GLOBALS['tmpl']->assign("notice",$notice);
					
					$msg = $GLOBALS['tmpl']->fetch("str:".$tmpl_content);
					
					$msg_data['dest'] = $user_info['mobile'];
					$msg_data['send_type'] = 0;
					$msg_data['title'] = "续约申请通过短信提醒";
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
				//$content = "您于".to_date($vo['create_time'],"Y年m月d日 H:i:s")."提交的 “".D("Deal")->where("id=".$vo['deal_id'])->getField("name")."” 续约申请被我们驳回，驳回原因\"".$data['memo']."\"";
				
				$group_arr = array(0,$user_id);
				sort($group_arr);
				$group_arr[] =  20;
				
				$sh_notice['time'] = to_date($vo['create_time'],"Y年m月d日 H:i:s");								//提交时间
				$sh_notice['deal_name'] = D("Deal")->where("id=".$vo['deal_id'])->getField("name");				//贷款名称
				$sh_notice['msg'] = $data['memo'];																//驳回原因
				$GLOBALS['tmpl']->assign("sh_notice",$sh_notice);
				$tmpl_sz_failed_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_INS_XY_FAILED_SMS'",false);
				$sh_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_sz_failed_content['content']);
				$msg_data['content'] = $sh_content;
				$msg_data['to_user_id'] = $user_id;
				$msg_data['create_time'] = TIME_UTC;
				$msg_data['type'] = 0;
				$msg_data['group_key'] = implode("_",$group_arr);
				$msg_data['is_notice'] = 20;
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."msg_box",$msg_data);
				$id = $GLOBALS['db']->insert_id();
				$GLOBALS['db']->query("update ".DB_PREFIX."msg_box set group_key = '".$msg_data['group_key']."_".$id."' where id = ".$id);
			}
			
			save_log("编号为".$data['id']."的续约申请".L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		}else {
			//错误提示
			$DBerr = M()->getDbError();
			save_log("编号为".$data['id']."的续约申请".L("UPDATE_FAILED").$DBerr,0);
			$this->error(L("UPDATE_FAILED").$DBerr,0);
		}
	}
}


function get_deal_url($id){
	return '<a href="'.__ROOT__.'/index.php?ctl=deal&id='.$id.'" target="_blank">'.D("Deal")->where("id=".$id)->getField("name").'</a>';
}