<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class transferModule extends SiteBaseModule
{
	public function index(){
		$field = es_cookie::get("shop_sort_field"); 
		$field_sort = es_cookie::get("shop_sort_type"); 
		
		require APP_ROOT_PATH.'app/Lib/page.php';
		$level_list = load_auto_cache("level");
		$GLOBALS['tmpl']->assign("level_list",$level_list['list']);
		if(check_ipop_limit(CLIENT_IP,"transfer_status",10)){
			syn_transfer_status();
		}
		
		if(trim($_REQUEST['cid'])=="last"){
			$cate_id = "-1";
			$page_title = $GLOBALS['lang']['LAST_SUCCESS_DEALS']." - ";
		}
		else{
			$cate_id = intval($_REQUEST['cid']);
		}
		
		if($cate_id == 0){
			$page_title = $GLOBALS['lang']['ALL_TRANSFER']." - ";
		}
		
		$keywords = trim(htmlspecialchars($_REQUEST['keywords']));
		$GLOBALS['tmpl']->assign("keywords",$keywords);
		
		$level = intval($_REQUEST['level']);
		$GLOBALS['tmpl']->assign("level",$level);
		
		$interest = intval($_REQUEST['interest']);
		$GLOBALS['tmpl']->assign("interest",$interest);
		
		$months = intval($_REQUEST['months']);
		$GLOBALS['tmpl']->assign("months",$months);
		
		$months_type = intval($_REQUEST['months_type']);
		$GLOBALS['tmpl']->assign("months_type",$months_type);
		
		$lefttime = intval($_REQUEST['lefttime']);
		$GLOBALS['tmpl']->assign("lefttime",$lefttime);
		
		$city = intval($_REQUEST['city']);
		$GLOBALS['tmpl']->assign("city_id",$city);
			
		$scity = intval($_REQUEST['scity']);
		$GLOBALS['tmpl']->assign("scity_id",$scity);
		
		//输出分类
		$deal_cates_db = load_auto_cache("cache_deal_cate");
		$deal_cates = array();
		foreach($deal_cates_db as $k=>$v)
		{		
			if($cate_id==$v['id']){
				$v['current'] = 1;
				$page_title = $v['name']." - ";
			}
			$v['url'] = url("index","transfer",array("cid"=>$v['id']));
			$deal_cates[] = $v;
		}
		unset($deal_cates_db);
		
		//输出投标列表
		$page = intval($_REQUEST['p']);
		if($page==0)
			$page = 1;
		
		
		$page_args =array();
		$sfield = "";
		switch($field){
			case "borrow_amount":
				$sfield = "dlt.transfer_amount";
				break;
			case "rate":
				$sfield = "d.rate";
				break;
			case "repay_time":
				$sfield = "dlt.last_repay_time";
				break;
			case "remain_time":
				$sfield = "dlt.near_repay_time";
				break;
			default:
				$sfield = "";
		}
		
		$condition = " AND dlt.status=1 ";
		if($cate_id > 0){
			$condition .= "AND d.deal_status >=4 and cate_id=".$cate_id;
			
			if($sfield && $field_sort)
				$orderby = "$sfield $field_sort ,d.deal_status desc , d.sort DESC,d.id DESC";
			else
				$orderby = "d.update_time DESC ,d.sort DESC,d.id DESC";
		}
		elseif ($cate_id == 0){
			
			
			if($sfield && $field_sort)
				$orderby = "$sfield $field_sort, dlt.create_time DESC , dlt.id DESC ";
			else
				$orderby = " d.create_time DESC , dlt.id DESC";
			
		}
		elseif ($cate_id == "-1"){
			$condition .= "AND d.deal_status >=4 AND dlt.t_user_id > 0 ";
			$orderby = "dlt.transfer_time DESC,d.create_time DESC , dlt.id DESC";
		}
		
		if($keywords){
			$kw_unicode = str_to_unicode_string($keywords);
			$condition .=" and (match(d.name_match,d.deal_cate_match,d.tag_match,d.type_match) against('".$kw_unicode."' IN BOOLEAN MODE))";			
		}
		
		if($level > 0){
			$point  = $level_list['point'][$level];
			$condition .= " AND d.user_id in(SELECT u.id FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level ul ON ul.id=u.level_id WHERE ul.point >= $point)";
		}
		
		if($interest > 0){
			$condition .= " AND d.rate >= ".$interest;
		}
		
		if($months > 0){
			if($months==12)
				$condition .= " AND d.repay_time <= ".$months;
			elseif($months==18)
				$condition .= " AND d.repay_time >= ".$months;
		}
		
		if ($months_type > 0){
			if ($months_type == 1)
				$condition .= " AND ((d.repay_time < 3 and d.repay_time_type = 1) or d.repay_time_type = 0) ";
			else if ($months_type == 2)
				$condition .= " AND d.repay_time in (3,4,5)  and d.repay_time_type = 1 ";
			else if ($months_type == 3)
				$condition .= " AND d.repay_time in (6,7,8)  and d.repay_time_type = 1 ";
			else if ($months_type == 4)
				$condition .= " AND d.repay_time in (9,10,11)  and d.repay_time_type = 1 ";
			else
				$condition .= " AND d.repay_time >= 12  and d.repay_time_type = 1 ";
		}
		
		if ($city > 0){
			if($scity > 0){
				$dealid_list = $GLOBALS['db']->getAll("SELECT deal_id FROM ".DB_PREFIX."deal_city_link where city_id = ".$scity);
			}
			else{
				$dealid_list = $GLOBALS['db']->getAll("SELECT deal_id FROM ".DB_PREFIX."deal_city_link where city_id = ".$city);
			}
		
			$flatmap = array_map("array_pop",$dealid_list);
			$s2=implode(',',$flatmap);
			$condition .= " AND id in (".$s2.") ";
		}
		
		
		if($lefttime > 0){
			$condition .= " AND (d.next_repay_time + 24*3600 - 1 - ".TIME_UTC.") <= ".($lefttime*24*3600)." AND dlt.t_user_id = 0 ";
		}
		
		if(es_cookie::get("shop_sort_field")=="ulevel"){
			$union_sql = ' LEFT join '.DB_PREFIX.'user u ON d.user_id = u.id ';
			$extfield = ",u.level_id ";
		}
		
		$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
		
		$result = get_transfer_list($limit,$condition,$extfield,$union_sql,$orderby);
		
		if($result['rs_count'] > 0){
			
			$page_args['cid'] =  $cate_id;
			$page_args['keywords'] =  $keywords;
			$page_args['level'] =  $level;
			$page_args['interest'] =  $interest;
			$page_args['months'] =  $months;
			$page_args['lefttime'] =  $lefttime;
			
			$page_args['months_type'] =  $months_type;
			$page_args['city'] =  $city;
		
			$page_pram = "";
			foreach($page_args as $k=>$v){
				$page_pram .="&".$k."=".$v;
			}
						
			$page = new Page($result['rs_count'],app_conf("DEAL_PAGE_SIZE"),$page_pram);   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign('transfer_list',$result['list']);
		}
		
		
		
		//分类
		$cate_list_url = array();
		$tmp_args = $page_args;
		$tmp_args['cid'] = 0;
		$cate_list_url[0]['url'] = url("index","transfer#index",$tmp_args);
		$cate_list_url[0]['name'] = "不限";
		$cate_list_url[0]['id'] = 0;
		foreach($deal_cates as $k=>$v){
			$cate_list_url[$k+1] = $v;
			$tmp_args = $page_args;
			$tmp_args['cid'] = $v['id'];
			$cate_list_url[$k+1]['url'] = url("index","transfer#index",$tmp_args);
		}
		
		$GLOBALS['tmpl']->assign('cate_list_url',$cate_list_url);
		
		//利率
		$interest_url = array(
			array(
				"interest"=>0,
				"name" => "不限",
			),
			array(
				"interest"=>10,
				"name" => "10%",
			),
			array(
				"interest"=>12,
				"name" => "12%",
			),
			array(
				"interest"=>15,
				"name" => "15%",
			),
			array(
				"interest"=>18,
				"name" => "18",
			),
		);
		
		foreach($interest_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['interest'] = $v['interest'];
			$interest_url[$k]['url'] = url("index","transfer#index",$tmp_args);
		}
		$GLOBALS['tmpl']->assign('interest_url',$interest_url);
		
		//几天内
		$lefttime_url = array(
			array(
				"lefttime"=>0,
				"name" => "不限",
			),
			array(
				"lefttime"=>1,
				"name" => "1天",
			),
			array(
				"lefttime"=>3,
				"name" => "3天",
			),
			array(
				"lefttime"=>6,
				"name" => "6天",
			),
			array(
				"lefttime"=>9,
				"name" => "9天",
			),
			array(
				"lefttime"=>12,
				"name" => "12天",
			),
		);
		
		foreach($lefttime_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['lefttime'] = $v['lefttime'];
			$lefttime_url[$k]['url'] = url("index","transfer#index",$tmp_args);
		}
		$GLOBALS['tmpl']->assign('lefttime_url',$lefttime_url);
		
		//借款期限
		$months_type_url = array(
					array(
							"name" => "不限",
					),
					array(
							"name" => "3 个月以下",
						),
					array(
							"name" => "3-6 个月",
					),
					array(
							"name" => "6-9 个月",
					),
					array(
							"name" => "9-12 个月",
					),
					array(
						"name" => "12 个月以上",
					),
				);
	
		foreach($months_type_url as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['months_type'] = $k;
			$months_type_url[$k]['url'] = url("index","transfer#index",$tmp_args);
		}
	
		$GLOBALS['tmpl']->assign('months_type_url',$months_type_url);
		
		

		//城市
		$temp_city_urls =load_auto_cache("deal_city");
			
		$city_urls[0]['id'] = 0;
		$city_urls[0]['name'] = "全部";
		if(count($temp_city_urls) == 1){
			$temp_city_urls = $temp_city_urls[key($temp_city_urls)]['child'];
		}
	
		$temp_city_urls = array_merge($city_urls,$temp_city_urls);
		
		$city_urls = array();
		foreach($temp_city_urls as $k=>$v){
			$city_urls[$v['id']] = $v;
			$tmp_args = $page_args;
			$tmp_args['city'] = $v['id'];
			$city_urls[$v['id']]['url'] = url("index","transfer#index",$tmp_args);
		}
		
		$GLOBALS['tmpl']->assign('city_urls',$city_urls);
			
		$sub_citys = $city_urls[$city]['child'];
		foreach($sub_citys as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['city'] = $v['pid'];
			$tmp_args['scity'] = $v['id'];
			$sub_citys[$k]['url'] = url("index","transfer#index",$tmp_args);
		}
			
		$GLOBALS['tmpl']->assign('sub_citys',$sub_citys);
		
		
		
		//使用技巧
		$use_tech_list  = get_article_list(4,6);
		$GLOBALS['tmpl']->assign("use_tech_list",$use_tech_list);
		
		//输出公告
		$notice_list = get_notice(3);
		$GLOBALS['tmpl']->assign("notice_list",$notice_list);
		
		//会员等级
		$level_list_url = array();
		$tmp_args = $page_args;
		$tmp_args['level'] = 0;
		$level_list_url[0]['url'] = url("index","transfer#index",$tmp_args);
		$level_list_url[0]['name'] = "不限";
		foreach($level_list['list'] as $k=>$v){
			$tmp_args = $page_args;
			$tmp_args['level'] = $v['id'];
			$level_list_url[$k+1] = $v;
			$level_list_url[$k+1]['url'] = url("index","transfer#index",$tmp_args);
		}
		$GLOBALS['tmpl']->assign('level_list_url',$level_list_url);
		
		$GLOBALS['tmpl']->assign("page_title",$page_title . $GLOBALS['lang']['FINANCIAL_MANAGEMENT']);
				
		$GLOBALS['tmpl']->assign("cate_id",$cate_id);
		$GLOBALS['tmpl']->assign("cid",strim($_REQUEST['cid']));
		$GLOBALS['tmpl']->assign("keywords",$keywords);
		$GLOBALS['tmpl']->assign("deal_cate_list",$deal_cates);
		$GLOBALS['tmpl']->assign("field",$field); 
		$GLOBALS['tmpl']->assign("field_sort",$field_sort); 
		
		$GLOBALS['tmpl']->display("page/transfers.html");
	}
	
	public function detail(){
		/*if(!$GLOBALS['user_info']){
			
			app_redirect(url("index","user#login")); 
		}*/
		set_gopreview();
		$id = intval($_REQUEST['id']);
		
		$deal_id = $GLOBALS['db']->getOne("SELECT deal_id FROM ".DB_PREFIX."deal_load_transfer WHERE id=".$id);
		if($deal_id==0){
			echo "不存在的债权"; die();
		}
		
		$deal = get_deal($deal_id);
		
		syn_transfer_status($id);
		
		$deal['yq_count'] =  $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."deal_repay WHERE has_repay = 1 and deal_id=".$deal_id." AND status >= 2");
		$GLOBALS['tmpl']->assign('deal',$deal);
		
		//借款列表
		$load_list = $GLOBALS['db']->getAll("SELECT deal_id,user_id,user_name,money,is_auto,create_time FROM ".DB_PREFIX."deal_load WHERE deal_id = ".$deal_id);
		
		$u_info = get_user("*",$deal['user_id']);
		
		if($deal['view_info']!=""){
			$view_info_list = unserialize($deal['view_info']);
			$GLOBALS['tmpl']->assign('view_info_list',$view_info_list);
		}
		
		
		//可用额度
		$can_use_quota=get_can_use_quota($deal['user_id']);
		$GLOBALS['tmpl']->assign('can_use_quota',$can_use_quota);
		
		$credit_file = get_user_credit_file($deal['user_id']);
		$deal['is_faved'] = 0;
		if($GLOBALS['user_info']){
			if($u_info['user_type']==1)
				$company = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_company WHERE user_id=".$u_info['id'],false);
			
			if($deal['deal_status'] >=4){
				//还款列表
				$loan_repay_list = get_deal_load_list($deal);
				$GLOBALS['tmpl']->assign("loan_repay_list",$loan_repay_list);
				
				if($loan_repay_list){
					$temp_self_money_list = $GLOBALS['db']->getAll("SELECT sum(self_money) as total_money,u_key FROM ".DB_PREFIX."deal_load_repay WHERE has_repay=1 AND deal_id=".$id." group by u_key ",false);
					$self_money_list = array();
					foreach($temp_self_money_list as $k=>$v){
						$self_money_list[$v['u_key']]= $v['total_money'];
					}
					
					foreach($load_list as $k=>$v){
						$load_list[$k]['remain_money'] = $v['money'] -$self_money_list[$k];
						if($load_list[$k]['remain_money'] <=0){
							$load_list[$k]['remain_money'] = 0;
							$load_list[$k]['status'] = 1;
						}
					}
				}
				
				
			}	
			$user_statics = sys_user_status($deal['user_id'],true);
			$GLOBALS['tmpl']->assign("user_statics",$user_statics);
			$GLOBALS['tmpl']->assign("company",$company);
		}
		
		$GLOBALS['tmpl']->assign("load_list",$load_list);	
		$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		$GLOBALS['tmpl']->assign("u_info",$u_info);
		
		//工作认证是否过期
		//$GLOBALS['tmpl']->assign('expire',user_info_expire($u_info));
		
		//留言
		require APP_ROOT_PATH.'app/Lib/message.php';
		require APP_ROOT_PATH.'app/Lib/page.php';
		
		$rel_table = 'transfer';
		
		$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."'");
		$condition = "rel_table = '".$rel_table."' and rel_id = ".$id;
	
		if(app_conf("USER_MESSAGE_AUTO_EFFECT")==0)
		{
			$condition.= " and user_id = ".intval($GLOBALS['user_info']['id']);
		}
		else 
		{
			if($message_type['is_effect']==0)
			{
				$condition.= " and user_id = ".intval($GLOBALS['user_info']['id']);
			}
		}
		
		//message_form 变量输出
		$GLOBALS['tmpl']->assign('rel_id',$id);
		$GLOBALS['tmpl']->assign('rel_table',$rel_table);
		
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		$msg_condition = $condition." AND is_effect = 1 ";
		$message = get_message_list($limit,$msg_condition);
		
		$page = new Page($message['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		
		foreach($message['list'] as $k=>$v){
			$msg_sub = get_message_list("","pid=".$v['id'],false);
			$message['list'][$k]["sub"] = $msg_sub["list"];
		}
		
		$GLOBALS['tmpl']->assign("message_list",$message['list']);
		
		
		//==================================================
		
		$condition = ' AND dlt.id='.$id.' AND d.deal_status >= 4 and d.is_effect=1 and d.is_delete=0  and d.repay_time_type =1 and  d.publish_wait=0 ';
		$union_sql = " LEFT JOIN ".DB_PREFIX."deal_load_transfer dlt ON dlt.deal_id = dl.deal_id ";
		
		$transfer = get_transfer($union_sql,$condition);
		$GLOBALS['tmpl']->assign('transfer',$transfer);
		
		if($deal['type_match_row'])
			$seo_title = $deal['seo_title']!=''?$deal['seo_title']:$deal['type_match_row'] . " - " . $deal['name'];
		else
			$seo_title = $deal['seo_title']!=''?$deal['seo_title']: $deal['name'];
			
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['TRANSFER']." - ".$seo_title);
		$seo_keyword = $deal['seo_keyword']!=''?$deal['seo_keyword']:$deal['type_match_row'].",".$deal['name'];
		$GLOBALS['tmpl']->assign("page_keyword",$GLOBALS['lang']['TRANSFER'].",".$seo_keyword.",");
		$seo_description = $deal['seo_description']!=''?$deal['seo_description']:$deal['name'];
		$GLOBALS['tmpl']->assign("seo_description",$GLOBALS['lang']['TRANSFER'].",".$seo_description.",");
		
		$GLOBALS['tmpl']->assign("deal",$deal);
		
		$GLOBALS['tmpl']->display("page/transfer.html");
	}
	
	public function dotrans(){
		$ajax = intval($_REQUEST['ajax']);
		$paypassword = strim(FW_DESPWD($_REQUEST['paypassword']));
		$id = intval($_REQUEST['id']);
		
		
		$status = dotrans($id,$paypassword);
		if($status['status'] == 2){
			ajax_return($status);
		}	
		elseif($status['status'] == 1){
			showSuccess($status['show_err'],$ajax);
		}else{
			showErr($status['show_err'],$ajax);
		}
		
		
	} 
}
?>
