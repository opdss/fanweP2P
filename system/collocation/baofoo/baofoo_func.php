<?php

/**
 *
 * @param unknown_type $cfg
 * @param unknown_type $type type 当前共五种类型：  1-投标 2-满标 3-流标   4-还款 5-充值 6-提现 ;
 * @param unknown_type $order_id
 * @param unknown_type $post_url
 * @return Ambigous <multitype:number string , multitype:unknown number string >
 */
function p2pQuery($cfg,$type, $order_id,$post_url){
	$merchant_id = $cfg['merchant_id'];
	$terminal_id = $cfg['terminal_id'];
	$key=$cfg['key'];
	$iv=$cfg['iv'];

	/* 请求参数 */
	$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
			."<custody_req>"
			."<order_id>" .$order_id ."</order_id>"
					."<type>" .$type."</type>"
							."</custody_req>";

	$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
	$requestParams=str_replace('\\','',$strxml);//去除转义反斜杠\


	$sign=md5($requestParams."~|~".$key);

	//PHP提交POST
	$post_data = array("merchant_id"=>$merchant_id,"terminal_id"=>$terminal_id,"sign"=>$sign,"requestParams"=>$requestParams);

	$baofoo_log = array();
	$baofoo_log['code'] = 'p2pQuery';
	$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
	$baofoo_log['strxml'] =$requestParams;
	$baofoo_log['html'] = print_r($post_data,true);
	$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);


	$resultStr = httpRequestPOST($post_url."custody/p2pQuery.do",$post_data);

	//print($result);
	if (empty($resultStr)){
		$result = array();
		$result['pErrCode'] = 9999;
		$result['pErrMsg'] = '返回出错';
	}else{
		require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
		$str3ParaInfo = @XML_unserialize($resultStr);
		$str3Req = $str3ParaInfo['crs'];

		$sign = $str3Req["sign"];
		$Md5sign = Md5($str3Req["code"].'~|~'.$str3Req["msg"].'~|~'.$key);

		if ($sign == $Md5sign){
			$result = array();
			$result['pErrCode'] = $str3Req["code"];
			$result['pErrMsg'] = $str3Req["msg"];
			$result['result'] = $str3Req["result"];
		}else{
			$result = array();
			$result['pErrCode'] = -1;
			$result['pErrMsg'] = '签名验证失败';
		}
	}

	return $result;
}



/**
 *（十一）转账（服务端接口）

 1 该接口只能操作 商户的可用资金和会员的托管资金。
   2 例如 user_id=100 和 user_id=101 之前转账
      amount=100  fee=10  fee_taken_on=1（收款方）
      则 user_id=100 托管账户减少100，user_id=101托管账户增加90 平台可用增加10
如果fee_taken_on=0 
      则user_id=100 托管账户减少110，user_id=101托管账户增加100 平台可用增加10
      
 * @param unknown_type $cfg
 * @param unknown_type $type type 当前共五种类型：  1-投标 2-满标 3-流标   4-还款 5-充值 6-提现 ;
 * @param unknown_type $order_id
 * @param unknown_type $post_url
 * @return Ambigous <multitype:number string , multitype:unknown number string >
 * 
 * $params 参数内容
 * merchant_id	商户号	是	100000675
order_id	订单号	是	
payer_user_id	用户号	是	如对应的类型为1 则为商户号，否则为平台user_id
payee_user_id	用户号	是	如对应的类型为1 则为商户号，否则为平台user_id
payer_type	付款方帐号类型0或1	是	0为普通用户(平台的user_id) 1为商户号
payee_type	收款方帐号类型0或1	是	0为普通用户(平台的user_id) 1为商户号
amount	转账金额	是	
fee	手续费	是	该费用将会从指定费用方账户收取到平台可用账户
fee_taken_on	费用收取方0或1	是	0付款方1收款方
req_time	请求时间	是	例如 1405668253874

 */
function acctTrans($cfg,$params,$post_url){
	$merchant_id = $cfg['merchant_id'];
	$terminal_id = $cfg['terminal_id'];
	$key=$cfg['key'];
	$iv=$cfg['iv'];

	
	$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_acct_trans",$params,'INSERT');
	$id = $GLOBALS['db']->insert_id();
	if (intval($params['order_id']) == 0){
		$params['order_id'] = $id;
	}
	
	/* 请求参数 */
	$strxml = "<?xml version='1.0' encoding='UTF-8'?>"
			."<custody_req>"
			."<merchant_id>" .$params['merchant_id'] ."</merchant_id>"
			."<order_id>" .$params['order_id'] ."</order_id>"
			."<payer_user_id>" .$params['payer_user_id'] ."</payer_user_id>"
			."<payee_user_id>" .$params['payee_user_id'] ."</payee_user_id>"
			."<payer_type>" .$params['payer_type'] ."</payer_type>"
			."<payee_type>" .$params['payee_type'] ."</payee_type>"
			."<amount>" .$params['amount'] ."</amount>"
			."<fee>" .$params['fee'] ."</fee>"
			."<fee_taken_on>" .$params['fee_taken_on'] ."</fee_taken_on>"
			."<req_time>" .$params['req_time'] ."</req_time>"
			."</custody_req>";

	$strxml=preg_replace("/[\s]{2,}/","",$strxml);//去除空格、回车、换行等空白符
	$requestParams=str_replace('\\','',$strxml);//去除转义反斜杠\


	$sign=md5($requestParams."~|~".$key);

	//PHP提交POST
	$post_data = array("merchant_id"=>$merchant_id,"terminal_id"=>$terminal_id,"sign"=>$sign,"requestParams"=>$requestParams);

	$baofoo_log = array();
	$baofoo_log['code'] = 'acctTrans';
	$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
	$baofoo_log['strxml'] =$requestParams;
	$baofoo_log['html'] = print_r($post_data,true);
	$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);

	
	
	$resultStr = httpRequestPOST($post_url."custody/acctTrans.do",$post_data);

	//print($result);
	if (empty($resultStr)){
		$result = array();
		$result['code'] = 9999;
		$result['msg'] = '返回出错';
	}else{
		require_once(APP_ROOT_PATH.'system/collocation/ips/xml.php');
		$str3ParaInfo = @XML_unserialize($resultStr);
		$str3Req = $str3ParaInfo['crs'];

		$sign = $str3Req["sign"];
		$Md5sign = Md5($str3Req["code"].'~|~'.$str3Req["msg"].'~|~'.$key);

		if ($sign == $Md5sign){
			
			
			$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_acct_trans",$str3Req,'UPDATE', "id = ".$id);
			
			$result = array();
			$result['code'] = $str3Req["code"];
			$result['msg'] = $str3Req["msg"];
			$result['sign'] = $str3Req["sign"];
		}else{
			$result = array();
			$result['code'] = -1;
			$result['msg'] = '签名验证失败';
		}
	}

	return $result;
}

/** 获取当前时间戳，精确到毫秒 */

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((int)($usec * 1000) + (float)$sec * 1000);
}

//PHP请求GET函数
function httpRequestGET($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout=20;
	if(($fsock = fsockopen($host_ip, 80, $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}

	$request = $url2["path"] . ($url2["query"] != "" ? "?" . $url2["query"] : "") . ($url2["fragment"] != "" ? "#" . $url2["fragment"] : "");
	$in = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	unset($in);

	$out = "";
	while($buff = @fgets($fsock, 2048)){
		$out .= $buff;
	}
	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;
		}else{
			return false;
		}
	}else{
		return false;
	}
}


////PHP请求POST 函数
function httpRequestPOST($url,$post_data){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout=20;//秒
	if(($fsock = fsockopen($host_ip, 80, $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}

	$request = $url2["path"] . ($url2["query"] != "" ? "?" . $url2["query"] : "") . ($url2["fragment"] != "" ? "#" . $url2["fragment"] : "");

	$needChar = false;

	foreach($post_data as $key => $val) {

		$post_data2 .= ($needChar ? "&" : "") . urlencode($key) . "=" . urlencode($val);
		$needChar = true;
	}
	$in = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";

	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	unset($in);

	$out = "";
	while($buff = fgets($fsock, 2048)){
		$out .= $buff;
	}

	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;
		}else{
			return false;
		}
	}else{
		return false;
	}
}

//接口托管加密和解密类AES

class MyAES{


	public function encrypt($data,$key,$iv){
		$encrypted=mcrypt_encrypt(MCRYPT_RIJNDAEL_128,$key,$data,MCRYPT_MODE_CBC,$iv);
		$data=bin2hex($encrypted);
		return $data;
	}

	public function decrypt($sData,$sKey,$iv){
		$sData=MyAES::hex2bin($sData);
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128,$sKey,$sData,MCRYPT_MODE_CBC,$iv);
	}

	public static function hex2bin($hexdata){
		$bindata='';
		$length=strlen($hexdata);
		for($i==0;$i<$length;$i+=2){
			$bindata.=chr(hexdec(substr($hexdata,$i,2)));
		}
		return $bindata;
	}

}

?>