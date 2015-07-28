<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/uc.php';
require APP_ROOT_PATH."app/Lib/deal.php";

class debit_uc_centerModule extends SiteBaseModule
{
	private $space_user;
	public function init_main()
	{
		$GLOBALS['tmpl']->assign('user_auth',get_user_auth());
	}
	
	public function init_user(){
		
		if(!$GLOBALS['user_info']){
    		showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$is_ajax,url("debit","debit_user#login"));
    	}
		
		$this->user_data = $GLOBALS['user_info'];
		
		$province_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->user_data['province_id']);
		$city_str = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."region_conf where id = ".$this->user_data['city_id']);
		if($province_str.$city_str=='')
			$user_location = $GLOBALS['lang']['LOCATION_NULL'];
		else 
			$user_location = $province_str." ".$city_str;
		
		$this->user_data['fav_count'] = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."topic where user_id = ".$this->user_data['id']." and fav_id <> 0");
		$this->user_data['user_location'] = $user_location;
		$this->user_data['group_name'] = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."user_group where id = ".$this->user_data['group_id']." ");
		
		$this->user_data['user_statics'] =sys_user_status($GLOBALS['user_info']['id'],false);
		$GLOBALS['tmpl']->assign('user_statics',$this->user_data['user_statics']);
	}
	
	public function index()
	{	
		$this->init_user();
		$user_info = $this->user_data;
		
			 
		$ajax =intval($_REQUEST['ajax']);
		if($ajax==0)
		{
			$this->init_main();			
		}
		$user_id = intval($GLOBALS['user_info']['id']);
		
		$GLOBALS['tmpl']->assign("user_info",$user_info);
		
		$GLOBALS['tmpl']->assign("inc_file","debit/debit_uc_center_index.html");
		$GLOBALS['tmpl']->display("debit/debit_uc.html");
		
	}
	//我的订单
	public function order()
	{
		$this->init_user();
		
		$user_id = $GLOBALS['user_info']['id'];
		
		$status = intval($_REQUEST['status']);
		
		$GLOBALS['tmpl']->assign("status",$status);
		
		//输出借款记录
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");

		$result = get_deal_list($limit,0,"(deal_status in(1,2,3,4,5) or publish_wait in (1,2,3) ) AND user_id=".$user_id,"id DESC");
		$deal_ids = array();
		foreach($result['list'] as $k=>$v){
			if($v['repay_progress_point'] >= $v['generation_position'])
				$result['list'][$k]["can_generation"] = 1;
			
			$deal_ids[] = $v['id'];
		}
		if($deal_ids){
			$temp_ids = $GLOBALS['db']->getAll("SELECT `deal_id`,`status` FROM ".DB_PREFIX."generation_repay_submit WHERE deal_id in(".implode(",",$deal_ids).") ");
			$deal_g_ids = array();
			foreach($temp_ids as $k=>$v){
				$deal_g_ids[$v['deal_id']] = $v;
			}
		
		
			foreach($result['list'] as $k=>$v){
				if(isset($deal_g_ids[$v['id']])){
					//申请中
					$result['list'][$k]['generation_status'] = $deal_g_ids[$v['id']]['status'] + 1; 
				}
				switch($v["deal_status"])
				{
					case "0":
						$result['list'][$k]['deal_status'] = "等待审核";break;
					case "1":
						if($v["publish_wait"] == 1 || $v["publish_wait"] == 2 || $v["publish_wait"] == 3)
							$result['list'][$k]['deal_status'] = "等待审核";
						else
						{
							$result['list'][$k]['deal_status'] = "审核通过";
						};
						break;
					case "2":
						$result['list'][$k]['deal_status'] = "审核通过";break;
					case "3":
						$result['list'][$k]['deal_status'] = "审核未通过";break;
					case "4":
						$result['list'][$k]['deal_status'] = "还款中";break;
					case "5":
						$result['list'][$k]['deal_status'] = "已还清";break;
					default:break;
				}
			}
		}
		
		$GLOBALS['tmpl']->assign("deal_list",$result['list']);
		
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		$GLOBALS['tmpl']->assign("inc_file","debit/debit_uc_center_order.html");
		$GLOBALS['tmpl']->display("debit/debit_uc.html");
	}
	public function order_detail()
	{
		$id = intval($_REQUEST['id']);
		if($id == 0){
			showErr("操作失败！");
		}
		$deal = get_deal($id);
		if(!$deal)
		{
			showErr("借款不存在！");
		}
		if($deal['user_id']!=$GLOBALS['user_info']['id']){
			showErr("不属于你的借款！");
		}
		/*if($deal['deal_status']!=4){
			showErr("借款不是还款状态！");
		}*/
		$GLOBALS['tmpl']->assign('deal',$deal);
		
		//还款列表
		$loan_list = get_deal_load_list($deal);
		
		$left_repay_count = $deal["repay_time"];

		foreach($loan_list as $k => $v)
		{
			if($v["has_repay"]==1)
			{
				$left_repay_count --;
			}
		}
		
		$deal["left_repay_count"] = $left_repay_count;
		
		$GLOBALS['tmpl']->assign("loan_list",$loan_list);
		$GLOBALS['tmpl']->assign("deal",$deal);
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_DEAL_REFUND']);
		$GLOBALS['tmpl']->assign("inc_file","debit/debit_uc_center_order_detail.html");
		$GLOBALS['tmpl']->display("debit/debit_uc.html");
	}
	public function save_alipay()
	{
		$this->init_user();
		
		$ajax = intval($_REQUEST['is_ajax']);
		
		if(!strim($_REQUEST["real_name"]))
		{
			showErr("请填写真实姓名！",$ajax);
		}
		if(!strim($_REQUEST["u_alipay"]))
		{
			showErr("请填写支付宝号！",$ajax);
		}
		$data =  array();
		
		$data["real_name"] = strim($_REQUEST["real_name"]);
		$data["u_alipay"] = strim($_REQUEST["u_alipay"]);
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."user",$data,"UPDATE","id=".$GLOBALS["user_info"]["id"]);
		
		$return = array();
		$return['status'] = 1;
		$return['info'] = "修改成功";
		ajax_return($return);
	}
	public function security()
	{
		if($GLOBALS['user_info']['idcardpassed'] == 0)
		{
			$idcard_credit = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_credit_file WHERE type='credit_identificationscanning' and user_id=".$GLOBALS['user_info']['id']." ");
	    	
	    	if($idcard_credit){
	    		$file_list = array();
	    		if($idcard_credit['file'])
	    			$file_list = unserialize($idcard_credit['file']);
	    		
	    		if(is_array($file_list)) 
	    			$idcard_credit['file_list']= $file_list;
	    		
	    	}
	    	
	    	$GLOBALS['tmpl']->assign('idcard_credit',$idcard_credit);
		}
		
		$user_type = $GLOBALS['db']->getOne("SELECT user_type FROM ".DB_PREFIX."user WHERE id=".$GLOBALS['user_info']['id']);
		$GLOBALS['tmpl']->assign('user_type',$user_type);
		
		$GLOBALS['tmpl']->assign("inc_file","debit/debit_uc_security.html");
		$GLOBALS['tmpl']->display("debit/debit_uc.html");
	}
	
	public function uinfo()
	{
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['UC_ACCOUNT']);
		
		//扩展字段
		$field_list = load_auto_cache("user_field_list");
		
		foreach($field_list as $k=>$v)
		{
			$field_list[$k]['value'] = $GLOBALS['db']->getOne("select value from ".DB_PREFIX."user_extend where user_id=".$GLOBALS['user_info']['id']." and field_id=".$v['id']);
		}
		
		$GLOBALS['tmpl']->assign("field_list",$field_list);
		
		
		//地区列表
		$region_lv2_name ="";
		$region_lv2 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where region_level = 2");  //二级地址
		foreach($region_lv2 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['province_id']))
			{
				$region_lv2[$k]['selected'] = 1;
				$region_lv2_name = $v['name'];
			}
			
			if($v['id'] == intval($GLOBALS['user_info']['n_province_id']))
			{
				$region_lv2[$k]['nselected'] = 1;
				$n_region_lv2_name = $v['name'];
			}
		}
		$GLOBALS['tmpl']->assign("region_lv2",$region_lv2);
		$GLOBALS['tmpl']->assign("region_lv2_name",$region_lv2_name);
		$GLOBALS['tmpl']->assign("n_region_lv2_name",$n_region_lv2_name);
			
		$region_lv3_name = "";
		$region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($GLOBALS['user_info']['province_id']));  //三级地址
		foreach($region_lv3 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['city_id']))
			{
				$region_lv3[$k]['selected'] = 1;
				$region_lv3_name = $v['name'];
				break;
			}
		}
		$GLOBALS['tmpl']->assign("region_lv3",$region_lv3);
		$GLOBALS['tmpl']->assign("region_lv3_name",$region_lv3_name);
			
		$n_region_lv3_name = "";
		$n_region_lv3 = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."region_conf where pid = ".intval($GLOBALS['user_info']['n_province_id']));  //三级地址
		foreach($n_region_lv3 as $k=>$v)
		{
			if($v['id'] == intval($GLOBALS['user_info']['n_city_id']))
			{
				$n_region_lv3[$k]['selected'] = 1;
				$n_region_lv3_name = $v['name'];
				break;
			}
		}
		$GLOBALS['tmpl']->assign("n_region_lv3",$n_region_lv3);
		$GLOBALS['tmpl']->assign("n_region_lv3_name",$n_region_lv3_name);
			
		$GLOBALS['tmpl']->assign("inc_file","debit/debit_uc_center_uinfo.html");
		$GLOBALS['tmpl']->display("debit/debit_uc.html");
	}
	
	public function save()
	{
		require_once APP_ROOT_PATH.'system/libs/user.php';
		foreach($_REQUEST as $k=>$v)
		{
			$_REQUEST[$k] = htmlspecialchars(addslashes(trim($v)));
		}
		
		if ($_REQUEST['sta'] == 1){
			if(md5(strim($_REQUEST['old_password']).$GLOBALS['user_info']['code'])!=$GLOBALS['user_info']['user_pwd'])
			{
				showErr("旧密码错误！",intval($_REQUEST['is_ajax']));
			}
		}
		
		/*if($_REQUEST["u_alipay"] == "")
		{
			showErr("支付宝号不能为空！",intval($_REQUEST['is_ajax']));
		}
		
		if(isset($_REQUEST['u_year']))
			$_REQUEST['u_year'] = strim($_REQUEST['u_year']);
		if(isset($_REQUEST['u_special']))
			$_REQUEST['u_special'] = strim($_REQUEST['u_special']);
		if(isset($_REQUEST['university']))
			$_REQUEST['university'] = strim($_REQUEST['university']);
		if(isset($_REQUEST['u_alipay']))
			$_REQUEST['u_alipay'] = strim($_REQUEST['u_alipay']);*/
		
		$_REQUEST['id'] = intval($GLOBALS['user_info']['id']);
		
		if($GLOBALS['user_info']['user_name']!="")
			$_REQUEST['user_name'] =  $_REQUEST['old_user_name'] = $GLOBALS['user_info']['user_name'];
		if($GLOBALS['user_info']['email']!="")
			$_REQUEST['email'] = $_REQUEST['old_email'] = $GLOBALS['user_info']['email'];
		if($GLOBALS['user_info']['mobile']!="")
			$_REQUEST['mobile'] = $_REQUEST['old_mobile'] = $GLOBALS['user_info']['mobile'];
			
		/*$_REQUEST['old_password'] = strim($_REQUEST['old_password']);
		
		$_REQUEST["marriage"] = "未婚";
		
		$_REQUEST["province_id"] = 1;
		$_REQUEST["city_id"] = 1;*/

		$res = save_user($_REQUEST,'UPDATE');
		if($res['status'] == 1)
		{
			$s_user_info = es_session::get("user_info");
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = '".intval($s_user_info['id'])."'");
			es_session::set("user_info",$user_info);
			if(intval($_REQUEST['is_ajax'])==1)
				showSuccess($GLOBALS['lang']['SUCCESS_TITLE'],1);
			else{
				app_redirect(url("debit","debit_uc_center#uinfo"));
			}		
		}
		else
		{
			$error = $res['data'];		
			if(!$error['field_show_name'])
			{
					$error['field_show_name'] = $GLOBALS['lang']['USER_TITLE_'.strtoupper($error['field_name'])];
			}
			if($error['error']==EMPTY_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EMPTY_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==FORMAT_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['FORMAT_ERROR_TIP'],$error['field_show_name']);
			}
			if($error['error']==EXIST_ERROR)
			{
				$error_msg = sprintf($GLOBALS['lang']['EXIST_ERROR_TIP'],$error['field_show_name']);
			}
			showErr($error_msg,intval($_REQUEST['is_ajax']));
		}
	}
	
}
?>