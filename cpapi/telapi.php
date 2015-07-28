<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//用于接收电话验证
//请求链接：
// 验证 http://xxxxx/cpapi/telapi.php?action=query&callerid=88888888&num=12345678&serialno=12345678
// 消费http://xxxxx/cpapi/telapi.php?action=consume&callerid=88888888&num=12345678&secret=12345678&serialno=12345678
require 'cp_init.php';

$action = strtolower(trim($_REQUEST['action'])); 
if($action=='query')
{
	//验证
	$callerid = addslashes(trim($_REQUEST['callerid']));  //来电电话
	$sn = addslashes(trim($_REQUEST['num']));  //团购券序列号
	$query_id = addslashes(trim($_REQUEST['serialno']));  //第三方队列ID
	
	$now = get_gmtime();
	$coupon_data = $GLOBALS['db']->getRow("select c.confirm_time,c.end_time,doi.name as name,c.sn as sn,doi.sub_name as sub_name,doi.unit_price as price from ".DB_PREFIX."deal_coupon as c left join ".DB_PREFIX."deal_order_item as doi on c.order_deal_id = doi.id where c.sn = '".$sn."' and c.is_valid = 1 and c.is_delete = 0 and c.begin_time <".$now);
	
	if($coupon_data)
	{
		if(($coupon_data['end_time']==0||$coupon_data['end_time']>$now)&&$coupon_data['confirm_time']==0)
		{
			$msg = $GLOBALS['lang']['COUPON_VERIFY_LOG'].":(".$callerid.")".sprintf($GLOBALS['lang']['COUPON_IS_VALID'],$coupon_data['sub_name'],$coupon_data['sn']);
			log_coupon($sn,$msg,$query_id);
			header('Content-type: text/xml; charset=utf-8');
			$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
			$xml.="<coupon>\r\n";
			$xml.="<result>3</result>\r\n";
			$xml.="<id>".$coupon_data['sn']."</id>\r\n";
			$xml.="<product>".$coupon_data['sub_name']."</product>\r\n";
			$xml.="<price>".round($coupon_data['price'],2)."</price>\r\n";
			$xml.="</coupon>";
			echo $xml;
			exit;
		}
		else
		{
			if($coupon_data['confirm_time']>0)
			{
				$msg = $GLOBALS['lang']['COUPON_VERIFY_LOG'].":(".$callerid.")".sprintf($GLOBALS['lang']['COUPON_INVALID_USED'],to_date($coupon_data['confirm_time']));;
				log_coupon($sn,$msg,$query_id);
				header('Content-type: text/xml; charset=utf-8');
				$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
				$xml.="<coupon>\r\n";
				$xml.="<result>1</result>\r\n";
				$xml.="<id></id>\r\n";
				$xml.="<product></product>\r\n";
				$xml.="<price></price>\r\n";
				$xml.="</coupon>";
				echo $xml;
				exit;
			}
			
			if($coupon_data['end_time']!=0&&$coupon_data['end_time']<$now)
			{
				$msg = $GLOBALS['lang']['COUPON_VERIFY_LOG'].":(".$callerid.")".$GLOBALS['lang']['COUPON_ENDED'];
				log_coupon($sn,$msg,$query_id);
				header('Content-type: text/xml; charset=utf-8');
				$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
				$xml.="<coupon>\r\n";
				$xml.="<result>2</result>\r\n";
				$xml.="<id></id>\r\n";
				$xml.="<product></product>\r\n";
				$xml.="<price></price>\r\n";
				$xml.="</coupon>";
				echo $xml;
				exit;
			}
		}
		
	}
	else
	{
		$msg = $GLOBALS['lang']['COUPON_VERIFY_LOG'].":(".$callerid.")".$GLOBALS['lang']['COUPON_INVALID'];
		log_coupon($sn,$msg,$query_id);
		header('Content-type: text/xml; charset=utf-8');
		$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
		$xml.="<coupon>\r\n";
		$xml.="<result>0</result>\r\n";
		$xml.="<id></id>\r\n";
		$xml.="<product></product>\r\n";
		$xml.="<price></price>\r\n";
		$xml.="</coupon>";
		echo $xml;
		exit;
	}	
}
elseif($action=='consume')
{
	//消费	
	$callerid = addslashes(trim($_REQUEST['callerid']));  //来电电话
	$sn = addslashes(trim($_REQUEST['num']));  //团购券序列号
	$query_id = addslashes(trim($_REQUEST['serialno']));  //第三方队列ID
	$pwd = addslashes(trim($_REQUEST['secret']));  //密码	
	$now = get_gmtime();

		$coupon_data = $GLOBALS['db']->getRow("select c.id as id,doi.name as name,doi.sub_name as sub_name,doi.unit_price as price,c.sn as sn,c.supplier_id as supplier_id,c.confirm_time as confirm_time from ".DB_PREFIX."deal_coupon as c left join ".DB_PREFIX."deal_order_item as doi on c.order_deal_id = doi.id where c.sn = '".$sn."' and c.password = '".$pwd."' and c.is_valid = 1 and c.is_delete = 0  and c.begin_time <".$now." and (c.end_time = 0 or c.end_time>".$now.")"); 
		if($coupon_data)
		{
			if($coupon_data['confirm_time'] > 0)
			{
				$msg = $GLOBALS['lang']['COUPON_USE_LOG'].":(".$callerid.")".sprintf($GLOBALS['lang']['COUPON_INVALID_USED'],to_date($coupon_data['confirm_time']));
				log_coupon($coupon_data['sn'],$msg,$query_id);
				header('Content-type: text/xml; charset=utf-8');
				$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
				$xml.="<coupon>\r\n";
				$xml.="<result>false</result>\r\n";
				$xml.="</coupon>";
				echo $xml;
				exit;
			}
			else
			{
				//开始确认
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set confirm_account = 999999,confirm_time=".$now." where id = ".intval($coupon_data['id']));
				$msg = $GLOBALS['lang']['COUPON_USE_LOG'].":(".$callerid.")".sprintf($GLOBALS['lang']['COUPON_USED_OK'],to_date($now));
				log_coupon($coupon_data['sn'],$msg,$query_id);
				send_use_coupon_sms(intval($coupon_data['id'])); //发送团购券确认消息
				send_use_coupon_mail(intval($coupon_data['id'])); //发送团购券确认消息
				header('Content-type: text/xml; charset=utf-8');
				$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
				$xml.="<coupon>\r\n";
				$xml.="<result>true</result>\r\n";
				$xml.="<product>".$coupon_data['sub_name']."</product>\r\n";
				$xml.="<price>".round($coupon_data['price'],2)."</price>\r\n";
				$xml.="</coupon>";
				echo $xml;
				exit;
				
			}
		}
		else
		{				
			$msg = $GLOBALS['lang']['COUPON_USE_LOG'].":(".$callerid.")".$GLOBALS['lang']['PASSWORD_ERROR'];
			log_coupon($sn,$msg,$query_id);
			header('Content-type: text/xml; charset=utf-8');
			$xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
			$xml.="<coupon>\r\n";
			$xml.="<result>false</result>\r\n";
			$xml.="</coupon>";
			echo $xml;
			exit;
		}
	
}
else
{
	header("Content-Type:text/html; charset=utf-8");
	echo "非法访问";
	exit;
}
?>