<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'盛支付',
	'sdo_account'	=>	'商户编号',
	'sdo_key'	=>	'商户密钥',
	
	'sdopay_gateway'	=>	'支持的银行',
	'sdopay_gateway_SDO1'	=>	'盛付通',
	'sdopay_gateway_ICBC'	=>	'工商银行',
	'sdopay_gateway_CCB'	=>	'建设银行',
	'sdopay_gateway_ABC'	=>	'农业银行',
	'sdopay_gateway_CMB'	=>	'招商银行',
	'sdopay_gateway_COMM'	=>	'交通银行',
	'sdopay_gateway_CMBC'	=>	'民生银行',
	'sdopay_gateway_CIB'	=>	'兴业银行',
	'sdopay_gateway_HCCB'	=>	'杭州银行',
	'sdopay_gateway_CEB'	=>	'光大银行',
	'sdopay_gateway_CITIC'	=>	'中信银行',
	'sdopay_gateway_GZCB'	=>	'广州银行',
	'sdopay_gateway_HXB'	=>	'华夏银行',
	'sdopay_gateway_HKBEA'	=>	'东亚银行',
	'sdopay_gateway_BOC'	=>	'中国银行',
	'sdopay_gateway_WZCB'	=>	'温州银行',
	'sdopay_gateway_BCCB'	=>	'北京银行',
	'sdopay_gateway_SXJS'	=>	'晋商银行',
	'sdopay_gateway_NBCB'	=>	'宁波银行',
	'sdopay_gateway_SZPAB'	=>	'平安银行',
	'sdopay_gateway_BOS'	=>	'上海银行',
	'sdopay_gateway_NJCB'	=>	'南京银行',
	'sdopay_gateway_SPDB'	=>	'浦东发展银行',
	'sdopay_gateway_GNXS'	=>	'广州市农村信用合作社',
	'sdopay_gateway_GDB'	=>	'广东发展银行',
	'sdopay_gateway_SHRCB'	=>	'上海市农村商业银行',
	'sdopay_gateway_CBHB'	=>	'渤海银行',
	'sdopay_gateway_HKBCHINA'	=>	'汉口银行',
	'sdopay_gateway_ZHNX'	=>	'珠海市农村信用合作联社',
	'sdopay_gateway_SDE'	=>	'顺德农信社',
	'sdopay_gateway_YDXH'	=>	'尧都信用合作联社',
	'sdopay_gateway_CZCB'	=>	'浙江稠州商业银行',
	'sdopay_gateway_BJRCB'	=>	'北京农商行',
	'sdopay_gateway_PSBC'	=>	'中国邮政储蓄银行',
	'sdopay_gateway_SDB'	=>	'深圳发展银行',
	
);
$config = array(
	'sdo_account'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户编号
	'sdo_key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户密钥 
	
	//'sdo_bankcode'=>'SDTBNK',  //银行编号
	'sdopay_gateway'	=>	array(
		'INPUT_TYPE'	=>	'3',
		'VALUES'	=>	array(
			'SDO1',    //盛付通
			'ICBC',    //工商银行
			'CCB',    //建设银行
			'ABC',    //农业银行
			'CMB',    //招商银行
			'COMM',    //交通银行
			'CMBC',    //民生银行
			'CIB',    //兴业银行
			'HCCB',    //杭州银行
			'CEB',    //光大银行
			'CITIC',    //中信银行
			'GZCB',    //广州银行
			'HXB',    //华夏银行
			'HKBEA',    //东亚银行
			'BOC',    //中国银行
			'WZCB',    //温州银行
			'BCCB',    //北京银行
			'SXJS',    //晋商银行
			'NBCB',    //宁波银行
			'SZPAB',    //平安银行
			'BOS',    //上海银行
			'NJCB',    //南京银行
			'SPDB',    //浦东发展银行
			'GNXS',    //广州市农村信用合作社
			'GDB',    //广东发展银行
			'SHRCB',    //上海市农村商业银行
			'CBHB',    //渤海银行
			'HKBCHINA',    //汉口银行
			'ZHNX',    //珠海市农村信用合作联社
			'SDE',    //顺德农信社
			'YDXH',    //尧都信用合作联社
			'CZCB',    //浙江稠州商业银行
			'BJRCB',    //北京农商行
			'PSBC',    //中国邮政储蓄银行
			'SDB',    //深圳发展银行
			
		)
	)	
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Sdo';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    
    $module['reg_url'] = 'https://www.shengpay.com/';
    
    return $module;
}


//http://pre.biz.sfubao.com/MerLogin.aspx 测试商户号:827438 测试登陆密码:38953532

require_once(APP_ROOT_PATH.'system/payment/SdoBank/core.php');
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Sdo_payment implements payment{
	private  $payment_lang = array(
		'GO_TO_PAY'	=>	'前往盛支付支付',
		'sdopay_gateway'	=>	'支持的银行',
		'sdopay_gateway_SDO1'	=>	'盛付通',
		'sdopay_gateway_SDTBNK'	=>	'测试银行',
		'sdopay_gateway_ICBC'	=>	'工商银行',
		'sdopay_gateway_CCB'	=>	'建设银行',
		'sdopay_gateway_ABC'	=>	'农业银行',
		'sdopay_gateway_CMB'	=>	'招商银行',
		'sdopay_gateway_COMM'	=>	'交通银行',
		'sdopay_gateway_CMBC'	=>	'民生银行',
		'sdopay_gateway_CIB'	=>	'兴业银行',
		'sdopay_gateway_HCCB'	=>	'杭州银行',
		'sdopay_gateway_CEB'	=>	'光大银行',
		'sdopay_gateway_CITIC'	=>	'中信银行',
		'sdopay_gateway_GZCB'	=>	'广州银行',
		'sdopay_gateway_HXB'	=>	'华夏银行',
		'sdopay_gateway_HKBEA'	=>	'东亚银行',
		'sdopay_gateway_BOC'	=>	'中国银行',
		'sdopay_gateway_WZCB'	=>	'温州银行',
		'sdopay_gateway_BCCB'	=>	'北京银行',
		'sdopay_gateway_SXJS'	=>	'晋商银行',
		'sdopay_gateway_NBCB'	=>	'宁波银行',
		'sdopay_gateway_SZPAB'	=>	'平安银行',
		'sdopay_gateway_BOS'	=>	'上海银行',
		'sdopay_gateway_NJCB'	=>	'南京银行',
		'sdopay_gateway_SPDB'	=>	'浦东发展银行',
		'sdopay_gateway_GNXS'	=>	'广州市农村信用合作社',
		'sdopay_gateway_GDB'	=>	'广东发展银行',
		'sdopay_gateway_SHRCB'	=>	'上海市农村商业银行',
		'sdopay_gateway_CBHB'	=>	'渤海银行',
		'sdopay_gateway_HKBCHINA'	=>	'汉口银行',
		'sdopay_gateway_ZHNX'	=>	'珠海市农村信用合作联社',
		'sdopay_gateway_SDE'	=>	'顺德农信社',
		'sdopay_gateway_YDXH'	=>	'尧都信用合作联社',
		'sdopay_gateway_CZCB'	=>	'浙江稠州商业银行',
		'sdopay_gateway_BJRCB'	=>	'北京农商行',
		'sdopay_gateway_PSBC'	=>	'中国邮政储蓄银行',
		'sdopay_gateway_SDB'	=>	'深圳发展银行',
	);
	private $config = array(
		'sdo_paychannel'=>'04',  //支付通道
		'sdo_defaultchannel'=>'04',  //默认支付通道
	);
	
	private $bank_types = array(
			'SDO1',//盛付通
			'SDTBNK',    //测试银行
			'ICBC',    //工商银行
			'CCB',    //建设银行
			'ABC',    //农业银行
			'CMB',    //招商银行
			'COMM',    //交通银行
			'CMBC',    //民生银行
			'CIB',    //兴业银行
			'HCCB',    //杭州银行
			'CEB',    //光大银行
			'CITIC',    //中信银行
			'GZCB',    //广州银行
			'HXB',    //华夏银行
			'HKBEA',    //东亚银行
			'BOC',    //中国银行
			'WZCB',    //温州银行
			'BCCB',    //北京银行
			'SXJS',    //晋商银行
			'NBCB',    //宁波银行
			'SZPAB',    //平安银行
			'BOS',    //上海银行
			'NJCB',    //南京银行
			'SPDB',    //浦东发展银行
			'GNXS',    //广州市农村信用合作社
			'GDB',    //广东发展银行
			'SHRCB',    //上海市农村商业银行
			'CBHB',    //渤海银行
			'HKBCHINA',    //汉口银行
			'ZHNX',    //珠海市农村信用合作联社
			'SDE',    //顺德农信社
			'YDXH',    //尧都信用合作联社
			'CZCB',    //浙江稠州商业银行
			'BJRCB',    //北京农商行
			'PSBC',    //中国邮政储蓄银行
			'SDB',    //深圳发展银行
	);	
	
		
	public function get_name($bank_id)
	{
		return $this->payment_lang['sdopay_gateway_'.$bank_id];
	}
	
	public function get_payment_code($payment_notice_id)
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		
		/* 银行类型 */
        //$bank_id = $GLOBALS['db']->getOne("select bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$bank_id = $payment_notice['bank_id'];
		
		$payChannel = $this->config['sdo_paychannel'];
		$defaultChannel = $this->config['sdo_defaultchannel'];
		
		if ($bank_id=='0' || trim($bank_id) == 'SDO1' || trim($bank_id) == 'SDO'){
			$bank_id = '';
		}		
				
		$postBackURL = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Sdo';//付款完成后的跳转页面
		$notifyURL = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Sdo';//通知发货页面
		
        
        $shengpay=new shengpay();
		$array=array(
			'Name'=>'B2CPayment',
			'Version'=>'V4.1.1.1.1',
			'Charset'=>'UTF-8',
			'MsgSender'=>$payment_info['config']['sdo_account'],
			'SendTime'=>to_date(get_gmtime(),'YmdHis'),
			'OrderTime'=>to_date(get_gmtime(),'YmdHis'),
			'PayType'=>'PT001',
			'PayChannel'=>'14,18,19,20',/*（19 储蓄卡，20 信用卡）做直连时，储蓄卡和信用卡需要分开*/
			'InstCode'=>$bank_id, //银行编码，参考接口文档
			'PageUrl'=>$postBackURL,
			'NotifyUrl'=>$notifyURL,
			'ProductName'=>$payment_notice_id,
			'BuyerContact'=>'',
			'BuyerIp'=>'',
			'Ext1'=>'',
			'Ext2'=>'',
			'SignType'=>'MD5',
		);
		$shengpay->init($array);
		$shengpay->setKey($payment_info['config']['sdo_key']);
		
		
		/*
		/*
			商家自行检测传入的价格与数据库订单需支付金额是否相同
		*/
		$code = $shengpay->takeOrder($payment_notice_id,$money,$payment_info);
				
        $code.="<br /><span class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</span>";
        return $code;
	}
	
	public function response($request)
	{	
        
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Sdo'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$shengpay=new shengpay();
		$shengpay->setKey($payment['config']['sdo_key']);
		
		
		if($shengpay->returnSign()){
			/*支付成功
			$oid=$_POST['OrderNo'];
			$fee=$_POST['TransAmount'];
			*/
        	$payment_log_id = intval($_POST['OrderNo']);
        	$_serialNo = trim($_POST['TransNo']);
			
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_log_id."'");
			
			require_once APP_ROOT_PATH."system/libs/cart.php";
			//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$_serialNo."' where id = ".$payment_notice['id']);
			$rs = payment_paid($payment_notice['id'],$_serialNo);						
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['id']))); //支付成功
			}else{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
        }
        else{
		    showErr($GLOBALS['payment_lang']["PAY_FAILED"]);
        }  	
	}
	
	public function notify($request)
	{
		$return_res = array(
			'info'=>'',
			'status'=>false,
		);
		
		
		
		$payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Sdo'");  
    	$payment['config'] = unserialize($payment['config']);
    	
		$shengpay=new shengpay();
		$shengpay->setKey($payment['config']['sdo_key']);
		
       
		if($shengpay->returnSign()){
			/*支付成功
			$oid=$_POST['OrderNo'];
			$fee=$_POST['TransAmount'];
			*/
        	$payment_log_id = intval($_POST['OrderNo']);
        	$_serialNo = trim($_POST['TransNo']);
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_log_id."'");
			
			$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/libs/cart.php";
			//$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set outer_notice_sn = '".$_serialNo."' where id = ".$payment_notice['id']);
			$rs = payment_paid($payment_notice['id'],$_serialNo);						
			if($rs)
			{
				$rs = order_paid($payment_notice['order_id']);
				if($rs)
				{
					echo 'OK';
				}
				else 
				{
					echo 'OK';
				}
				
			}
			else
			{
				echo 'Error';
			}
        }
        else{
		    echo 'Error';
        }  	
	}
	
	
	public function get_display_code(){
		
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Sdo'");
		
		$payment_cfg = unserialize($payment_item['config']);
 		if($payment_item)
		{
	        $def_url = "<style type='text/css'>.bank_sdo_types{float:left; display:block; background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/SdoBank/banklogo.gif) no-repeat; font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px; }";
	        
	        $def_url .=".bk_typeSDO1{background-position:15px -1414px; }";    //盛付通
			$def_url .=".bk_typeSDTBNK{background-position:15px -5px; }";    //测试银行
			$def_url .=".bk_typeICBC{background-position:15px -42px; }";    //工商银行
			$def_url .=".bk_typeCCB{background-position:15px -82px; }";    //建设银行
			$def_url .=".bk_typeABC{background-position:15px -120px; }";    //农业银行
			$def_url .=".bk_typeCMB{background-position:15px -160px; }";    //招商银行
			$def_url .=".bk_typeCOMM{background-position:15px -204px; }";    //交通银行
			$def_url .=".bk_typeCMBC{background-position:15px -244px; }";    //民生银行
			$def_url .=".bk_typeCIB{background-position:15px -280px; }";    //兴业银行
			$def_url .=".bk_typeHCCB{background-position:15px -324px; }";    //杭州银行
			$def_url .=".bk_typeCEB{background-position:15px -362px; }";    //光大银行
			$def_url .=".bk_typeCITIC{background-position:15px -400px; }";    //中信银行
			$def_url .=".bk_typeGZCB{background-position:15px -440px; }";    //广州银行
			$def_url .=".bk_typeHXB{background-position:15px -480px; }";    //华夏银行
			$def_url .=".bk_typeHKBEA{background-position:15px -519px; }";    //东亚银行
			$def_url .=".bk_typeBOC{background-position:15px -566px; }";    //中国银行
			$def_url .=".bk_typeWZCB{background-position:15px -608px; }";    //温州银行
			$def_url .=".bk_typeBCCB{background-position:15px -651px; }";    //北京银行
			$def_url .=".bk_typeSXJS{background-position:15px -700px; }";    //晋商银行
			$def_url .=".bk_typeNBCB{background-position:15px -743px; }";    //宁波银行
			$def_url .=".bk_typeSZPAB{background-position:15px -783px; }";    //平安银行
			$def_url .=".bk_typeBOS{background-position:15px -821px; }";    //上海银行
			$def_url .=".bk_typeNJCB{background-position:15px -860px; }";    //南京银行
			$def_url .=".bk_typeSPDB{background-position:15px -900px; }";    //浦东发展银行
			$def_url .=".bk_typeGNXS{background-position:15px -935px; }";    //广州市农村信用合作社
			$def_url .=".bk_typeGDB{background-position:15px -975px; }";    //广东发展银行
			$def_url .=".bk_typeSHRCB{background-position:15px -1015px; }";    //上海市农村商业银行
			$def_url .=".bk_typeCBHB{background-position:15px -1052px; }";    //渤海银行
			$def_url .=".bk_typeHKBCHINA{background-position:15px -1095px; }";    //汉口银行
			$def_url .=".bk_typeZHNX{background-position:15px -1135px; }";    //珠海市农村信用合作联社
			$def_url .=".bk_typeSDE{background-position:15px -1175px; }";    //顺德农信社
			$def_url .=".bk_typeYDXH{background-position:15px -1215px; }";    //尧都信用合作联社
			$def_url .=".bk_typeCZCB{background-position:15px -1255px; }";    //浙江稠州商业银行
			$def_url .=".bk_typeBJRCB{background-position:15px -1295px; }";    //北京农商行
			$def_url .=".bk_typePSBC{background-position:15px -1335px; }";    //中国邮政储蓄银行
			$def_url .=".bk_typeSDB{background-position:15px -1375px; }";    //深圳发展银行        
	        $def_url .="</style>";
	        $def_url .="<script type='text/javascript'>function set_bank(bank_id)";
			$def_url .="{";
			$def_url .="$(\"input[name='bank_id']\").val(bank_id);";
			$def_url .="}</script>";
			foreach ($payment_cfg['sdopay_gateway'] AS $key=>$val)
	        {
	            $def_url  .= "<label class='bank_sdo_types bk_type".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
	        }
	        $def_url .= "<input type='hidden' name='bank_id' />";
			return $def_url;
		}
		else
		{
			return '';
		}
	}
		
	public function verifySign($amount , $payAmount ,$orderNo
    		,$serialNo,$status,$merchantNo , $payChannel
    		,$discount,$signType ,$payTime,$currencyType
    		,$productNo,$productDesc,$remark1,$remark2,$exInfo,$md5key){
    			
			$toSignString = $amount."|".$payAmount."|".$orderNo."|".
										$serialNo."|".$status."|".$merchantNo."|".
										$payChannel."|".$discount."|".$signType."|".
										$payTime."|".$currencyType."|".$productNo."|".
										$productDesc."|".$remark1."|".$remark2."|".$exInfo;

			return  md5($toSignString. "|" . $md5key);			
		}	
		
}
?>