<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'线下充值接口',
	'pay_bank'	=>	'开户行',
	'pay_name'	=>	'收款方式名称',
	'pay_account'	=>	'收款帐号',
	'pay_account_name'	=>	'收款人',

);
$config = array(
	'pay_bank'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //开户行
	'pay_name'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //收款方式名称
	'pay_account'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //收款帐号
	'pay_account_name'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //收款人
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Otherpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '0';

    /* 配送 */
    $module['config'] = $config;
    
    $module['reg_url'] = '';
    
    $module['lang'] = $payment_lang;
    return $module;
}

// 其他自定义支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Otherpay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);	
		//$bank_id = (int)$GLOBALS['db']->getOne("select bank_id from ".DB_PREFIX."deal_order where id=".intval($payment_notice['order_id']));
		$bank_id = $payment_notice['bank_id'];
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		$code = "收款帐号:".$payment_info['config']['pay_account'][$bank_id]."<br /><br />开户行:".$payment_info['config']['pay_bank'][$bank_id]."<br /><br />收款人:".$payment_info['config']['pay_account_name'][$bank_id];
		/*if($payment_notice['memo'])
			$code .= "<br /><br />银行流水号：".$payment_notice['memo'];*/
		$code.="<br /><br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div> <br/> 已经提交,等待管理员审核";
        return $code;
	}
	
	public function response($request)
	{
        
		return false;
	}
	
	public function notify($request)
	{
		return false;
	}
	
	public function get_display_code()
	{
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Otherpay'");
		if($payment_item)
		{
			$payment_item['config'] = unserialize($payment_item['config']);
			
			$html = "<div class='clearfix'>".nl2br($payment_item['description'])."</div><div class='blank'></div>";
			$html .= "<div class='clearfix below_pay_list'>";
			$html .= "<div class='f_l w90 '>银行流水号：</div><div class='f_l  pt10  '><input type='text' name='memo' class='f-input ui-textbox' value='' /></div>";
			$html .="<div class='blank'></div><div class='f_l  w90'>充值银行：</div><div class='f_l'>";
			$count = count($payment_item['config']['pay_name']);
			for($kk=0;$kk<$count;$kk++){
				
				$html .= "<div class='clearfix'>";
				$html .= "<label class='f_l ui-radiobox payment_lbl' rel='common_payment'>" .
						"<input type='radio' name='payment' value='".$payment_item['id']."' onclick='set_bank(\"".$kk."\")' />".
						$payment_item['config']['pay_name'][$kk]." 收款人：".$payment_item['config']['pay_account_name'][$kk]."&nbsp;&nbsp;&nbsp;&nbsp;" .
						"收款帐号：".$payment_item['config']['pay_account'][$kk]."&nbsp;&nbsp;&nbsp;&nbsp;开户行：".$payment_item['config']['pay_bank'][$kk]."" .
						"</label>";
				$html .="</div><div class='blank'></div>";
			}
			$html .="</div></div>";
			
			if($payment_item['logo']!='')
			{
				$html .= "<div class='clearfix'><img src='".APP_ROOT.$payment_item['logo']."' /></div>";
			}
			
			$html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$html .= "<input type='hidden' name='bank_id' />";
			return $html;
		}
		else
		{
			return '';
		}
	}
}
?>