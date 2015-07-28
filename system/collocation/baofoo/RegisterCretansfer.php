<?php
		
	/**
	 * 登记债权转让
	 * @param int $transfer_id  转让id
	 * @param int $t_user_id  受让用户ID
	 * @param int $MerCode  商户ID
	 * @param string $cert_md5 
	 * @param string $post_url
	 * @return string
	 */
	function RegisterCretansfer($cfg,$transfer_id,$t_user_id,$post_url){
	
		$transfer = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_load_transfer where id = ".$transfer_id);
		$deal_id = intval($transfer['deal_id']);
		$user_id = intval($transfer['user_id']);	
		
		$user = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id);
		$tuser = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$t_user_id);
		$user_load_transfer_fee = $GLOBALS['db']->getOne("SELECT user_load_transfer_fee FROM ".DB_PREFIX."deal WHERE id=".$deal_id);

		if (empty($user['ips_acct_no']) || empty($tuser['ips_acct_no'])){
			return '有一方未申请 托管 帐户';
		}
		
		
		$sql = "update ".DB_PREFIX."deal_load_transfer set lock_user_id = ".$t_user_id.", lock_time =".TIME_UTC;
		$sql .= " where ips_status = 0 and t_user_id = 0 and status = 1 and (lock_user_id = 0 || lock_user_id =".$t_user_id." || (lock_user_id > 0 && lock_time < ".(TIME_UTC - 600)."))";
		$sql .= " and id = ".$transfer_id;
		//echo $sql; exit;
		$GLOBALS['db']->query($sql);
		//转让锁定
		if ($GLOBALS['db']->affected_rows()){
					
			$parms = array();
			$parms['merchant_id'] = $cfg['merchant_id'];//	商户号	是	100000675
			$parms['order_id'] = 0;//	订单号	是
			$parms['payer_user_id'] = $t_user_id;//付款方帐号	是	如对应的类型为1 则为商户号，否则为平台user_id
			$parms['payee_user_id'] = $user_id;//收款方帐号	是	如对应的类型为1 则为商户号，否则为平台user_id
			$parms['payer_type'] = 0;//	付款方帐号类型0或1	是	0为普通用户(平台的user_id) 1为商户号
			$parms['payee_type'] = 0;//	收款方帐号类型0或1	是	0为普通用户(平台的user_id) 1为商户号
			$parms['amount'] = round($transfer['transfer_amount'],2);//	转账金额	是
			$parms['fee'] = round($transfer['transfer_amount']*$user_load_transfer_fee*0.01,2);//	手续费	是	该费用将会从指定费用方账户收取到平台可用账户
			$parms['fee_taken_on'] = 1;//	费用收取方0或1	是	0付款方1收款方
			$parms['req_time'] = microtime_float();//	请求时间	是	例如 1405668253874
		
			$parms['ref_id'] = $transfer_id;//转发类型关联的id;ref_type=1时fanwe_deal_load_transfer.id
			$parms['ref_type'] = 1;//转帐类型;1:债权转让
				
			//调用转帐接口,手费费由转出方出
			$result = acctTrans($cfg,$parms,$post_url);
			
			if ($result['code'] == 'CSD000'){			
				$sql = "update ".DB_PREFIX."deal_load_transfer set ips_status = 2, pMerBillNo = '".$t_user_id."',t_user_id = lock_user_id, transfer_time = '".get_gmtime()."', ips_bill_no = id where ips_status = 0 and id =".$transfer_id;
				//echo $sql;
				$GLOBALS['db']->query($sql);
				
				//将用户投资回款计划,收款人更改为：承接者
				$sql = "update ".DB_PREFIX."deal_load_repay set t_user_id = ".$t_user_id." where has_repay = 0 and load_id =".intval($transfer['load_id'])." and user_id =".intval($transfer['user_id'])." and deal_id = ".$deal_id;
					//echo $sql;
				$GLOBALS['db']->query($sql);
									
				return '转让成功';
			}else{
				//解除锁定
				$sql = "update ".DB_PREFIX."deal_load_transfer set lock_user_id = 0, lock_time = 0 where id =".$transfer_id;				
				$GLOBALS['db']->query($sql);
				
				return '转帐失败code:'.$result['code'].';msg:'.$result['msg'];
			}
		}else{
			return '该债权转让已经被其它用户锁定';
		}		
	}	
	
?>