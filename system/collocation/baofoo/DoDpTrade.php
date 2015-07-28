<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function DoDpTradeXml($data,$pWebUrl,$pS2SUrl){	

		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<merchant_id>" .$data['merchant_id'] ."</merchant_id>"
				."<user_id>" .$data['user_id'] ."</user_id>"
				."<order_id>" .$data['order_id'] ."</order_id>"
				."<amount>" .$data['amount'] ."</amount>"	
				."<fee>" .$data['mer_fee'] ."</fee>"
				."<fee_taken_on>" .$data['fee_taken_on'] ."</fee_taken_on>"
				."<additional_info>" .$data['additional_info'] ."</additional_info>"	
				."<return_url><![CDATA[" .$pS2SUrl ."]]></return_url>"
				."<page_url><![CDATA[" .$pWebUrl ."]]></page_url>"				
				."</custody_req>";
				
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	


	function DoDpTrade($cfg,$user_id,$pTrdAmt,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];
		
		
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=DoDpTrade&from=".$_REQUEST['from'];//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=DoDpTrade&from=".$_REQUEST['from'];//s2s方式返回		
	
		$user = array();
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
				
		
		$data = array();
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['order_id'] = 0;//订单号 (唯一不可重复)		
		$data['user_id'] = $user_id;
		$data['amount'] = $pTrdAmt;//充值金额，单位：元
		$data['mer_fee'] = 0;//商户收取的手续费
		$data['fee_taken_on'] = '1';//费用承担方(宝付收取的费用),1平台2用户自担 
		$data['additional_info'] = '';//其它信息
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_recharge",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();
	
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_recharge",$data_update,'UPDATE','id='.$id);
		
		$strxml = DoDpTradeXml($data,$pWebUrl,$pS2SUrl);


		
		$pSign = md5($strxml."~|~".$key);
		//$aes=new MyAES();
		//$requestParams=$aes->encrypt($strxml,$key,$iv); //加密
		
		
				
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'custody/recharge.do" target="_self">		
			
				merchant_id:<input type="hidden" name="merchant_id" value="'.$merchant_id.'" /><br>
				terminal_id:<input type="hidden" name="terminal_id" value="'.$terminal_id.'" /><br>
				sign:<input type="hidden" name="sign" value="'.$pSign.'" /><br>					
				requestParams:<textarea name="requestParams" cols="100" rows="5">'.$strxml.'</textarea>	<br>	
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'recharge';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		return $html;
	
	}
	
	
	function DoDpTradeCallBack($str3Req){
		//print_r($str3XmlParaInfo);
		$order_id = $str3Req["order_id"];
		$where = " order_id = '".$order_id."'";
		$sql = "update ".DB_PREFIX."baofoo_recharge set is_callback = 1,create_time = ".TIME_UTC." where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($GLOBALS['db']->affected_rows()){		

			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_recharge",$str3Req,'UPDATE',$where);
				
			return 1;					
		}else{
			return 1;
		}
	}	
	
?>