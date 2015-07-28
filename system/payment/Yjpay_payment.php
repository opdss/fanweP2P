<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

/*
// 商户编号
$merchantaccount = 'YB01000000144';
// 商户私钥
$merchantPrivateKey = 'MIICdQIBADANBgkqhkiG9w0BAQEFAASCAl8wggJbAgEAAoGBAPGE6DHyrUUAgqep/oGqMIsrJddJNFI1J/BL01zoTZ9+YiluJ7I3uYHtepApj+Jyc4Hfi+08CMSZBTHi5zWHlHQCl0WbdEkSxaiRX9t4aMS13WLYShKBjAZJdoLfYTGlyaw+tm7WG/MR+VWakkPX0pxfG+duZAQeIDoBLVfL++ihAgMBAAECgYAw2urBV862+5BybA/AmPWy4SqJbxR3YKtQj3YVACTbk4w1x0OeaGlNIAW/7bheXTqCVf8PISrA4hdL7RNKH7/mhxoX3sDuCO5nsI4Dj5xF24CymFaSRmvbiKU0Ylso2xAWDZqEs4Le/eDZKSy4LfXA17mxHpMBkzQffDMtiAGBpQJBAPn3mcAwZwzS4wjXldJ+Zoa5pwu1ZRH9fGNYkvhMTp9I9cf3wqJUN+fVPC6TIgLWyDf88XgFfjilNKNz0c/aGGcCQQD3WRxwots1lDcUhS4dpOYYnN3moKNgB07Hkpxkm+bw7xvjjHqI8q/4Jiou16eQURG+hlBZlZz37Y7P+PHF2XG3AkAyng/1WhfUAfRVewpsuIncaEXKWi4gSXthxrLkMteM68JRfvtb0cAMYyKvr72oY4Phyoe/LSWVJOcW3kIzW8+rAkBWekhQNRARBnXPbdS2to1f85A9btJP454udlrJbhxrBh4pC1dYBAlz59v9rpY+Ban/g7QZ7g4IPH0exzm4Y5K3AkBjEVxIKzb2sPDe34Aa6Qd/p6YpG9G6ND0afY+m5phBhH+rNkfYFkr98cBqjDm6NFhT7+CmRrF903gDQZmxCspY';
// 商户公钥
$merchantPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDxhOgx8q1FAIKnqf6BqjCLKyXXSTRSNSfwS9Nc6E2ffmIpbieyN7mB7XqQKY/icnOB34vtPAjEmQUx4uc1h5R0ApdFm3RJEsWokV/beGjEtd1i2EoSgYwGSXaC32ExpcmsPrZu1hvzEflVmpJD19KcXxvnbmQEHiA6AS1Xy/vooQIDAQAB';
// 易宝公钥
$yeepayPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCxnYJL7fH7DVsS920LOqCu8ZzebCc78MMGImzW8MaP/cmBGd57Cw7aRTmdJxFD6jj6lrSfprXIcT7ZXoGL5EYxWUTQGRsl4HZsr1AlaOKxT5UnsuEhA/K1dN1eA4lBpNCRHf9+XDlmqVBUguhNzy6nfNjb2aGE+hkxPP99I1iMlQIDAQAB';
*/

$payment_lang = array(
	'name'	=>	'易宝一键支付pc',
	'merchantaccount'	=>	'商户编号',
	'merchantPrivateKey'=>	'商户私钥',
	'merchantPublicKey'	=>	'商户公钥',
	'yeepayPublicKey'	=>	'易宝公钥',
);

$config = array(
	'merchantaccount'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户编号
	'merchantPrivateKey'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户私钥
	'merchantPublicKey'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户公钥
	'yeepayPublicKey'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //易宝公钥			
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Yjpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'http://www.yeepay.com/';
    
    return $module;
}

// 易宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Yjpay_payment implements payment {
	
	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,user_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		
		$order_sn = $payment_notice['notice_sn'];
		$user_id = $payment_notice['user_id'];
		 
		include("yeepay/yeepayMPay.php");
		

		$yeepay = new yeepayMPay($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPublicKey'],$payment_info['config']['merchantPrivateKey'],$payment_info['config']['yeepayPublicKey']);
		//print_r($payment_info['config']);exit;
		
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Yjpay';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Yjpay';
		
		
		$order_id = $payment_notice['notice_sn'];//网页支付的订单在订单有效期内可以进行多次支付请求，但是需要注意的是每次请求的业务参数都要一致，交易时间也要保持一致。否则会报错“订单与已存在的订单信息不符”
		$transtime = get_gmtime();// time();//交易时间，是每次支付请求的时间，注意此参数在进行多次支付的时候要保持一致。
		$product_catalog ='1';//商品类编码是我们业管根据商户业务本身的特性进行配置的业务参数。
		$identity_id = $user_id;//用户身份标识，是生成绑卡关系的因素之一，在正式环境此值不能固定为一个，要一个用户有唯一对应一个用户标识，以防出现盗刷的风险且一个支付身份标识只能绑定5张银行卡
		$identity_type = 2;     //支付身份标识类型码
		$user_ip = CLIENT_IP; //此参数不是固定的商户服务器ＩＰ，而是用户每次支付时使用的网络终端IP，否则的话会有不友好提示：“检测到您的IP地址发生变化，请注意支付安全”。
		$user_ua =  $_SERVER['HTTP_USER_AGENT'];//'NokiaN70/3.0544.5.1 Series60/2.8 Profile/MIDP-2.0 Configuration/CLDC-1.1';//用户ua
		$callbackurl = $data_notify_url;//商户后台系统回调地址，前后台的回调结果一样
		$fcallbackurl = $data_return_url;//商户前台系统回调地址，前后台的回调结果一样
		$product_name = '订单号-'.$order_sn;//出于风控考虑，请按下面的格式传递值：应用-商品名称，如“诛仙-3 阶成品天琊”
		$product_desc = '';//商品描述
		$terminaltype = 3;
		$terminalid = '';//其他支付身份信息
		$amount = $money * 100;//订单金额单位为分，支付时最低金额为2分，因为测试和生产环境的商户都有手续费（如2%），易宝支付收取手续费如果不满1分钱将按照1分钱收取。
		
		
		$url = $yeepay->pcWebPay($order_id,$transtime,$amount,$product_catalog,$identity_id,$identity_type,$user_ip,$user_ua,$callbackurl,$fcallbackurl,$currency=156,$product_name,$product_desc,$terminaltype,$terminalid,$orderexp_date=60);
		
		$arr = explode("&",$url);
		$encrypt = explode("=",$arr[1]);
		$data = explode("=",$arr[2]);
		$code = "<a target='' href=$url>一键支付</a>";
		$code.="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</span>";
		return $code;
		
		
			

	}
	
	public function response($request)
	{

		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Yjpay'");  
    	$payment_info['config'] = unserialize($payment['config']);
    	
    	include("yeepay/yeepayMPay.php");
    	
    	/**
    	 *此类文件是有关回调的数据处理文件，根据易宝回调进行数据处理
    	
    	 */
		$yeepay = new yeepayMPay($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPublicKey'],$payment_info['config']['merchantPrivateKey'],$payment_info['config']['yeepayPublicKey']);	
    	try {
    		$return = $yeepay->callback($_POST['data'], $_POST['encryptkey']);
    		// TODO:添加订单处理逻辑代码
    		/*
    		名称 	中文说明 	数据类型 	描述
    		merchantaccount 	商户账户 	string
    		yborderid 	易宝交易流水号 	string
    		orderid 	交易订单 	String
    		amount 	支付金额 	int 	以“分”为单位的整型
    		bankcode 	银行编码 	string 	支付卡所属银行的编码，如ICBC
    		bank 	银行信息 	string 	支付卡所属银行的名称
    		cardtype 	卡类型 	int 	支付卡的类型，1为借记卡，2为信用卡
    		lastno 	卡号后4位 	string 	支付卡卡号后4位
    		status 	订单状态 	int 	1：成功
    		*/
    		
    		$payment_notice_sn = $return['orderid'];
    		$money = intval($return['amount']/100);
    		$outer_notice_sn = $return['yborderid'];
    		
    		if ($return['amount'] == 1){   		
	    		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
	    		require_once APP_ROOT_PATH."system/libs/cart.php";
	    		$rs = payment_paid($payment_notice['id'],$outer_notice_sn);
    		
	    		$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
				if ($is_paid == 1){
					app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
				}else{
					app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
				}
    		}else{
    			showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
    		}
    	}catch (yeepayMPayException $e) {
    		// TODO：添加订单支付异常逻辑代码
    		showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
    	} 
	}
	
	public function notify($request)
	{
		
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Yjpay'");  
    	$payment_info['config'] = unserialize($payment['config']);
    	
    	include("yeepay/yeepayMPay.php");
    	
    	/**
    	 *此类文件是有关回调的数据处理文件，根据易宝回调进行数据处理
    	
    	 */
		$yeepay = new yeepayMPay($payment_info['config']['merchantaccount'],$payment_info['config']['merchantPublicKey'],$payment_info['config']['merchantPrivateKey'],$payment_info['config']['yeepayPublicKey']);	
    	try {
    		$return = $yeepay->callback($_POST['data'], $_POST['encryptkey']);
    		// TODO:添加订单处理逻辑代码
    		/*
    		名称 	中文说明 	数据类型 	描述
    		merchantaccount 	商户账户 	string
    		yborderid 	易宝交易流水号 	string
    		orderid 	交易订单 	String
    		amount 	支付金额 	int 	以“分”为单位的整型
    		bankcode 	银行编码 	string 	支付卡所属银行的编码，如ICBC
    		bank 	银行信息 	string 	支付卡所属银行的名称
    		cardtype 	卡类型 	int 	支付卡的类型，1为借记卡，2为信用卡
    		lastno 	卡号后4位 	string 	支付卡卡号后4位
    		status 	订单状态 	int 	1：成功
    		*/
    		
    		$payment_notice_sn = $return['orderid'];
    		$money = intval($return['amount']/100);
    		$outer_notice_sn = $return['yborderid'];
    		
    		if ($return['amount'] == 1){   		
	    		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
	    		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
	    		require_once APP_ROOT_PATH."system/libs/cart.php";
	    		$rs = payment_paid($payment_notice['id'],$outer_notice_sn);
	    		
	    		if($rs)
	    		{
	    			$rs = order_paid($payment_notice['order_id']);
	    			if($rs)
	    			{
	    				//开始更新相应的outer_notice_sn
	    				//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$outer_notice_sn."' where id = ".$payment_notice['id']);
	    				return 'success';
	    			}
	    			else
	    			{
	    				if($order_info['pay_status'] == 2)
	    				{
	    					return 'success';
	    				}
	    				else
	    					return 'fail';
	    			}
	    		}
	    		else
	    		{
	    			return 'fail';
	    		}
    		}else{
    			return 'fail';
    		}
    	}catch (yeepayMPayException $e) {
    		// TODO：添加订单支付异常逻辑代码
    		return 'fail';
    	} 
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Yjpay'");
		if($payment_item)
		{
			$html = "<label class='f_l ui-radiobox' rel='common_payment' style='background:url(".APP_ROOT.$payment_item['logo'].")' title='".$payment_item['name']."'>".
					"<input type='radio' name='payment' value='".$payment_item['id']."' />&nbsp;";
					if($payment_item['logo']==""){
						$html .=$payment_item['name'];
					}
					$html .= "</label>";
			return $html;
		}
		else
		{
			return '';
		}
	}	
}
?>