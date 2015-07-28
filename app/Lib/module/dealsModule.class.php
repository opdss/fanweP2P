<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class dealsModule extends SiteBaseModule
{
	public function index(){
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 60;  //首页缓存10分钟
		$field = es_cookie::get("shop_sort_field"); 
		$field_sort = es_cookie::get("shop_sort_type"); 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.implode(",",$_REQUEST).$field.$field_sort);	
		if (!$GLOBALS['tmpl']->is_cached("page/deals.html", $cache_id))
		{	
			require APP_ROOT_PATH.'app/Lib/page.php';
			$level_list = load_auto_cache("level");
			$GLOBALS['tmpl']->assign("level_list",$level_list['list']);
			
			if(trim($_REQUEST['cid'])=="last"){
				$cate_id = "-1";
				$page_title = $GLOBALS['lang']['LAST_SUCCESS_DEALS']." - ";
			}
			else{
				$cate_id = intval($_REQUEST['cid']);
			}
			
			if($cate_id == 0){
				$page_title = $GLOBALS['lang']['ALL_DEALS']." - ";
			}
			
			$keywords = trim(htmlspecialchars($_REQUEST['keywords']));
			$GLOBALS['tmpl']->assign("keywords",$keywords);
			
			$level = intval($_REQUEST['level']);
			$GLOBALS['tmpl']->assign("level",$level);
			
			$interest = intval($_REQUEST['interest']);
			$GLOBALS['tmpl']->assign("interest",$interest);
			
			$months = intval($_REQUEST['months']);
			$GLOBALS['tmpl']->assign("months",$months);
			
			$lefttime = intval($_REQUEST['lefttime']);
			$GLOBALS['tmpl']->assign("lefttime",$lefttime);
			
			$months_type = intval($_REQUEST['months_type']);
			$GLOBALS['tmpl']->assign("months_type",$months_type);
					
			$deal_status = intval($_REQUEST['deal_status']);
			$GLOBALS['tmpl']->assign("deal_status",$deal_status);
			
			$cates = intval($_REQUEST['cates']);
			$GLOBALS['tmpl']->assign("cates",$cates);
			
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
				$v['url'] = url("index","deals",array("cid"=>$v['id']));
				$deal_cates[] = $v;
			}
			unset($deal_cates_db);
				
			//输出投标列表
			$page = intval($_REQUEST['p']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");
			
			$n_cate_id = 0;
			$condition = " publish_wait = 0 and is_hidden = 0 ";
			$orderby = "";
			
	
			if($cate_id > 0){
				$n_cate_id = $cate_id;
				if($field && $field_sort)
					$orderby = "$field $field_sort ,deal_status desc , sort DESC,id DESC";
				else
					$orderby = "sort DESC,id DESC";
				$total_money = $GLOBALS['db']->getOne("SELECT sum(borrow_amount) FROM ".DB_PREFIX."deal WHERE cate_id=$cate_id AND deal_status in(4,5) AND is_effect = 1 and is_delete = 0 ");
			}
			elseif ($cate_id == 0){
				$n_cate_id = 0;
				if($field && $field_sort)
					$orderby = "$field $field_sort ,sort DESC,id DESC";
				else
					$orderby = "sort DESC , id DESC";
				$total_money = $GLOBALS['db']->getOne("SELECT sum(borrow_amount) FROM ".DB_PREFIX."deal WHERE deal_status in(4,5) AND is_effect = 1 and is_delete = 0");
			}
			elseif ($cate_id == "-1"){
				$n_cate_id = 0;
				$condition .= "AND deal_status in(2,4,5) ";
				$orderby = "deal_status ASC,success_time DESC,sort DESC,id DESC";
			}
		
			
			if($keywords){
				$kw_unicode = str_to_unicode_string($keywords);
				$condition .=" and (match(name_match,deal_cate_match,tag_match,type_match) against('".$kw_unicode."' IN BOOLEAN MODE))";			
			}
			
			if($level > 0){
				$point  = $level_list['point'][$level];
				$condition .= " AND user_id in(SELECT u.id FROM ".DB_PREFIX."user u LEFT JOIN ".DB_PREFIX."user_level ul ON ul.id=u.level_id WHERE ul.point >= $point)";
			}
			
			if($interest > 0){
				$condition .= " AND rate >= ".$interest;
			}
			
			if($months > 0){
				if($months==12)
					$condition .= " AND repay_time <= ".$months;
				elseif($months==18)
					$condition .= " AND repay_time >= ".$months;
			}
			
			if($lefttime > 0){
				$condition .= " AND (start_time + enddate*24*3600 - ".TIME_UTC.") <= ".$lefttime*24*3600;
			}
		
			
			if ($deal_status == 19){
				$condition .= " AND deal_status = 1 AND start_time > ".TIME_UTC." ";
			}
			elseif($deal_status > 0){
				$condition .= " AND deal_status = ".$deal_status." AND start_time <= ".TIME_UTC." ";
			}
			
			
			if ($months_type > 0){
				if ($months_type == 1)
					$condition .= " AND ((repay_time < 3  and repay_time_type = 1) or repay_time_type = 0) ";
				else if ($months_type == 2)
					$condition .= " AND repay_time in (3,4,5) and repay_time_type = 1 ";
				else if ($months_type == 3)
					$condition .= " AND repay_time in (6,7,8) and repay_time_type = 1 ";
				else if ($months_type == 4)
					$condition .= " AND repay_time in (9,10,11) and repay_time_type = 1 ";
				else
					$condition .= " AND repay_time >= 12 and repay_time_type = 1 ";
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
			
			//使用技巧
			$use_tech_list  = get_article_list(4,6);
			$GLOBALS['tmpl']->assign("use_tech_list",$use_tech_list);
			
			$result = get_deal_list($limit,$n_cate_id,$condition,$orderby);
			
			$GLOBALS['tmpl']->assign("deal_list",$result['list']);
			$GLOBALS['tmpl']->assign("total_money",$total_money);
			
			//输出公告
			$notice_list = get_notice(3);
			$GLOBALS['tmpl']->assign("notice_list",$notice_list);
			
			$page_args['cid'] =  $cate_id;
			$page_args['keywords'] =  $keywords;
			$page_args['level'] =  $level;
			$page_args['interest'] =  $interest;
			$page_args['months'] =  $months;
			$page_args['lefttime'] =  $lefttime;
			
			
			$page_args['months_type'] =  $months_type;
			$page_args['deal_status'] =  $deal_status;
			$page_args['city'] =  $city;
			
			//分类
			$cate_list_url = array();
			$tmp_args = $page_args;
			$tmp_args['cid'] = 0;
			$cate_list_url[0]['url'] = url("index","deals#index",$tmp_args);
			$cate_list_url[0]['name'] = "不限";
			$cate_list_url[0]['id'] = 0;
			foreach($deal_cates as $k=>$v){
				$cate_list_url[$k+1] = $v;
				$tmp_args = $page_args;
				$tmp_args['cid'] = $v['id'];
				$cate_list_url[$k+1]['url'] = url("index","deals#index",$tmp_args);
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
				$interest_url[$k]['url'] = url("index","deals#index",$tmp_args);
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
				$lefttime_url[$k]['url'] = url("index","deals#index",$tmp_args);
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
				$months_type_url[$k]['url'] = url("index","deals#index",$tmp_args);
			}
		
			$GLOBALS['tmpl']->assign('months_type_url',$months_type_url);
			
			
			//标状态
			$deal_status_url = array(
					array(
						"key"=>0,
						"name" => "不限",
					),
					array(
						"key"=>19,
						"name" => "未开始",
					),
					array(
						"key"=>1,
						"name" => "进行中",
					),
					array(
						"key"=>2,
						"name" => "满标",
					),
					array(
						"key"=>3,
						"name" => "流标",
					),
					array(
						"key"=>4,
						"name" => "还款中",
					),
					array(
						"key"=>5,
						"name" => "已还清",
					),
			);
			
			
			foreach($deal_status_url as $k=>$v){
				$tmp_args = $page_args;
				$tmp_args['deal_status'] = $v['key'];
				$deal_status_url[$k]['url'] = url("index","deals#index",$tmp_args);
			}
			$GLOBALS['tmpl']->assign('deal_status_url',$deal_status_url);
			
			
			//会员等级
			$level_list_url = array();
			$tmp_args = $page_args;
			$tmp_args['level'] = 0;
			$level_list_url[0]['url'] = url("index","deals#index",$tmp_args);
			$level_list_url[0]['name'] = "不限";
			foreach($level_list['list'] as $k=>$v){
				$tmp_args = $page_args;
				$tmp_args['level'] = $v['id'];
				$level_list_url[$k+1] = $v;
				$level_list_url[$k+1]['url'] = url("index","deals#index",$tmp_args);
			}
			$GLOBALS['tmpl']->assign('level_list_url',$level_list_url);
		

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
				$city_urls[$v['id']]['url'] = url("index","deals#index",$tmp_args);
			}
			
			$GLOBALS['tmpl']->assign('city_urls',$city_urls);
			
			$sub_citys = $city_urls[$city]['child'];
			foreach($sub_citys as $k=>$v){
				$tmp_args = $page_args;
				$tmp_args['city'] = $v['pid'];
				$tmp_args['scity'] = $v['id'];
				$sub_citys[$k]['url'] = url("index","deals#index",$tmp_args);
			}
			
			$GLOBALS['tmpl']->assign('sub_citys',$sub_citys);
			
			$page_pram = "";
			foreach($page_args as $k=>$v){
				$page_pram .="&".$k."=".$v;
			}
			
			$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"),$page_pram);   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$GLOBALS['tmpl']->assign("page_title",$page_title . $GLOBALS['lang']['FINANCIAL_MANAGEMENT']);
					
			$GLOBALS['tmpl']->assign("cate_id",$cate_id);
			$GLOBALS['tmpl']->assign("cid",strim($_REQUEST['cid']));
			$GLOBALS['tmpl']->assign("keywords",$keywords);
			$GLOBALS['tmpl']->assign("deal_cate_list",$deal_cates);
			$GLOBALS['tmpl']->assign("page_args",$page_args);
			$GLOBALS['tmpl']->assign("field",$field); 
			$GLOBALS['tmpl']->assign("field_sort",$field_sort); 
		}
		
		$GLOBALS['tmpl']->display("page/deals.html",$cache_id);
	}
	
	public function about(){
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 6000;  //首页缓存10分钟
		$name = trim($_REQUEST['u']) == "" ? "financing" : trim($_REQUEST['u']);
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$name);	
		if (!$GLOBALS['tmpl']->is_cached("page/deals_about.html", $cache_id))
		{	
			$info = get_article_buy_uname($name);
			$info['content']=$GLOBALS['tmpl']->fetch("str:".$info['content']);
			$GLOBALS['tmpl']->assign("info",$info);
			
			$about_list = get_article_list(20,7,"","id ASC",true);
			
			$GLOBALS['tmpl']->assign("about_list",$about_list['list']);
			
			$seo_title = $info['seo_title']!=''?$info['seo_title']:$info['title'];
			$GLOBALS['tmpl']->assign("page_title",$seo_title);
			$seo_keyword = $info['seo_keyword']!=''?$info['seo_keyword']:$info['title'];
			$GLOBALS['tmpl']->assign("page_keyword",$seo_keyword.",");
			$seo_description = $info['seo_description']!=''?$info['seo_description']:$info['title'];
			$GLOBALS['tmpl']->assign("page_description",$seo_description.",");
		}
		$GLOBALS['tmpl']->display("page/deals_about.html",$cache_id);
	}
}
?>
