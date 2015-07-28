<?php
class pay_order{
	public function index(){
		$email = strim($GLOBALS['request']['email']);//用户名或邮箱
		$pwd = strim($GLOBALS['request']['pwd']);//密码
		
		//检查用户,用户密码
		$user = user_check($email,$pwd);
		$user_id  = intval($user['id']);		
		
			
		$root = array();
		$root['response_code'] = 1;
		if($user_id>0)
		{
			$order_id = intval($GLOBALS['request']['order_id']);			
			
			$root['user_login_status'] = 1;
			
			$root['pay_status'] = 0;//0:订单未收款(全额);1:订单已经收款(全额)
			$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where user_id = {$user_id} and id = ".$order_id);
			if (empty($order)){
				$root['pay_status'] = 1;
				$root['pay_info'] = '订单不存在.';
				$root['show_pay_btn'] = 0;
				output($root);		
			}
			
			if ($order['is_paid'] == 2){
				$root['pay_status'] = 1;
				$root['pay_code'] = '';
				$root['order_id'] = $order_id;
				$root['order_sn'] = $order['notice_sn'];				
				$root['pay_info'] = '订单已经收款.';
				$root['show_pay_btn'] = 0;
				output($root);
			}
			
			
			$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id=".intval($order['payment_id']));
			$pay_code = strtolower($payment_info['class_name']);
			if (!($pay_code == 'malipay' || $pay_code == 'mtenpay' || $pay_code == 'mcod' || $pay_code == 'walipay' || $pay_code == 'wtenpay')){
				$root['response_code'] = 0;
				$root['pay_info'] = '手机版本不支付,无法在手机上支付.'.$pay_code;
				$root['show_pay_btn'] = 0;	
				output($root);
			}
			
			//3. 相应的支付接口
			$pay_price = $order['money'];
			
			//创建了支付单号，通过支付接口创建支付数据
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			$pay = $payment_object->get_payment_code($order_id);
			
			
			$root['response_code'] = 1;
			$root['pay_code'] = $pay['pay_code'];
			$root['order_id'] = $order_id;
			$root['order_sn'] = $order['notice_sn'];
			$root['show_pay_btn'] = 0;//0:不显示，支付按钮; 1:显示支付按钮
			
			//支付接口支付 malipay,支付宝;mtenpay,财付通;mcod,货到付款/现金支付
			if ($pay['pay_code'] == 'malipay'){
				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['malipay'] = $pay;
				
				if ($root['pay_money'] > 0){
					$root['show_pay_btn'] = 1;
				}
			}else if ($pay['pay_code'] == 'mtenpay'){
				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['mtenpay'] = $pay;
				if ($root['pay_money'] > 0){
					$root['show_pay_btn'] = 1;
				}		
			}else if ($pay['pay_code'] == 'mcod'){
				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['mcod'] = $pay;
				
				$root['show_pay_btn'] = 0;
			}else if ($pay['pay_code'] == 'walipay'){
				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['pay_wap'] = $pay['notify_url'];
				$root['walipay'] = $pay;
				
				if ($root['pay_money'] > 0){
					$root['show_pay_btn'] = 1;
				}
			}else if ($pay['pay_code'] == 'wtenpay'){
				$root['pay_money_format'] = $pay['total_fee_format'];
				$root['pay_money'] = $pay['total_fee'];
				$root['pay_info'] = $pay['body'];
				$root['pay_wap'] = $pay['notify_url'];
				$root['wtenpay'] = $pay;
				
				if ($root['pay_money'] > 0){
					$root['show_pay_btn'] = 1;
				}
			}else{
				$root['response_code'] = 0;
				$root['pay_info'] = '手机版本不支付,无法在手机上支付.';
				$root['show_pay_btn'] = 0;
			}
			
			output($root);
		}
		else {
			$root['user_login_status'] = 0;
			output($root);
		}		
	}
}
?>