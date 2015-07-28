<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'宝付支付',
	'baofoo_account'	=>	'商户号',
	'baofoo_key'		=>	'密钥',
	'baofoo_terminal'		=>	'终端号',
	'GO_TO_PAY'	=>	'前往宝付在线支付',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',

	'baofoo_gateway'	=>	'支持的银行',
	'baofoo_gateway_0'	=>	'网银支付（总）',
	//借记卡
	'baofoo_gateway_3001'	=>	'招商银行',
	'baofoo_gateway_3002'	=>	'工商银行',
	'baofoo_gateway_3003'	=>	'建设银行',
	'baofoo_gateway_3004'	=>	'浦发银行',
	'baofoo_gateway_3005'	=>	'农业银行',
	'baofoo_gateway_3006'	=>	'民生银行',
	'baofoo_gateway_3008'   =>	'深圳发展银行',
	'baofoo_gateway_3009'	=>	'兴业银行',
	'baofoo_gateway_3020'	=>	'交通银行',
	'baofoo_gateway_3022'	=>	'光大银行',
	'baofoo_gateway_3026'	=>	'中国银行',
	'baofoo_gateway_3032'	=>	'北京银行',
	'baofoo_gateway_3033'	=>	'BEA 东亚银行',
	'baofoo_gateway_3034'	=>	'渤海银行',
	'baofoo_gateway_3035'	=>	'平安银行',
	'baofoo_gateway_3036'	=>	'广发银行',
	'baofoo_gateway_3037'	=>	'上海农商银行',
	'baofoo_gateway_3038'	=>	'中国邮政储蓄银行',
	'baofoo_gateway_3039'	=>	'中信银行',
	'baofoo_gateway_3046'	=>	'宁波银行',
	'baofoo_gateway_3047'	=>	'日照银行',
	'baofoo_gateway_3048'	=>	'河北银行',
	'baofoo_gateway_3049'	=>	'湖南省农信联合社',
	'baofoo_gateway_3050'	=>	'华夏银行',
	'baofoo_gateway_3051'	=>	'威海市商业银行',
	'baofoo_gateway_3054'	=>	'重庆农村商业银行',
	'baofoo_gateway_3055'	=>	'大连银行',
	'baofoo_gateway_3056'	=>	'东莞银行',
	'baofoo_gateway_3057'	=>	'富滇银行',
	'baofoo_gateway_3059'	=>	'上海银行',
	'baofoo_gateway_3080'	=>	'银联在线',
	
	//信用卡
	'baofoo_gateway_4001'	=>	'招商银行[信用卡]',
	'baofoo_gateway_4002'	=>	'中国工商银行[信用卡]',
	'baofoo_gateway_4003'	=>	'中国建设银行[信用卡]',
	'baofoo_gateway_4004'	=>	'上海浦东发展银行[信用卡]',
	'baofoo_gateway_4005'	=>	'中国农业银行[信用卡]',
	'baofoo_gateway_4006'	=>	'中国民生银行[信用卡]',
	'baofoo_gateway_4008'	=>	'深圳发展银行[信用卡]',
	'baofoo_gateway_4009'	=>	'兴业银行[信用卡]',
	'baofoo_gateway_4020'	=>	'中国交通银行[信用卡]',
	'baofoo_gateway_4022'	=>	'中国光大银行[信用卡]',
	'baofoo_gateway_4026'	=>	'中国银行[信用卡]',
	'baofoo_gateway_4032'	=>	'北京银行[信用卡]',
	'baofoo_gateway_4033'	=>	'BEA东亚银行	[信用卡]',
	'baofoo_gateway_4034'	=>	'渤海银行[信用卡]',
	'baofoo_gateway_4035'	=>	'平安银行[信用卡]',
	'baofoo_gateway_4036'	=>	'广发银行[信用卡]',
	'baofoo_gateway_4037'	=>	'上海农商银行	[信用卡]',
	'baofoo_gateway_4038'	=>	'中国邮政储蓄银行[信用卡]',
	'baofoo_gateway_4039'	=>	'中信银行[信用卡]',
	'baofoo_gateway_4046'	=>	'宁波银行[信用卡]',
	'baofoo_gateway_4047'	=>	'日照银行[信用卡]',
	'baofoo_gateway_4048'	=>	'河北银行[信用卡]',
	'baofoo_gateway_4049'	=>	'湖南省农信联合社[信用卡]',
	'baofoo_gateway_4050'	=>	'华夏银行[信用卡]',
	'baofoo_gateway_4051'	=>	'威海市商业银行[信用卡]',
	'baofoo_gateway_4054'	=>	'重庆农村商业银行[信用卡]',
	'baofoo_gateway_4055'	=>	'大连银行[信用卡]',
	'baofoo_gateway_4056'	=>	'东莞银行[信用卡]',
	'baofoo_gateway_4057'	=>	'富滇银行[信用卡]',
	'baofoo_gateway_4059'	=>	'上海银行[信用卡]',
	'baofoo_gateway_4080'	=>	'银联在线[信用卡]',
	
);
$config = array(
	'baofoo_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //支付宝帐号: 
	'baofoo_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码
	'baofoo_terminal'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //终端号
	'baofoo_gateway'	=>	array(
		'INPUT_TYPE'	=>	'3',
		'VALUES'	=>	array(
				'0',//'网银支付（总）',
				
				//借记卡
				'3001',//'招商银行',
				'3002',//'工商银行',
				'3003',//'建设银行',
				'3004',//'浦发银行',
				'3005',//'农业银行',
				'3006',//'民生银行',
				'3008',//深圳发展银行	
				'3009',//'兴业银行',
				'3020',//'交通银行',
				'3022',//'光大银行',
				'3026',//'中国银行',
				'3032',//北京银行
				'3033',//东亚银行
				'3034',//渤海银行
				'3035',//'平安银行',
				'3036',//广发银行
				'3037',//上海农商银行
				'3038',//'中国邮政储蓄银行',
				'3039',//'中信银行',
				'3046',//'宁波银行',
				'3047',//'日照银行',
				'3048',//'河北银行',
				'3049',//'湖南省农村信用社联合社',
				'3050',//'华夏银行',
				'3051',//'威海市首页银行',
				'3054',//'重庆农村商业银行',
				'3055',//'大连银行',
				'3056',//'东莞银行',
				'3057',//'富滇银行',
				'3059',//'上海银行',
				'3080',//'银联在线',
				
				//信用卡
				'4001',//招商银行
				'4002',//中国工商银行
				'4003',//中国建设银行
				'4004',//上海浦东发展银行
				'4005',//中国农业银行
				'4006',//中国民生银行
				'4008',//深圳发展银行
				'4009',//兴业银行	
				'4020',//中国交通银行
				'4022',//中国光大银行	
				'4026',//中国银行
				'4032',//北京银行	
				'4033',//BEA东亚银行	
				'4034',//渤海银行	
				'4035',//平安银行	
				'4036',//广发银行|CGB
				'4037',//上海农商银行	
				'4038',//中国邮政储蓄银行
				'4039',//中信银行
				'4046',//宁波银行	
				'4047',//日照银行	
				'4048',//河北银行	
				'4049',//湖南省农村信用社联合社
				'4050',//华夏银行
				'4051',//威海市商业银行
				'4054',//重庆农村商业银行	
				'4055',//大连银行	
				'4056',//东莞银行	
				'4057',//富滇银行	
				'4059',//上海银行	
				'4080',//银联在线	
			)
	), //可选的银行网关
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Baofoo';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://www.baofoo.com';
    return $module;
}

// 支付宝直连支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Baofoo_payment implements payment {
	private $payment_lang = array(
		'name'	=>	'宝付支付',
		'baofoo_account'	=>	'商户号',
		'baofoo_key'		=>	'密钥',
		'baofoo_terminal'	=>	'终端号',
		'GO_TO_PAY'	=>	'前往宝付在线支付',
		'VALID_ERROR'	=>	'支付验证失败',
		'PAY_FAILED'	=>	'支付失败',
	
		'baofoo_gateway'	=>	'支持的银行',
		'baofoo_gateway_0'	=>	'网银支付（总）',
		//借记卡
		'baofoo_gateway_3001'	=>	'招商银行',
		'baofoo_gateway_3002'	=>	'工商银行',
		'baofoo_gateway_3003'	=>	'建设银行',
		'baofoo_gateway_3004'	=>	'浦发银行',
		'baofoo_gateway_3005'	=>	'农业银行',
		'baofoo_gateway_3006'	=>	'民生银行',
		'baofoo_gateway_3008'   =>	'深圳发展银行',
		'baofoo_gateway_3009'	=>	'兴业银行',
		'baofoo_gateway_3020'	=>	'交通银行',
		'baofoo_gateway_3022'	=>	'光大银行',
		'baofoo_gateway_3026'	=>	'中国银行',
		'baofoo_gateway_3032'	=>	'北京银行',
		'baofoo_gateway_3033'	=>	'BEA 东亚银行',
		'baofoo_gateway_3034'	=>	'渤海银行',
		'baofoo_gateway_3035'	=>	'平安银行',
		'baofoo_gateway_3036'	=>	'广发银行',
		'baofoo_gateway_3037'	=>	'上海农商银行',
		'baofoo_gateway_3038'	=>	'中国邮政储蓄银行',
		'baofoo_gateway_3039'	=>	'中信银行',
		'baofoo_gateway_3046'	=>	'宁波银行',
		'baofoo_gateway_3047'	=>	'日照银行',
		'baofoo_gateway_3048'	=>	'河北银行',
		'baofoo_gateway_3049'	=>	'湖南省农信联合社',
		'baofoo_gateway_3050'	=>	'华夏银行',
		'baofoo_gateway_3051'	=>	'威海市商业银行',
		'baofoo_gateway_3054'	=>	'重庆农村商业银行',
		'baofoo_gateway_3055'	=>	'大连银行',
		'baofoo_gateway_3056'	=>	'东莞银行',
		'baofoo_gateway_3057'	=>	'富滇银行',
		'baofoo_gateway_3059'	=>	'上海银行',
		'baofoo_gateway_3080'	=>	'银联在线',
		
		//信用卡
		'baofoo_gateway_4001'	=>	'招商银行',
		'baofoo_gateway_4002'	=>	'中国工商银行',
		'baofoo_gateway_4003'	=>	'中国建设银行',
		'baofoo_gateway_4004'	=>	'上海浦东发展银行',
		'baofoo_gateway_4005'	=>	'中国农业银行',
		'baofoo_gateway_4006'	=>	'中国民生银行',
		'baofoo_gateway_4008'	=>	'深圳发展银行',
		'baofoo_gateway_4009'	=>	'兴业银行',
		'baofoo_gateway_4020'	=>	'中国交通银行',
		'baofoo_gateway_4022'	=>	'中国光大银行',
		'baofoo_gateway_4026'	=>	'中国银行',
		'baofoo_gateway_4032'	=>	'北京银行',
		'baofoo_gateway_4033'	=>	'BEA东亚银行	',
		'baofoo_gateway_4034'	=>	'渤海银行',
		'baofoo_gateway_4035'	=>	'平安银行',
		'baofoo_gateway_4036'	=>	'广发银行',
		'baofoo_gateway_4037'	=>	'上海农商银行	',
		'baofoo_gateway_4038'	=>	'中国邮政储蓄银行',
		'baofoo_gateway_4039'	=>	'中信银行',
		'baofoo_gateway_4046'	=>	'宁波银行',
		'baofoo_gateway_4047'	=>	'日照银行',
		'baofoo_gateway_4048'	=>	'河北银行',
		'baofoo_gateway_4049'	=>	'湖南省农信联合社',
		'baofoo_gateway_4050'	=>	'华夏银行',
		'baofoo_gateway_4051'	=>	'威海市商业银行',
		'baofoo_gateway_4054'	=>	'重庆农村商业银行',
		'baofoo_gateway_4055'	=>	'大连银行',
		'baofoo_gateway_4056'	=>	'东莞银行',
		'baofoo_gateway_4057'	=>	'富滇银行',
		'baofoo_gateway_4059'	=>	'上海银行',
		'baofoo_gateway_4080'	=>	'银联在线',
	
	
	);
	public function get_payment_code($payment_notice_id) {
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$_TransID = $payment_notice['notice_sn'];
		$_OrderMoney = round($payment_notice['money'],2);
		
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
       
        $_Merchant_url =  SITE_DOMAIN.APP_ROOT.'/callback/pay/baofoo_callback.php?act=response';
        $_Return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/baofoo_callback.php?act=notify';
        
        /* 交易日期 */
        $_TradeDate = to_date($payment_notice['create_time'], 'YmdHis');
		$_MerchantID = $payment_info['config']['baofoo_account'];
        
        $_PayID = $payment_notice['bank_id'];
		if(intval($_PayID) == 1000 || intval($_PayID)==0){
			$_PayID = "";
		}
        $_NoticeType = 1;
        $_Md5Key = $payment_info['config']['baofoo_key'];
        $_TerminalID = $payment_info['config']['baofoo_terminal'];
       
		$_AdditionalInfo = $payment_notice_id;
		$_Md5_OrderMoney = $_OrderMoney*100;
		$MARK = "|";
      	$_Signature=md5($_MerchantID.$MARK.$_PayID.$MARK.$_TradeDate.$MARK.$_TransID.$MARK.$_Md5_OrderMoney.$MARK.$_Merchant_url.$MARK.$_Return_url.$MARK.$_NoticeType.$MARK.$_Md5Key);
        /*交易参数*/
        $parameter = array(
            'MemberID' => $_MerchantID,
            'TransID' => $_TransID,//流水号
			'PayID' => $_PayID,//支付方式
			'TradeDate' => $_TradeDate,//交易时间
			'OrderMoney' => $_OrderMoney*100,//订单金额
			'ProductName' => $_TransID,//产品名称
			'Amount' => 1,//数量
			'ProductLogo' => '',//产品logo
			'Username' => '',//支付用户名
			'AdditionalInfo' => $_AdditionalInfo,//订单附加消息
			'PageUrl' => $_Merchant_url,//商户通知地址 
			'ReturnUrl' => $_Return_url,//用户通知地址
			'NoticeType' => $_NoticeType,//通知方式
			'Signature'=>$_Signature,
			'TerminalID'=>$_TerminalID,
			'InterfaceVersion'=> "4.0",
			'KeyType'=> "1"
        );
        
        $def_url = '<form style="text-align:center;" action="http://gw.baofoo.com/payindex" target="_blank" style="margin:0px;padding:0px" method="POST" >';

        foreach ($parameter AS $key => $val) {
            $def_url .= "<input type='hidden' name='$key' value='$val' />";
        }
        $def_url .= "<input type='submit' class='paybutton' value='前往".$this->payment_lang['baofoo_gateway_'.intval($_PayID)]."' />";
        $def_url .= "</form>";
        $def_url.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($_OrderMoney)."</div>";
        return $def_url;
    }

     public function response($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
        /* 取返回参数 */
        $MemberID=$request['MemberID'];//商户号
		$TerminalID =$request['TerminalID'];//商户终端号
		$TransID =$request['TransID'];//商户流水号
		$Result=$request['Result'];//支付结果
		$ResultDesc=$request['ResultDesc'];//支付结果描述
		$FactMoney=$request['FactMoney'];//实际成功金额
		$AdditionalInfo=$request['AdditionalInfo'];//订单附加消息
		$SuccTime=$request['SuccTime'];//支付完成时间
		$Md5Sign=$request['Md5Sign'];//md5签名
		

        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Baofoo'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$_Md5Key= $payment['config']['baofoo_key'];
		$payment_notice_sn = $AdditionalInfo;
		$gopayOutOrderId =  $TransID;
		
		$MARK = "~|~";
        /*比对连接加密字符串*/
		$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$_Md5Key);

        if ($Md5Sign != $WaitSign) {
        	showErr($GLOBALS['payment_lang']["VALID_ERROR"]);
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);						
			
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
			}else{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
        }
    }
    
     public function notify($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
         /* 取返回参数 */
        $MemberID=$request['MemberID'];//商户号
		$TerminalID =$request['TerminalID'];//商户终端号
		$TransID =$request['TransID'];//商户流水号
		$Result=$request['Result'];//支付结果
		$ResultDesc=$request['ResultDesc'];//支付结果描述
		$FactMoney=$request['FactMoney'];//实际成功金额
		$AdditionalInfo=$request['AdditionalInfo'];//订单附加消息
		$SuccTime=$request['SuccTime'];//支付完成时间
		$Md5Sign=$request['Md5Sign'];//md5签名
		

        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Baofoo'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$_Md5Key= $payment['config']['baofoo_key'];
		$payment_notice_sn = $AdditionalInfo;
		$gopayOutOrderId =  $TransID;
		
		$MARK = "~|~";
        /*比对连接加密字符串*/
		$WaitSign=md5('MemberID='.$MemberID.$MARK.'TerminalID='.$TerminalID.$MARK.'TransID='.$TransID.$MARK.'Result='.$Result.$MARK.'ResultDesc='.$ResultDesc.$MARK.'FactMoney='.$FactMoney.$MARK.'AdditionalInfo='.$AdditionalInfo.$MARK.'SuccTime='.$SuccTime.$MARK.'Md5Sign='.$_Md5Key);

        if ($Md5Sign != $WaitSign) {
        	echo "Md5CheckFail";
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);				
				if($rs)
				{
					//开始更新相应的outer_notice_sn					
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$gopayOutOrderId."' where id = ".$payment_notice['id']);
					echo "ok";
				}
				else 
				{
					echo "ok";
				}
			}
			else
			{
				echo "OrderFail";
			}
        }
    }

    public function get_display_code() {
        $payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Baofoo'");
		if($payment_item)
		{
			$payment_cfg = unserialize($payment_item['config']);
			$html = "<style type='text/css'>.baofoo_types{ background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/Baofoo/banklogo.gif) no-repeat 0 0; float:left; display:block;width:150px; height:14px;font-size:0px;  text-align:left; padding:15px 0px;}";
	        $html .=".baofoo_types input{margin:0 }";
	        $html .=".bk_type_0{background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/Baofoo/baofoo.jpg) no-repeat 0px -2px; font-size:0px; width:150px; height:10px;}"; 
	        $html .=".bk_type_0{ width:150px; height:10px;}"; 
	        $html .=".bk_type_3001,.bk_type_4001{ background-position:10px -447px}"; //招商银行
	        $html .=".bk_type_3002,.bk_type_4002{ background-position:10px -406px}"; //工商银行
	        $html .=".bk_type_3003,.bk_type_4003{ background-position:10px -85px}"; //建设银行
	        $html .=".bk_type_3004,.bk_type_4004{ background-position:10px -366px}"; //浦发
	        $html .=".bk_type_3005,.bk_type_4005{ background-position:10px -47px}"; //农业
	        $html .=".bk_type_3006,.bk_type_4006{ background-position:10px -167px}"; //民生银行
	        $html .=".bk_type_3008,.bk_type_4008{ background-position:10px -326px}"; //深圳发展银行
	        $html .=".bk_type_3009,.bk_type_4009{ background-position:10px -486px}"; //兴业银行
	        $html .=".bk_type_3020,.bk_type_4020{ background-position:10px -205px}"; //交通银行
	        $html .=".bk_type_3022,.bk_type_4022{ background-position:10px -126px}"; //中国光大银行
	        $html .=".bk_type_3026,.bk_type_4026{ background-position:10px -829px}"; //中国银行
	        $html .=".bk_type_3032,.bk_type_4032{ background-position:10px -614px}"; //北京银行
	        $html .=".bk_type_3033,.bk_type_4033{ background-position:10px -1051px}"; //BEA东亚银行	
	        $html .=".bk_type_3034,.bk_type_4034{ background-position:10px -1090px}"; //渤海银行
	        $html .=".bk_type_3035,.bk_type_4035{ background-position:10px -901px}"; //平安银行
	        $html .=".bk_type_3036,.bk_type_4036{ background-position:10px -246px}"; //广发银行
	        $html .=".bk_type_3037,.bk_type_4037{ background-position:10px -1130px}"; //上海农商银行
	        $html .=".bk_type_3038,.bk_type_4038{ background-position:5px -526px}"; //中国邮政储蓄银行
	        $html .=".bk_type_3039,.bk_type_4039{ background-position:10px -287px}"; //中信银行
	        $html .=".bk_type_3046,.bk_type_4046{ background-position:10px -1172px}"; //宁波银行
	        $html .=".bk_type_3047,.bk_type_4047{ background-position:10px -1220px}"; //日照银行
	        $html .=".bk_type_3048,.bk_type_4048{ background-position:10px -1263px}"; //河北银行
	        $html .=".bk_type_3049,.bk_type_4049{ background-position:10px -1307px}"; //湖南省农信联合社
	        $html .=".bk_type_3050,.bk_type_4050{ background-position:10px -1349px}"; //华夏银行
	        $html .=".bk_type_3051,.bk_type_4051{ background-position:10px -1390px}"; //威海市商业银行
	        $html .=".bk_type_3054,.bk_type_4054{ background-position:10px -1432px}"; //重庆农村商业银行
	        $html .=".bk_type_3055,.bk_type_4055{ background-position:10px -1472px}"; //大连银行
	        $html .=".bk_type_3056,.bk_type_4056{ background-position:10px -1514px}"; //东莞银行
	        $html .=".bk_type_3057,.bk_type_4057{ background-position:10px -1557px}"; //富滇银行
	        $html .=".bk_type_3059,.bk_type_4059{ background-position:10px -1599px}"; //上海银行
	        $html .=".bk_type_3080,.bk_type_4080{ background-position:10px -1645px}"; //银联在线
	        $html .="</style>";
	        $html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$is_show_jieji = false;
			$is_show_xyk = false;
	       foreach ($payment_cfg['baofoo_gateway'] AS $key=>$val)
	        {
	        	if((int)$key<4000 && (int)$key>=3000 && $is_show_jieji == false){
	        		$html.= "<h3 class='clearfix tl'><b>宝付支付借记卡</b></h3><div class='blank1'></div><hr />";
	        		$is_show_jieji = true;
	        	}
	        	elseif((int)$key>=4000 && $is_show_xyk ==false){
	        		$html.= "<h3 class='clearfix tl'><b>宝付支付信用卡</b></h3><div class='blank1'></div><hr />";
	        		$is_show_xyk = true;
	        	}
	            $html  .= "<label class='baofoo_types bk_type_".$key." ui-radiobox' rel='common_payment' title='".$this->payment_lang["baofoo_gateway_".$key]."' ><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
	        }
	        $html .= "<input type='hidden' name='bank_id' /><div class='blank'></div>";
			return $html;
		}
		else{
			return '';
		}
    }
    /**
     * 字符转义
     * @return string
     */
    function fStripslashes($string)
    {
            if(is_array($string))
            {
                    foreach($string as $key => $val)
                    {
                            unset($string[$key]);
                            $string[stripslashes($key)] = fStripslashes($val);
                    }
            }
            else
            {
                    $string = stripslashes($string);
            }

            return $string;
    }
    
    public function orderquery($payment_notice_id){
    	/*$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$_TransID = $order['order_sn'];
		
		$payment_info = $GLOBALS['db']->getRow("select config from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
       
		$_MerchantID = $payment_info['config']['baofoo_account'];
        $_Md5Key = $payment_info['config']['baofoo_key'];
        
		//此处加入判断，如果前面出错了跳转到其他地方而不要进行提交
		$_Md5Sign = md5($_MerchantID.$_TransID.$_Md5Key);
		
		$info = @file_get_contents("http://paygate.baofoo.com/Check/OrderQuery.aspx?MerchantID=".$_MerchantID."&TransID=".$_TransID."&Md5Sign=".$_Md5Sign);
		
		if($info){
			$data = array();
			$data['status'] = 1;
			list($data['MerchantID'],$data['TransID'],$data['CheckResult'],$data["factMoney"],$data['SuccTime'],$data['Md5Sign']) = explode("|",$info);
			
			$CheckResult = $data['CheckResult'];
			
			if($CheckResult == "Y")
			{
				$data['CheckResult'] = "成功";
			}
			elseif($CheckResult == "F")
			{
				$data['CheckResult'] = "失败";
			}
			elseif($CheckResult == "P")
			{
				$data['CheckResult'] = "处理中";
				
			}
			elseif($CheckResult == "N"){
				$data['CheckResult'] = "没有订单";
			}
			
			$time_span = to_timespan($data['SuccTime'],"YmdHis");
			$data['SuccTime'] =  to_date($time_span,"Y-m-d H:i:s");
			
			$data['factMoney'] = format_price($data['factMoney']/100);
			
			echo json_encode($data);
		}
		else{
			$data['status'] = 0;
			echo json_encode($data);
		}*/
		
		$data['status'] = 1;
		$data['CheckResult'] = "4.0接口不支持查询";
		echo json_encode($data);
    }
}
?>
