<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'财付通直连支付',
	'tencentpay_id'	=>	'商户ID',
	'tencentpay_key'	=>	'商户密钥',
	'tencentpay_sign'	=>	'自定义签名',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'GO_TO_PAY'	=>	'前往财付通支付',
	'tencentpay_gateway'	=>	'支持的银行',
	'tencentpay_gateway_0'	=>	'纯网关支付',
	'tencentpay_gateway_1002'	=>	'中国工商银行',
	'tencentpay_gateway_1001'	=>	'招商银行',
	'tencentpay_gateway_1003'	=>	'中国建设银行',
	'tencentpay_gateway_1005'	=>	'中国农业银行',
	'tencentpay_gateway_1004'	=>	'上海浦东发展银行',
	'tencentpay_gateway_1008'	=>	'深圳发展银行',
	'tencentpay_gateway_1009'	=>	'兴业银行',
	'tencentpay_gateway_1032'	=>	'北京银行',
	'tencentpay_gateway_1022'	=>	'中国光大银行',
	'tencentpay_gateway_1006'	=>	'中国民生银行',
	'tencentpay_gateway_1021'	=>	'中信银行',
	'tencentpay_gateway_1027'	=>	'广东发展银行',
	'tencentpay_gateway_1010'	=>	'平安银行',
	'tencentpay_gateway_1052'	=>	'中国银行',
	'tencentpay_gateway_1020'	=>	'交通银行',
	'tencentpay_gateway_1030'	=>	'中国工商银行(企业)',
	'tencentpay_gateway_1042'	=>	'招商银行(企业)',
	'tencentpay_gateway_1028'	=>	'中国邮政储蓄银行(银联)',


);
$config = array(
	'tencentpay_id'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户ID
	'tencentpay_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户密钥
	'tencentpay_sign'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //自定义签名
	'tencentpay_gateway'	=>	array(
		'INPUT_TYPE'	=>	'3',
		'VALUES'	=>	array(
				0,    //纯网关支付
				1002, //中国工商银行
				1001, //招商银行
				1003, //中国建设银行
				1005, //中国农业银行
				1004, //上海浦东发展银行
				1008, //深圳发展银行
				1009, //兴业银行
				1032, //北京银行
				1022, //中国光大银行
				1006, //中国民生银行
				1021, //中信银行
				1027, //广东发展银行
				1010, //平安银行
				1052, //中国银行
				1020, //交通银行
				1030, //中国工商银行(企业)
				1042, //招商银行(企业)
				1028, //中国邮政储蓄银行(银联)
			)
	), //可选的银行网关
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'TenpayBank';

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

// 财付通直连支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class TenpayBank_payment implements payment {
	private $payment_lang = array(
		'GO_TO_PAY'	=>	'前往%s支付',
		'tencentpay_gateway_0'	=>	'财付通',
		'tencentpay_gateway_1002'	=>	'中国工商银行',
		'tencentpay_gateway_1001'	=>	'招商银行',
		'tencentpay_gateway_1003'	=>	'中国建设银行',
		'tencentpay_gateway_1005'	=>	'中国农业银行',
		'tencentpay_gateway_1004'	=>	'上海浦东发展银行',
		'tencentpay_gateway_1008'	=>	'深圳发展银行',
		'tencentpay_gateway_1009'	=>	'兴业银行',
		'tencentpay_gateway_1032'	=>	'北京银行',
		'tencentpay_gateway_1022'	=>	'中国光大银行',
		'tencentpay_gateway_1006'	=>	'中国民生银行',
		'tencentpay_gateway_1021'	=>	'中信银行',
		'tencentpay_gateway_1027'	=>	'广东发展银行',
		'tencentpay_gateway_1010'	=>	'平安银行',
		'tencentpay_gateway_1052'	=>	'中国银行',
		'tencentpay_gateway_1020'	=>	'交通银行',
		'tencentpay_gateway_1030'	=>	'中国工商银行(企业)',
		'tencentpay_gateway_1042'	=>	'招商银行(企业)',
		'tencentpay_gateway_1028'	=>	'中国邮政储蓄银行(银联)',	
	);
	public function get_name($bank_id=0)
	{
		$bank_id = intval($bank_id);
		return $this->payment_lang['tencentpay_gateway_'.$bank_id];
	}
	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);

		
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=TenpayBank';

        $cmd_no = '1';

        /* 获得订单的流水号，补零到10位 */
        $sp_billno = $payment_notice_id;

        $spbill_create_ip =  $_SERVER['REMOTE_ADDR'];
        
        /* 交易日期 */
        $today = to_date($payment_notice['create_time'],'Ymd');


        /* 将商户号+年月日+流水号 */
        $bill_no = str_pad($payment_notice_id, 10, 0, STR_PAD_LEFT);
        $transaction_id = $payment_info['config']['tencentpay_id'].$today.$bill_no;

        /* 银行类型:支持纯网关和财付通 */
       // $bank_type = intval($GLOBALS['db']->getOne("select bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']));
		$bank_type = $payment_notice['bank_id'];

        $desc = $payment_notice['notice_sn']."payment_notice:[".$payment_notice['notice_sn']."]";
        $attach = $payment_info['config']['tencentpay_sign'];

		
        /* 返回的路径 */
        $return_url = $data_return_url;

        /* 总金额 */
        $total_fee = $money*100;

        /* 货币类型 */
        $fee_type = '1';

        /* 重写自定义签名 */
        //$payment['magic_string'] = abs(crc32($payment['magic_string']));

        /* 数字签名 */
        $sign_text = "cmdno=" . $cmd_no . "&date=" . $today . "&bargainor_id=" . $payment_info['config']['tencentpay_id'] .
          "&transaction_id=" . $transaction_id . "&sp_billno=" . $sp_billno .
          "&total_fee=" . $total_fee . "&fee_type=" . $fee_type . "&return_url=" . $return_url .
          "&attach=" . $attach . "&spbill_create_ip=" . $spbill_create_ip ."&key=" . $payment_info['config']['tencentpay_key'];
        $sign = strtoupper(md5($sign_text));

        /* 交易参数 */
        $parameter = array(
            'cmdno'             => $cmd_no,                     // 业务代码, 财付通支付支付接口填  1
            'date'              => $today,                      // 商户日期：如20051212
            'bank_type'         => $bank_type,                  // 银行类型:支持纯网关和财付通
            'desc'              => $desc,                       // 交易的商品名称
            'purchaser_id'      => '',                          // 用户(买方)的财付通帐户,可以为空
            'bargainor_id'      => $payment_info['config']['tencentpay_id'],  // 商家的财付通商户号
            'transaction_id'    => $transaction_id,             // 交易号(订单号)，由商户网站产生(建议顺序累加)
            'sp_billno'         => $sp_billno,                  // 商户系统内部的定单号,最多10位
            'total_fee'         => $total_fee,                  // 订单金额
            'fee_type'          => $fee_type,                   // 现金支付币种
            'return_url'        => $return_url,                 // 接收财付通返回结果的URL
            'attach'            => $attach,                     // 用户自定义签名
        	'spbill_create_ip'  => $spbill_create_ip,           // 安全防范参数
            'sign'              => $sign,                       // MD5签名
            //'sys_id'            => '542554970',                 
            //'sp_suggestuser'    => '1202822001'                 //财付通分配的商户号
        );
		//
		

		
		$payLinks = '<form style="text-align:center;" action="https://www.tenpay.com/cgi-bin/v1.0/pay_gate.cgi" target="_blank" style="margin:0px;padding:0px" >';

	 	foreach ($parameter AS $key=>$val)
        {
            $payLinks  .= "<input type='hidden' name='$key' value='$val' />";
        }
        
    	if(!empty($payment_info['logo']))
		{
			$payLinks .= "<input type='image' src='".APP_ROOT.$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
		}
		$payLinks .= "<input type='submit' class='paybutton' value='".sprintf($this->payment_lang['GO_TO_PAY'],$this->get_name($bank_type))."'></form>";
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
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='TenpayBank'");  
    	$payment['config'] = unserialize($payment['config']);
    	
    	
        /*取返回参数*/
        $cmd_no         = $request['cmdno'];
        $pay_result     = $request['pay_result'];
        $pay_info       = $request['pay_info'];
        $bill_date      = $request['date'];
        $bargainor_id   = $request['bargainor_id'];
        $transaction_id = $request['transaction_id'];
        $sp_billno      = $request['sp_billno'];
        $total_fee      = $request['total_fee'];
        $fee_type       = $request['fee_type'];
        $attach         = $request['attach'];
        $sign           = $request['sign'];

        //$payment    = D("Payment")->where("class_name='Tencentpay'")->find(); 
        //$order_sn   = $bill_date . str_pad(intval($sp_billno), 5, '0', STR_PAD_LEFT);
        //$log_id = preg_replace('/0*([0-9]*)/', '\1', $sp_billno); //取得支付的log_id
        //开始初始化参数
        $payment_notice_id = intval($sp_billno);
    	$payment_id = $payment['id'];
 
		if ($pay_result > 0)
        {
            showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
        }
        
        $total_price = $total_fee / 100;

        /* 检查数字签名是否正确 */
        $sign_text  = "cmdno=" . $cmd_no . "&pay_result=" . $pay_result .
                          "&date=" . $bill_date . "&transaction_id=" . $transaction_id .
                            "&sp_billno=" . $sp_billno . "&total_fee=" . $total_fee .
                            "&fee_type=" . $fee_type . "&attach=" . $attach .
                            "&key=" . $payment['config']['tencentpay_key'];
        $sign_md5 = strtoupper(md5($sign_text));
        if ($sign_md5 == $sign)
		{			
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_id."'");
			require_once APP_ROOT_PATH."system/libs/cart.php";
			//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$transaction_id."' where id = ".$payment_notice['id']);
			$rs = payment_paid($payment_notice['id'],$transaction_id);						
		
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
		return false;
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='TenpayBank'");
		if($payment_item)
		{
			$payment_cfg = unserialize($payment_item['config']);

			$html = "<style type='text/css'>.bank_types{float:left; display:block; background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/TenpayBank/banklogo.gif); font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px;}";
	        $html .=".bk_type0{background-position:10px -10px; }"; //默认
	        $html .=".bk_type1001{background-position:15px -444px; }";  //招行
	        $html .=".bk_type1002{background-position:15px -404px; }";  //工行
	        $html .=".bk_type1003{background-position:15px -84px; }"; //建行
	        $html .=".bk_type1005{background-position:15px -44px; }"; //农行
	        $html .=".bk_type1004{background-position:15px -364px; }"; //上海浦东发展银行
	        $html .=".bk_type1008{background-position:15px -324px; }"; //深圳发展银行
	        $html .=".bk_type1009{background-position:15px -484px; }"; //兴业银行
	        $html .=".bk_type1032{background-position:15px -610px; }"; //北京银行
	        $html .=".bk_type1022{background-position:15px -124px; }"; //光大银行
	        $html .=".bk_type1006{background-position:15px -164px; }"; //民生银行
	        $html .=".bk_type1021{background-position:15px -284px; }"; //中信银行
	        $html .=".bk_type1027{background-position:15px -244px; }"; //广东发展银行
	        $html .=".bk_type1010{background-position:15px -903px; }"; //平安银行
	        $html .=".bk_type1052{background-position:15px -939px; }"; //中国银行
	        $html .=".bk_type1020{background-position:15px -204px; }"; //交通银行
	        $html .=".bk_type1030{background-position:15px -782px; }"; //工行企业
	        $html .=".bk_type1042{background-position:15px -864px; }"; //招行企业
	        $html .=".bk_type1028{background-position:15px -524px; }"; //中国邮政储蓄银行(银联)
	        $html .="</style>";
        	$html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$html.= "<h3 class='clearfix tl'><b>财付通直连</b></h3><div class='blank1'></div><hr />";
			foreach ($payment_cfg['tencentpay_gateway'] AS $key=>$val)
	        {
	            $html  .= "<label class='bank_types bk_type".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(".$key.")' /></label>";
	        }
	        $html .= "<input type='hidden' name='bank_id' /><div class='blank'></div>";
			return $html;
		}
		else
		{
			return '';
		}
	}
}
?>