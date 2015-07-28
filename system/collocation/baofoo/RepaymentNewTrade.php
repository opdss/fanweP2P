<?php
	
	/**
	 *
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function RepaymentNewTradeXml($data,$actions){
		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<merchant_id>" .$data['merchant_id'] ."</merchant_id>"
				."<action_type>" .$data['action_type'] ."</action_type>"
				."<order_id>" .$data['order_id'] ."</order_id>"
				."<cus_id>" .$data['cus_id'] ."</cus_id>"
				."<cus_name><![CDATA[" .$data['cus_name'] ."]]></cus_name>"
				."<brw_id>" .$data['brw_id'] ."</brw_id>"
				."<req_time>" .$data['req_time'] ."</req_time>"
				.$actions
				."</custody_req>";
	
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\
		return $strxml;
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
	function RepaymentNewTrade($cfg,$deal, $repaylist, $deal_repay_id, $post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];
		
	
		//$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		//$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		
		
		$data = array();
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['action_type'] = 4;//请求类型，投标为1，满标为2，流标为3，还标为4
		$data['order_id'] = 0;
		
		$data['user_id'] = $deal['user_id'];//借款人
		$data['special'] = 1;
		
		$data['cus_id'] = $deal['id'];
		$data['cus_name'] = $deal['sub_name'];//项目名称
		$data['brw_id'] = $deal['user_id'];//借款人
		$data['req_time'] =  microtime_float();// get_gmtime();//请求时间 例如 1405668253874    （当前时间转换毫秒）
		
		$data['fee'] = 0; //手续费(涉及到满标、还款接口)
		
		$data['repay_start_time'] = 0;// 开始还款日期
		$data['load_amount'] = 0;// 记录投标金额
		$data['bids_msg'] = '';//流标原因
		$data['deal_repay_id'] = $deal_repay_id;//还款计划ID
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();
		
				
		
		$fee = 0;
		foreach($repaylist as $k=>$v){
				
			//平台收取：借款者 的管理费 + 管理逾期罚息
			$fee = $fee + $v['repay_manage_money'] + $v['repay_manage_impose_money'];
				
			//==============================投资者获取的，费用===================================
			$detail = array();
			$detail['pid'] = $id;
			$detail['deal_load_repay_id'] = $v['id'];
			$detail['repay_manage_impose_money'] = $v['repay_manage_impose_money'];//平台收取 借款者 的管理费逾期罚息
			$detail['impose_money'] = $v['impose_money'];//投资者收取 借款者 的逾期罚息			
			$detail['repay_status'] = intval($v['status']) - 1;//还款状态
			$detail['true_repay_time'] = TIME_UTC;//还款时间
			
			
			//投资人会员编号
			if ($v['t_user_id']){
				//债权转让后,还款时，转给：承接者, 在债权转让后需要更新 fanwe_deal_load_repay.t_user_id 数据值
				$detail['user_id'] = $v['t_user_id'];
			}else{
				$detail['user_id'] = $v['user_id'];
			}
		
			//投资者获取的，费用 [宝付会自动扣除 $detail['fee'] 部分，所以最终获得的收入为：$v['month_repay_money'] + $v['impose_money'] - $v['manage_money'] - $v['manage_interest_money']
			$detail['amount'] = round($v['month_repay_money'] + $v['impose_money'],2);
		
			//平台收取：投资者 的投资金额管理费 + 利息管理费
			$detail['fee'] = round($v['manage_money']+$v['manage_interest_money'],2);
				
				
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business_detail",$detail,'INSERT');
			$details[] = $detail;
		}
			
		
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$data['fee'] = round($fee,2); //手续费(涉及到满标、还款接口)
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data_update,'UPDATE','id='.$id);
		
				
		$actions = "<actions>";		
		foreach($details as $k=>$v){
			$actions .= "<action><user_id>".intval($v['user_id'])."</user_id><amount>".$v['amount']."</amount><fee>".$v['fee']."</fee></action>";
		}
		
		$actions .= "<action><user_id>".intval($deal['user_id'])."</user_id><special>1</special><fee>".round($fee,2)."</fee></action>";
		
		$actions .= "</actions>";
		
			
		$strxml = RepaymentNewTradeXml($data,$actions);
			
		$pSign = md5($strxml."~|~".$key);			
			
		/*
		
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=RepaymentNewTrade&from=".$_REQUEST['from']."&order_id=".$id;//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=RepaymentNewTrade&from=".$_REQUEST['from']."&order_id=".$id;//s2s方式返回
		
		
		
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'custody/businessPage.do" target="_self">		
				merchant_id:<input type="text" name="merchant_id" value="'.$merchant_id.'" /><br>
				terminal_id:<input type="text" name="terminal_id" value="'.$terminal_id.'" /><br>
				sign:<input type="text" name="sign" value="'.$pSign.'" /><br>					
				requestParams:<textarea name="requestParams" cols="100" rows="5">'.$strxml.'</textarea>	<br>
				page_url:<input type="text" name="page_url" value="'.$pWebUrl.'" /><br>
				service_url:<input type="text" name="service_url" value="'.$pS2SUrl.'" /><br>
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		';//<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		*/
		$post_data = array("merchant_id"=>$merchant_id,"terminal_id"=>$terminal_id,"sign"=>$pSign,"requestParams"=>$strxml);
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'business_4';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = print_r($post_data,true);
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		
		$resultStr = httpRequestPOST($post_url."custody/p2pRequest.do",$post_data);
		
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'p2pRequest_4';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =print_r($resultStr,true);
		$baofoo_log['html'] = '';
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		
		//还款
		require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
		$str3ParaInfo = @XML_unserialize($resultStr);
		$str3Req = $str3ParaInfo['crs'];			
			
		$sign = $str3Req["sign"];
		$Md5sign = Md5($str3Req["code"].'~|~'.$str3Req["msg"].'~|~'.$key);
		
		if ($sign == $Md5sign){
			$order_id = $id;
			$where = " order_id = '".$order_id."'";
			$sql = "update ".DB_PREFIX."baofoo_business set is_callback = 1 where is_callback = 0 and ".$where;
			$GLOBALS['db']->query($sql);
			if ($GLOBALS['db']->affected_rows()){
				//操作成功
				$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$str3Req,'UPDATE',$where);
				
				$ipsdata = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."baofoo_business where ".$where);
					
				$deal_id = intval($ipsdata['cus_id']);
				$deal_repay_id = intval($ipsdata['deal_repay_id']);
					
				if ($str3Req['code'] == 'CSD000'){
				
					$sql = "select * from ".DB_PREFIX."baofoo_business_detail where deal_load_repay_id > 0 and pid = ".$ipsdata['id'];
					$list = $GLOBALS['db']->getAll($sql);
					foreach($list as $k=>$v){
						$load_repay = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_load_repay where id = ".$v['deal_load_repay_id']);
							
						//repay_status,repay_manage_impose_money							
				
						$detail = array();
						$detail['repay_manage_impose_money'] = $v["repay_manage_impose_money"];//平台收取 借款者 的管理费逾期罚息
						$detail['impose_money'] = $v["impose_money"];//投资者收取 借款者 的逾期罚息
						$detail['status'] = $v["repay_status"];//还款状态
						$detail['true_repay_time'] = $v["true_repay_time"];//还款时间
						$detail['true_repay_date'] = to_date($v["true_repay_time"]);
							
							
						$detail['has_repay'] = 1;//0未收到还款，1已收到还款
						$detail['true_manage_money'] = $load_repay['manage_money'];
						$detail['true_manage_interest_money'] = $load_repay["manage_interest_money"];
						$detail['true_repay_manage_money'] = $load_repay["repay_manage_money"];
						$detail['true_repay_money'] =$load_repay["repay_money"];
						$detail['true_self_money'] = $load_repay['self_money'];
						$detail['true_interest_money'] =  $load_repay['interest_money'];
				
						$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load_repay",$detail,'UPDATE'," has_repay = 0 and id = ".intval($v['deal_load_repay_id']));
						
						//普通会员邀请返利
						get_referrals($v['deal_load_repay_id']);		
					}
				
					//更新用户回款计划
					require_once APP_ROOT_PATH."app/Lib/deal.php";
					syn_deal_repay_status($deal_id,$deal_repay_id);
					
					return '已还款';
				}else{
					return 'code:'.$str3Req['code'].';msg:'.$str3Req['msg'];
				}
			}else{
				return '重复调用';
			}
		}else{
			return '验证不通过';
		}
		
	}
	
	
?>