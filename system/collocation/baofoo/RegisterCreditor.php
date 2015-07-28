<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function RegisterCreditorXml($data,$actions){	
		
		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<action_type>" .$data['action_type'] ."</action_type>"
				."<merchant_id>" .$data['merchant_id'] ."</merchant_id>"
				."<order_id>" .$data['order_id'] ."</order_id>"
				."<cus_id>" .$data['cus_id'] ."</cus_id>"
				."<cus_name><![CDATA[" .$data['cus_name'] ."]]></cus_name>"		
				."<brw_id>" .$data['brw_id'] ."</brw_id>"
				."<req_time>" .$data['req_time'] ."</req_time>"
				.$actions
				."<fee>" .$data['fee'] ."</fee>"
				."</custody_req>";
		
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	

	
	/**
	 * 投标
	 * @param int $user_id  用户ID
	 * @param int $deal_id  标的ID
	 * @param float $pAuthAmt 投资金额
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	
	
	function RegisterCreditor($cfg,$user_id,$deal_id,$pAuthAmt,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];

		
		//$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=RegisterCreditor&from=".$_REQUEST['from'];//web方式返回
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=deal&id=".$deal_id;//
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=RegisterCreditor&from=".$_REQUEST['from'];//s2s方式返回		
	
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		
		
		$data = array();
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['action_type'] = 1;//请求类型，投标为1，满标为2，流标为3，还标为4
		$data['order_id'] = 0;
		
		$data['cus_id'] = $deal_id;
		$data['cus_name'] = $deal['sub_name'];//项目名称
		$data['brw_id'] = intval($deal['user_id']);//借款人	
		$data['req_time'] =  microtime_float();// get_gmtime();//请求时间 例如 1405668253874    （当前时间转换毫秒）
		
		$data['fee'] = 0; //手续费(涉及到满标、还款接口)
		
		$data['load_user_id'] = $user_id;// 记录：投标人
		$data['load_amount'] = $pAuthAmt;// 记录：投标金额
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();

		
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data_update,'UPDATE','id='.$id);
		
		//user_id:投资人; amount:投资金额
		$actions = "<actions><action><user_id>".$user_id."</user_id><user_name></user_name><amount>".$pAuthAmt."</amount></action></actions>";
									
		//print_r($data);exit;
		$strxml = RegisterCreditorXml($data,$actions);			
		
		$pSign = md5($strxml."~|~".$key);
		//$aes=new MyAES();
		//$requestParams=$aes->encrypt($strxml,$key,$iv); //加密
		
		
				
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'custody/businessPage.do" target="_self">		
			
				merchant_id:<input type="hidden" name="merchant_id" value="'.$merchant_id.'" /><br>
				terminal_id:<input type="hidden" name="terminal_id" value="'.$terminal_id.'" /><br>
				sign:<input type="hidden" name="sign" value="'.$pSign.'" /><br>					
				requestParams:<textarea name="requestParams" cols="100" rows="5">'.$strxml.'</textarea>	<br>
				page_url:<input type="hidden" name="page_url" value="'.$pWebUrl.'" /><br>
				service_url:<input type="hidden" name="service_url" value="'.$pS2SUrl.'" /><br>
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		';//<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'business_1';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		return $html;
	}
	
	//投资回调
	function RegisterCreditorCallBack($str3Req){
		//print_r($str3XmlParaInfo);
		$order_id = $str3Req["order_id"];
		$where = " order_id = '".$order_id."'";
		$sql = "update ".DB_PREFIX."baofoo_business set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($GLOBALS['db']->affected_rows()){		
			//操作成功
			$data = array();
						
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$str3Req,'UPDATE',$where);
							
			if ($str3Req['code'] == 'CSD000'){
				
				$ipsdata = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."baofoo_business where ".$where);
				$user_id = intval($ipsdata['load_user_id']);
				$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
				
				$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".(int)$ipsdata['cus_id']);
				
				$data['pMerBillNo'] = $order_id;
				$data['pContractNo'] = $order_id;
				$data['pP2PBillNo'] = $order_id;
				$data['user_id'] = $user_id;
				$data['user_name'] = $user['user_name'];
				$data['deal_id'] = $ipsdata['cus_id'];
				$data['money'] = $ipsdata['load_amount'];
				
				$insertdata = return_deal_load_data($data,$user,$deal);
				
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$insertdata,"INSERT");
				$load_id = $GLOBALS['db']->insert_id();
				if($load_id > 0){				
					require APP_ROOT_PATH.'app/Lib/deal_func.php';
					dobid2_ok($ipsdata['cus_id'], $user_id);	
					return $ipsdata;			
				}
			}			
		}else{
			return 1;
		}
	}	
	
?>