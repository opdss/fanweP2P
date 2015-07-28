<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

define("EMPTY_ERROR",1);  //未填写的错误
define("FORMAT_ERROR",2); //格式错误
define("EXIST_ERROR",3); //已存在的错误

define("ACCOUNT_NO_EXIST_ERROR",1); //帐户不存在
define("ACCOUNT_PASSWORD_ERROR",2); //帐户密码错误
define("ACCOUNT_NO_VERIFY_ERROR",3); //帐户未激活


	/**
	 * 生成会员数据
	 * @param $user_data  提交[post或get]的会员数据
	 * @param $mode  处理的方式，注册或保存
	 * 返回：data中返回出错的字段信息，包括field_name, 可能存在的field_show_name 以及 error 错误常量
	 * 不会更新保存的字段为：score,money,verify,pid
	 */
	function save_user($user_data,$mode='INSERT')
	{		
		//开始数据验证
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if($mode=="INSERT" || isset($user_data['user_name'])){
			if(trim($user_data['user_name'])=='')
			{
				$field_item['field_name'] = 'user_name';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if(!preg_match("/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\']+$/",$user_data['user_name']) ||  is_numeric($user_data['user_name'])){
				$field_item['field_name'] = 'user_name';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where user_name = '".trim($user_data['user_name'])."' and id <> ".intval($user_data['id']))>0)
			{
				$field_item['field_name'] = 'user_name';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		}
		
		

		if((intval($_REQUEST["REGISTER_TYPE"])==0 || intval($_REQUEST["REGISTER_TYPE"])==2)&&intval($user_data["user_type"])!=2 && intval($user_data["user_type"])!=3 &&(( $mode=="INSERT" && (intval(app_conf('REGISTER_TYPE')) == 0 || intval(app_conf('REGISTER_TYPE')) == 2 )) || isset($user_data['email']))){
			if(strim($user_data['email'])!="" && $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".trim($user_data['email'])."' and id <> ".intval($user_data['id']))>0)
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			if(trim($user_data['email'])=='')
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			if(!check_email(trim($user_data['email'])))
			{
				$field_item['field_name'] = 'email';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			if(isset($user_data['emailpassed']))
				$user['emailpassed'] = intval($user_data['emailpassed']);
		}
		if(intval($user_data["user_type"])==3 && $user_data["idno"] !="")
		{
			$user["idcardpassed"] = $user_data["idcardpassed"];
			$user["idcardpassed_time"] = $user_data["idcardpassed_time"];
		}

		if((intval($_REQUEST["REGISTER_TYPE"])==0 || intval($_REQUEST["REGISTER_TYPE"])==1)&&intval($user_data["user_type"])!=2 && intval($user_data["user_type"])!=3 &&(($mode=="INSERT" && (intval(app_conf('REGISTER_TYPE')) == 0 || intval(app_conf('REGISTER_TYPE')) == 1 ) ) || isset($user_data['mobile']))){
			
			if((trim($user_data['mobile'])==''))
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if(!check_mobile(trim($user_data['mobile'])))
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if($user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".trim($user_data['mobile'])."' and id <> ".intval($user_data['id']))>0)
			{
				$field_item['field_name'] = 'mobile';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if(isset($user_data['mobilepassed']))
				$user['mobilepassed'] = intval($user_data['mobilepassed']);
		}
		
		
		if(isset($user_data['idno']) && strim($user_data['idno'])!=""){
			if(getIDCardInfo($user_data['idno'])==0)
			{
				$field_item['field_name'] = 'idno';
				$field_item['error']	=	FORMAT_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
			
			if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where idno = '".trim($user_data['idno'])."' and id <> ".intval($user_data['id']))>0)
			{
				$field_item['field_name'] = 'idno';
				$field_item['error']	=	EXIST_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
			}
		
		}
		
		
		//验证扩展字段
		if(isset($user_data['user_pwd'])){
			$user_field = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."user_field");
			foreach($user_field as $field_item)
			{
				if($field_item['is_must']==1&&trim($user_data[$field_item['field_name']])=='')
				{
					$field_item['error']	=	EMPTY_ERROR;
					$res['status'] = 0;
					$res['data'] = $field_item;
					return $res;
				}
			}
		}
		
		
		//验证结束开始插入数据
		if($mode=="INSERT" || $user_data['user_name'])
			$user['user_name'] = $user_data['user_name'];
		
		$user['update_time'] = TIME_UTC;
		
		if(isset($user_data['pid']))
			$user['pid'] = $user_data['pid'];
		if(isset($user_data['referral_rate']))
			$user['referral_rate'] = $user_data['referral_rate'];
		if(isset($user_data['real_name']))
			$user['real_name'] = $user_data['real_name'];
		if(isset($user_data['idno']))
			$user['idno'] = $user_data['idno'];
		if(isset($user_data['graduation']))
			$user['graduation'] = $user_data['graduation'];
		if(isset($user_data['graduatedyear']))
			$user['graduatedyear'] = intval($user_data['graduatedyear']);
		if(isset($user_data['university']))
			$user['university'] = $user_data['university'];
		if(isset($user_data['marriage']))
			$user['marriage'] = $user_data['marriage'];
		if(isset($user_data['haschild']))
			$user['haschild'] = intval($user_data['haschild']);
		if(isset($user_data['hashouse']))
			$user['hashouse'] = intval($user_data['hashouse']);
		if(isset($user_data['houseloan']))
			$user['houseloan'] = intval($user_data['houseloan']);
		if(isset($user_data['hascar']))
			$user['hascar'] = intval($user_data['hascar']);
		if(isset($user_data['carloan']))
			$user['carloan'] = intval($user_data['carloan']);
		if(isset($user_data['address']))
			$user['address'] = $user_data['address'];
		if(isset($user_data['phone']))
			$user['phone'] = $user_data['phone'];
		if(isset($user_data['n_province_id']))
			$user['n_province_id'] = intval($user_data['n_province_id']);
		if(isset($user_data['n_city_id']))
			$user['n_city_id'] = intval($user_data['n_city_id']);
		if(isset($user_data['province_id']))
			$user['province_id'] = intval($user_data['province_id']);
		if(isset($user_data['city_id']))
			$user['city_id'] = intval($user_data['city_id']);
		if(isset($user_data['sex']))
			$user['sex'] = intval($user_data['sex']);
		if(isset($user_data['byear']))
			$user['byear'] = intval($user_data['byear']);
		if(isset($user_data['bmonth']))
			$user['bmonth'] = intval($user_data['bmonth']);
		if(isset($user_data['bday']))
			$user['bday'] = intval($user_data['bday']);
		
		if(isset($user_data['referer_memo']))
			$user['referer_memo'] = $user_data['referer_memo'];
		if(isset($user_data['admin_id']))
			$user['admin_id'] = $user_data['admin_id'];
		
		/**担保机构字段**/
		if(isset($user_data['short_name']))
			$user['short_name'] = $user_data['short_name'];
		if(isset($user_data['brief']))
			$user['brief'] = $user_data['brief'];
		if(isset($user_data['header']))
			$user['header'] = $user_data['header'];
		if(isset($user_data['company_brief']))
			$user['company_brief'] = $user_data['company_brief'];
		if(isset($user_data['history']))
			$user['history'] = $user_data['history'];
		if(isset($user_data['content']))
			$user['content'] = $user_data['content'];
		if(isset($user_data['sort']))
			$user['sort'] = $user_data['sort'];
		if(isset($user_data['ips_mer_code']))
			$user['ips_mer_code'] = $user_data['ips_mer_code'];
		if(isset($user_data['ips_acct_no']))
			$user['ips_acct_no'] = $user_data['ips_acct_no'];
		if(isset($user_data['acct_type']))
			$user['acct_type'] = intval($user_data['acct_type']);	
		
		if(isset($user_data['u_year']))
			$user['u_year'] = $user_data['u_year'];
		if(isset($user_data['u_special']))
			$user['u_special'] = $user_data['u_special'];
		if(isset($user_data['university']))
			$user['university'] = $user_data['university'];
		if(isset($user_data['u_alipay']))
			$user['u_alipay'] = $user_data['u_alipay'];
			

//		//定义注册完成为普通VIP会员
//		$vip_grade="普通VIP会员";
//		$vip_grade_id=$GLOBALS['db']->getOne("select id from ".DB_PREFIX."vip_type where vip_grade = '".$vip_grade."' ");
//		if($vip_grade_id){
//			$user['vip_grade'] = $vip_grade_id;
//		}else{
//			$user['vip_grade'] = 1;
//		}
//		$user['vip_state'] = 1;
			
	
		//自动获取会员分组
		if(intval($user_data['group_id'])!=0)
			$user['group_id'] = $user_data['group_id'];
		else
		{
			if($mode=='INSERT')
			{
				//获取默认会员组, 即升级积分最小的会员组
				$user['group_id'] = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."user_group order by score asc limit 1");
			}
		}
		
		//会员状态
		if(intval($user_data['is_effect'])!=0)
		{
			$user['is_effect'] = $user_data['is_effect'];
		}
		else
		{
			if($mode == 'INSERT')
			{
				if(intval(app_conf("USER_VERIFY")) == 4)
					$user['is_effect'] = 0;
				elseif(app_conf("USER_VERIFY") == 3)
					$user['is_effect'] = 1;
			}
		}
		
		if($mode=="INSERT" || isset($user_data['email']))
			$user['email'] = $user_data['email'];
			
		if($mode=="INSERT" || isset($user_data['mobile']))
			$user['mobile'] = $user_data['mobile'];
		
		if($mode=="INSERT" || isset($user_data['user_type'])){
			
			$user['user_type'] = intval($user_data['user_type']);
		}
		if($mode == 'INSERT')
		{
			$user['create_time'] = TIME_UTC;
			$user['create_date'] = to_date(TIME_UTC,"Y-m-d");
			$user['code'] = ''; //默认不使用code, 该值用于其他系统导入时的初次认证
		}
		else
		{
			$user['code'] = $GLOBALS['db']->getOne("select code from ".DB_PREFIX."user where id =".$user_data['id']);
		}
		
		if(isset($user_data['user_pwd'])&&$user_data['user_pwd']!='')
			$user['user_pwd'] = md5($user_data['user_pwd'].$user['code']);
		$user['old_user_name'] = $user_data['old_user_name'];
		$user['old_email'] = $user_data['old_email'];
		$user['old_password'] = $user_data['old_password'];
		$user['new_password'] = $user_data['user_pwd'];
		$date_time = to_date(TIME_UTC);
		
		//载入会员整合
		$integrate_code = trim(app_conf("INTEGRATE_CODE"));
		if($integrate_code!='')
		{
			$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
			if(file_exists($integrate_file))
			{
				require_once $integrate_file;
				$integrate_class = $integrate_code."_integrate";
				$integrate_obj = new $integrate_class;
			}	
		}
		//同步整合
		if($integrate_obj)
		{
			if(empty($user_data['email'])){
				if (!empty($user_data['mobile'])){
					//如果有手机号码则使用:  手机号@域名  格式组成邮箱
					$user_data['email'] = get_site_email($user_data['mobile']);
				}else{					
					if (ctype_alnum($user_data['user_name'])){
						//昵称是字母跟数字的组合则:  昵称@域名  格式组成邮箱
						$user_data['email'] = get_site_email($user_data['user_name']);
					}else{
						//昵称是中文组合则:  base64(昵称)@域名  格式组成邮箱
						$user_data['email'] = get_site_email(base64_encode($user_data['user_name']));
					}					
				}
				
				$user['email'] = $user_data['email'];
			}

			
			if($mode == 'INSERT')
			{
				
				$res = $integrate_obj->add_user($user_data['user_name'],$user_data['user_pwd'],$user_data['email']);
				$user['integrate_id'] = intval($res['data']);
			}
			else
			{
				$add_res = $integrate_obj->add_user($user_data['user_name'],$user_data['user_pwd'],$user_data['email']);
				if(intval($add_res['status']) && $integrate_code!="Cn273")
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."user set integrate_id = ".intval($add_res['data'])." where id = ".intval($user_data['id']));
				}
				else
				{
					if(isset($user_data['user_pwd'])&&$user_data['user_pwd']!='') //有新密码
					{
						$status = $integrate_obj->edit_user($user,$user_data['user_pwd']);
						if($status<=0)
						{
							//修改密码失败
							$res['status'] = 0;						
						}
					}
				}
			}			
			if(intval($res['status'])==0) //整合注册失败
			{
				return $res;
			}
		}
		
		//引入时区配置及定义时间函数
		if(function_exists('date_default_timezone_set'))
			date_default_timezone_set(app_conf('DEFAULT_TIMEZONE'));
		
		
		if($mode == 'INSERT')
		{
			$user['register_ip'] = CLIENT_IP;
			$s_api_user_info = es_session::get("api_user_info");
			$user[$s_api_user_info['field']] = $s_api_user_info['id'];
			es_session::delete("api_user_info");
			$where = '';
		}
		else
		{			
			unset($user['pid']);
			$where = "id=".intval($user_data['id']);
		}
		if($GLOBALS['db']->autoExecute(DB_PREFIX."user",$user,$mode,$where))
		{
			if($mode == 'INSERT' && ($user_data["user_type"] == 0 ||  $user_data["user_type"] == 1))
			{
				$user_id = $GLOBALS['db']->insert_id();	
				$register_money = doubleval(app_conf("USER_REGISTER_MONEY"));
				$register_score = intval(app_conf("USER_REGISTER_SCORE"));
				$register_point = intval(app_conf("USER_REGISTER_POINT"));
				$register_lock_money = intval(app_conf("USER_LOCK_MONEY"));
				if($register_money>0||$register_score>0 || $register_point > 0 || $register_lock_money>0)
				{
					$user_get['score'] = $register_score;
					$user_get['money'] = $register_money;
					$user_get['point'] = $register_point;
					$user_get['lock_money'] = $register_lock_money;
					modify_account($user_get,intval($user_id),"在".$date_time."注册成功",18);
				}
			}
			else
			{
				$user_id = $user_data['id'];
			}
		}
		$res['data'] = $user_id;
		
		
			
		//开始更新处理扩展字段
		if($mode == 'INSERT')
		{
			foreach($user_field as $field_item)
			{
				$extend = array();
				$extend['user_id'] = $user_id;
				$extend['field_id'] = $field_item['id'];
				$extend['value'] = $user_data[$field_item['field_name']];
				$GLOBALS['db']->autoExecute(DB_PREFIX."user_extend",$extend,$mode);
			}
					
		}
		else
		{
			foreach($user_field as $field_item)
			{
				$extend = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_extend where user_id=".$user_id." and field_id =".$field_item['id']);
				if($extend)
				{
					$extend['value'] = $user_data[$field_item['field_name']];
					$where = 'id='.$extend['id'];
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_extend",$extend,$mode,$where);
				}
				else
				{
					$extend = array();
					$extend['user_id'] = $user_id;
					$extend['field_id'] = $field_item['id'];
					$extend['value'] = $user_data[$field_item['field_name']];
					$GLOBALS['db']->autoExecute(DB_PREFIX."user_extend",$extend,"INSERT");
				}
				
			}
		}
		return $res;
	}

	/**
	 * 删除会员以及相关数据
	 * @param integer $id
	 */
	function delete_user($id)
	{
		
		$result = 1;
		//载入会员整合
		$integrate_code = trim(app_conf("INTEGRATE_CODE"));
		if($integrate_code!='')
		{
			$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
			if(file_exists($integrate_file))
			{
				require_once $integrate_file;
				$integrate_class = $integrate_code."_integrate";
				$integrate_obj = new $integrate_class;
			}	
		}
		if($integrate_obj)
		{
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$id);
			$result = $integrate_obj->delete_user($user_info);					
		}
		
		if($result>0)
		{
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user where id =".$id); //删除会员
			
			//以上数据不删除，只更新字段内容
			$GLOBALS['db']->query("update ".DB_PREFIX."user set pid = 0 where pid = ".$id); //更新推荐人数据为0
			$GLOBALS['db']->query("update ".DB_PREFIX."referrals set rel_user_id = 0 where rel_user_id=".$id);  //更新返利记录的推荐人为0
			$GLOBALS['db']->query("update ".DB_PREFIX."payment_notice set user_id = 0 where user_id=".$id);    //收款单
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set user_id= 0 where user_id=".$id);  //订单 
 
			
			//开始删除关联数据
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_auth where user_id=".$id);  //权限
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_extend where user_id=".$id);  //扩展字段
			$GLOBALS['db']->query("delete from ".DB_PREFIX."promote_msg_list where user_id=".$id);  //推广队列
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_msg_list where user_id=".$id);  //业务队列
			$GLOBALS['db']->query("delete from ".DB_PREFIX."msg_conf where user_id=".$id);//通知配置
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_carry where user_id=".$id); //提现
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_credit_file where user_id=".$id); //认证
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_autobid where user_id=".$id); //自动投标
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_work where user_id=".$id); //工作信息
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_company where user_id=".$id); //工作信息
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_sta where user_id=".$id); //用户统计
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_bank where user_id=".$id); //工作信息
			
			//删除会员相关的关注
			//取出被删除会员ID关注的会员IDS,即我的关注
			$focus_user_ids = $GLOBALS['db']->getOne("select group_concat(focused_user_id) from ".DB_PREFIX."user_focus where focus_user_id = ".$id);
			if($focus_user_ids)
			$GLOBALS['db']->query("update ".DB_PREFIX."user set focused_count = focused_count - 1 where id in (".$focus_user_ids.")"); //减去相应会员的被关注数，即粉丝数
			
			
			//关注我的粉丝ID
			$fans_user_ids = $GLOBALS['db']->getOne("select group_concat(focus_user_id) from ".DB_PREFIX."user_focus where focused_user_id = ".$id);
			if($fans_user_ids)
			$GLOBALS['db']->query("update ".DB_PREFIX."user set focus_count = focus_count - 1 where id in (".$fans_user_ids.")"); //减去相应会员的关注数
			
			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."user_focus where focus_user_id = ".$id." or focused_user_id = ".$id);
			
		}
	}

	/**
	 * 会员资金积分变化操作函数
	 * @param array $data 包括 score,money,point
	 * @param integer $user_id
	 * @param string $log_msg 日志内容
	 * @param integer $type  0结存，1充值，2投标成功，3招标成功，4偿还本息，5回收本息，6提前还款，7提前回收，8申请提现，9提现手续费，10借款管理费，11逾期罚息，12逾期管理费，13人工充值，14借款服务费，15出售债权，16购买债权，17债权转让管理费，18开户奖励，19流标还返，20投标管理费，21投标逾期收入，22兑换，23邀请返利，24投标返利，26逾期罚金（垫付后），27其他费用 ，28投资奖励，29红包奖励
	 */
	function modify_account($data,$user_id,$log_msg='',$type=0)
	{
		if(intval($data['score'])!=0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set score = score + ".intval($data['score'])." where id =".$user_id);
		}
		if(intval($data['point'])!=0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set point = point + ".intval($data['point'])." where id =".$user_id);
		}
		if(floatval($data['money'])!=0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set money = money + ".floatval($data['money'])." where id =".$user_id);
		}
		
		if(floatval($data['quota'])!=0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set quota = quota + ".floatval($data['quota'])." where id =".$user_id);
		}
		
		if(floatval($data['lock_money'])!=0)
		{
			$GLOBALS['db']->query("update ".DB_PREFIX."user set lock_money = lock_money + ".floatval($data['lock_money'])." where id =".$user_id);
		}
		
		//不可提现的金额
		if(floatval($data['nmc_amount'])!=0){
			$GLOBALS['db']->query("update ".DB_PREFIX."user set nmc_amount = nmc_amount + ".floatval($data['nmc_amount'])." where id =".$user_id);
		}
		
		
		if(intval($data['score'])!=0||floatval($data['money'])!=0||intval($data['point'])!=0||floatval($data['quota'])!=0 || floatval($data['lock_money']) != 0)
		{		
			$log_info['log_info'] = $log_msg;
			$log_info['log_time'] = TIME_UTC;
			$adm_session = es_session::get(md5(app_conf("AUTH_KEY")));
			$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".$user_id);
			$adm_id = intval($adm_session['adm_id']);
			if($adm_id!=0)
			{
				$log_info['log_admin_id'] = $adm_id;
			}
			else
			{
				$log_info['log_user_id'] = intval($user_info['id']);
			}
			$log_info['money'] = floatval($data['money']);
			$log_info['score'] = intval($data['score']);
			$log_info['point'] = intval($data['point']);
			$log_info['quota'] = floatval($data['quota']);
			$log_info['lock_money'] = floatval($data['lock_money']);
			$log_info['user_id'] = $user_id;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_log",$log_info);
			
		}
		
		if(isset($data['money']) || isset($data['fee_amount'])){
			$money_log_info['memo'] = $log_msg;
			$money_log_info['money'] = floatval($data['money']);
			$money_log_info['account_money'] = $GLOBALS['db']->getOne("SELECT money FROM ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".$user_id);
			$money_log_info['user_id'] = $user_id;
			$money_log_info['create_time'] = TIME_UTC;
			$money_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
			$money_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
			$money_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
			$money_log_info['type'] = $type;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_money_log",$money_log_info);
			
			//网站收益表  跟会员的刚好相反
			$is_add = false;
			switch((int)$type){ 
				//case 7 : //提前回收 + 
				case 1 : //充值手续费 +
					$is_add = true;
					if($data['fee_amount']==0){
						$is_add = false;
					}
					else{
						$site_money = floatval($data['fee_amount']);
						$money_log_info['money'] = $site_money;
					}
					break;
				case 9 : //提现手续费 +
				case 10 : //借款管理费 +
				case 12 : //逾期管理费 +
				case 13 : //人工充值
				case 14 : //借款服务费 +
				case 17 : //债权转让管理费  +
				case 18 : //开户奖励   -
				case 20 : //投标管理费 +
				case 22 : //兑换  
				case 23 : //邀请返利 -
				case 24 : //投标返利 -
				case 25 : //签到成功 -
				case 26 : //逾期罚金（垫付后）
				case 27 : //其他费用
				case 28 : //投资奖励
				case 29 : //红包奖励
					$is_add = true;
					$site_money = floatval($data['money']);
					$money_log_info['money'] = -$site_money;
					break;
			}
			if($is_add == true){
				$GLOBALS['db']->autoExecute(DB_PREFIX."site_money_log",$money_log_info);
			}
		}
		
		if(isset($data['score'])){
			$score_log_info['memo'] = $log_msg;
			$score_log_info['score'] = floatval($data['score']);
			$score_log_info['account_score'] = $GLOBALS['db']->getOne("SELECT score FROM ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".$user_id);
			$score_log_info['user_id'] = $user_id;
			$score_log_info['create_time'] = TIME_UTC;
			$score_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
			$score_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
			$score_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
			$score_log_info['type'] = $type;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_score_log",$score_log_info);
			
		}
		
		if(isset($data['lock_money'])){
			$money_log_info['memo'] = $log_msg;
			$money_log_info['lock_money'] = floatval($data['lock_money']);
			$money_log_info['account_lock_money'] = $GLOBALS['db']->getOne("SELECT lock_money FROM ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".$user_id);
			$money_log_info['user_id'] = $user_id;
			$money_log_info['create_time'] = TIME_UTC;
			$money_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
			$money_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
			$money_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
			$money_log_info['type'] = $type;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_lock_money_log",$money_log_info);
			
		}
		
		if(isset($data['point'])){
			$point_log_info['memo'] = $log_msg;
			$point_log_info['point'] = floatval($data['point']);
			$point_log_info['account_point'] = $GLOBALS['db']->getOne("SELECT point FROM ".DB_PREFIX."user where is_delete = 0 and is_effect = 1 and id = ".$user_id);
			$point_log_info['user_id'] = $user_id;
			$point_log_info['create_time'] = TIME_UTC;
			$point_log_info['create_time_ymd'] = to_date(TIME_UTC,"Y-m-d");
			$point_log_info['create_time_ym'] = to_date(TIME_UTC,"Ym");
			$point_log_info['create_time_y'] = to_date(TIME_UTC,"Y");
			$point_log_info['type'] = $type;
			$GLOBALS['db']->autoExecute(DB_PREFIX."user_point_log",$point_log_info);
		}
		
	}

	/**
	 * 处理cookie的自动登录
	 * @param $user_name_or_email  用户名或邮箱
	 * @param $user_md5_pwd  md5加密过的密码
	 */
	function auto_do_login_user($user_name_or_email,$user_md5_pwd)
	{
		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where (user_name='".$user_name_or_email."' or email = '".$user_name_or_email."' or mobile = '".$user_name_or_email."') and is_delete = 0");
	
		if($user_data)
		{
			if(md5($user_data['user_pwd']."_EASE_COOKIE")==$user_md5_pwd)
			{
				//成功
				/*
				//登录成功自动检测关于会员等级
				$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
				$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
				if($user_current_group['score']<$user_group['score']&& $user_data['group_id']!=$user_group['id']&& $user_group['id'] > 0)
				{
					$user_data['group_id'] = intval($user_group['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$user_data['group_id']." where id = ".$user_data['id']);
					$pm_title = "您已经成为".$user_group['name']."";
					$pm_content = "恭喜您，您已经成为".$user_group['name']."。";
					if($user_group['discount']<1)
					{
						$pm_content.="您将享有".($user_group['discount']*10)."折的购物优惠";
					}
					send_user_msg($pm_title,$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);					
				}*/

				$user_current_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($user_data['level_id']));
				$user_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where point <=".intval($user_data['point'])." order by point desc");
				if($user_current_level['point']<$user_level['point'] && $user_data['level_id']!=$user_level['id'] && $user_level['id'] > 0)
				{
					$user_data['level_id'] = intval($user_level['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$user_data['level_id']." where id = ".$user_data['id']);
					
					/*
					$pm_title = "您已经成为".$user_level['name']."";
					$pm_content = "恭喜您，您已经成为".$user_level['name']."。";	*/
					$notice['level_name']=$user_level['name'];
						
					$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_LEVEL_ADD'",false);
					$GLOBALS['tmpl']->assign("notice",$notice);
					$pm_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
					send_user_msg($tmpl_content['name'],$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);
					
				}
				
				if($user_current_level['point']>$user_level['point']&& $user_data['level_id']!=$user_level['id'] && $user_level['id'] > 0)
				{
					$user_data['level_id'] = intval($user_level['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$user_data['level_id']." where id = ".$user_data['id']);
					
					/*
					$pm_title = "您已经降为".$user_level['name']."";
					$pm_content = "很报歉，您已经降为".$user_level['name']."。";	*/
					
					$notice['level_name']=$user_level['name'];
					
					$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_LEVEL_DEL'",false);
					$GLOBALS['tmpl']->assign("notice",$notice);
					$pm_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
					
					send_user_msg($tmpl_content['name'],$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);
				}
				
				es_session::set("user_info",$user_data);
				$GLOBALS['user_info'] = $user_data;
				/*//检测勋章
				$medal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."medal where is_effect = 1 and allow_check = 1");
				foreach($medal_list as $medal)
				{
					$file = APP_ROOT_PATH."system/medal/".$medal['class_name']."_medal.php";
					$cls = $medal['class_name']."_medal";					
					if(file_exists($file))
					{
						require_once $file;
						if(class_exists($cls))
						{
							$o = new $cls;
							$check_result = $o->check_medal();
							if($check_result['status']==0)
							{
								send_user_msg($check_result['info'],$check_result['info'],0,$user_data['id'],TIME_UTC,0,true,true);
							}
						}
					}
				}
				$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);
				*/				
			}
		}
	}
	/**
	 * 处理会员登录
	 * @param $user_name_or_email 用户名或邮箱地址
	 * @param $user_pwd 密码
	 * 
	 */
	function do_login_user($user_name_or_email,$user_pwd)
	{

		$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where (user_name='".$user_name_or_email."' or email = '".$user_name_or_email."' or mobile = '".$user_name_or_email."') and is_delete = 0");

		if($user_data['user_type']==0|| $user_data['user_type']==1){
			//载入会员整合
			$integrate_code = trim(app_conf("INTEGRATE_CODE"));
			if($integrate_code!='')
			{
				$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
				if(file_exists($integrate_file))
				{
					require_once $integrate_file;
					$integrate_class = $integrate_code."_integrate";
					$integrate_obj = new $integrate_class;
				}	
			}
			if($integrate_obj)
			{			
				$result = $integrate_obj->login($user_name_or_email,$user_pwd);	
								
			}
			
			//引入时区配置及定义时间函数
			if(function_exists('date_default_timezone_set'))
				date_default_timezone_set(app_conf('DEFAULT_TIMEZONE'));
		
		
			$user_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where (user_name='".$user_name_or_email."' or email = '".$user_name_or_email."' or mobile = '".$user_name_or_email."') and is_delete = 0");
		}	

		if(!$user_data)
		{			
			$result['status'] = 0;
			$result['data'] = ACCOUNT_NO_EXIST_ERROR;
			return $result;
		}
		else
		{
			$result['user'] = $user_data;
			
			if(($user_data['user_type']==0|| $user_data['user_type']==1)&&$user_data['is_effect'] != 1)
			{
				$result['status'] = 0;
				$result['data'] = ACCOUNT_NO_VERIFY_ERROR;
				return $result;
			}			
			
			$is_use_pass = false;
			
			if(strlen($user_pwd) == 32 && $user_data['user_pwd'] == $user_pwd)
			{		
				$is_use_pass = true;								
			}else if($user_data['user_pwd'] == md5($user_pwd.$user_data['code']))
			{
				$is_use_pass = true;
			}
						
			if($is_use_pass) //未整合，则直接成功
			{
				$result['status'] = 1;
				
				if($user_data['user_type']==0|| $user_data['user_type']==1){
					/*
					$user_current_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where id = ".intval($user_data['group_id']));
					$user_group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_group where score <=".intval($user_data['score'])." order by score desc");
					if($user_current_group['score']<$user_group['score'] && $user_data['group_id']!=$user_group['id']&& $user_group['id'] > 0)
					{
						$user_data['group_id'] = intval($user_group['id']);
						$GLOBALS['db']->query("update ".DB_PREFIX."user set group_id = ".$user_data['group_id']." where id = ".$user_data['id']);
						$pm_title = "您已经成为".$user_group['name']."";
						$pm_content = "恭喜您，您的会有组升级为".$user_group['name']."。";
						if($user_group['discount']<1)
						{
							$pm_content.="您将享有".($user_group['discount']*10)."折的购物优惠";
						}
						send_user_msg($pm_title,$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);	
					}
					
					*/
					
					$user_current_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where id = ".intval($user_data['level_id']));
					$user_level = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_level where point <=".intval($user_data['point'])." order by point desc");
					if($user_current_level['point']<=$user_level['point']&& $user_data['level_id']!=$user_level['id'] && $user_level['id'] > 0)
					{
						$user_data['level_id'] = intval($user_level['id']);
						$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$user_data['level_id']." where id = ".$user_data['id']);					
						
						/*
						$pm_title = "您信用等级升级为：".$user_level['name']."";
						$pm_content = "恭喜您，您的信用等级升级到".$user_level['name']."。";	*/
						
						$notice['level_name']=$user_level['name'];
						
						$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_XY_LEVEL_ADD'",false);
						$GLOBALS['tmpl']->assign("notice",$notice);
						$pm_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
						send_user_msg($tmpl_content['name'],$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);
					}
					
					if($user_current_level['point']>$user_level['point'] && $user_data['level_id']!=$user_level['id'] && $user_level['id'] > 0)
					{
						$user_data['level_id'] = intval($user_level['id']);
						$GLOBALS['db']->query("update ".DB_PREFIX."user set level_id = ".$user_data['level_id']." where id = ".$user_data['id']);
						
						/*$pm_title = "您已经降为".$user_level['name']."";
						$pm_content = "很报歉，您的信用等级降为".$user_level['name']."。";	*/
						$notice['level_name']=$user_level['name'];
						$tmpl_content = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."msg_template where name = 'TPL_XY_LEVEL_DEL'",false);
						$GLOBALS['tmpl']->assign("notice",$notice);
						$pm_content = $GLOBALS['tmpl']->fetch("str:".$tmpl_content['content']);
						send_user_msg($tmpl_content['name'],$pm_content,0,$user_data['id'],TIME_UTC,0,true,true);
					}
				
					es_session::set("user_info",$user_data);
				
					$GLOBALS['user_info'] = $user_data;
					/*
					//检测勋章
					$medal_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."medal where is_effect = 1 and allow_check = 1");
					foreach($medal_list as $medal)
					{
						$file = APP_ROOT_PATH."system/medal/".$medal['class_name']."_medal.php";
						$cls = $medal['class_name']."_medal";					
						if(file_exists($file))
						{
							require_once $file;
							if(class_exists($cls))
							{
								$o = new $cls;
								$check_result = $o->check_medal();
								if($check_result['status']==0)
								{
									send_user_msg($check_result['info'],$check_result['info'],0,$user_data['id'],TIME_UTC,0,true,true);
								}
							}
						}
					}
					*/
					
					$GLOBALS['db']->query("update ".DB_PREFIX."user set locate_time=login_time where id =".$user_data['id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set login_ip = '".CLIENT_IP."',login_time= ".TIME_UTC.",group_id=".intval($user_data['group_id'])." where id =".$user_data['id']);				
					
					$s_api_user_info = es_session::get("api_user_info");
					
					if($s_api_user_info)
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."user set ".$s_api_user_info['field']." = '".$s_api_user_info['id']."' where id = ".$user_data['id']." and (".$s_api_user_info['field']." = 0 or ".$s_api_user_info['field']."='')");
						es_session::delete("api_user_info");
					}
					
					$result['step'] = intval($user_data["step"]);
				}
				elseif($user_data['user_type']==3){
					es_session::set("authorized_info",$user_data);
				}
				elseif($user_data['user_type']==2){
					es_session::set("manageagency_info",$user_data);
				}
				
				return $result;
			}else{
				$result['status'] = 0;
				$result['data'] = ACCOUNT_PASSWORD_ERROR;
				return $result;				
			}
		}
	}
	
	/**
	 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
	 */
	function loginout_user()
	{
		$user_info = es_session::get("user_info");
		if(!$user_info)
		{
			return false;
		}
		else
		{
			//载入会员整合
			$integrate_code = trim(app_conf("INTEGRATE_CODE"));
			if($integrate_code!='')
			{
				$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
				if(file_exists($integrate_file))
				{
					require_once $integrate_file;
					$integrate_class = $integrate_code."_integrate";
					$integrate_obj = new $integrate_class;
				}	
			}
			if($integrate_obj)
			{
				$result = $integrate_obj->logout();					
			}
			if(intval($result['status'])==0)	
			{
				$result['status'] = 1;
			}
						
			es_session::delete("user_info");
			return $result;
		}
	}
	/**
		授权管理机构登出
	**/
	function loginout_authorized()
	{
		$user_info = es_session::get("authorized_info");
		if(!$user_info)
		{
			return false;
		}
		else
		{
			es_session::delete("authorized_info");
		}
	}
	/**
	 * 登出,返回 array('status'=>'',data=>'',msg=>'') msg存放整合接口返回的字符串
	 */
	function loginout_manageagency()
	{
		$user_info = es_session::get("manageagency_info");
		if(!$user_info)
		{
			return false;
		}
		else
		{
			//载入会员整合
			$integrate_code = trim(app_conf("INTEGRATE_CODE"));
			if($integrate_code!='')
			{
				$integrate_file = APP_ROOT_PATH."system/integrate/".$integrate_code."_integrate.php";
				if(file_exists($integrate_file))
				{
					require_once $integrate_file;
					$integrate_class = $integrate_code."_integrate";
					$integrate_obj = new $integrate_class;
				}	
			}
			if($integrate_obj)
			{
				$result = $integrate_obj->logout();					
			}
			if(intval($result['status'])==0)	
			{
				$result['status'] = 1;
			}
						
			es_session::delete("manageagency_info");
			return $result;
		}
	}
	
	
	
	/**
	 * 验证会员数据
	 */
	function check_user($field_name,$field_data)
	{		
		//开始数据验证
		$user_data[$field_name] = $field_data;
		$res = array('status'=>1,'info'=>'','data'=>''); //用于返回的数据
		if(trim($user_data['user_name'])==''&&$field_name=='user_name')
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='user_name'&&(!preg_match("/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\']+$/",trim($user_data['user_name'])) || is_numeric($user_data['user_name'])))
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='user_name'&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where (user_name = '".trim($user_data['user_name'])."' or mobile = '".trim($user_data['user_name'])."') and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'user_name';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		
		
		if($field_name=='email'&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where email = '".trim($user_data['email'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='email'&&trim($user_data['email'])=='')
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='email'&&!check_email(trim($user_data['email'])))
		{
			$field_item['field_name'] = 'email';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='mobile'&&intval(app_conf("MOBILE_MUST"))==1&&trim($user_data['mobile'])=='')
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EMPTY_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		
		if($field_name=='mobile'&&!check_mobile(trim($user_data['mobile'])))
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	FORMAT_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		if($field_name=='mobile'&&$user_data['mobile']!=''&&$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."user where mobile = '".trim($user_data['mobile'])."' and id <> ".intval($user_data['id']))>0)
		{
			$field_item['field_name'] = 'mobile';
			$field_item['error']	=	EXIST_ERROR;
			$res['status'] = 0;
			$res['data'] = $field_item;
			return $res;
		}
		//验证扩展字段
		$field_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user_field where field_name = '".$field_name."'");
	
		if($field_item['is_must']==1&&trim($user_data[$field_item['field_name']])=='')
		{
				$field_item['error']	=	EMPTY_ERROR;
				$res['status'] = 0;
				$res['data'] = $field_item;
				return $res;
		}
		
		
		
		return $res;
	}


?>