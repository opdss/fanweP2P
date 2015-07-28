<?php
	
	function QueryForAccBalance($cfg,$user_id,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];
		
		/* 请求参数 */
		$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
				."<custody_req>"
				."<merchant_id>" .$merchant_id ."</merchant_id>"
				."<user_id>" .$user_id."</user_id>"
				."</custody_req>";

		$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
		$requestParams=str_replace('\\','',$strxml);//去除转义反斜杠\		
	
		
		$sign=md5($requestParams."~|~".$key);
		//$aes=new MyAES();
		//$requestParams=$aes->encrypt($requestParams,$key,$iv); //加密
		
		//PHP提交POST
		$post_data = array("merchant_id"=>$merchant_id,"terminal_id"=>$terminal_id,"sign"=>$sign,"requestParams"=>$requestParams);
		/*
		$baofoo_log = array();
		$baofoo_log['code'] = 'accountBalance';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$requestParams;
		$baofoo_log['html'] = print_r($post_data,true);
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		*/
		
		$resultStr = httpRequestPOST($post_url."custody/accountBalance.do",$post_data);
		
		//print($result);
		if (empty($resultStr)){
			$result = array();
			$result['pErrCode'] = 9999;
			$result['pErrMsg'] = '返回出错';
			$result['pIpsAcctNo'] = '';
			$result['pBalance'] = 0;
			$result['pLock'] = 0;
			$result['pNeedstl'] = 0;
		}else{
			
				require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
				$str3ParaInfo = @XML_unserialize($resultStr);
				//print_r($str3ParaInfo);
				$str3Req = $str3ParaInfo['crs'];
		
				$sign = $str3Req["sign"];
				$Md5sign = Md5($str3Req["code"].'~|~'.$str3Req["balance"].'~|~'.$key);
				
				if ($sign == $Md5sign){				
					$result = array();
					$result['pErrCode'] = '0000';
					$result['pErrMsg'] = $str3Req["msg"];
					$result['pIpsAcctNo'] = $user_id;
					$result['pBalance'] = $str3Req["balance"];
					$result['pLock'] = 0;
					$result['pNeedstl'] = 0;// $str3Req["availableAmount"];	
				}else{
					$result = array();
					$result['pErrCode'] = $str3Req["code"];
					$result['pErrMsg'] = '签名验证失败';
					$result['pIpsAcctNo'] = '';
					$result['pBalance'] = 0;
					$result['pLock'] = 0;
					$result['pNeedstl'] = 0;
				}		
		}
		
		return $result;
		
		/*
		 * <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response platformNo="10040011137">
    <code>1</code>
    <description>操作成功</description>
    <memberType>PERSONAL</memberType>
    <activeStatus>ACTIVATED</activeStatus>
    <balance>9980.98</balance>
    <availableAmount>9980.98</availableAmount>
    <freezeAmount>0.00</freezeAmount>
    <cardNo>********5512</cardNo>
    <cardStatus>VERIFIED</cardStatus>
    <bank>CCB</bank>
    <autoTender>false</autoTender>
</response>
		 */
		
	
	}	
	
?>