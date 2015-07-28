<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
		'name'	=>	'环讯资金托管',
		'mer_code'	=>	'签约号',
		'cert_md5'		=>	'证书',
		'3des_key'		=>	'3DES密钥',		
		'3des_iv'		=>	'3DES向量',
		'fee_type'		=>	'谁付环讯充值手续费',
		'fee_type_1'		=>	'平台支付',
		'fee_type_2'		=>	'用户支付',
		'is_debug'		=>	'测试帐户',
		'is_debug_0'		=>	'否',
		'is_debug_1'		=>	'是',		
);


$config = array(
		'mer_code'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'cert_md5'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'3des_key'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'3des_iv'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), 
		'fee_type'	=>	array(
				'INPUT_TYPE'	=>	'1',
				'VALUES'	=>	array(1,2),
		), 
		'is_debug'	=>	array(
				'INPUT_TYPE'	=>	'1',
				'VALUES'	=>	array(0,1),
		),		
);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == TRUE)
{
	$module['class_name']    = 'Ips';

	/* 名称 */
	$module['name']    = $payment_lang['name'];

	/* 配送 */
	$module['config'] = $config;

	$module['lang'] = $payment_lang;
	 
	/* 插件作者的官方网站 */
	$module['reg_url'] = 'http://www.fanwe.com';

	return $module;
}


require_once(APP_ROOT_PATH.'system/collocation/ips/Crypt3Des.php');
require_once(APP_ROOT_PATH.'system/libs/collocation.php');
class Ips_collocation implements collocation {
	private $payment_lang = array(
		'GO_TO_PAY'	=>	'前往%s支付',
	);
	
	/* IPS证书 http://merchant.ips.net.cn:8086 */
	//private $cert_md5="GPhKt7sh4dxQQZZkINGFtefRKNPyAj8S00cgAwtRyy0ufD7alNC28xCBKpa6IU7u54zzWSAv4PqUDKMgpOnM7fucO1wuwMi4RgPAnietmqYIhHXZ3TqTGKNzkxA55qYH";
	//private $MerCode = 808801;
	private $MerCode = "";
	private $cert_md5= "";
	//正式环境
	//private $post_url="https://p2p.ips.com.cn/CreditWeb/";
	//private $ws_url= "https://p2p.ips.com.cn/CreditWS/Service.asmx?wsdl";
	//测试环境
	private $post_url="http://p2p.ips.net.cn/CreditWeb/";
	private $ws_url= "http://p2p.ips.net.cn/CreditWS/Service.asmx?wsdl";
	
	function __construct(){
		
		$collocation_item = $GLOBALS['db']->getRow("select config from ".DB_PREFIX."collocation where class_name='Ips'");
		$collocation_cfg = unserialize($collocation_item['config']);
		$this->MerCode = $collocation_cfg['mer_code'];
		$this->cert_md5 = $collocation_cfg['cert_md5'];
		
		//$this->MerCode = app_conf("IPS_MERCODE");
		//$this->cert_md5 = app_conf("IPS_KEY");
		
		if ($collocation_cfg['is_debug'] == 1){
			$this->post_url="http://p2p.ips.net.cn/CreditWeb/";
			$this->ws_url= "http://p2p.ips.net.cn/CreditWS/Service.asmx?wsdl";
		}else{
			$this->post_url="https://p2p.ips.com.cn/CreditWeb/";
			$this->ws_url= "https://p2p.ips.com.cn/CreditWS/Service.asmx?wsdl";
		}
	} 
	
	
	/**
	 * 创建新帐户
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function CreateNewAcct($user_id,$user_type){
		require_once(APP_ROOT_PATH.'system/collocation/ips/CreateNewAcct.php');
		
		return CreateNewAcct($user_id,$user_type,$this->MerCode,$this->cert_md5,$this->post_url);
		
	}
	
	/**
	 * 标的登记 及 流标
	 * @param int $deal_id
	 * @param int $pOperationType 标的操作类型，1：新增，2：结束 “新增”代表新增标的，“结束”代表标的正常还清、丌 需要再还款戒者标的流标等情况。标的“结束”后，投资 人投标冻结金额、担保方保证金、借款人保证金均自劢解 冻
	 * @param int $status; 0:新增; 1:标的正常结束; 2:流标结束
	 * @param string $status_msg 主要是status_msg=2时记录的，流标原因
	 */
	function RegisterSubject($deal_id,$pOperationType,$status, $status_msg){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterSubject.php');
		
		return RegisterSubject($deal_id,$pOperationType, $status, $status_msg, $this->MerCode,$this->cert_md5,$this->post_url);
	
	}	
	
	
	/**
	 * 登记债权人
	 * @param int $user_id  用户ID
	 * @param int $deal_id  标的ID
	 * @param float $pAuthAmt 投资金额
	 * @return string
	 */
	function RegisterCreditor($user_id,$deal_id,$pAuthAmt){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCreditor.php');
		
		return RegisterCreditor($user_id,$deal_id,$pAuthAmt,$this->MerCode,$this->cert_md5,$this->post_url);
	}	
	
	/**
	 * 登记债权转让
	 * @param int $transfer_id  转让id
	 * @param int $t_user_id  受让用户ID
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RegisterCretansfer($transfer_id,$t_user_id){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCretansfer.php');
		
		return RegisterCretansfer($transfer_id,$t_user_id, $this->MerCode,$this->cert_md5,$this->post_url);
	}
	
		/**
	 * 账户余额查询(WS) 
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $ws_url
	 * @return
	 * 			pMerCode 6 “平台”账号 否 由IPS颁发的商户号
				pErrCode 4 返回状态 否 0000成功； 9999失败；
				pErrMsg 100 返回信息 否 状态0000：成功 除此乊外：反馈实际原因
				pIpsAcctNo 30 IPS账户号 否 查询时提交
				pBalance 10 可用余额 否 带正负符号，带小数点，最多保留两位小数
				pLock 10 冻结余额 否 带正负符号，带小数点，最多保留两位小数
				pNeedstl 10 未结算余额 否 带正负符号，带小数点，最多保留两位小数
	 */
	function QueryForAccBalance($user_id,$user_type){
		require_once(APP_ROOT_PATH.'system/collocation/ips/QueryForAccBalance.php');
		//echo 'sss'; exit;
		return QueryForAccBalance($user_id,$user_type,$this->MerCode,$this->cert_md5,$this->ws_url);			
	}
	
	
	/**
	 * 解冻保证金
	 * @param int $deal_id 标的号
	 * @param int $pUnfreezenType 解冻类型 否 1#解冻借款方；2#解冻担保方
	 * @param float $money 解冻金额;默认为0时，则解冻所有未解冻的金额
	 * @return string
	 */
	function GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money){
		require_once(APP_ROOT_PATH.'system/collocation/ips/GuaranteeUnfreeze.php');
				
		return GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money,$this->MerCode,$this->cert_md5,$this->ws_url);
	}	

	/**
	 * 充值
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param float $pTrdAmt 充值金额
	 * @param string $pTrdBnkCode 银行编号
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoDpTrade($user_id,$user_type,$pTrdAmt,$pTrdBnkCode){	
		require_once(APP_ROOT_PATH.'system/collocation/ips/DoDpTrade.php');
		
		return DoDpTrade($user_id,$user_type,$pTrdAmt,$pTrdBnkCode,$this->MerCode,$this->cert_md5,$this->post_url);
	}
	
	/**
	 * 绑定银行卡
	 * @param unknown_type $user_id
	 */
	function BindBankCard($user_id){
		
	}
	
	
	/**
	 * 用户提现
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param float $pTrdAmt 提现金额
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoDwTrade($user_id,$user_type,$pTrdAmt){
		require_once(APP_ROOT_PATH.'system/collocation/ips/DoDwTrade.php');
		
		return DoDwTrade($user_id,$user_type,$pTrdAmt,$this->MerCode,$this->cert_md5,$this->post_url);
	}
	
	/**
	 * 商户端获取银行列表查询(WS) 
	 * @param int $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $ws_url
	 * @return  
	 * 		  pMerCode 6 “平台”账号 否 由IPS颁发的商户号
	 * 		  pErrCode 4 返回状态 否 0000成功； 9999失败；
	 * 		  pErrMsg 100 返回信息 否 状态0000：成功 除此乊外：反馈实际原因 
	 * 		  pBankList 银行名称|银行卡别名|银行卡编号#银行名称|银行卡别名|银行卡编号
	 * 		  BankList[] = array('name'=>银行名称,'sub_name'=>银行卡别名,'id'=>银行卡编号);
	 */
	function GetBankList(){
		require_once(APP_ROOT_PATH.'system/collocation/ips/GetBankList.php');
		
		return GetBankList($this->MerCode,$this->cert_md5,$this->ws_url);
	}
	
	/**
	 * 登记担保方
	 * @param int $deal_id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return string
	 */
	function RegisterGuarantor($deal_id){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterGuarantor.php');
		
		return RegisterGuarantor($deal_id,$this->MerCode,$this->cert_md5,$this->post_url);		
	}	
	
	/**
	 * 还款
	 * @param deal $deal  标的数据
	 * @param array $repaylist  还款列表
	 * @param int $deal_repay_id  还款计划ID
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RepaymentNewTrade($deal, $repaylist, $deal_repay_id){
		require_once(APP_ROOT_PATH.'system/collocation/ips/RepaymentNewTrade.php');
		
		return RepaymentNewTrade($deal,$repaylist,$deal_repay_id, $this->MerCode,$this->cert_md5,$this->post_url);
		
	}
	
	/**
	 * 转帐
	 * @param int $pTransferType;//转账类型  否  转账类型  1：投资（报文提交关系，转出方：转入方=N：1），  2：代偿（报文提交关系，转出方：转入方=1：N），  3：代偿还款（报文提交关系，转出方：转入方=1：1），  4：债权转让（报文提交关系，转出方：转入方=1：1），  5：结算担保收益（报文提交关系，转出方：转入方=1： 1）
	 * @param int $deal_id  标的id
	 * @param string $ref_data 逗号分割的,代偿，代偿还款列表; 债权转让: id; 结算担保收益:金额，如果为0,则取fanwe_deal.guarantor_pro_fit_amt ;
	 * @return string
	 */
	function Transfer($pTransferType, $deal_id, $ref_data){
		require_once(APP_ROOT_PATH.'system/collocation/ips/Transfer.php');
				
		return Transfer($pTransferType,$deal_id,$ref_data,$this->MerCode,$this->cert_md5,$this->ws_url);
	}
	
	//(显式回调)
	function response($request,$class_act){
		//print_r($request); exit;
		
		$pMerCode = $request["pMerCode"];
		$pErrCode = $request["pErrCode"];
		$pErrMsg = $request["pErrMsg"];
		$p3DesXmlPara = $request["p3DesXmlPara"];
		$pSign = $request["pSign"];
		
		$signPlainText = $pMerCode.$pErrCode.$pErrMsg.$p3DesXmlPara.$this->cert_md5;
		$localSign = md5($signPlainText);
		if($localSign==$pSign){
			//echo "<br/>验签通过";exit;
			
			$Crypt3Des=new Crypt3Des();//new 3des class
			$str3XmlParaInfo=$Crypt3Des->DESDecrypt($p3DesXmlPara);//3des解密
			
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$str3ParaInfo = @XML_unserialize($str3XmlParaInfo);
			$str3Req = $str3ParaInfo['pReq'];
						
			//
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/CreateNewAcct.php');				
				$user_type = CreateNewAcctCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else{
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}
				
			}else if ($class_act == 'RegisterSubject'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterSubject.php');				
				RegisterSubjectCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				
			}else if ($class_act == 'RegisterCreditor'){
				//投资,登记债权人
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCreditor.php');				
				$ipsdata = RegisterCreditorCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					if($ipsdata)
						showIpsInfo($pErrMsg,url("index","deal",array("id"=>$ipsdata['deal_id'])));	
					else
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);	
				}
				
				
							
			}else if ($class_act == 'RegisterCretansfer'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCretansfer.php');				
				RegisterCretansferCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else{
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);	
				}
							
			}else if ($class_act == 'GuaranteeUnfreeze'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/GuaranteeUnfreeze.php');				
				GuaranteeUnfreezeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);				
			}else if ($class_act == 'DoDpTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/DoDpTrade.php');				
				DoDpTradeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				

				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'DoDwTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/DoDwTrade.php');				
				DoDwTradeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				
				
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{					
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);	
				}		
			}else if ($class_act == 'RegisterGuarantor'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterGuarantor.php');				
				RegisterGuarantorCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);				
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);				
			}else if ($class_act == 'RepaymentNewTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RepaymentNewTrade.php');
				$ipsdata = RepaymentNewTradeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				if($ipsdata)
					showIpsInfo($pErrMsg,url("index","uc_deal#quick_refund",array("id"=>$ipsdata['deal_id'])));
				else
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'Transfer'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/Transfer.php');
				$result = TransferCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					if(intval($str3Req["pTransferType"])==4)
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
					else
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);					
				}else{
					if(intval($str3Req["pTransferType"])==4)
						showIpsInfo($pErrMsg,url("index","transfer#detail",array("id"=>$result['id'])));
					else
						showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}
				
				
			}
			
		}else{
			echo "<br/>验签不通过:$localSign";exit;
		}	
	}
	
	//(后台回调)
	function notify($request,$class_act){
		$pMerCode = $request["pMerCode"];
		$pErrCode = $request["pErrCode"];
		$pErrMsg = $request["pErrMsg"];
		$p3DesXmlPara = $request["p3DesXmlPara"];
		$pSign = $request["pSign"];
		
		$signPlainText = $pMerCode.$pErrCode.$pErrMsg.$p3DesXmlPara.$this->cert_md5;
		$localSign = md5($signPlainText);
		if($localSign==$pSign){
			//echo "<br/>验签通过";
			$Crypt3Des=new Crypt3Des();//new 3des class
			$str3XmlParaInfo=$Crypt3Des->DESDecrypt($p3DesXmlPara);//3des解密
				
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$str3ParaInfo = @XML_unserialize($str3XmlParaInfo);
			$str3Req = $str3ParaInfo['pReq'];
			
			//
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/CreateNewAcct.php');
				CreateNewAcctCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
	
	
			}else if ($class_act == 'RegisterSubject'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterSubject.php');
				RegisterSubjectCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
	
			}else if ($class_act == 'RegisterCreditor'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCreditor.php');
				RegisterCreditorCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'RegisterCretansfer'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RegisterCretansfer.php');
				RegisterCretansferCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
	
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'GuaranteeUnfreeze'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/GuaranteeUnfreeze.php');
				GuaranteeUnfreezeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'RepaymentNewTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/RepaymentNewTrade.php');
				RepaymentNewTradeCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'Transfer'){
				require_once(APP_ROOT_PATH.'system/collocation/ips/Transfer.php');
				TransferCallBack($pMerCode,$pErrCode,$pErrMsg,$str3Req);
				//showSuccess($pErrMsg,0,SITE_DOMAIN.APP_ROOT);
			}
				
			
		}else{
			echo "<br/>验签不通过:$localSign<br/>";
			
			$Crypt3Des=new Crypt3Des();//new 3des class
			$str3XmlParaInfo=$Crypt3Des->DESDecrypt($p3DesXmlPara);//3des解密
			print_r($str3XmlParaInfo);
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$str3ParaInfo = @XML_unserialize($str3XmlParaInfo);
			print_r($str3ParaInfo);
			
			exit;
		}
	}

	
}
?>
