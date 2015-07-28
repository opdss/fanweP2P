<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH."app/Lib/message.php";
require APP_ROOT_PATH.'app/Lib/page.php';
class commentModule extends SiteBaseModule
{
	public function index()
	{			
		$goods_id = intval($_REQUEST['id']);
		$is_buy = intval($_REQUEST['is_buy']);	
		$GLOBALS['tmpl']->assign("goods_id",$goods_id);
		$GLOBALS['tmpl']->assign("is_buy",$is_buy);
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");			
		$result = get_message_list_shop($limit," rel_table='deal' and rel_id = ".$goods_id." and is_buy = ".$is_buy);		
		$GLOBALS['tmpl']->assign("message_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
			
		if(!$GLOBALS['user_info'])
		{
			$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
		}
		$GLOBALS['tmpl']->display("inc/inc_goods_comment_list.html");
	}
	
	public function add()
	{
		$ajax = intval($_REQUEST['ajax']);
		if(!$GLOBALS['user_info'])
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax);
		}
		if($_REQUEST['content']=='')
		{
			showErr($GLOBALS['lang']['MESSAGE_CONTENT_EMPTY'],$ajax);
		}
		//验证码
		if(app_conf("VERIFY_IMAGE")==1)
		{
			$verify = md5(trim($_REQUEST['verify']));
			$session_verify = es_session::get('verify');
			if($verify!=$session_verify)
			{				
				showErr($GLOBALS['lang']['VERIFY_CODE_ERROR'],$ajax);
			}
		}
		es_session::delete("verify");
		if(!check_ipop_limit(CLIENT_IP,"message",intval(app_conf("SUBMIT_DELAY")),0))
		{
			showErr($GLOBALS['lang']['MESSAGE_SUBMIT_FAST'],$ajax);
		}
		
		$rel_table = $_REQUEST['rel_table'];
//		$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."' and type_name <> 'supplier'");
//		if(!$message_type)
//		{
//			showErr($GLOBALS['lang']['INVALID_MESSAGE_TYPE'],$ajax);
//		}
		
		$message_group = $_REQUEST['message_group'];
	
		//添加留言
		$message['title'] = $_REQUEST['title']?htmlspecialchars(addslashes(valid_str($_REQUEST['title']))):htmlspecialchars(addslashes(valid_str($_REQUEST['content'])));
		$message['content'] = htmlspecialchars(addslashes(valid_str($_REQUEST['content'])));
		$message['title'] = valid_str($message['title']);
		if($message_group)
		{
			$message['title']="[".$message_group."]:".$message['title'];
			$message['content']="[".$message_group."]:".$message['content'];
		}
		
		$message['create_time'] = TIME_UTC;
		$message['rel_table'] = $rel_table;
		$rel_id = $message['rel_id'] = intval($_REQUEST['rel_id']);
		$message['user_id'] = intval($GLOBALS['user_info']['id']);
		
		
		if(isset($_REQUEST['is_effect']))
		{
			$message_effect = intval($_REQUEST['is_effect']);
		}
		else
		{
			if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
			{
				$message_effect = 0;
			}
			else
			{
				$message_effect = $message_type['is_effect'];
			}
		}
		$message['is_effect'] = $message_effect;		
		$message['is_buy'] = intval($_REQUEST['is_buy']);
		
		$message['contact'] = $_REQUEST['contact']?htmlspecialchars(addslashes($_REQUEST['contact'])):'';
		$message['contact_name'] = $_REQUEST['contact_name']?htmlspecialchars(addslashes($_REQUEST['contact_name'])):'';
		if($message['is_buy']==1)
		{
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal_order as do on doi.order_id = do.id where doi.deal_id = ".intval($message['rel_id'])." and do.user_id = ".intval($message['user_id'])." and do.pay_status = 2")==0)
			{
				showErr($GLOBALS['lang']['AFTER_BUY_MESSAGE_TIP'],$ajax);
			}
		}
		$message['point'] = intval($_REQUEST['point']);
		$GLOBALS['db']->autoExecute(DB_PREFIX."message",$message);
		$message_id = $GLOBALS['db']->insert_id();
		
		if($message['is_buy']==1)
		{			
			$attach_list = get_topic_attach_list(); 
			$deal_info = $GLOBALS['db']->getRow("select id,is_shop,name,sub_name from ".DB_PREFIX."deal where id = ".$rel_id);
			if($deal_info['is_shop']==0)
			{
				$url_route = array(
					'rel_app_index'	=>	'tuan',
					'rel_route'	=>	'deal',
					'rel_param' => 'id='.$deal_info['id']
				);
				$type = "tuancomment";
				
				$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_location_link where deal_id = ".$deal_info['id']);
				$dp_title = "对".$deal_info['sub_name']."的消费点评";
				foreach($locations as $location)
				{				
					insert_dp($dp_title,$message['content'],$location['location_id'],$message['point'],$is_buy=1,$from="tuan",$url_route,$message_id);
				}
			}
			if($deal_info['is_shop']==1)
			{
				$url_route = array(
					'rel_app_index'	=>	'shop',
					'rel_route'	=>	'goods',
					'rel_param' => 'id='.$deal_info['id']
				);
				$type = "shopcomment";
				
				$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_location_link where deal_id = ".$deal_info['id']);
				$dp_title = "对".$deal_info['sub_name']."的消费点评";
				foreach($locations as $location)
				{				
					insert_dp($dp_title,$message['content'],$location['location_id'],$message['point'],$is_buy=1,$from="shop",$url_route,$message_id);
				}
			}
			if($deal_info['is_shop']==2)
			{
				$url_route = array(
					'rel_app_index'	=>	'store',
					'rel_route'	=>	'ydetail',
					'rel_param' => 'id='.$deal_info['id']
				);
				$type = "youhuicomment";
				
				$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_location_link where deal_id = ".$deal_info['id']);
				$dp_title = "对".$deal_info['sub_name']."的消费点评";
				foreach($locations as $location)
				{				
					insert_dp($dp_title,$message['content'],$location['location_id'],$message['point'],$is_buy=1,$from="daijin",$url_route,$message_id);
				}
			}
			increase_user_active(intval($GLOBALS['user_info']['id']),"点评了一个商品");
			$title = "对".$deal_info['sub_name']."发表了点评";			
			$tid = insert_topic($message['content'],$title,$type,$group="", $relay_id = 0, $fav_id = 0,$group_data = "",$attach_list=array(),$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
		}
		
		if($message['rel_table']=='youhui')
		{
			increase_user_active(intval($GLOBALS['user_info']['id']),"点评了一个优惠券");
			$youhui_info = $GLOBALS['db']->getRow("select name,id from ".DB_PREFIX."youhui where id = ".$rel_id);
			$title = "对".$youhui_info['name']."发表了点评";
			$url_route = array(
					'rel_app_index'	=>	'store',
					'rel_route'	=>	'fdetail',
					'rel_param' => 'id='.$youhui_info['id']
				);
			$tid = insert_topic($message['content'],$title,"fyouhuicomment",$group="", $relay_id = 0, $fav_id = 0,$group_data = "",$attach_list=array(),$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
			
			$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."youhui_location_link where youhui_id = ".$youhui_info['id']);
			$dp_title = "对".$deal_info['name']."的点评";
			foreach($locations as $location)
			{				
				insert_dp($dp_title,$message['content'],$location['location_id'],3,$is_buy=0,$from="youhui",$url_route,$message_id);
			}
		}
		
		if($message['rel_table']=='event')
		{
			increase_user_active(intval($GLOBALS['user_info']['id']),"点评了一个活动");
			$event_info = $GLOBALS['db']->getRow("select name,id from ".DB_PREFIX."event where id = ".$rel_id);
			$title = "对".$event_info['name']."发表了点评";
			$url_route = array(
					'rel_app_index'	=>	'store',
					'rel_route'	=>	'edetail',
					'rel_param' => 'id='.$event_info['id']
				);
			
			$tid = insert_topic($message['content'],$title,"eventcomment",$group="", $relay_id = 0, $fav_id = 0,$group_data ="",$attach_list=array(),$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
			$GLOBALS['db']->query("update ".DB_PREFIX."event set reply_count = reply_count+1 where id =".$rel_id);
			
			$locations = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."event_location_link where event_id = ".$event_info['id']);
			$dp_title = "对".$event_info['name']."的点评";
			foreach($locations as $location)
			{				
				insert_dp($dp_title,$message['content'],$location['location_id'],3,$is_buy=0,$from="event",$url_route,$message_id);
			}
		}
		
		if($message['rel_table']=='supplier_location')
		{
			increase_user_active(intval($GLOBALS['user_info']['id']),"点评了一家商户");
			$supplier_info = $GLOBALS['db']->getRow("select name,id from ".DB_PREFIX."supplier_location where id = ".$rel_id);
			$title = "对".$supplier_info['name']."发表了点评";
			$url_route = array(
					'rel_app_index'	=>	'store',
					'rel_route'	=>	'view',
					'rel_param' => 'id='.$supplier_info['id']
				);
			$tid = insert_topic($message['content'],$title,"slocationcomment",$group="", $relay_id = 0, $fav_id = 0,$group_data = "",$attach_list=array(),$url_route);
			if($tid)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."topic set source_name = '网站' where id = ".intval($tid));
			}
		}
		
		//开始处理为deal时的计分
		if($rel_table=='deal')
		{
			$total_point = $GLOBALS['db']->getOne("select sum(point) from ".DB_PREFIX."message where rel_table = 'deal' and rel_id = ".intval($_REQUEST['rel_id']));
			$total_comment = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_table = 'deal' and rel_id = ".intval($_REQUEST['rel_id']));
			$avg_point = round($total_point/$total_comment);
			$GLOBALS['db']->query("update ".DB_PREFIX."deal set total_point = ".$total_point.",avg_point = ".$avg_point." where id =".intval($_REQUEST['rel_id']));
		}
		
		
		showSuccess($GLOBALS['lang']['MESSAGE_POST_SUCCESS'],$ajax);

	}
}
?>