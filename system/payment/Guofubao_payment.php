<?php

// +----------------------------------------------------------------------
// | Fanwe 多语商城建站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: jobin.lin(jobin.lin@gmail.com)
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
// | 国付报 直连银行支付
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'国付宝支付',
	'merchant_id'	=>	'合作者身份ID',
	'virCardNoIn'	=>	'国付宝账户',
	'VerficationCode'		=>	'商户识别码',
	'GO_TO_PAY'	=>	'前往国付宝在线支付',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'guofubao_gateway'	=>	'支持的银行',
	'guofubao_gateway_0'	=>	'纯网关支付',
	'guofubao_gateway_CCB'	=>	'中国建设银行',
	'guofubao_gateway_CMB'	=>	'招商银行',
	'guofubao_gateway_ICBC'	=>	'中国工商银行',
	'guofubao_gateway_BOC'	=>	'中国银行',
	'guofubao_gateway_ABC'	=>	'中国农业银行',
	'guofubao_gateway_BOCOM'	=>	'中国交通银行',
	'guofubao_gateway_CMBC'	=>	'中国民生银行',
	'guofubao_gateway_HXBC'	=>	'华夏银行',
	'guofubao_gateway_CIB'	=>	'兴业银行',
	'guofubao_gateway_SPDB'	=>	'上海浦东发展银行',
	'guofubao_gateway_GDB'	=>	'广东发展银行',
	'guofubao_gateway_CITIC'	=>	'中信银行',
	'guofubao_gateway_CEB'	=>	'光大银行',
	'guofubao_gateway_PSBC'	=>	'中国邮政储蓄银行',
	'guofubao_gateway_SDB'	=>	'深圳发展银行',
	'guofubao_gateway_BOBJ'	=>	'北京银行',
	'guofubao_gateway_PAB'	=>	'平安银行',
	'guofubao_gateway_BOS'	=>	'上海银行',
	'guofubao_gateway_NBCB'	=>	'宁波银行',
	'guofubao_gateway_NJCB'	=>	'南京银行',
	'guofubao_gateway_TCCB'	=>	'天津银行',
);

$config = array(
    'merchant_id' => '', //商户ID
    'virCardNoIn' => '', //国付宝账户
    'VerficationCode'=>'',  //商户识别码
    'guofubao_gateway' => array(
    	'INPUT_TYPE'	=>	'3',
    	'VALUES'	=>	array(
    		'0', //纯网关
	        'CCB', //中国建设银行
	        'CMB', //招商银行
	        'ICBC', //中国工商银行
	        'BOC', //中国银行
	        'ABC', //中国农业银行
	        'BOCOM', //交通银行
	        'CMBC', //中国民生银行
	        'HXBC', //华夏银行
	        'CIB', //兴业银行
	        'SPDB', //上海浦东发展银行
	        'GDB', //广东发展银行
	        'CITIC', //中信银行
	        'CEB', //光大银行
	        'PSBC', //中国邮政储蓄银行
	        'SDB', //深圳发展银行
	        'BOBJ', //北京银行
	        'PAB', //平安银行
	        'BOS', //上海银行
	        'NBCB', //宁波银行
	        'NJCB', //南京银行
	        'TCCB', //天津银行
        ),
    ),
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true){
    
    /* 会员数据整合插件的代码必须和文件名保持一致 */
    $module['class_name']    = 'Guofubao';

    /* 被整合的第三方程序的名称 */
    $module['name'] = $payment_lang['name'];
    
    /* 支付方式：1：在线支付；0：线下支付 */
    $module['online_pay'] = '1';
	
	 /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
	
    $module['reg_url'] = 'https://www.gopay.com.cn';
    
    return $module;
}

// 国付宝模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Guofubao_payment implements payment {

    public function get_payment_code($payment_notice_id) {
        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		//$order = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$order_sn = $payment_notice['notice_sn'];
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
        
        /*$data_front_url =  SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=notify&class_name=Guofubao';
        $data_front_url="";
        $data_return_url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=payment&act=response&class_name=Guofubao';
        */
        //新
        $data_front_url =  SITE_DOMAIN.APP_ROOT.'/callback/pay/guofubao_callback.php?act=notify';
        //$data_front_url="";
        $data_return_url = SITE_DOMAIN.APP_ROOT.'/callback/pay/guofubao_callback.php?act=response';


        $tranCode = '8888';

        //$spbill_create_ip = $_SERVER['REMOTE_ADDR'];
        $spbill_create_ip = CLIENT_IP;

        /* 交易日期 */
        $today = to_date($payment_notice['create_time'], 'YmdHis');

        
        $bank_id = $payment_notice['bank_id'];
        if ($bank_id == '0')
        {
        	$bank_id = '';
        }
       
        $desc = $order_sn;
       
       	include_once(APP_ROOT_PATH."system/libs/iconv.php");
		$chinese = new Chinese();
		$desc = $chinese->Convert("UTF-8","GBK",$desc);
      
        /* 货币类型 */
        $currencyType = '156';


        /* 数字签名 */
        $version = '2.1';   
        $tranCode = $tranCode; 
        $merchant_id = $payment_info['config']['merchant_id'];
        $merOrderNum = $order_sn;    
        $tranAmt = $money;     // 总金额 
        $feeAmt = '';  
        $tranDateTime = $today;      
        $frontMerUrl = $data_front_url;      
        $backgroundMerUrl = $data_return_url;   //返回的路径   
        $tranIP = $spbill_create_ip != ""?$spbill_create_ip:'';  
        //商户识别码
        $verficationCode = $payment_info['config']['VerficationCode'];
        $gopayServerTime = trim(file_get_contents("https://www.gopay.com.cn/PGServer/time"));

        $signValue='version=['.$version.']tranCode=['.$tranCode.']merchantID=['.$merchant_id.']merOrderNum=['.$merOrderNum.']tranAmt=['.$tranAmt.']feeAmt=['.$feeAmt.']tranDateTime=['.$tranDateTime.']frontMerUrl=['.$frontMerUrl.']backgroundMerUrl=['.$backgroundMerUrl.']orderId=[]gopayOutOrderId=[]tranIP=['.$tranIP.']respCode=[]gopayServerTime=['.$gopayServerTime.']VerficationCode=['.$verficationCode.']';

        $signValue = md5($signValue);

        /*交易参数*/
        $parameter = array(
            'version'=>'2.1',//版本号
            'charset'=>'2',//字符集1GBK 2UTF-8
            'language'=>'1',//语言种类
            'signType'=>'1',//签名类型
            'tranCode'=>'8888', //交易代码
            
            //用户账户信息
            'merchantID'=>$merchant_id,//商户ID
            'virCardNoIn'=>$payment_info['config']['virCardNoIn'],//转入账户
            
            //订单信息
            'merOrderNum'=>$merOrderNum,//订单号
            'tranAmt'=>$tranAmt,//交易金额
            //'feeAmt'=>'',//手续费  
            'currencyType'=>$currencyType,//币种 default
            'tranDateTime'=> $tranDateTime ,//交易时间
            'tranIP'=>$spbill_create_ip,//用户IP
            
            'goodsName'=>$desc,//商品名称
            'goodsDetail'=>'',//商品描述
            'buyerName'=>'',//买方姓名
            'buyerContact'=>'',//买方联系方式
            
            
            //通知配置
            'frontMerUrl'=>$frontMerUrl,//前台通知地址
            'backgroundMerUrl'=>$backgroundMerUrl,//后台通知地址
            
            //生成信息
            'signValue'=>$signValue,//密文串
            'gopayServerTime'=>  $gopayServerTime ,//服务器时间
            
            //银行直连必填
            'bankCode'=>$bank_id,//银行代码
            'userType'=>1,//用户类型1（为个人支付）；2（为企业支付）

            //可选参数
            'feeAmt'=>'',
            'isRepeatSubmit'=>'',
            'merRemark1'=>$payment_notice_id,  //系统订单号
            'merRemark2'=>'',
            
        );
        
        $def_url = '<form style="text-align:center;" action="https://gateway.gopay.com.cn/Trans/WebClientAction.do" target="_blank" style="margin:0px;padding:0px" method="get" >';

        foreach ($parameter AS $key => $val) {
            $def_url .= "<input type='hidden' name='$key' value='$val' />";
        }
        $def_url .= "<input type='submit' class='paybutton' value='前往国付宝在线支付' />";
        $def_url .= "</form>";
        $def_url.="<br /><div style='text-align:center' class='red'>".$GLOBALS['lang']['PAY_TOTAL_PRICE'].":".format_price($money)."</div>";
        return $def_url;
    }

    public function response($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
        /* 取返回参数 */
        $version = $request["version"];
		$charset = $request["charset"];
		$language = $request["language"];
		$signType = $request["signType"];
		$tranCode = $request["tranCode"];
		$merchantID = $request["merchantID"];
		$merOrderNum = $request["merOrderNum"];
		$tranAmt = $request["tranAmt"];
		$feeAmt = $request["feeAmt"];
		$frontMerUrl = $request["frontMerUrl"];
		$backgroundMerUrl = $request["backgroundMerUrl"];
		$tranDateTime = $request["tranDateTime"];
		$tranIP = $request["tranIP"];
		$respCode = $request["respCode"];
		$msgExt = $request["msgExt"];
		$orderId = $request["orderId"];
		$gopayOutOrderId = $request["gopayOutOrderId"];
		$bankCode = $request["bankCode"];
		$tranFinishTime = $request["tranFinishTime"];
		$merRemark1 = $request["merRemark1"];
		$merRemark2 = $request["merRemark2"];
		$signValue = $request["signValue"];

        //参数转换
        $payment_notice_sn = $merRemark1;  //系统订单号
        $total_price = $tranAmt;//总价
	
        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Guofubao'");  
    	$payment['config'] = unserialize($payment['config']);
    	
        $currency_id = $payment['currency'];
		
        /*比对连接加密字符串*/
		$signValue2='version=['.$version.']tranCode=['.$tranCode.']merchantID=['.$merchantID.']merOrderNum=['.$merOrderNum.']tranAmt=['.$tranAmt.']feeAmt=['.$feeAmt.']tranDateTime=['.$tranDateTime.']frontMerUrl=['.$frontMerUrl.']backgroundMerUrl=['.$backgroundMerUrl.']orderId=['.$orderId.']gopayOutOrderId=['.$gopayOutOrderId.']tranIP=['.$tranIP.']respCode=['.$respCode.']gopayServerTime=[]VerficationCode=['.$payment['config']['VerficationCode'].']';

        $signValue2 = md5($signValue2);

        if ($signValue != $signValue2 || $respCode != "0000") {
        	echo "RespCode=9999|JumpURL=".SITE_DOMAIN.url("index","payment#pay",array("id"=>$payment_notice_sn)); 
        } else {
	        $payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = '".$payment_notice_sn."'");
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$rs = payment_paid($payment_notice['id'],$gopayOutOrderId);	
			
			$is_paid = intval($GLOBALS['db']->getOne("select is_paid from ".DB_PREFIX."payment_notice where id = '".intval($payment_notice['id'])."'"));
			if ($is_paid == 1){
				echo "RespCode=0000|JumpURL=".SITE_DOMAIN.url("index","payment#incharge_done",array("id"=>$payment_notice['id'])); //支付成功
			}else{
				echo "RespCode=9999|JumpURL=".SITE_DOMAIN.url("index","payment#pay",array("id"=>$payment_notice['id'])); 
			}									
        }
    }
    
     public function notify($request) {
		$return_res = array(
            'info' => '',
            'status' => false,
        );
		
        /* 取返回参数 */
        $version = $request["version"];
		$charset = $request["charset"];
		$language = $request["language"];
		$signType = $request["signType"];
		$tranCode = $request["tranCode"];
		$merchantID = $request["merchantID"];
		$merOrderNum = $request["merOrderNum"];
		$tranAmt = $request["tranAmt"];
		$feeAmt = $request["feeAmt"];
		$frontMerUrl = $request["frontMerUrl"];
		$backgroundMerUrl = $request["backgroundMerUrl"];
		$tranDateTime = $request["tranDateTime"];
		$tranIP = $request["tranIP"];
		$respCode = $request["respCode"];
		$msgExt = $request["msgExt"];
		$orderId = $request["orderId"];
		$gopayOutOrderId = $request["gopayOutOrderId"];
		$bankCode = $request["bankCode"];
		$tranFinishTime = $request["tranFinishTime"];
		$merRemark1 = $request["merRemark1"];
		$merRemark2 = $request["merRemark2"];
		$signValue = $request["signValue"];

        //参数转换
        $payment_notice_sn = $merRemark1;  //系统订单号
        $total_price = $tranAmt;//总价
	
        /*获取支付信息*/
        $payment = $GLOBALS['db']->getRow("select id,config from ".DB_PREFIX."payment where class_name='Guofubao'");  
    	$payment['config'] = unserialize($payment['config']);
    	
        $currency_id = $payment['currency'];
		
        /*比对连接加密字符串*/
		$signValue2='version=['.$version.']tranCode=['.$tranCode.']merchantID=['.$merchantID.']merOrderNum=['.$merOrderNum.']tranAmt=['.$tranAmt.']feeAmt=['.$feeAmt.']tranDateTime=['.$tranDateTime.']frontMerUrl=['.$frontMerUrl.']backgroundMerUrl=['.$backgroundMerUrl.']orderId=['.$orderId.']gopayOutOrderId=['.$gopayOutOrderId.']tranIP=['.$tranIP.']respCode=['.$respCode.']gopayServerTime=[]VerficationCode=['.$payment['config']['VerficationCode'].']';

        $signValue2 = md5($signValue2);

        if ($signValue != $signValue2 || $respCode != "0000") {
        	showErr($GLOBALS['payment_lang']["VALID_ERROR"]);
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
					if($order_info['type']==0)
						app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
					else
						app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
				}
				else 
				{
					if($order_info['pay_status'] == 2)
					{				
						if($order_info['type']==0)
							app_redirect(url("index","payment#done",array("id"=>$payment_notice['order_id']))); //支付成功
						else
							app_redirect(url("index","payment#incharge_done",array("id"=>$payment_notice['order_id']))); //支付成功
					}
					else
						app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
				}
			}
			else
			{
				app_redirect(url("index","payment#pay",array("id"=>$payment_notice['id'])));
			}
        }
    }

    public function get_display_code() {
        $payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Guofubao'");
		if($payment_item)
		{
			$payment_cfg = unserialize($payment_item['config']);

	        $html = "<style type='text/css'>.guofubao_types{float:left; display:block; background:url(".SITE_DOMAIN.APP_ROOT."/system/payment/Guofubao/banklist_hnapay.jpg); font-size:0px; width:150px; height:10px; text-align:left; padding:15px 0px;}";
	        $html .=".gfb_type_0{background-position:15px -908px; }"; //中国建设银行
	        $html .=".gfb_type_CCB{background-position:15px -72px; }"; //中国建设银行
	        $html .=".gfb_type_CMB{background-position:15px -196px; }"; //招商银行
	        $html .=".gfb_type_ICBC{background-position:15px 3px; }"; //中国工商银行
	        $html .=".gfb_type_BOC{background-position:15px -113px; }"; //中国银行
	        $html .=".gfb_type_ABC{background-position:15px -34px; }"; //中国农业银行
	        $html .=".gfb_type_BOCOM{background-position:15px -155px; }"; //交通银行
	        $html .=".gfb_type_CMBC{background-position:15px -230px; }"; //中国民生银行
	        $html .=".gfb_type_HXBC{background-position:15px -358px; }"; //华夏银行
	        $html .=".gfb_type_CIB{background-position:15px -270px; }"; //兴业银行
	        $html .=".gfb_type_SPDB{background-position:15px -312px; }"; //上海浦东发展银行
	        $html .=".gfb_type_GDB{background-position:15px -475px; }"; //广东发展银行
	        $html .=".gfb_type_CITIC{background-position:15px -396px; }"; //中信银行
	        $html .=".gfb_type_CEB{background-position:15px -435px; }"; //光大银行
	        $html .=".gfb_type_PSBC{background-position:15px -513px; }"; //中国邮政储蓄银行
	        $html .=".gfb_type_SDB{background-position:15px -558px; }"; //深圳发展银行
	        $html .=".gfb_type_BOBJ{background-position:15px -697px; }"; //北京银行
			$html .=".gfb_type_PAB{background-position:15px -827px; }"; //平安银行
			$html .=".gfb_type_BOS{background-position:15px -789px; }"; //上海银行
			$html .=".gfb_type_NBCB{background-position:15px -649px; }"; //宁波银行
			$html .=".gfb_type_NJCB{background-position:15px -743px; }"; //南京银行
			$html .=".gfb_type_TCCB{background-position:15px -869px; }"; //天津银行
	        $html .="</style>";
	        $html .="<script type='text/javascript'>function set_bank(bank_id)";
			$html .="{";
			$html .="$(\"input[name='bank_id']\").val(bank_id);";
			$html .="}</script>";
			$html.= "<h3 class='clearfix tl'><b>国付宝支付</b></h3><div class='blank1'></div><hr />";
	       foreach ($payment_cfg['guofubao_gateway'] AS $key=>$val)
	        {
	            $html  .= "<label class='guofubao_types gfb_type_".$key." ui-radiobox' rel='common_payment'><input type='radio' name='payment' value='".$payment_item['id']."' rel='".$key."' onclick='set_bank(\"".$key."\")' /></label>";
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

}

?>
