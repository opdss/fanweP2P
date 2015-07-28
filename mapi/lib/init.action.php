<?php
class init{
	public function index()
	{		
		$root = array();
		$root['response_code'] = 1;
		
		//print_r($GLOBALS['db_conf']); exit;

		$root['kf_phone'] = $GLOBALS['m_config']['kf_phone'];//客服电话
		$root['kf_email'] = $GLOBALS['m_config']['kf_email'];//客服邮箱
		
		//$pattern = "/<img([^>]*)\/>/i";
		//$replacement = "<img width=300 $1 />";
		//$goods['goods_desc'] = preg_replace($pattern, $replacement, get_abs_img_root($goods['goods_desc']));
		//关于我们(填文章ID)
		$root['about_info'] = intval($GLOBALS['m_config']['about_info']);
		
		
		
		$root['version'] = VERSION; //接口版本号int
		$root['page_size'] = PAGE_SIZE;//默认分页大小
		$root['program_title'] = $GLOBALS['m_config']['program_title'];
		$root['site_domain'] = str_replace("/mapi", "", SITE_DOMAIN.APP_ROOT);//站点域名;
		$root['site_domain'] = str_replace("http://", "", $root['site_domain']);//站点域名;
		$root['site_domain'] = str_replace("https://", "", $root['site_domain']);//站点域名;
		//$root['newslist'] = $GLOBALS['m_config']['newslist'];
		
		//累计投资金额
		$stats['total_load'] = $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."deal_load where is_repay= 0 ");
		$stats['total_load_format'] = format_conf_count(number_format($stats['total_load'],2));
		//成交笔数
		$stats['deal_total_count'] = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal where  deal_status >=4 ");
		//累计创造收益
		$stats['total_rate'] = $GLOBALS['db']->getOne("SELECT sum(true_interest_money + impose_money + true_reward_money - true_manage_money - true_manage_interest_money) FROM ".DB_PREFIX."deal_load_repay where  has_repay = 1 ");
		$stats['total_rate'] += $GLOBALS['db']->getOne("SELECT sum(rebate_money) FROM ".DB_PREFIX."deal_load where  is_has_loans = 1 ");//加上返利
		$stats['total_rate'] -= $GLOBALS['db']->getOne("SELECT sum(fee_amount) FROM ".DB_PREFIX."payment_notice WHERE  is_paid =1  "); //减去充值手续费
		$stats['total_rate'] -= $GLOBALS['db']->getOne("SELECT sum(fee) FROM ".DB_PREFIX."user_carry WHERE status =1  "); //减去提现手续费
		$stats['total_rate'] += $GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."referrals WHERE pay_time >0  "); //加上邀请返利
		$stats['total_rate_format'] = format_conf_count(number_format($stats['total_rate'],2));
		//本息保证金（元）
		$stats['total_bzh'] = $GLOBALS['db']->getOne("SELECT sum(guarantor_real_freezen_amt+real_freezen_amt) FROM ".DB_PREFIX."deal where deal_status= 4 ");
		$stats['total_bzh_format'] = format_conf_count(number_format($stats['total_bzh'],2));
		//待收资金（元）
		$stats['total_repay'] = $GLOBALS['db']->getOne("SELECT sum(repay_money) FROM ".DB_PREFIX."deal_load_repay where has_repay = 1 ");
		$stats['total_repay_format'] = format_conf_count(number_format($stats['total_repay'],2));
		//待投资金（元）
		$statsU = $GLOBALS['db']->getRow("SELECT sum(money) as total_usermoney ,count(*) total_user FROM ".DB_PREFIX."user where is_effect = 1 and is_delete=0 ");
		$stats['total_usermoney'] = $statsU['total_usermoney'];
		$stats['total_usermoney_format'] = format_conf_count(number_format($stats['total_usermoney'],2));
		$stats['total_user'] = $statsU['total_user'];
		$GLOBALS['tmpl']->assign("stats",$stats);
				
		$root['virtual_money_1'] = strip_tags($GLOBALS['db_conf']['VIRTUAL_MONEY_1'] + $stats['total_load']);//虚拟的累计成交额;
		$root['virtual_money_2'] = strip_tags($GLOBALS['db_conf']['VIRTUAL_MONEY_2'] + $stats['total_rate']);//虚拟的累计创造收益;
		$root['virtual_money_3'] = strip_tags($GLOBALS['db_conf']['VIRTUAL_MONEY_3'] + $stats['total_bzh']);//虚拟的本息保障金;
		
		$index_list = $GLOBALS['cache']->get("MOBILE_INDEX_ADVS");
		if(true || $index_list===false)
		{
			$advs = $GLOBALS['db']->getAll(" select * from ".DB_PREFIX."m_adv where status = 1 order by sort desc ");
			$adv_list = array();
			$deal_list = array();
			$condition = "-1";
			foreach($advs as $k=>$v)
			{
				if ($v['page'] == 'top'){
					/*
					$adv_list[]['id'] = $v['id'];
					$adv_list[]['name'] = $v['name'];
					if ($v['page'] == 'top' && $v['img'] != ''){
						$adv_list[]['img'] = get_abs_img_root(get_spec_image($v['img'],640,240,1));
					}else{
						$adv_list[]['img'] = '';
					}
					$adv_list[]['type'] = $v['type'];
					$adv_list[]['open_url_type'] = $v['open_url_type'];
					$adv_list[]['data'] = $v['data'];
					*/
					if ($v['img'] != '')
						$v['img'] = get_abs_img_root(get_spec_image($v['img'],640,240,1));
					$adv_list[] = $v;
				}else{
					/*
					$deal_list[]['id'] = $v['id'];
					$deal_list[]['name'] = $v['name'];					
					$deal_list[]['img'] = '';					
					$deal_list[]['type'] = $v['type'];
					$deal_list[]['open_url_type'] = $v['open_url_type'];
					$deal_list[]['data'] = $v['data'];
					*/
					//$v['img'] = '';
					//$deal_list[] = $v;
					$condition .= ",".intval($v['data']);
				}			
			}
			
			//$condition = " id in (".$condition.")";
			//publish_wait 0:已审核 1:等待审核;deal_status 0待等材料，1进行中，2满标，3流标，4还款中，5已还清
			$condition = " publish_wait = 0 AND deal_status in (1,2,4,5)";
			require APP_ROOT_PATH.'app/Lib/deal.php';
			$limit = "0,5";
			$orderby = "deal_status ASC,sort DESC,id DESC";
			
			//print_r($limit);
			//print_r($condition);
			
			$result = get_deal_list($limit,0,$condition,$orderby);			
			
			$index_list['adv_list'] = $adv_list;
			$index_list['deal_list'] = $result['list'];
			$GLOBALS['cache']->set("MOBILE_INDEX_ADVS",$index_list);
		}
		$root['index_list'] = $index_list;
		
		$root['deal_cate_list'] = getDealCateArray();//分类
		
		if(strim($GLOBALS['m_config']['sina_app_key'])!=""&&strim($GLOBALS['m_config']['sina_app_secret'])!="")
		{
			$root['api_sina'] = 1;
			$root['sina_app_key'] = $GLOBALS['m_config']['sina_app_key'];
			$root['sina_app_secret'] = $GLOBALS['m_config']['sina_app_secret'];
			$root['sina_bind_url'] = $GLOBALS['m_config']['sina_bind_url'];
		}
		if(strim($GLOBALS['m_config']['tencent_app_key'])!=""&&strim($GLOBALS['m_config']['tencent_app_secret'])!="")
		{
			$root['api_tencent'] = 1;
			$root['tencent_app_key'] = $GLOBALS['m_config']['tencent_app_key'];
			$root['tencent_app_secret'] = $GLOBALS['m_config']['tencent_app_secret'];
			$root['tencent_bind_url'] = $GLOBALS['m_config']['tencent_bind_url'];
		}

		output($root);
	}
}

function getDealCateArray(){
	//$land_list = FanweService::instance()->cache->loadCache("land_list");
		
		$sql = "select id, pid, name, icon from ".DB_PREFIX."deal_cate where pid = 0 and is_effect = 1 and is_delete = 0 order by sort desc ";
		//echo $sql; exit;
		$list = $GLOBALS['db']->getAll($sql);

	return $list;
}
?>