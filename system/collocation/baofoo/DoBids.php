<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function DoBidsXml($data,$actions){	

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
				."<fee>" .$data['fee'] ."</fee>"
				."</custody_req>";
		
		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
	}
	

	/**
	 * 流标
	 * @param unknown_type $deal_id	 
	 * @param unknown_type $bids_msg 流标原因
	 * @return string
	 */
	function DoBids($cfg,$deal_id,$bids_msg,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];
		
		
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=DoBids&from=".$_REQUEST['from'];//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=DoBids&from=".$_REQUEST['from'];//s2s方式返回
		
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		
		
		$data = array();
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['action_type'] = 3;//请求类型，投标为1，满标为2，流标为3，还标为4
		$data['order_id'] = 0;
		
		$data['cus_id'] = $deal_id;
		$data['cus_name'] = $deal['sub_name'];//项目名称
		$data['brw_id'] = intval($deal['user_id']);//借款人
		$data['req_time'] =  microtime_float();// get_gmtime();//请求时间 例如 1405668253874    （当前时间转换毫秒）
		
		$data['fee'] = 0; //手续费(涉及到满标、还款接口)
		
		$data['load_user_id'] = 0;// 记录：投标人
		$data['load_amount'] = 0;// 记录：投标金额
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();
		
		
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data_update,'UPDATE','id='.$id);
		
		
		$actions = "<actions>";
		$r_load_list = $GLOBALS['db']->getAll("SELECT id,user_id,money FROM ".DB_PREFIX."deal_load WHERE is_repay=0 AND deal_id=$deal_id");
		foreach($r_load_list as $k=>$v){
			$actions .= "<action><user_id>".intval($v['user_id'])."</user_id><user_name></user_name><amount>".$v['money']."</amount></action>";	

			$detail = array();
			$detail['pid'] = $id;
			$detail['deal_load_id'] = $v['id'];
			$detail['user_id'] = $v['user_id'];
			$detail['amount'] = $v['money'];
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business_detail",$detail,'INSERT');
		}				
		$actions .= "</actions>";
									
		
		$strxml = DoBidsXml($data,$actions);			
		
		$pSign = md5($strxml."~|~".$key);
		//$aes=new MyAES();
		//$requestParams=$aes->encrypt($strxml,$key,$iv); //加密
		
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
		<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'business_3';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		return $html;
	}
	
	//回调
	function DoBidsCallBack($str3Req){
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
				
				
				$deal_id = (int)$ipsdata['cus_id'];
								
				
				require_once APP_ROOT_PATH.'app/Lib/common.php';
				$result = do_received($deal_id,1,$ipsdata['bids_msg']);
				
				//本地解冻:借款保证金,担保保证金0
				$GLOBALS['db']->query("update ".DB_PREFIX."deal set ips_over = 1 ,un_real_freezen_amt = real_freezen_amt,un_guarantor_real_freezen_amt = guarantor_real_freezen_amt where id = ".$deal_id);
			}			
		}else{
			return 1;
		}
	}	
	
?>