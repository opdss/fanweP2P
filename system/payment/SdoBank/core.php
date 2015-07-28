<?php

class shengpay{

	private $payHost;
	private $debug=false;
	private $key='shengfutongSHENGFUTONGtest';
	private $params=array(
		'Name'=>'B2CPayment',
		'Version'=>'V4.1.1.1.1',
		'Charset'=>'UTF-8',
		'MsgSender'=>'100894',
		'SendTime'=>'',
		'OrderNo'=>'',
		'OrderAmount'=>'',
		'OrderTime'=>'',
		'PayType'=>'PT001',
		'PayChannel'=>'19', /*（19 储蓄卡，20 信用卡）做直连时，储蓄卡和信用卡需要分开*/
		'InstCode'=>'CMB',  /*银行编码，参看接口文档*/
		'PageUrl'=>'',
		'NotifyUrl'=>'',
		'ProductName'=>'',
		'BuyerContact'=>'',
		'BuyerIp'=>'',
		'Ext1'=>'',
		'Ext2'=>'',
		'SignType'=>'MD5',
		'SignMsg'=>'',
	);

	function init($array=array()){
		if($this->debug)
			$this->payHost='https://mer.mas.sdo.com/web-acquire-channel/cashier.htm';
		else
			$this->payHost='https://mas.sdo.com/web-acquire-channel/cashier.htm';
		foreach($array as $key=>$value){
			$this->params[$key]=$value;
		}
	}

	function setKey($key){
		$this->key=$key;
	}
	function setParam($key,$value){
		$this->params[$key]=$value;
	}

	function takeOrder($oid,$fee,$payment_info){
		$this->params['OrderNo']=$oid;
		$this->params['OrderAmount']=$fee;
		$origin='';
		foreach($this->params as $key=>$value){
			if(!empty($value))
				$origin.=$value;
		}
		$SignMsg=strtoupper(md5($origin.$this->key));
		$this->params['SignMsg']=$SignMsg;
		$html =  '<meta http-equiv = "content-Type" content = "text/html; charset = '.$this->params['Charset'].'"/>
			<form  method="post" action="'.$this->payHost.'">';
			foreach($this->params as $key=>$value){
				$html.='<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
			}
			if(!empty($payment_info['logo']))
			$html .= "<input type='image' src='".APP_ROOT.$payment_info['logo']."' style='border:solid 1px #ccc;'><div class='blank'></div>";
			$html.='<input type="submit" name="submit" class="paybutton" value="提交到盛付通" id="dh">
			</form>';
		return $html;
	}

	function returnSign(){
		$params=array(
			'Name'=>'',
			'Version'=>'',
			'Charset'=>'',
			'TraceNo'=>'',
			'MsgSender'=>'',
			'SendTime'=>'',
			'InstCode'=>'',
			'OrderNo'=>'',
			'OrderAmount'=>'',
			'TransNo'=>'',
			'TransAmount'=>'',
			'TransStatus'=>'',
			'TransType'=>'',
			'TransTime'=>'',
			'MerchantNo'=>'',
			'ErrorCode'=>'',
			'ErrorMsg'=>'',
			'Ext1'=>'',
			'Ext2'=>'',
			'SignType'=>'MD5',
		);
		foreach($_POST as $key=>$value){
			if(isset($params[$key])){
				$params[$key]=$value;
			}
		}
		$TransStatus=(int)$_POST['TransStatus'];
		$origin='';
		foreach($params as $key=>$value){
			if(!empty($value))
				$origin.=$value;
		}
		$SignMsg=strtoupper(md5($origin.$this->key));
		if($SignMsg==$_POST['SignMsg'] and $TransStatus==1){
			return true;
		}else{
			return false;
		}
	}

}

