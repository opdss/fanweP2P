<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

//http://119.161.147.110:8088/confluence/pages/viewpage.action?pageId=524290

/*
$merchant_id = '100000675';
$terminal_id = '100000701';
$key='n725d5gsb7mlyzzw';
$iv='n725d5gsb7mlyzzw';
 */

$payment_lang = array(
		'name'	=>	'宝付资金托管',
		'merchant_id'	=>	'商户号',
		'terminal_id'		=>	'终端号',
		'md5_key'		=>	'商户密钥',		
		'aes_iv'		=>	'AES向量',
		'fee_taken_on'		=>	'提现费用承担方',
		'fee_taken_on_1'		=>	'平台支付',
		'fee_taken_on_2'		=>	'用户支付',
		'is_debug'		=>	'测试帐户',
		'is_debug_0'		=>	'否',
		'is_debug_1'		=>	'是',		
);


$config = array(
		'merchant_id'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'terminal_id'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'md5_key'	=>	array(
				'INPUT_TYPE'	=>	'0'
		),
		'aes_iv'	=>	array(
				'INPUT_TYPE'	=>	'0'
		), 
		'fee_taken_on'	=>	array(
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
	$module['class_name']    = 'Baofoo';

	/* 名称 */
	$module['name']    = $payment_lang['name'];

	/* 配送 */
	$module['config'] = $config;

	$module['lang'] = $payment_lang;
	 
	/* 插件作者的官方网站 */
	$module['reg_url'] = 'http://www.fanwe.com';

	return $module;
}

require_once(APP_ROOT_PATH.'system/collocation/baofoo/baofoo_func.php');
require_once(APP_ROOT_PATH.'system/libs/collocation.php');
class Baofoo_collocation implements collocation {

	/* IPS证书 http://merchant.ips.net.cn:8086 */
	//private $cert_md5="GPhKt7sh4dxQQZZkINGFtefRKNPyAj8S00cgAwtRyy0ufD7alNC28xCBKpa6IU7u54zzWSAv4PqUDKMgpOnM7fucO1wuwMi4RgPAnietmqYIhHXZ3TqTGKNzkxA55qYH";
	//private $MerCode = 808801;
	private $MerCode = "";
	private $cfg = array(
			'merchant_id'	=>	'100000675',
			'terminal_id'		=>	'100000701',
			'key'		=>	'n725d5gsb7mlyzzw',
			'iv'		=>	'n725d5gsb7mlyzzw',
			'fee_taken_on'		=>	'2',
			'is_debug'		=>	'1',
			);
	//正式环境
	private $post_url="https://pm.baofoo.com/";
	//测试环境
	//private $post_url="https://paytest.baofoo.com/baofoo-custody/";
	
	function __construct(){		
		$collocation_item = $GLOBALS['db']->getRow("select config from ".DB_PREFIX."collocation where class_name='Baofoo'");
		
		$collocation_cfg = unserialize($collocation_item['config']);
		
		$this->cfg['merchant_id'] = $collocation_cfg['merchant_id'];
		$this->cfg['terminal_id'] = $collocation_cfg['terminal_id'];
		$this->cfg['key'] = $collocation_cfg['md5_key'];
		$this->cfg['iv'] = $collocation_cfg['aes_iv'];
		$this->cfg['fee_taken_on'] = $collocation_cfg['fee_taken_on'];
		
		if ($collocation_cfg['is_debug'] == 1){
			$this->post_url = "https://paytest.baofoo.com/baofoo-custody/";
		}else{
			$this->post_url = "https://pm.baofoo.com/";
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/CreateNewAcct.php');
		
		return CreateNewAcct($this->cfg,$user_id,$this->post_url);
		
	}
	
	/**
	 * 标的登记 及 流标
	 * @param int $deal_id
	 * @param int $pOperationType 标的操作类型，1：新增，2：结束 “新增”代表新增标的，“结束”代表标的正常还清、丌 需要再还款戒者标的流标等情况。标的“结束”后，投资 人投标冻结金额、担保方保证金、借款人保证金均自劢解 冻
	 * @param int $status; 0:新增; 1:标的正常结束; 2:流标结束
	 * @param string $status_msg 主要是status_msg=2时记录的，流标原因
	 */
	function RegisterSubject($deal_id,$pOperationType,$status, $status_msg){
		if ($pOperationType == 1){
			$data = array();		
			$data['ips_bill_no'] = $deal_id;
			$data['mer_bill_no'] = $deal_id;
			$GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,'UPDATE',"id=".$deal_id);
			
			showIpsInfo('同步成功',SITE_DOMAIN.APP_ROOT);
		}else if ($pOperationType == 2 && $status == 2){
			require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoBids.php');
			return DoBids($this->cfg,$deal_id,$status_msg,$this->post_url);
			
		}else if ($pOperationType == 2 && $status == 1){
			//本地解冻:借款保证金,担保保证金0
			$sql = "update ".DB_PREFIX."deal set ips_over = 1 ,un_real_freezen_amt = real_freezen_amt,un_guarantor_real_freezen_amt = guarantor_real_freezen_amt where id = ".$deal_id;
			$GLOBALS['db']->query($sql);	
			//http://p2p.fanwe.net/m.php?m=Deal&a=index&
			$url = SITE_DOMAIN.APP_ROOT.'/m.php?m=Deal&a=index';
			showSuccess('操作成功',0,$url);
		}
	}	
	
	
	/**
	 * 投标
	 * @param int $user_id  用户ID
	 * @param int $deal_id  标的ID
	 * @param float $pAuthAmt 投资金额
	 * @return string
	 */
	function RegisterCreditor($user_id,$deal_id,$pAuthAmt){
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/RegisterCreditor.php');
		
		return RegisterCreditor($this->cfg,$user_id,$deal_id,$pAuthAmt,$this->post_url);
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/RegisterCretansfer.php');
		
		$pErrMsg = RegisterCretansfer($this->cfg,$transfer_id,$t_user_id, $this->post_url);
		
		$url = SITE_DOMAIN.APP_ROOT.'/index.php?ctl=transfer&act=detail&id='.$transfer_id;
		
		showIpsInfo($pErrMsg,$url);
	}
	
		/**
	 * 账户余额查询(WS) 
	 * @param int $user_id
	 * @param int $user_type 0:普通用户fanwe_user.id;1:担保用户fanwe_deal_agency.id
	 * @param unknown_type $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/QueryForAccBalance.php');
		//echo 'sss'; exit;
		return QueryForAccBalance($this->cfg,$user_id,$this->post_url);			
	}
	
	
	/**
	 * 解冻保证金
	 * @param int $deal_id 标的号
	 * @param int $pUnfreezenType 解冻类型 否 1#解冻借款方；2#解冻担保方
	 * @param float $money 解冻金额;默认为0时，则解冻所有未解冻的金额
	 * @return string
	 */
	function GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money){
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/GuaranteeUnfreeze.php');
				
		return GuaranteeUnfreeze($deal_id,$pUnfreezenType, $money,$this->MerCode,$this->cert_md5,$this->post_url);
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDpTrade.php');
		
		return DoDpTrade($this->cfg,$user_id,$pTrdAmt,$this->post_url);
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDwTrade.php');
		
		return DoDwTrade($this->cfg,$user_id,$pTrdAmt,$this->post_url);
	}
	
	/**
	 * 商户端获取银行列表查询(WS) 
	 * @param int $MerCode
	 * @param unknown_type $cert_md5
	 * @param unknown_type $post_url
	 * @return  
	 * 		  pMerCode 6 “平台”账号 否 由IPS颁发的商户号
	 * 		  pErrCode 4 返回状态 否 0000成功； 9999失败；
	 * 		  pErrMsg 100 返回信息 否 状态0000：成功 除此乊外：反馈实际原因 
	 * 		  pBankList 银行名称|银行卡别名|银行卡编号#银行名称|银行卡别名|银行卡编号
	 * 		  BankList[] = array('name'=>银行名称,'sub_name'=>银行卡别名,'id'=>银行卡编号);
	 */
	function GetBankList(){ 
		
		$result = array ();
		$result ['pErrCode'] = '0000';
		$result ['pErrMsg'] = '';
		
		$BankList = array();
		$BankList[] = array('name'=>'宝付资金托管','sub_name'=>'在线充值','id'=>'1');
		$result ['BankList'] = $BankList;
		
		return $result;
		//require_once(APP_ROOT_PATH.'system/collocation/baofoo/GetBankList.php');
		
		//return GetBankList($this->MerCode,$this->cert_md5,$this->post_url);
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/RegisterGuarantor.php');
		
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
		require_once(APP_ROOT_PATH.'system/collocation/baofoo/RepaymentNewTrade.php');
		
		$pErrMsg = RepaymentNewTrade($this->cfg,$deal,$repaylist,$deal_repay_id, $this->post_url);
		
		//http://p2p.fanwe.net/member.php?ctl=uc_deal&act=quick_refund&id=35400
		//http://p2p.fanwe.net/member.php?ctl=uc_deal&act=refdetail&id=35401
		
		$deal_status = $GLOBALS['db']->getOne("select deal_status from ".DB_PREFIX."deal where id = ".intval($deal['id']));
		$url = '';
		if($deal_status==4){
			$url = SITE_DOMAIN.APP_ROOT.'/member.php?ctl=uc_deal&act=quick_refund&id='.$deal['id'];
		}else if ($deal_status==5){
			$url = SITE_DOMAIN.APP_ROOT.'/member.php?ctl=uc_deal&act=refdetail&id='.$deal['id'];
		}else{
			$url = SITE_DOMAIN.APP_ROOT;
		}
		
		showIpsInfo($pErrMsg,$url);
	}
	
	/**
	 * 转帐
	 * @param int $pTransferType;//转账类型  否  转账类型  1：投资（报文提交关系，转出方：转入方=N：1），  2：代偿（报文提交关系，转出方：转入方=1：N），  3：代偿还款（报文提交关系，转出方：转入方=1：1），  4：债权转让（报文提交关系，转出方：转入方=1：1），  5：结算担保收益（报文提交关系，转出方：转入方=1： 1）
	 * @param int $deal_id  标的id
	 * @param string $ref_data 逗号分割的,代偿，代偿还款列表; 债权转让: id; 结算担保收益:金额，如果为0,则取fanwe_deal.guarantor_pro_fit_amt ;
	 * @return string
	 */
	function Transfer($pTransferType, $deal_id, $ref_data){
		
		if ($pTransferType == 1){
			//满标放款
			require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoLoans.php');		
			return DoLoans($this->cfg,$deal_id,$ref_data, $this->post_url);
		}else{
		
			require_once(APP_ROOT_PATH.'system/collocation/baofoo/Transfer.php');
					
			return Transfer($pTransferType,$deal_id,$ref_data,$this->MerCode,$this->cert_md5,$this->post_url);
		}
	}
	
	//(显式回调)
	function response($request,$class_act){
		//print_r($_POST);
		//print_r($_GET);
		//print_r($_REQUEST);
		//print_r($request); 
		
		$merchant_id = $this->cfg['merchant_id'];
		$terminal_id = $this->cfg['terminal_id'];
		$key=$this->cfg['key'];
		$iv=$this->cfg['iv'];
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'response';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$class_act;
		$baofoo_log['html'] = print_r($_POST,true);
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		//exit;
			
		$Md5sign = "";
		if ($class_act == 'CreateNewAcct' || $class_act == 'DoDpTrade' || $class_act == 'DoDwTrade'){
			//Md5sign = Md5(result + ~|~ + "商户密钥")
			$result = $_POST["result"];
			$sign = $_POST["sign"];
			
			
			$Md5sign = Md5($result.'~|~'.$key);	

			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			
			
			$str3ParaInfo = @XML_unserialize($result);
			$str3Req = $str3ParaInfo['crs'];
		}else if ($class_act == 'RegisterCreditor' || $class_act == 'DoLoans'){
			
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$result = $_POST["result"];
			
			$str3ParaInfo = @XML_unserialize($result);
			$str3Req = $str3ParaInfo['crs'];
			
			//Sign =  MD5 ( code + ~|~ + msg + ~|~ + orderId + ~|~+商户秘钥 );			
			$Md5sign = Md5($str3Req['code'].'~|~'.$str3Req['msg'].'~|~'.$str3Req['order_id'].'~|~'.$key);
			
			$sign = $str3Req["sign"];
		}
		
		if($Md5sign==$sign){
			//echo "<br/>验签通过";exit;
					
			//
			
			$pErrMsg = $str3Req['msg'];
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/CreateNewAcct.php');				
				$user_type = CreateNewAcctCallBack($str3Req);
				
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else{
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}				
			}else if ($class_act == 'DoDpTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDpTrade.php');				
				DoDpTradeCallBack($str3Req);				

				$pErrMsg = '充值完成';
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
				}
			}else if ($class_act == 'DoDwTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDwTrade.php');				
				DoDwTradeCallBack($str3Req);				
				$pErrMsg = '操作完成';
				if ($request['from'] == 'app'){
					showIpsInfo($pErrMsg);
				}else if ($request['from'] == 'wap'){
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT.'/wap/index.php?ctl=uc_center');
				}else{					
					showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);	
				}		
			}else if ($class_act == 'DoLoans'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoLoans.php');				
				DoLoansCallBack($str3Req);
				showIpsInfo($pErrMsg,SITE_DOMAIN.APP_ROOT);
			}else if ($class_act == 'RegisterCreditor'){
				//投资,登记债权人
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/RegisterCreditor.php');				
				$ipsdata = RegisterCreditorCallBack($str3Req);
				
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
			}			
		}else{
			echo "<br/>验签不通过";exit;
		}	
	}
	
	//(后台回调)
	function notify($request,$class_act){
		//print_r($_POST);
		//print_r($_GET);
		//print_r($_REQUEST);
		//print_r($request);
		/*
		if ($class_act == 'DoLoans'){
			require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoLoans.php');
			$str3Req = array();
			$str3Req['order_id'] = 17022;
			$str3Req['code'] = 'CSD000';
			print_r(DoLoansCallBack($str3Req));
			exit;
		}*/
		
		$merchant_id = $this->cfg['merchant_id'];
		$terminal_id = $this->cfg['terminal_id'];
		$key=$this->cfg['key'];
		$iv=$this->cfg['iv'];
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'notify';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$class_act;
		$baofoo_log['html'] = print_r($_POST,true);
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		//exit;
		
		
		
		
		
		$Md5sign = "";
		if ($class_act == 'CreateNewAcct' || $class_act == 'DoDpTrade' || $class_act == 'DoDwTrade'){
			//Md5sign = Md5(result + ~|~ + "商户密钥")
			$result = $_POST["result"];
			$sign = $_POST["sign"];
				
				
			$Md5sign = Md5($result.'~|~'.$key);
		
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$str3ParaInfo = @XML_unserialize($result);
			$str3Req = $str3ParaInfo['crs'];
		}else if ($class_act == 'RegisterCreditor' || $class_act == 'DoBids' || $class_act == 'DoLoans'){
				
			require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
			$result = $_POST["result"];
			$str3ParaInfo = @XML_unserialize($result);
			$str3Req = $str3ParaInfo['crs'];
				
			//print_r($str3ParaInfo);exit;
			
			//Sign =  MD5 ( code + ~|~ + msg + ~|~ + orderId + ~|~+商户秘钥 );
			$Md5sign = Md5($str3Req['code'].'~|~'.$str3Req['msg'].'~|~'.$str3Req['order_id'].'~|~'.$key);
				
			$sign = $str3Req["sign"];
		}
		
		
		
		if($Md5sign==$sign){
			//echo "<br/>验签通过";exit;
				
			//
				
			$pErrMsg = $str3Req['msg'];
			if ($class_act == 'CreateNewAcct'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/CreateNewAcct.php');
				$user_type = CreateNewAcctCallBack($str3Req);
		
		
			}else if ($class_act == 'DoDpTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDpTrade.php');
				DoDpTradeCallBack($str3Req);
		
			}else if ($class_act == 'DoDwTrade'){
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoDwTrade.php');
				DoDwTradeCallBack($str3Req);

			}else if ($class_act == 'RegisterCreditor'){
				//投资,登记债权人
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/RegisterCreditor.php');
				$ipsdata = RegisterCreditorCallBack($str3Req);
					
			}else if ($class_act == 'DoLoans'){
				//满标放款
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoLoans.php');
				DoLoansCallBack($str3Req);
					
			}else if ($class_act == 'DoBids'){
				//流标
				require_once(APP_ROOT_PATH.'system/collocation/baofoo/DoBids.php');
				DoBidsCallBack($str3Req);
			}				
		}else{
			echo "<br/>验签不通过";exit;
		}
		
	}

	
}
?>
