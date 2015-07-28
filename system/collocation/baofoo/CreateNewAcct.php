<?php
	/**
	 * 
	 * @param unknown_type $pMerBillNo
	 * @return string
	 */
	function CreateNewAcctXml($IpsAcct,$pWebUrl,$pS2SUrl){	

		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<bf_account>" .$IpsAcct['bf_account'] ."</bf_account>"
				."<name>" .$IpsAcct['name'] ."</name>"
				."<id_card>" .$IpsAcct['id_card'] ."</id_card>"
				."<user_id>" .$IpsAcct['user_id'] ."</user_id>"	
				."<return_url><![CDATA[" .$pS2SUrl ."]]></return_url>"
				."<page_url><![CDATA[" .$pWebUrl ."]]></page_url>"				
				."</custody_req>";	

		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$strxml=str_replace('\\','',$strxml);//去除转义反斜杠\		
		return $strxml;		
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
	function CreateNewAcct($cfg,$user_id,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];
		
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php";//web方式返回
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=CreateNewAcct&from=".$_REQUEST['from'];//s2s方式返回		
	
		
		//$pWebUrl = 'http://www.test.com/ser_url';
		//$pS2SUrl = 'http://www.test.com/ser_url';
		
		$user = array();
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		
		$data = array();
		$data['merchant_id'] = $merchant_id;
		$data['terminal_id'] = $terminal_id;
		$data['bf_account'] = $user['mobile'];
		$data['name'] = $user['real_name'];
		$data['id_card'] = $user['idno'];
		$data['user_id'] = $user_id;
		$data['create_time'] = TIME_UTC;
	
		$sql = "select id from ".DB_PREFIX."baofoo_bind_state where user_id = ".$user_id;
		$id = intval($GLOBALS['db']->getOne($sql));
		
		if ($id == 0){
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_bind_state",$data,'INSERT');
			$id = $GLOBALS['db']->insert_id();
		}else{
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_bind_state",$data,'UPDATE','id='.$id);
		}
		
		$strxml = CreateNewAcctXml($data,$pWebUrl,$pS2SUrl);


		$pSign = md5($strxml."~|~".$key);
		$aes=new MyAES();
		
		$requestParams=$aes->encrypt($strxml,$key,$iv); //加密

				
		$html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /></head><body>
		<form name="form1" id="form1" method="post" action="'.$post_url.'custody/bindState.do" target="_self">		
		<input type="hidden" name="sign" value="'.$requestParams.'" />		
				<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
				<input type="hidden" name="terminal_id" value="'.$terminal_id.'" />				
				<input type="submit" value="提交"></input>
		</form>
		</body></html>
		<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'bindState';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		
		return $html;
	
	}
	
	//创建新帐户回调
	function CreateNewAcctCallBack($str3Req){
		//print_r($str3XmlParaInfo);
		$user_id = $str3Req["user_id"];
		$where = " user_id = '".$user_id."'";
		$sql = "update ".DB_PREFIX."baofoo_bind_state set is_callback = 1 where is_callback = 0 and ".$where;
		$GLOBALS['db']->query($sql);
		if ($GLOBALS['db']->affected_rows()){		
			//操作成功
			$data = array();			
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_bind_state",$str3Req,'UPDATE',$where);
			
			if ($str3Req['code'] == 'CSD000'){				
				$GLOBALS['db']->query("update ".DB_PREFIX."user set ips_acct_no = '".$user_id."' where id = ".$user_id);	
				return 1;		
			}else{
				return 0;
			}
		}else{
			return 1;
		}
	}	
	
?>