<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'支付宝银行直连支付',
	'alipay_partner'	=>	'合作者身份ID',
	'alipay_account'	=>	'支付宝帐号',
	'alipay_key'		=>	'校验码',
	'GO_TO_PAY'	=>	'前往支付宝在线支付',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',

	'alipay_gateway'	=>	'支持的银行',
	'alipay_gateway_ICBCB2C'	=>	'中国工商银行',
	'alipay_gateway_CMB'	=>	'招商银行',
	'alipay_gateway_CCB'	=>	'中国建设银行',
	'alipay_gateway_ABC'	=>	'中国农业银行',
	'alipay_gateway_SPDB'	=>	'上海浦东发展银行',
	'alipay_gateway_SDB'	=>	'深圳发展银行',
	'alipay_gateway_CIB'	=>	'兴业银行',
	'alipay_gateway_BJBANK'	=>	'北京银行',
	'alipay_gateway_CEBBANK'	=>	'中国光大银行',
	'alipay_gateway_CMBC'	=>	'中国民生银行',
	'alipay_gateway_CITIC'	=>	'中信银行',
	'alipay_gateway_GDB'	=>	'广东发展银行',
	'alipay_gateway_SPABANK'	=>	'平安银行',
	'alipay_gateway_BOCB2C'	=>	'中国银行',
	'alipay_gateway_COMM'	=>	'交通银行',
	'alipay_gateway_ICBCBTB'	=>	'中国工商银行(企业)',
	'alipay_gateway_PSBC-DEBIT'	=>	'中国邮政储蓄银行(银联)',	


);
$config = array(
	'alipay_partner'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //合作者身份ID
	'alipay_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //支付宝帐号: 
	'alipay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码
	'alipay_gateway'	=>	array(
		'INPUT_TYPE'	=>	'3',
		'VALUES'	=>	array(
				'ICBCB2C', //中国工商银行
				'CMB', //招商银行
				'CCB', //中国建设银行
				'ABC', //中国农业银行
				'SPDB', //上海浦东发展银行
				'SDB', //深圳发展银行
				'CIB', //兴业银行
				'BJBANK', //北京银行
				'CEBBANK', //中国光大银行
				'CMBC', //中国民生银行
				'CITIC', //中信银行
				'GDB', //广东发展银行
				'SPABANK', //平安银行
				'BOCB2C', //中国银行
				'COMM', //交通银行
				'ICBCBTB', //中国工商银行(企业)
				'PSBC-DEBIT', //中国邮政储蓄银行(银联)
			)
	), //可选的银行网关
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'AlipayBank';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = 'http://act.life.alipay.com/systembiz/fangwei/';
    return $module;
}

// 支付宝直连支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class AlipayBank_payment implements payment {
	private $payment_lang = array(
		'GO_TO_PAY'	=>	'前往%s支付',
		'alipay_gateway_ICBCB2C'	=>	'中国工商银行',
		'alipay_gateway_CMB'	=>	'招商银行',
		'alipay_gateway_CCB'	=>	'中国建设银行',
		'alipay_gateway_ABC'	=>	'中国农业银行',
		'alipay_gateway_SPDB'	=>	'上海浦东发展银行',
		'alipay_gateway_SDB'	=>	'深圳发展银行',
		'alipay_gateway_CIB'	=>	'兴业银行',
		'alipay_gateway_BJBANK'	=>	'北京银行',
		'alipay_gateway_CEBBANK'	=>	'中国光大银行',
		'alipay_gateway_CMBC'	=>	'中国民生银行',
		'alipay_gateway_CITIC'	=>	'中信银行',
		'alipay_gateway_GDB'	=>	'广东发展银行',
		'alipay_gateway_SPABANK'	=>	'平安银行',
		'alipay_gateway_BOCB2C'	=>	'中国银行',
		'alipay_gateway_COMM'	=>	'交通银行',
		'alipay_gateway_ICBCBTB'	=>	'中国工商银行(企业)',
		'alipay_gateway_PSBC-DEBIT'	=>	'中国邮政储蓄银行(银联)',	
	);
	public function get_name($bank_id)
	{
		return $this->payment_lang['alipay_gateway_'.$bank_id];
	}
	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		
		$data_return_url = get_domain().APP_ROOT.'/index.php?ctl=payment&act=response&class_name=AlipayBank';
		$data_notify_url = get_domain().APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=AlipayBank';

		$service = 'create_direct_pay_by_user';
		/* 银行类型 */
        $bank_type = $GLOBALS['db']->getOne("select bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$parameter = array(
            'service'           => $service,
            'partner'           => $payment_info['config']['alipay_partner'],
            //'partner'           => ALIPAY_ID,
            '_input_charset'    => 'utf-8',
            'notify_url'        => $data_notify_url,
            'return_url'        => $data_return_url,
            /* 业务参数 */
            'subject'           => $order_sn,
            'out_trade_no'      => $payment_notice['notice_sn'], 
            'price'             => $money,
            'quantity'          => 1,
            'payment_type'      => 1,
            /* 物流参数 */
            'logistics_type'    => 'EXPRESS',
            'logistics_fee'     => 0,
            'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
            /* 买卖双方信息 */
            'seller_email'      => $payment_info['config']['alipay_account'],
			'defaultbank'	=>	$bank_type,
			'payment'	=>	'bankPay'
        );
        
        ksort($parameter);
        reset($parameter);

        $param = '';
        $sign  = '';

        foreach ($parameter AS $key => $val)
        {
        	$param .= "$key=" .urlencode($val). "&";
            $sign  .= "$key=$val&";
        }

        $param = substr($param, 0, -1);
        $sign  = substr($sign, 0, -1). $payment_info['config']['alipay_key'];
        $sign_md5 = md5($sign);

		
		$payLinks = '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.$sign_md5.'&sign_type=MD5" target="_blank"><span class="paybutton" name="buy">'.sprintf($this->payment_lang['GO_TO_PAY'],$this->get_name($bank_type)).'</span></a>';

    	if(!empty($payment_info['logo']))
		{
			$payLinks = '<a href="https://www.alipay.com/cooperate/gateway.do?'.$param. '&sign='.$sign_md5.'&sign_type=MD5" target="_blank" class="payLink"><img src='.APP_ROOT.$payment_info['logo'].' style="border:solid 1px #ccc;" /></a><div class="blank"></div>'.$payLinks;
		}
		
        $code = '<div style="text-align:center">'.$payLinks.'</div>';
		$code.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $code;
	}
	
	public function response($request)
	{
        
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='AlipayBank'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
        /* 检查数字签名是否正确 */
        ksort($request);
        reset($request);
	
        foreach ($request AS $key=>$val)
        {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key!='class_name' && $key!='act' && $key!='ctl'&& $key!='city' )
            {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $payment['config']['alipay_key'];

		if (md5($sign) != $request['sign'])
        {
            showErr($GLOBALS['payment_lang']["VALID_ERROR"]);
        }
		
        $payment_notice_sn = $request['out_trade_no'];
        
    	$money = $request['total_fee'];
		
    	$outer_notice_sn = $request['trade_no'];
		
		if ($request['trade_status'] == 'TRADE_SUCCESS' || $request['trade_status'] == 'TRADE_FINISHED' || $request['trade_status'] == 'WAIT_SELLER_SEND_GOODS'|| $request['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS'){
			
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
	}
	
	public function notify($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='AlipayBank'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
        /* 检查数字签名是否正确 */
        ksort($request);
        reset($request);
	
        foreach ($request AS $key=>$val)
        {
            if ($key != 'sign' && $key != 'sign_type' && $key != 'code' && $key!='class_name' && $key!='act' && $key!='ctl'&& $key!='city' )
            {
                $sign .= "$key=$val&";
            }
        }

        $sign = substr($sign, 0, -1) . $payment['config']['alipay_key'];

		if (md5($sign) != $request['sign'])
        {
            echo '0';
        }
		
        $payment_notice_sn = $request['out_trade_no'];
        
    	$money = $request['total_fee'];
		$outer_notice_sn = $request['trade_no'];

		if ($request['trade_status'] == 'TRADE_SUCCESS' || $request['trade_status'] == 'TRADE_FINISHED' || $request['trade_status'] == 'WAIT_SELLER_SEND_GOODS' || $request['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS'){
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where notice_sn = '".$payment_notice_sn."'");
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$outer_notice_sn);								
			if($rs)
			{			
				//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$outer_notice_sn."' where id = ".$payment_notice['id']);				
				order_paid($payment_notice['order_id']);	
				$this->auto_do_send_goods($payment_notice,$order_info);				
				echo '1';
			}
			else
			{
				$this->auto_do_send_goods($payment_notice,$order_info);	
				echo '0';
			}
			
		}else{
		   echo '0';
		}   
	}
	
	public function get_display_code()
	{		
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='AlipayBank'");
		if($payment_item)
		{
			$payment_cfg = unserialize($payment_item['config']);

			$html = "<style type='text/css'>.alibank_types{float:left; display:block; background:url(".get_domain().APP_ROOT."/system/payment/AlipayBank/banklogo.gif); font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px;}";
	        $html .=".bk_typeCMB{background-position:15px -444px; }";  //招行
	        $html .=".bk_typeICBCB2C{background-position:15px -404px; }";  //工行
	        $html .=".bk_typeCCB{background-position:15px -84px; }"; //建行
	        $html .=".bk_typeABC{background-position:15px -44px; }"; //农行
	        $html .=".bk_typeSPDB{background-position:15px -364px; }"; //上海浦东发展银行
	        $html .=".bk_typeSDB{background-position:15px -324px; }"; //深圳发展银行
	        $html .=".bk_typeCIB{background-position:15px -484px; }"; //兴业银行
	        $html .=".bk_typeBJBANK{background-position:15px -610px; }"; //北京银行
	        $html .=".bk_typeCEBBANK{background-position:15px -124px; }"; //光大银行
	        $html .=".bk_typeCMBC{background-position:15px -164px; }"; //民生银行
	        $html .=".bk_typeCITIC{background-position:15px -284px; }"; //中信银行
	        $html .=".bk_typeGDB{background-position:15px -244px; }"; //广东发展银行
	        $html .=".bk_typeSPABANK{background-position:15px -903px; }"; //平安银行
	        $html .=".bk_typeBOCB2C{background-position:15px -939px; }"; //中国银行
	        $html .=".bk_typeCOMM{background-position:15px -204px; }"; //交通银行
	        $html .=".bk_typeICBCBTB{background-position:15px -784px; }"; //工行企业
	        $html .=".bk_typePSBC-DEBIT{background-position:15px -524px; }"; //中国邮政储蓄银行(银联)
	        $html .="</style>";
        	$html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$html .= "<div class='blank'></div><h3 class='clearfix tl'><b>支付宝直连</b></h3><div class='blank1'></div><hr />";
			foreach ($payment_cfg['alipay_gateway'] AS $key=>$val)
	        {
	            $html  .= "<label class='alibank_types bk_type".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
	        }
	        $html .= "<input type='hidden' name='bank_id' />";
			return $html;
		}
		else
		{
			return '';
		}
	}
	
	public function auto_do_send_goods($payment_notice,$order_info)
	{

		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_info['id']);
		if($order_info['pay_status']==2)//定单全额支付
		{			
			$is_delivery = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where doi.order_id = ".$order_info['id']." and d.is_delivery = 1");
			//当 $is_delivery 为 0， 表示都不用实体配送，自动发货
			if(intval($is_delivery) == 0)
			{
				$this->do_send_goods($payment_notice['id'],"订单".$order_info['order_sn']."支付成功，无需配送");
			}
		}
	}
	
	public function do_send_goods($payment_notice_id,$delivery_notice_sn)
	{
		require_once APP_ROOT_PATH."system/utils/XmlBase.php"; 
		$payment_notice  = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='AlipayBank'");  
    	$payment['config'] = unserialize($payment['config']);

    	
		$gateway = "https://www.alipay.com/cooperate/gateway.do";
			
		$parameter = array(
			'service'	=>	'send_goods_confirm_by_platform',
			'partner'	=>	$payment['config']['alipay_partner'],
			'_input_charset'	=>	'utf-8',
			'invoice_no'	=>	$delivery_notice_sn,
			'transport_type'	=>	'EXPRESS',
			'logistics_name'	=>	'NONE',
			'trade_no'	=>	$payment_notice['outer_notice_sn']
		);
		
		ksort($parameter);
        reset($parameter);

        $sign  = '';
        $param = '';

        foreach ($parameter AS $key => $val)
        {
            $sign  .= "$key=$val&";
            $param .= "$key=" .urlencode($val). "&";
        }

        $param  = substr($param, 0, -1);
        $sign  = substr($sign, 0, -1).$payment['config']['alipay_key'];
        $sign_md5 = md5($sign);
        
        
        $gateway = $gateway."?".$param."&sign=".$sign_md5."&sign_type=MD5";

        $result = file_get_contents($gateway);

		$result = toArray($result,"alipay");

		if($result['is_success'][0]=='T')
		{
			return "支付宝发货成功";
		}
		else
		{
			if($result['error']=='ILLEGAL_ARGUMENT')
			{
				return $result['error'].' 参数不正确';
			}
			elseif($result['error']=='TRADE_NOT_EXIST')
			{
				return $result['error'].' 交易单号有误';
			}
			elseif($result['error']=='GENERIC_FAILURE')
			{
				return $result['error'].' 执行命令错误';
			}
			else
			{
				return $result['error'];
			}			
		}
	}
}
?>