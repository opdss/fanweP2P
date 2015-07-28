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
	'name'	=>	'快钱支付',
	'kq_account'	=>	'快钱支付帐号',
	'kq_key'		=>	'校验码',
	'GO_TO_PAY'	=>	'前往快钱在线支付',
	'KUAIQIAN_ERROR_01'	=>	'商户号错误',
	'KUAIQIAN_ERROR_02'	=>	'支付结果失败',
	'KUAIQIAN_ERROR_03'	=>	'密钥校对错误',
);

$config = array( 
	'kq_account'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //支付宝帐号:
	'kq_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //校验码
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == TRUE)
{
    $module['class_name']    = 'Kuaiqian';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';
    
     /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
       
    /* 插件作者的官方网站 */
    $module['reg_url'] = 'https://www.99bill.com/jieru/jieru.html';

    return $module;
}

// 贝宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Kuaiqian_payment implements payment {
	
	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
				
				
		$data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Kuaiqian';
		$data_notify_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Kuaiqian';
		
		$merchant_acctid    = trim($payment_info['config']['kq_account']);           //人民币账号 不可空
		$key                = trim($payment_info['config']['kq_key']);
		$input_charset      = 1;                                                		//字符集 默认1=utf-8
		$page_url           = $data_return_url;
		$bg_url             = $data_notify_url;
		$version            = 'v2.0';
		$language           = 1;
		$sign_type          = 1;                                           //签名类型 不可空 固定值 1:md5
		$payer_name         = '';
		$payer_contact_type = '';
		$payer_contact      = '';
		$order_id           = $payment_notice['notice_sn'];                                    //商户订单号 不可空
		$order_amount       = $money * 100;                        		  //商户订单金额 不可空
		$order_time         = to_date(TIME_UTC, 'YmdHis');            	  //商户订单提交时间 不可空 14位
		$product_name       = '';
		$product_num        = '';
		$product_id         = '';
		$product_desc       = '';
		$ext1               = $payment_notice_id;
		$ext2               = '';
		$pay_type           = '00';                                                //支付方式 不可空
		$bank_id            = '';
		$redo_flag          = '0';
		$pid                = '';

      
        /* 生成加密签名串 请务必按照如下顺序和规则组成加密串！*/
        $signmsgval = '';
        $signmsgval = $this->append_param($signmsgval, "inputCharset", $input_charset);
        $signmsgval = $this->append_param($signmsgval, "pageUrl", $page_url);
        $signmsgval = $this->append_param($signmsgval, "bgUrl", $bg_url);
        $signmsgval = $this->append_param($signmsgval, "version", $version);
        $signmsgval = $this->append_param($signmsgval, "language", $language);
        $signmsgval = $this->append_param($signmsgval, "signType", $sign_type);
        $signmsgval = $this->append_param($signmsgval, "merchantAcctId", $merchant_acctid);
        $signmsgval = $this->append_param($signmsgval, "payerName", $payer_name);
        $signmsgval = $this->append_param($signmsgval, "payerContactType", $payer_contact_type);
        $signmsgval = $this->append_param($signmsgval, "payerContact", $payer_contact);
        $signmsgval = $this->append_param($signmsgval, "orderId", $order_id);
        $signmsgval = $this->append_param($signmsgval, "orderAmount", $order_amount);
        $signmsgval = $this->append_param($signmsgval, "orderTime", $order_time);
        $signmsgval = $this->append_param($signmsgval, "productName", $product_name);
        $signmsgval = $this->append_param($signmsgval, "productNum", $product_num);
        $signmsgval = $this->append_param($signmsgval, "productId", $product_id);
        $signmsgval = $this->append_param($signmsgval, "productDesc", $product_desc);
        $signmsgval = $this->append_param($signmsgval, "ext1", $ext1);
        $signmsgval = $this->append_param($signmsgval, "ext2", $ext2);
        $signmsgval = $this->append_param($signmsgval, "payType", $pay_type);
        $signmsgval = $this->append_param($signmsgval, "bankId", $bank_id);
        $signmsgval = $this->append_param($signmsgval, "redoFlag", $redo_flag);
        $signmsgval = $this->append_param($signmsgval, "pid", $pid);
        $signmsgval = $this->append_param($signmsgval, "key", $key);
        $signmsg    = strtoupper(md5($signmsgval));    //签名字符串 不可空

		//https://www.99bill.com/gateway/recvMerchantInfoAction.htm
        //https://sandbox.99bill.com/gateway/recvMerchantInfoAction.htm
        $def_url  = '<div style="text-align:center"><form name="kqPay" style="text-align:center;" method="post" action="https://www.99bill.com/gateway/recvMerchantInfoAction.htm" target="_blank">';
        $def_url .= "<input type='hidden' name='inputCharset' value='" . $input_charset . "' />";
        $def_url .= "<input type='hidden' name='bgUrl' value='" . $bg_url . "' />";
        $def_url .= "<input type='hidden' name='pageUrl' value='" . $page_url . "' />";
        $def_url .= "<input type='hidden' name='version' value='" . $version . "' />";
        $def_url .= "<input type='hidden' name='language' value='" . $language . "' />";
        $def_url .= "<input type='hidden' name='signType' value='" . $sign_type . "' />";
        $def_url .= "<input type='hidden' name='signMsg' value='" . $signmsg . "' />";
        $def_url .= "<input type='hidden' name='merchantAcctId' value='" . $merchant_acctid . "' />";
        $def_url .= "<input type='hidden' name='payerName' value='" . $payer_name . "' />";
        $def_url .= "<input type='hidden' name='payerContactType' value='" . $payer_contact_type . "' />";
        $def_url .= "<input type='hidden' name='payerContact' value='" . $payer_contact . "' />";
        $def_url .= "<input type='hidden' name='orderId' value='" . $order_id . "' />";
        $def_url .= "<input type='hidden' name='orderAmount' value='" . $order_amount . "' />";
        $def_url .= "<input type='hidden' name='orderTime' value='" . $order_time . "' />";
        $def_url .= "<input type='hidden' name='productName' value='" . $product_name . "' />";
        $def_url .= "<input type='hidden' name='payType' value='" . $pay_type . "' />";
        $def_url .= "<input type='hidden' name='productNum' value='" . $product_num . "' />";
        $def_url .= "<input type='hidden' name='productId' value='" . $product_id . "' />";
        $def_url .= "<input type='hidden' name='productDesc' value='" . $product_desc . "' />";
        $def_url .= "<input type='hidden' name='ext1' value='" . $ext1 . "' />";
        $def_url .= "<input type='hidden' name='ext2' value='" . $ext2 . "' />";
        $def_url .= "<input type='hidden' name='bankId' value='" . $bank_id . "' />";
        $def_url .= "<input type='hidden' name='redoFlag' value='" . $redo_flag ."' />";
        $def_url .= "<input type='hidden' name='pid' value='" . $pid . "' />";
        $def_url .= "<input type='submit' name='submit' class='paybutton' value='前往快钱在线支付' />";
        $def_url .= "</form></div></br>";
		$def_url .="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</span>";
        return $def_url;	
	}
	
	public function get_display_code(){
    	$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Kuaiqian'");
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
    	
    	$ext1  = trim($request['ext1']);
        //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Kuaiqian'");  
    	$payment['config'] = unserialize($payment['config']);
    	    	    	
        $merchant_acctid     = $payment['config']['kq_account'];                 //人民币账号 不可空
        $key                 = $payment['config']['kq_key'];
        $get_merchant_acctid = trim($request['merchantAcctId']);
        $pay_result          = trim($request['payResult']);
        $version             = trim($request['version']);
        $language            = trim($request['language']);
        $sign_type           = trim($request['signType']);
        $pay_type            = trim($request['payType']);
        $bank_id             = trim($request['bankId']);
        $order_id            = trim($request['orderId']);
        $order_time          = trim($request['orderTime']);
        $order_amount        = trim($request['orderAmount']);
        $deal_id             = trim($request['dealId']);
        $bank_deal_id        = trim($request['bankDealId']);
        $deal_time           = trim($request['dealTime']);
        $pay_amount          = trim($request['payAmount']);
        $fee                 = trim($request['fee']);
        
        $ext2                = trim($request['ext2']);
        $err_code            = trim($request['errCode']);
        $sign_msg            = trim($request['signMsg']);

        
        //开始初始化参数
        $payment_log_id = intval($ext1);
    	$money = $pay_amount/100;
    	$payment_id = $payment['id'];
    	$currency_id = $payment['currency'];         
        
        //生成加密串。必须保持如下顺序。
        $merchant_signmsgval = '';
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"merchantAcctId",$merchant_acctid);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"version",$version);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"language",$language);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"signType",$sign_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payType",$pay_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"bankId",$bank_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderId",$order_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderTime",$order_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderAmount",$order_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"dealId",$deal_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"bankDealId",$bank_deal_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"dealTime",$deal_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payAmount",$pay_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"fee",$fee);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"ext1",$ext1);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"ext2",$ext2);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payResult",$pay_result);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"errCode",$err_code);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"key",$key);
        $merchant_signmsg    = md5($merchant_signmsgval);

        //首先对获得的商户号进行比对
        if ($get_merchant_acctid != $merchant_acctid)
        {
            //商户号错误
            showErr($GLOBALS['payment_lang']['KUAIQIAN_ERROR_01']);
        }

        if (strtoupper($sign_msg) == strtoupper($merchant_signmsg))
        {
            if ($pay_result == 10 || $pay_result == 00)
            {
                $payment_log_id = intval($ext1);
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_log_id."'");
				
				require_once APP_ROOT_PATH."system/libs/cart.php";
				//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$deal_id."' where id = ".$payment_notice['id']);
				$rs = payment_paid($payment_notice['id'],$deal_id);						
            	
				$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
				if ($is_paid == 1){
					app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
				}else{
					app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
				}
            }
            else
            {
                //'支付结果失败';
                showErr($GLOBALS['payment_lang']['KUAIQIAN_ERROR_02']);
            }
        }
        else
        {
            //'密钥校对错误';
            showErr($GLOBALS['payment_lang']['KUAIQIAN_ERROR_03']);
        }         
         
         
	}
	public function notify($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
    	
    	$ext1  = trim($request['ext1']);
        //$payment_id = $GLOBALS['db']->getOne("select payment_id from ".DB_PREFIX."payment_log where id=".intval($ext1));
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Kuaiqian'");  
    	$payment['config'] = unserialize($payment['config']);
    	    	    	
        $merchant_acctid     = $payment['config']['kq_account'];                 //人民币账号 不可空
        $key                 = $payment['config']['kq_key'];
        $get_merchant_acctid = trim($request['merchantAcctId']);
        $pay_result          = trim($request['payResult']);
        $version             = trim($request['version']);
        $language            = trim($request['language']);
        $sign_type           = trim($request['signType']);
        $pay_type            = trim($request['payType']);
        $bank_id             = trim($request['bankId']);
        $order_id            = trim($request['orderId']);
        $order_time          = trim($request['orderTime']);
        $order_amount        = trim($request['orderAmount']);
        $deal_id             = trim($request['dealId']);
        $bank_deal_id        = trim($request['bankDealId']);
        $deal_time           = trim($request['dealTime']);
        $pay_amount          = trim($request['payAmount']);
        $fee                 = trim($request['fee']);
        
        $ext2                = trim($request['ext2']);
        $err_code            = trim($request['errCode']);
        $sign_msg            = trim($request['signMsg']);

        
        //开始初始化参数
        $payment_log_id = intval($ext1);
    	$money = $pay_amount/100;
    	$payment_id = $payment['id'];
    	$currency_id = $payment['currency'];         
        
        //生成加密串。必须保持如下顺序。
        $merchant_signmsgval = '';
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"merchantAcctId",$merchant_acctid);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"version",$version);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"language",$language);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"signType",$sign_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payType",$pay_type);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"bankId",$bank_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderId",$order_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderTime",$order_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"orderAmount",$order_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"dealId",$deal_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"bankDealId",$bank_deal_id);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"dealTime",$deal_time);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payAmount",$pay_amount);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"fee",$fee);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"ext1",$ext1);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"ext2",$ext2);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"payResult",$pay_result);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"errCode",$err_code);
        $merchant_signmsgval = $this->append_param($merchant_signmsgval,"key",$key);
        $merchant_signmsg    = md5($merchant_signmsgval);

        //首先对获得的商户号进行比对
        if ($get_merchant_acctid != $merchant_acctid)
        {
            //商户号错误
            echo 0;
        }

        if (strtoupper($sign_msg) == strtoupper($merchant_signmsg))
        {
            if ($pay_result == 10 || $pay_result == 00)
            {
                $payment_log_id = intval($ext1);
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_log_id."'");
				
				//$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
				require_once APP_ROOT_PATH."system/libs/cart.php";
				$rs = payment_paid($payment_notice['id'],$deal_id);						
				if($rs)
				{			
					//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$deal_id."' where id = ".$payment_notice['id']);				
					order_paid($payment_notice['order_id']);				
					echo '1';
				}
				else
				{
					echo '0';
				}
            }
            else
            {
                //'支付结果失败';
                 echo 0;
            }
        }
        else
        {
            //'密钥校对错误';
            echo 0;
        }   
	}
	
    /**
    * 将变量值不为空的参数组成字符串
    * @param   string   $strs  参数字符串
    * @param   string   $key   参数键名
    * @param   string   $val   参数键对应值
    */
    function append_param($strs,$key,$val)
    {
        if($strs != "")
        {
            if($key != '' && $val != '')
            {
                $strs .= '&' . $key . '=' . $val;
            }
        }
        else
        {
            if($val != '')
            {
                $strs = $key . '=' . $val;
            }
        }
            return $strs;
    }	
}
?>