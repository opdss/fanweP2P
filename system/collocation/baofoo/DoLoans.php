<?php

	function DoLoansXml($data,$actions){
	
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
	 * 满标放款
	 * @param unknown_type $deal_id
	 * @param unknown_type $repay_start_time 开始还款日期
	 * @param unknown_type $post_url
	 * @return string
	 */
	function DoLoans($cfg,$deal_id,$repay_start_time,$post_url){
		$merchant_id = $cfg['merchant_id'];
		$terminal_id = $cfg['terminal_id'];
		$key=$cfg['key'];
		$iv=$cfg['iv'];

		
		//$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=response&class_name=Baofoo&class_act=DoLoans&from=".$_REQUEST['from'];//web方式返回
		$pWebUrl= SITE_DOMAIN.APP_ROOT."/index.php";
		$pS2SUrl= SITE_DOMAIN.APP_ROOT."/index.php?ctl=collocation&act=notify&class_name=Baofoo&class_act=DoLoans&from=".$_REQUEST['from'];//s2s方式返回		
	
		$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id = ".$deal_id);
		//$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		
		$pLendFee = round(floatval($deal['services_fee']) / 100 * $deal['borrow_amount'],2);//借款人手续费
		
		$data = array();
		$data['merchant_id'] = $merchant_id;//商户号
		$data['terminal_id'] = $terminal_id;//终端号
		$data['action_type'] = 2;//请求类型，投标为1，满标为2，流标为3，还标为4
		$data['order_id'] = 0;
		
		$data['cus_id'] = $deal_id;
		$data['cus_name'] = $deal['sub_name'];//项目名称
		$data['brw_id'] = $deal['user_id'];//借款人	
		$data['req_time'] =  microtime_float();// get_gmtime();//请求时间 例如 1405668253874    （当前时间转换毫秒）
		
		$data['fee'] = $pLendFee; //手续费(涉及到满标、还款接口)
		
		$data['repay_start_time'] = $repay_start_time;// 开始还款日期
		
		$data['load_amount'] = 0;// 记录投标金额
		
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data,'INSERT');
		$id = $GLOBALS['db']->insert_id();

		
		$data_update = array();
		$data_update['order_id'] = $id;
		$data['order_id'] = $id;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_business",$data_update,'UPDATE','id='.$id);
		
		//目前满标金额验证加入了，金额验证， {（投资总额 - 商户手续费） - 流标总额 }  = = 满标金额
		$actions = "<actions><action><user_id>".intval($deal['user_id'])."</user_id><is_voucher>0</is_voucher><amount>".$deal['borrow_amount']."</amount></action></actions>";
									
		
		$strxml = DoLoansXml($data,$actions);			
		
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
		';//<script language="javascript">document.form1.submit();</script>';
		//echo $html; exit;
		
		$baofoo_log = array();
		$baofoo_log['code'] = 'business_2';
		$baofoo_log['create_date'] = to_date(TIME_UTC,'Y-m-d H:i:s');
		$baofoo_log['strxml'] =$strxml;
		$baofoo_log['html'] = $html;
		$GLOBALS['db']->autoExecute(DB_PREFIX."baofoo_log",$baofoo_log);
		
		return $html;
	}
	
	//回调
	function DoLoansCallBack($str3Req){
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
								
				$deal_load = array();
				$deal_load['is_has_loans'] = 1;//1#转账成功
				$where = " deal_id = ".$deal_id;
				$GLOBALS['db']->autoExecute(DB_PREFIX."deal_load",$deal_load,'UPDATE',$where);
				
				$count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_load where is_has_loans = 0 and deal_id = ".$deal_id);
				if ($count == 0){
					//已经全部放款完成,生成：还款计划以及回款计划;
					$repay_start_time = intval($ipsdata['repay_start_time']);
					require_once(APP_ROOT_PATH."app/Lib/common.php");
					return do_loans($deal_id,$repay_start_time,1);
				}
			}			
		}else{
			return 1;
		}
	}	
	
?>