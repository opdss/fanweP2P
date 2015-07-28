<?php
	
	/**
	 * 转帐
	 * @param int $pTransferType;//转账类型  否  转账类型  1：投资（报文提交关系，转出方：转入方=N：1），  2：代偿（报文提交关系，转出方：转入方=1：N），  3：代偿还款（报文提交关系，转出方：转入方=1：1），  4：债权转让（报文提交关系，转出方：转入方=1：1），  5：结算担保收益（报文提交关系，转出方：转入方=1： 1）
	 * @param int $deal_id  标的id	 
	 * @param string $ref_data 逗号分割的, 1：投资,填还款日期(int)  ; 2代偿，3代偿还款列表; 4债权转让: id; 5结算担保收益:金额，如果为0,则取fanwe_deal.guarantor_pro_fit_amt ;
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function DoLoans($pTransferType, $deal_id, $repay_start_time, $platformNo,$post_url){
	
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Yeepay&class_act=DoLoans&from=".$_REQUEST['from']."&repay_start_time=".$repay_start_time;//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Yeepay&class_act=DoLoans&from=".$_REQUEST['from']."&repay_start_time=".$repay_start_time;//s2s方式返回
		
		$t_arr = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."yeepay_cp_transaction where code = 1 and tenderOrderNo = ".$deal_id);

		foreach($t_arr as $key => $t_r)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."yeepay_cp_transaction set repay_start_time = '".$repay_start_time."' where is_callback = 0 and requestNo = ".$t_r["requestNo"]);
			
			$data = array();
			$requestNo = $t_r["requestNo"]; 
			$data['requestNo'] = $requestNo;//请求流水号
			$data['platformNo'] = $platformNo;// 商户编号
			$data['mode'] = "CONFIRM";	
			
			/* 请求参数 */  
			$req = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>"
			."<request platformNo=\"".$platformNo."\">"
			."<requestNo>".$requestNo."</requestNo>"
			."<mode>CONFIRM</mode>"
			."<notifyUrl><![CDATA[" .$pS2SUrl ."]]></notifyUrl>"
			."</request>";
			
			$yeepay_log = array();
			$yeepay_log['code'] = 'bhaController';
			$yeepay_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
			$yeepay_log['strxml'] = $req;
			$GLOBALS['db']->autoExecute(DB_PREFIX."yeepay_log",$yeepay_log);
			//$id = $GLOBALS['db']->insert_id();
			
			/* 签名数据 */
			$sign = "xxxx";
			/* 调用账户查询服务 */
			$service = "COMPLETE_TRANSACTION";
			$ch = curl_init($post_url."/bhaexter/bhaController");
			curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_POSTFIELDS => 'service=' . $service . '&req=' . rawurlencode($req) . "&sign=" . rawurlencode($sign)
			));
			$resultStr = curl_exec($ch);
			
			$result = array();
			if (empty($ch)){

			}else{
				
				$GLOBALS['db']->query("update ".DB_PREFIX."yeepay_cp_transaction set is_callback = 1 where is_callback = 0 and requestNo = ".$requestNo);
				
				require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
				$str3ParaInfo = @XML_unserialize($resultStr);
				//print_r($str3ParaInfo);exit;
				$str3Req = $str3ParaInfo['response'];
				
				$result['pErrCode'] = $str3Req["code"];
				$result['pErrMsg'] = $str3Req["description"];
				//$result['pIpsAcctNo'] = $user_id;	
				if($str3Req["code"] == 1)
				{
					$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_complete_transaction = 1,update_time = ".TIME_UTC." where is_callback = 1 and requestNo = ".$requestNo;
					$GLOBALS['db']->query($sql);
					
					$deal_load = array();
					$deal_load['is_has_loans'] = 1;//1#转账成功
					$where = " pP2PBillNo = ".$requestNo;
					$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$deal_load,'UPDATE',$where);
				}
			}
		}
			
		$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_load where is_has_loans = 0 and deal_id = ".$deal_id);
		
		if ($count == 0){
			//已经全部放款完成,生成：还款计划以及回款计划;
			//$repay_start_time = intval($ipsdata['repay_start_time']);			
			require_once(APP_ROOT_PATH."app/Lib/common.php");
			return do_loans($deal_id,$repay_start_time,1);
			
		}
	}
		//回调
		function DoLoansCallBack($str3Req){
			
			$requestNo = $str3Req["requestNo"];
			$platformNo = $str3Req["platformNo"];
			
			$GLOBALS['db']->query("update ".DB_PREFIX."yeepay_cp_transaction set is_callback = 1 where is_callback = 0 and requestNo = ".$requestNo);
			
			$result['pErrCode'] = $str3Req["code"];
			$result['pErrMsg'] = $str3Req["message"];
			$result['pIpsAcctNo'] = $str3Req["requestNo"];	
			
			if($str3Req["code"] == 1)
			{
				$sql = "update ".DB_PREFIX."yeepay_cp_transaction set is_complete_transaction = 1,update_time = ".TIME_UTC." where is_callback = 1 and requestNo = ".$requestNo;
				$GLOBALS['db']->query($sql);
				
				$deal_load = array();
				$deal_load['is_has_loans'] = 1;//1#转账成功
				$where = " pP2PBillNo = ".$requestNo;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$deal_load,'UPDATE',$where);
			}
			
			$ipsdata = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."yeepay_cp_transaction where is_callback = 1 and requestNo =".$requestNo);
			
			$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_load where is_has_loans = 0 and deal_id = ".$ipsdata["tenderOrderNo"]);
			if ($count == 0){
				
				//已经全部放款完成,生成：还款计划以及回款计划;
				$repay_start_time = intval($ipsdata['repay_start_time']);

				require_once(APP_ROOT_PATH."app/Lib/common.php");
				$result = do_loans($ipsdata["tenderOrderNo"],$repay_start_time,1);
				showIpsInfo($result["info"],SITE_DOMAIN.APP_ROOT."/m.php?m=Deal&a=full");
			}
			else
			{
				return 1;
			}
	}
	
?>