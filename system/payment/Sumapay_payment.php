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
	'name'	=>	'丰付支付',
	'trade_process'	=>	'商户代码',
	'total_biz_type'	=>	'业务类型',	
		
	'mer_acct'	=>	'产品供应商的编码',
	'biz_type'	=>	'产品业务类型',
				
	'mer_key'		=>	'签名密钥',
		
	'sumapay_gateway'	=>	'支持的银行',
	'sumapay_gateway_0'	=>	'纯网关支付',
	'sumapay_gateway_icbc'	=>	'工商银行',
	'sumapay_gateway_abc'	=>	'农业银行',
	'sumapay_gateway_boc'	=>	'中国银行',		
	'sumapay_gateway_ccb'	=>	'建设银行',
	'sumapay_gateway_comm'	=>	'交通银行',
	'sumapay_gateway_ceb'	=>	'光大银行',
	'sumapay_gateway_hxb'	=>	'华夏银行',
	'sumapay_gateway_cmsb'	=>	'民生银行',
	'sumapay_gateway_cmb'	=>	'招商银行',
	'sumapay_gateway_spdb'	=>	'浦发银行',	
	'sumapay_gateway_cgb'	=>	'广发银行',
	'sumapay_gateway_cncb'	=>	'中信银行',
	'sumapay_gateway_pab'	=>	'平安银行',
	'sumapay_gateway_shb'	=>	'上海银行',
	'sumapay_gateway_psbc'	=>	'邮政储蓄',
	'sumapay_gateway_bjb'	=>	'北京银行',		
		
);

$config = array( 
	'trade_process'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户代码
		'total_biz_type'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), //业务类型	
		
		'mer_acct'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), //产品供应商的编码

		'biz_type'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), //产品业务类型
				
	'mer_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码	
	'sumapay_gateway' => array(
				'INPUT_TYPE'	=>	'3',
				'VALUES'	=>	array(
						'0', //纯网关
						'icbc', //工商银行
						'abc', //农业银行
						'boc', //中国银行
						'ccb', //建设银行
						'comm', //交通银行
						'ceb', //光大银行
						'hxb', //华夏银行						
						'cmsb', //民生银行						
						'cmb', //招商银行						
						'spdb', //浦发银行						
						'cgb', //广发银行						
						'cncb', //中信银行						
						'pab', //平安银行						
						'psbc', //邮政储蓄
						'shb', //上海银行						
						'bjb', //北京银行
				),
		),	
		
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == TRUE)
{
    $module['class_name']    = 'Sumapay';

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

require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Sumapay_payment implements payment {
	
	public function get_payment_code($payment_notice_id)
	{
		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
				
				
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Sumapay';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Sumapay';
		
		
		$requestId = $payment_notice['notice_sn'];//商户订单号
		$tradeProcess = trim($payment_info['config']['trade_process']);//商户代码，由支付系统为外部系统生成的唯一标示符
		$totalBizType = trim($payment_info['config']['total_biz_type']);//业务类型（商户支持的业务类型，由支付系统提供）
		
		$bank_id = $payment_notice['bank_id'];
		
		$totalPrice = $money;
		$backurl = $data_return_url;
		$returnurl = $data_return_url;
		$noticeurl = $data_notify_url;
		$description = '';
		$rnaName = '';
		$rnaIdNumber = '';
		$rnaMobilePhone = '';
		$goodsDesc = '';
		$userIdIdentity = '';
		$allowRePay = '';
		$rePayTimeOut = '';
		$bankCardType = '';
		
		$productId = $payment_notice_id;
		$productName = 'Online Payment';
		$fund = $money * 100;//产品定价（精确到分，不可为负）
		$merAcct = trim($payment_info['config']['mer_acct']);//'fanwe';//$_REQUEST['merAcct'];//产品供应商的编码，一般为商户代码
		$bizType = trim($payment_info['config']['biz_type']);//'BIZ00800';//$_REQUEST['bizType'];//产品业务类型
		$productNumber = 1;
		
		$merKey = trim($payment_info['config']['mer_key']);//$_REQUEST['merKey'];
		
		$sbOld = "";
		$sbOld = $sbOld . $requestId;
		$sbOld = $sbOld . $tradeProcess;
		$sbOld = $sbOld . $totalBizType;
		$sbOld = $sbOld . $totalPrice;
		$sbOld = $sbOld . $backurl;
		$sbOld = $sbOld . $returnurl;
		$sbOld = $sbOld . $noticeurl;
		$sbOld = $sbOld . $description;
		$signatrue = $this->HmacMd5($sbOld, $merKey);
		//$key=$key;
		
		//报文参数有消息校验
		//if(preg_match("/\d/",$pickupUrl)){
		//echo "<script>alert('pickupUrl有误！！');history.back();</script>";
		//}
		
        $def_url  = '<div style="text-align:center"><form name="form2" style="text-align:center;" method="post" action=https://www.sumapay.com/sumapay/unitivepay_bankPayForNoLoginUser target="_blank">';
        
        $def_url .= "<input type='hidden' name='requestId' id='requestId'  value='" . $requestId . "' />";        
        $def_url .= "<input type='hidden' name='tradeProcess' id='tradeProcess'  value='" . $tradeProcess . "' />";
        $def_url .= "<input type='hidden' name='mersignature' id='mersignature'  value='" . $signatrue . "' />";
        
        if ($bank_id != '0' && !empty($bank_id)){
        	$def_url .= "<input type='hidden' name='bankcode' id='bankcode'  value='" . $bank_id . "' />";
        }
        
        $def_url .= "<input type='hidden' name='totalBizType' id='totalBizType'  value='" . $totalBizType . "' />";
        $def_url .= "<input type='hidden' name='totalPrice' id='totalPrice'  value='" . $totalPrice . "' />";               
        $def_url .= "<input type='hidden' name='backurl' id='backurl'  value='" . $backurl . "' />";
        
        $def_url .= "<input type='hidden' name='returnurl' id='returnurl'  value='" . $returnurl . "' />";
        $def_url .= "<input type='hidden' name='noticeurl' id='noticeurl'  value='" . $noticeurl . "' />";        
        $def_url .= "<input type='hidden' name='description' id='description'  value='" . $description . "' />";   
             
        $def_url .= "<input type='hidden' name='rnaName' id='rnaName'  value='" . $rnaName . "' />";
        $def_url .= "<input type='hidden' name='rnaIdNumber' id='rnaIdNumber'  value='" . $rnaIdNumber . "' />";
        $def_url .= "<input type='hidden' name='rnaMobilePhone' id='rnaMobilePhone'  value='" . $rnaMobilePhone . "' />";      
                  
        $def_url .= "<input type='hidden' name='goodsDesc' id='goodsDesc'  value='" . $goodsDesc . "' />";
        $def_url .= "<input type='hidden' name='userIdIdentity' id='userIdIdentity'  value='" . $userIdIdentity. "' />";
        $def_url .= "<input type='hidden' name='allowRePay' id='allowRePay'  value='" . $allowRePay . "' />"; 
               
        $def_url .= "<input type='hidden' name='rePayTimeOut' id='rePayTimeOut'  value='" . $rePayTimeOut . "' />";
        $def_url .= "<input type='hidden' name='bankCardType' id='bankCardType'  value='" . $bankCardType. "' />";  
              
        $def_url .= "<input type='hidden' name='productId' id='productId'  value='" . $productId . "' />";
        
        $def_url .= "<input type='hidden' name='productName' id='productName'  value='" . $productName . "' />";        
        $def_url .= "<input type='hidden' name='fund' id='fund'  value='" . $fund . "' />";
        $def_url .= "<input type='hidden' name='bizType' id='bizType'  value='" . $bizType . "' />";
        
        $def_url .= "<input type='hidden' name='merAcct' id='merAcct'  value='" . $merAcct . "' />";
        $def_url .= "<input type='hidden' name='productNumber' id='productNumber'  value='" . $productNumber . "' />";
        
        $def_url .= "<input type='submit' name='submit' class='paybutton' value='确认付款，到丰付支付去啦' />";
        $def_url .= "</form></div></br>";
		$def_url .="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</span>";
        return $def_url;
	}
    
    public function get_display_code() {
    	$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Sumapay'");
    	if($payment_item)
    	{
    		$payment_cfg = unserialize($payment_item['config']);
    		
    		if (count($payment_cfg['sumapay_gateway']) > 0){    		
	    		$html = "<style type='text/css'>.sumapay_types{float:left; display:block; background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/Sumapay/banklist_hnapay.jpg); font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px;}";
	    		$html .=".fbk_type_0{background-position:15px -908px; }"; //网关
	    		$html .=".fbk_type_icbc{background-position:15px  2px;}"; //工商银行
	    		$html .=".fbk_type_abc{background-position:15px -34px; }"; //农业银行
	    		$html .=".fbk_type_boc{background-position:15px -113px; }"; //中国银行
	    		$html .=".fbk_type_ccb{background-position:15px  -72px;}"; //建设银行    		
	    		$html .=".fbk_type_comm{background-position:15px  -157px; }"; //交通银行
	    		$html .=".fbk_type_ceb{background-position:15px -435px; }"; //光大银行
	    		$html .=".fbk_type_hxb{background-position:15px -358px;}"; //华夏银行
	    		$html .=".fbk_type_cmsb{background-position:15px -232px;  }"; //民生银行
	    		$html .=".fbk_type_cmb{background-position:15px -196px; }"; //招商银行
	    		$html .=".fbk_type_spdb{background-position:15px -312px; }"; //浦发银行
	    		$html .=".fbk_type_cgb{background-position:15px -475px; }"; //广发银行
	    		$html .=".fbk_type_cncb{background-position:15px -398px; }"; //中信银行
	    		$html .=".fbk_type_pab{background-position:15px -827px;}"; //平安银行
	    		$html .=".fbk_type_shb{background-position:15px -790px; }"; //上海银行
	    		$html .=".fbk_type_psbc{background-position:15px -517px; }"; //邮政储蓄
	    		$html .=".fbk_type_bjb{background-position:15px -697px; }"; //北京银行
	    		$html .="</style>";
	    		$html .="<script type='text/javascript'>function set_bank(bank_id)";
	    		$html .="{";
	    		$html .="$(\"input[name='bank_id']\").val(bank_id);";
	    		$html .="}</script>";
	    		$html.= "<h3 class='clearfix tl'><b>丰付支付</b></h3><div class='blank1'></div><hr />";
	    		foreach ($payment_cfg['sumapay_gateway'] AS $key=>$val)
	    		{
	    			$html  .= "<label class='sumapay_types fbk_type_".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
	    		}
	    		$html .= "<input type='hidden' name='bank_id' /><div class='blank'></div>";
	    		return $html;
    		}else{
    			$html = "<label class='f_l ui-radiobox' rel='common_payment' style='background:url(".APP_ROOT.$payment_item['logo'].")' title='".$payment_item['name']."'>".
					"<input type='radio' name='payment' value='".$payment_item['id']."' />&nbsp;";
					if($payment_item['logo']==""){
						$html .=$payment_item['name'];
					}
					$html .= "</label><div class='blank'></div>";
				return $html;
    		}
    	}
    	else{
    		return '';
    	}
    }    
	
	public function response($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
    	
		
		//file_put_contents("D:/wamp/www/Sumapay/call_back_url_1_".strftime("%Y%m%d%H%M%S",time()).".txt",print_r($request,true));
		
        //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Sumapay'");  
    	$payment['config'] = unserialize($payment['config']);

    	//print_r($payment['config']);exit;
    	
		$requestId = $request['requestId'];
		$resultSignature = $request['resultSignature'];
		$payId = $request['payId'];
		$fiscalDate = $request['fiscalDate'];
		$description = $request['description'];
		
		$sbOld = "";
		$sbOld = $sbOld . $requestId;
		$sbOld = $sbOld . $payId;
		$sbOld = $sbOld . $fiscalDate;
		$sbOld = $sbOld . $description;
		$signatrue = $this->HmacMd5($sbOld, trim($payment['config']['mer_key']));
		
		if($resultSignature == $signatrue){
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$requestId."'");
				
			require_once APP_ROOT_PATH."system/libs/cart.php";
			
			$rs = payment_paid($payment_notice['id'],$payId);						
            	
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
    	
	 //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Sumapay'");  
    	$payment['config'] = unserialize($payment['config']);
    	    	    	
    	$requestId = $request['requestId'];
		$resultSignature = $request['resultSignature'];
		$payId = $request['payId'];
		$fiscalDate = $request['fiscalDate'];
		$description = $request['description'];
		
		$sbOld = "";
		$sbOld = $sbOld . $requestId;
		$sbOld = $sbOld . $payId;
		$sbOld = $sbOld . $fiscalDate;
		$sbOld = $sbOld . $description;
		$signatrue = $this->HmacMd5($sbOld, trim($payment['config']['mer_key']));
		
		if($resultSignature == $signatrue){
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$requestId."'");
				
			require_once APP_ROOT_PATH."system/libs/cart.php";
			
			$rs = payment_paid($payment_notice['id'],$payId);						
            	
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
	

	function HmacMd5($data, $key) {
		// RFC 2104 HMAC implementation for php.
		// Creates an md5 HMAC.
		// Eliminates the need to install mhash to compute a HMAC
		// Hacked by Lance Rushing(NOTE: Hacked means written)
	
		//需要配置环境支持iconv，否则中文参数不能正常处理
		$key = iconv("GB2312", "UTF-8", $key);
		$data = iconv("GB2312", "UTF-8", $data);
	
		$b = 64; // byte length for md5
		if (strlen($key) > $b) {
			$key = pack("H*", md5($key));
		}
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad;
		$k_opad = $key ^ $opad;
	
		return md5($k_opad . pack("H*", md5($k_ipad . $data)));
	}	
}
?>