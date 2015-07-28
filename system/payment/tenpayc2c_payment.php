<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'财付通中介担保支付',
	'tencentpay_id'	=>	'商户ID',
	'tencentpay_key'	=>	'商户密钥',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'GO_TO_PAY'	=>	'前往财付通支付',
);
$config = array(
	'tencentpay_id'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户ID
	'tencentpay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户密钥
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'tenpayc2c';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'http://union.tenpay.com/mch/mch_register.shtml?sp_suggestuser=222359';

    return $module;
}

// 余额支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class tenpayc2c_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=tenpayc2c';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=tenpayc2c';
		//$data_notify_url = SITE_DOMAIN.APP_ROOT."/tenpay.php";
		
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/RequestHandler.class.php";
		$out_trade_no = $payment_notice['notice_sn'];
		$total_fee = $money*100;
		
		
		$today = to_date($payment_notice['create_time'],'YmdHis');
		
		/* 创建支付请求对象 */
		$reqHandler = new RequestHandler();
		$reqHandler->init();
		$reqHandler->setKey($payment_info['config']['tencentpay_key']);
		$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
		
		//----------------------------------------
		//设置支付参数 
		//----------------------------------------
		$reqHandler->setParameter("partner", $payment_info['config']['tencentpay_id']);
		$reqHandler->setParameter("out_trade_no", $out_trade_no);
		$reqHandler->setParameter("total_fee", $total_fee);  //总金额
		$reqHandler->setParameter("return_url",  $data_return_url);
		$reqHandler->setParameter("notify_url", $data_notify_url);
		$reqHandler->setParameter("body", $order_sn);
		$reqHandler->setParameter("bank_type", "DEFAULT");  	  //银行类型，默认为财付通
		//用户ip
		$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
		$reqHandler->setParameter("fee_type", "1");               //币种
		$reqHandler->setParameter("subject",$order_sn);          //商品名称，（中介交易时必填）
		
		//系统可选参数
		$reqHandler->setParameter("sign_type", "MD5");  	 	  //签名方式，默认为MD5，可选RSA
		$reqHandler->setParameter("service_version", "1.0"); 	  //接口版本号
		$reqHandler->setParameter("input_charset", "UTF-8");   	  //字符集
		$reqHandler->setParameter("sign_key_index", "1");    	  //密钥序号
		
		//业务可选参数
		$reqHandler->setParameter("attach", "");             	  //附件数据，原样返回就可以了
		$reqHandler->setParameter("product_fee", "");        	  //商品费用
		$reqHandler->setParameter("transport_fee", "0");      	  //物流费用
		$reqHandler->setParameter("time_start",$today);  //订单生成时间
		$reqHandler->setParameter("time_expire", "");             //订单失效时间
		$reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
		$reqHandler->setParameter("goods_tag", "");               //商品标记
		$reqHandler->setParameter("trade_mode","2");              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
		$reqHandler->setParameter("transport_desc","");              //物流说明
		$reqHandler->setParameter("trans_type","1");              //交易类型
		$reqHandler->setParameter("agentid","");                  //平台ID
		$reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
		$reqHandler->setParameter("seller_id","");                //卖家的商户号
		
		
		$reqUrl = $reqHandler->getRequestURL();
		
		$payLinks = '<div style="margin:0px;padding:0px" >';

	 	
    	if(!empty($payment_info['logo']))
		{
			$payLinks .= "<input type='image' src='".APP_ROOT.$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
		}
		$payLinks .= "<input type='submit' class='paybutton' value='前往财付通支付' onclick='window.open(\"".$reqUrl."\");'></div>";
        $code = '<div style="text-align:center">'.$payLinks.'</div>';
		$code.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $code;
	}
	
	public function response($request)
	{
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/ResponseHandler.class.php";
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='tenpayc2c'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
    	/* 创建支付应答对象 */
		$resHandler = new ResponseHandler();
		$resHandler->setKey($payment['config']['tencentpay_key']);

    	
		if($resHandler->isTenpaySign()) {
		
			
			//通知id
			$notify_id = $resHandler->getParameter("notify_id");
			//商户订单号
			$out_trade_no = $resHandler->getParameter("out_trade_no");
			
			//财付通订单号
			$transaction_id = $resHandler->getParameter("transaction_id");
			//金额,以分为单位
			$total_fee = $resHandler->getParameter("total_fee");
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $resHandler->getParameter("discount");
			//支付结果
			$trade_state = $resHandler->getParameter("trade_state");
			//交易模式,1即时到账
			$trade_mode = $resHandler->getParameter("trade_mode");
			
			$status = $resHandler->getParameter("status");	

			if( "0" == $trade_state||$status=="3"){ 
					//------------------------------
					//处理业务开始
					//------------------------------
					
					//注意交易单不要重复处理
					//注意判断返回金额
					
					//------------------------------
					//处理业务完毕
					//------------------------------	
			
					$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
					if(!$payment_notice)
					$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where outer_notice_sn = '".$transaction_id."'");

					if(!$payment_notice)
					{
						showErr("请等待支付接口通知结果");
					}
					
					require_once APP_ROOT_PATH."system/libs/cart.php";
					$rs = payment_paid($payment_notice['id'],$transaction_id);						
			
					$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
					if ($is_paid == 1){
						app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
					}else{
						app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
					}
			
			} else {
					//当做不成功处理
					showErr("支付失败");
			}
			
			
		} else {
//			echo "<br/>" . "认证签名失败" . "<br/>";
//			echo $resHandler->getDebugInfo() . "<br>";
			showErr("认证签名失败");
		}
		
	}
	
	public function notify($request)
	{
//		$url = "http://o2o.7dit.com/tenpay.php?";
//		foreach($_REQUEST as $k=>$v)
//		{
//			$url.=$k."=".$v."&";
//		}
//		$str = file_get_contents(APP_ROOT_PATH."log.txt")."\n".print_r($_REQUEST,1)."\n".$url;
//		@file_put_contents(APP_ROOT_PATH."log.txt", $str);
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/ResponseHandler.class.php";
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/RequestHandler.class.php";
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/TenpayHttpClient.class.php";
		require_once APP_ROOT_PATH."system/payment/tenpayc2c/ClientResponseHandler.class.php";
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='tenpayc2c'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
    	/* 创建支付应答对象 */
		$resHandler = new ResponseHandler();
		$resHandler->setKey($payment['config']['tencentpay_key']);
		
		
	//判断签名
		if($resHandler->isTenpaySign()) 
		{
	
		//通知id
//		error_reporting(E_ALL);
//		ini_set("display_errors",1);
		$notify_id = $resHandler->getParameter("notify_id");

		//通过通知ID查询，确保通知来至财付通
		//创建查询请求
		$queryReq = new RequestHandler();
		$queryReq->init();
		$queryReq->setKey($payment['config']['tencentpay_key']);
		$queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
		$queryReq->setParameter("partner", $payment['config']['tencentpay_id']);
		$queryReq->setParameter("notify_id", $notify_id);
		
		//通信对象
		$httpClient = new TenpayHttpClient();
		$httpClient->setTimeOut(5);
		//设置请求内容
		$httpClient->setReqContent($queryReq->getRequestURL());
	
		//后台调用
		if($httpClient->call()) {
			//设置结果参数
			$queryRes = new ClientResponseHandler();
			$queryRes->setContent($httpClient->getResContent());
			$queryRes->setKey($payment['config']['tencentpay_key']);
	
			if ($resHandler->getParameter("trade_mode") == "2")		
		    {
		   		//判断签名及结果（中介担保）
				//只有签名正确,retcode为0，trade_state为0才是支付成功
//				print_r($queryRes);
				if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" ) 
				{
					//取结果参数做业务处理
					$out_trade_no = $resHandler->getParameter("out_trade_no");
					//财付通订单号
					$transaction_id = $resHandler->getParameter("transaction_id");
					//金额,以分为单位
					$total_fee = $resHandler->getParameter("total_fee");
					//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
					$discount = $resHandler->getParameter("discount");
					
					//------------------------------
					//处理业务开始
					//------------------------------
						
					//处理数据库逻辑
					//注意交易单不要重复处理
					//注意判断返回金额
		

				
					if ($resHandler->getParameter("trade_state")=="0"||$resHandler->getParameter("trade_state")=='5'||$resHandler->getParameter("status") == "3") 
					{
						$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$out_trade_no."'");
						$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
						require_once APP_ROOT_PATH."system/libs/cart.php";
						//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$transaction_id."' where id = ".$payment_notice['id']);
						$rs = payment_paid($payment_notice['id'],$transaction_id);						
						if($rs)
						{
							order_paid($payment_notice['order_id']);
							echo "success";
							
						}
						else
						{
							echo 'fail';
						}
					}
					else
					{
						echo "success";
					}
					
				 } 
				 else
			     {
					//错误时，返回结果可能没有签名，写日志trade_state、retcode、retmsg看失败详情。
					//echo "验证签名失败 或 业务错误信息:trade_state=" . $resHandler->getParameter("trade_state") . ",retcode=" . $queryRes->             										       getParameter("retcode"). ",retmsg=" . $queryRes->getParameter("retmsg") . "<br/>" ;
					echo "fail";
				 }
			  }
		
		
		
			//获取查询的debug信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
			/*
				echo "<br>------------------------------------------------------<br>";
				echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
				echo "query req:" . htmlentities($queryReq->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
				echo "query res:" . htmlentities($queryRes->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
				echo "query reqdebug:" . $queryReq->getDebugInfo() . "<br><br>" ;
				echo "query resdebug:" . $queryRes->getDebugInfo() . "<br><br>";
				*/
			}		
			else
			{
				//通信失败
				echo "fail";
				//后台调用通信失败,写日志，方便定位问题
				echo "<br>call err:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	 		} 
	
	
   		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo "<br/>" . "认证签名失败" . "<br/>";
   			echo $resHandler->getDebugInfo() . "<br>";
		}
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='tenpayc2c'");
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