<?php
// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------
$payment_lang = array(
	'name'	=>	'通联支付',
	'merchant_id'	=>	'商户号',
	'md5_key'		=>	'MD5校验码',
	'public_exponent'		=>	'公钥exponent',
	'public_modulus'		=>	'公钥modulus',
);

$config = array( 
	'merchant_id'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户号:
	'md5_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码

		'public_exponent'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), //公钥exponent
		'public_modulus'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), //公钥modulus
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == TRUE)
{
    $module['class_name']    = 'Allinpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';
    
     /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
       
    /* 插件作者的官方网站 */
    $module['reg_url'] = 'http://www.fanwe.com';

    return $module;
}

// 贝宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Allinpay_payment implements payment {
	
	public function get_payment_code($payment_notice_id)
	{
		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
				
				
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Allinpay';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Allinpay';
		
		$merchant_acctid    = trim($payment_info['config']['merchant_id']);           //人民币账号 不可空
		$key                = trim($payment_info['config']['md5_key']);

		//页面编码要与参数inputCharset一致，否则服务器收到参数值中的汉字为乱码而导致验证签名失败。
		//$serverUrl= 'http://ceshi.allinpay.com/gateway/index.do';//提交地址 https://service.allinpay.com/gateway/index.do;
		$serverUrl= 'https://service.allinpay.com/gateway/index.do';
		
		$inputCharset=1;//1. 字符集:
		$pickupUrl=  $data_return_url;//2. 取货地址:
		$receiveUrl= $data_notify_url;//3. 商户系统通知地址:
		$version='v1.0';//4. 版本号:
		$language='';//5. 语言:
		$signType=1;//6. 签名类型:
		$merchantId= $merchant_acctid;//7. 商户号:
		$payerName='';//8. 付款人姓名:
		$payerEmail='';//9. 付款人联系email:
		$payerTelephone='';//10. 付款人电话:
		$payerIDCard='';//11. 付款人证件号:
		$pid='';//12. 合作伙伴商户号:
		$orderNo= $payment_notice['notice_sn'];//13. 商户系统订单号:
		$orderAmount= $money * 100;//14. 订单金额(单位分):
		$orderCurrency='';//15. 订单金额币种类型:
		$orderDatetime= to_date(TIME_UTC, 'YmdHis');//16. 商户的订单提交时间:
		
		$orderExpireDatetime='';//17. 订单过期时间:
		$productName='';//18. 商品名称:
		$productId='';//19. 商品单价:
		$productPrice='';//20. 商品数量:
		$productNum='';//21. 商品标识:
		$productDesc='';//22. 商品描述:
		$ext1='';//23. 扩展字段1:
		$ext2='';//24. 扩展字段2:
		$extTL='';//25. 业务扩展字段:
		$payType=0; //26. 支付方式: payType   不能为空，必须放在表单中提交。
		$issuerId=''; //27. 发卡方代码: issueId 直联时不为空，必须放在表单中提交。
		$pan='';//28. 付款人支付卡号:
		$tradeNature='GOODS';//29. 贸易类型:
		//$key=$key;
		
		//报文参数有消息校验
		//if(preg_match("/\d/",$pickupUrl)){
		//echo "<script>alert('pickupUrl有误！！');history.back();</script>";
		//}
		
		// 生成签名字符串。
		$bufSignSrc="";
		if($inputCharset != "")
			$bufSignSrc=$bufSignSrc."inputCharset=".$inputCharset."&";
		if($pickupUrl != "")
			$bufSignSrc=$bufSignSrc."pickupUrl=".$pickupUrl."&";
		if($receiveUrl != "")
			$bufSignSrc=$bufSignSrc."receiveUrl=".$receiveUrl."&";
		if($version != "")
			$bufSignSrc=$bufSignSrc."version=".$version."&";
		if($language != "")
			$bufSignSrc=$bufSignSrc."language=".$language."&";
		if($signType != "")
			$bufSignSrc=$bufSignSrc."signType=".$signType."&";
		if($merchantId != "")
			$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";
		if($payerName != "")
			$bufSignSrc=$bufSignSrc."payerName=".$payerName."&";
		if($payerEmail != "")
			$bufSignSrc=$bufSignSrc."payerEmail=".$payerEmail."&";
		if($payerTelephone != "")
			$bufSignSrc=$bufSignSrc."payerTelephone=".$payerTelephone."&";
		if($payerIDCard != "")
			$bufSignSrc=$bufSignSrc."payerIDCard=".$payerIDCard."&";
		if($pid != "")
			$bufSignSrc=$bufSignSrc."pid=".$pid."&";
		if($orderNo != "")
			$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
		if($orderAmount != "")
			$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
		if($orderCurrency != "")
			$bufSignSrc=$bufSignSrc."orderCurrency=".$orderCurrency."&";
		if($orderDatetime != "")
			$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
		if($orderExpireDatetime != "")
			$bufSignSrc=$bufSignSrc."orderExpireDatetime=".$orderExpireDatetime."&";
		if($productName != "")
			$bufSignSrc=$bufSignSrc."productName=".$productName."&";
		if($productPrice != "")
			$bufSignSrc=$bufSignSrc."productPrice=".$productPrice."&";
		if($productNum != "")
			$bufSignSrc=$bufSignSrc."productNum=".$productNum."&";
		if($productId != "")
			$bufSignSrc=$bufSignSrc."productId=".$productId."&";
		if($productDesc != "")
			$bufSignSrc=$bufSignSrc."productDesc=".$productDesc."&";
		if($ext1 != "")
			$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
		if($ext2 != "")
			$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
		if($extTL != "")
			$bufSignSrc=$bufSignSrc."extTL".$extTL."&";
		//if($payType != "")
			$bufSignSrc=$bufSignSrc."payType=".$payType."&";
		if($issuerId != "")
			$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
		if($pan != "")
			$bufSignSrc=$bufSignSrc."pan=".$pan."&";
		if($tradeNature != "")
			$bufSignSrc=$bufSignSrc."tradeNature=".$tradeNature."&";
		$bufSignSrc=$bufSignSrc."key=".$key; //key为MD5密钥，密钥是在通联支付网关商户服务网站上设置。
		
		//签名，设为signMsg字段值。
		$signMsg = strtoupper(md5($bufSignSrc));
		
        $def_url  = '<div style="text-align:center"><form name="form2" style="text-align:center;" method="post" action='.$serverUrl.' target="_blank">';
        
        $def_url .= "<input type='hidden' name='inputCharset' id='inputCharset'  value='" . $inputCharset . "' />";        
        $def_url .= "<input type='hidden' name='pickupUrl' id='pickupUrl'  value='" . $pickupUrl . "' />";
        $def_url .= "<input type='hidden' name='receiveUrl' id='receiveUrl'  value='" . $receiveUrl . "' />";
        
        $def_url .= "<input type='hidden' name='version' id='version'  value='" . $version . "' />";
        $def_url .= "<input type='hidden' name='language' id='language'  value='" . $language . "' />";               
        $def_url .= "<input type='hidden' name='signType' id='signType'  value='" . $signType . "' />";
        
        $def_url .= "<input type='hidden' name='merchantId' id='merchantId'  value='" . $merchantId . "' />";
        $def_url .= "<input type='hidden' name='payerName' id='payerName'  value='" . $payerName . "' />";        
        $def_url .= "<input type='hidden' name='payerEmail' id='payerEmail'  value='" . $payerEmail . "' />";   
             
        $def_url .= "<input type='hidden' name='payerTelephone' id='payerTelephone'  value='" . $payerTelephone . "' />";
        $def_url .= "<input type='hidden' name='payerIDCard' id='payerIDCard'  value='" . $payerIDCard . "' />";
        $def_url .= "<input type='hidden' name='pid' id='pid'  value='" . $pid . "' />";      
                  
        $def_url .= "<input type='hidden' name='orderNo' id='orderNo'  value='" . $orderNo . "' />";
        $def_url .= "<input type='hidden' name='orderAmount' id='orderAmount'  value='" . $orderAmount . "' />";
        $def_url .= "<input type='hidden' name='orderCurrency' id='orderCurrency'  value='" . $orderCurrency . "' />"; 
               
        $def_url .= "<input type='hidden' name='orderDatetime' id='orderDatetime'  value='" . $orderDatetime . "' />";
        $def_url .= "<input type='hidden' name='orderExpireDatetime' id='orderExpireDatetime'  value='" . $orderExpireDatetime . "' />";        
        $def_url .= "<input type='hidden' name='productName' id='productName'  value='" . $productName . "' />";
        
        $def_url .= "<input type='hidden' name='productPrice' id='productPrice'  value='" . $productPrice . "' />";        
        $def_url .= "<input type='hidden' name='productNum' id='productNum'  value='" . $productNum . "' />";
        $def_url .= "<input type='hidden' name='productId' id='productId'  value='" . $productId . "' />";
        
        $def_url .= "<input type='hidden' name='productDesc' id='productDesc'  value='" . $productDesc . "' />";
        $def_url .= "<input type='hidden' name='ext1' id='ext1'  value='" . $ext1 . "' />";
        $def_url .= "<input type='hidden' name='ext2' id='ext2'  value='" . $ext2 . "' />";
        
        $def_url .= "<input type='hidden' name='extTL' id='extTL'  value='" . $extTL . "' />";
        $def_url .= "<input type='hidden' name='payType' id='payType'  value='" . $payType . "' />";
        $def_url .= "<input type='hidden' name='issuerId' id='issuerId'  value='" . $issuerId . "' />";  
              
        $def_url .= "<input type='hidden' name='pan' id='pan'  value='" . $pan . "' />";
        $def_url .= "<input type='hidden' name='tradeNature' id='tradeNature'  value='" . $tradeNature . "' />";
        $def_url .= "<input type='hidden' name='signMsg' id='signMsg'  value='" . $signMsg . "' />";     
           
        $def_url .= "<input type='submit' name='submit' class='paybutton' value='确认付款，到通联支付去啦' />";
        $def_url .= "</form></div></br>";
		$def_url .="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</span>";
        return $def_url;	
	}
	
	public function get_display_code(){
    	$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Allinpay'");
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
	
	public function response($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
    	
		
		//file_put_contents("./system/payment/log/response_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($request,true));
		
        //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Allinpay'");  
    	$payment['config'] = unserialize($payment['config']);

    	//print_r($payment['config']);exit;
    	
		$merchant_acctid    = trim($payment['config']['merchant_id']);           //人民币账号 不可空
		$key                = trim($payment['config']['md5_key']);
		
		
		$merchantId=$request["merchantId"];
		$version=$request['version'];
		$language=$request['language'];
		$signType=$request['signType'];
		$payType=$request['payType'];
		$issuerId=$request['issuerId'];
		$paymentOrderId=$request['paymentOrderId'];
		$orderNo=$request['orderNo'];
		$orderDatetime=$request['orderDatetime'];
		$orderAmount=$request['orderAmount'];
		$payDatetime=$request['payDatetime'];
		$payAmount=$request['payAmount'];
		$ext1=$request['ext1'];
		$ext2=$request['ext2'];
		$payResult=$request['payResult'];
		$errorCode=$request['errorCode'];
		$returnDatetime=$request['returnDatetime'];
		$signMsg=$request["signMsg"];
		
		
		$bufSignSrc="";
		if($merchantId != "")
			$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";
		if($version != "")
			$bufSignSrc=$bufSignSrc."version=".$version."&";
		if($language != "")
			$bufSignSrc=$bufSignSrc."language=".$language."&";
		if($signType != "")
			$bufSignSrc=$bufSignSrc."signType=".$signType."&";
		if($payType != "")
			$bufSignSrc=$bufSignSrc."payType=".$payType."&";
		if($issuerId != "")
			$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
		if($paymentOrderId != "")
			$bufSignSrc=$bufSignSrc."paymentOrderId=".$paymentOrderId."&";
		if($orderNo != "")
			$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
		if($orderDatetime != "")
			$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
		if($orderAmount != "")
			$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
		if($payDatetime != "")
			$bufSignSrc=$bufSignSrc."payDatetime=".$payDatetime."&";
		if($payAmount != "")
			$bufSignSrc=$bufSignSrc."payAmount=".$payAmount."&";
		if($ext1 != "")
			$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
		if($ext2 != "")
			$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
		if($payResult != "")
			$bufSignSrc=$bufSignSrc."payResult=".$payResult."&";
		if($errorCode != "")
			$bufSignSrc=$bufSignSrc."errorCode=".$errorCode."&";
		if($returnDatetime != "")
			$bufSignSrc=$bufSignSrc."returnDatetime=".$returnDatetime;
		
		/*
		//验签
		//解析publickey.txt文本获取公钥信息
		$publickeycontent = trim($payment['config']['public_key']);
		//echo "<br>".$content;
		$publickeyarray = explode(PHP_EOL, $publickeycontent);
		$publickey = explode('=',$publickeyarray[0]);
		$modulus = explode('=',$publickeyarray[1]);
		//echo "<br>publickey=".$publickey[1];
		//echo "<br>modulus=".$modulus[1];
		*/
		$publickey = trim($payment['config']['public_exponent']);
		$modulus = trim($payment['config']['public_modulus']);
		
		
		require_once APP_ROOT_PATH."system/payment/Allinpay/php_rsa.php";
		
		$keylength = 1024;
		//验签结果
		//$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey[1], $modulus[1], $keylength,"sha1");
		$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey, $modulus, $keylength,"sha1");
	/*	
		echo 'bufSignSrc:'.$bufSignSrc."<br>";
	echo 'signMsg:'.$signMsg."<br>";
	echo 'publickey:'.$publickey."<br>";
	echo 'modulus:'.$modulus."<br>";
	
	if($verifyResult){	
		echo "报文验签成功!";
	}else{
		echo "报文验签失败!";
	}
	exit;
		*/
		if($verifyResult){
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$orderNo."'");
				
			require_once APP_ROOT_PATH."system/libs/cart.php";
			
			$rs = payment_paid($payment_notice['id'],$paymentOrderId);						
            	
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
			}else{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
		}else{
			showErr("因报文验签失败，订单支付失败!");
		}			
	}
	
	public function notify($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
    	
		//file_put_contents("./system/payment/log/notify_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($request,true));
		
	  //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Allinpay'");  
    	$payment['config'] = unserialize($payment['config']);

    	//print_r($payment['config']);exit;
    	
		$merchant_acctid    = trim($payment['config']['merchant_id']);           //人民币账号 不可空
		$key                = trim($payment['config']['md5_key']);
		
		
		$merchantId=$request["merchantId"];
		$version=$request['version'];
		$language=$request['language'];
		$signType=$request['signType'];
		$payType=$request['payType'];
		$issuerId=$request['issuerId'];
		$paymentOrderId=$request['paymentOrderId'];
		$orderNo=$request['orderNo'];
		$orderDatetime=$request['orderDatetime'];
		$orderAmount=$request['orderAmount'];
		$payDatetime=$request['payDatetime'];
		$payAmount=$request['payAmount'];
		$ext1=$request['ext1'];
		$ext2=$request['ext2'];
		$payResult=$request['payResult'];
		$errorCode=$request['errorCode'];
		$returnDatetime=$request['returnDatetime'];
		$signMsg=$request["signMsg"];
		
		
		$bufSignSrc="";
		if($merchantId != "")
			$bufSignSrc=$bufSignSrc."merchantId=".$merchantId."&";
		if($version != "")
			$bufSignSrc=$bufSignSrc."version=".$version."&";
		if($language != "")
			$bufSignSrc=$bufSignSrc."language=".$language."&";
		if($signType != "")
			$bufSignSrc=$bufSignSrc."signType=".$signType."&";
		if($payType != "")
			$bufSignSrc=$bufSignSrc."payType=".$payType."&";
		if($issuerId != "")
			$bufSignSrc=$bufSignSrc."issuerId=".$issuerId."&";
		if($paymentOrderId != "")
			$bufSignSrc=$bufSignSrc."paymentOrderId=".$paymentOrderId."&";
		if($orderNo != "")
			$bufSignSrc=$bufSignSrc."orderNo=".$orderNo."&";
		if($orderDatetime != "")
			$bufSignSrc=$bufSignSrc."orderDatetime=".$orderDatetime."&";
		if($orderAmount != "")
			$bufSignSrc=$bufSignSrc."orderAmount=".$orderAmount."&";
		if($payDatetime != "")
			$bufSignSrc=$bufSignSrc."payDatetime=".$payDatetime."&";
		if($payAmount != "")
			$bufSignSrc=$bufSignSrc."payAmount=".$payAmount."&";
		if($ext1 != "")
			$bufSignSrc=$bufSignSrc."ext1=".$ext1."&";
		if($ext2 != "")
			$bufSignSrc=$bufSignSrc."ext2=".$ext2."&";
		if($payResult != "")
			$bufSignSrc=$bufSignSrc."payResult=".$payResult."&";
		if($errorCode != "")
			$bufSignSrc=$bufSignSrc."errorCode=".$errorCode."&";
		if($returnDatetime != "")
			$bufSignSrc=$bufSignSrc."returnDatetime=".$returnDatetime;
		
		/*
		//验签
		//解析publickey.txt文本获取公钥信息
		$publickeycontent = trim($payment['config']['public_key']);
		//echo "<br>".$content;
		$publickeyarray = explode(PHP_EOL, $publickeycontent);
		$publickey = explode('=',$publickeyarray[0]);
		$modulus = explode('=',$publickeyarray[1]);
		//echo "<br>publickey=".$publickey[1];
		//echo "<br>modulus=".$modulus[1];
		*/
		$publickey = trim($payment['config']['public_exponent']);
		$modulus = trim($payment['config']['public_modulus']);
		
		
		require_once APP_ROOT_PATH."system/payment/Allinpay/php_rsa.php";
		
		$keylength = 1024;
		//验签结果
		//$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey[1], $modulus[1], $keylength,"sha1");
		$verifyResult = rsa_verify($bufSignSrc,$signMsg, $publickey, $modulus, $keylength,"sha1");
	/*
		echo 'bufSignSrc:'.$bufSignSrc."<br>";
	echo 'signMsg:'.$signMsg."<br>";
	echo 'publickey:'.$publickey."<br>";
	echo 'modulus:'.$modulus."<br>";
	
	if($verifyResult){	
		echo "报文验签成功!";
	}else{
		echo "报文验签失败!";
	}
	exit;
		*/
		
		if($verifyResult){
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$orderNo."'");
				
			require_once APP_ROOT_PATH."system/libs/cart.php";
			
			$rs = payment_paid($payment_notice['id'],$paymentOrderId);						
            	
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				echo '1';
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
	}	
}
?>