<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

/*以下为动态载入的函数库*/

//动态加载今日团购
function insert_load_today_deal()
{
	require_once APP_ROOT_PATH.'app/Lib/deal.php';
	//输出今日团购
	$today_deal = get_deal_show_shop();
	$GLOBALS['tmpl']->assign("today_deal",$today_deal);
	return $GLOBALS['tmpl']->fetch("inc/insert/load_today_deal.html");
}
//动态加载用户提示
function insert_load_user_tip()
{
	if(intval($GLOBALS['user_info']['id']) > 0){
		//输出未读的消息数
		$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
		$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
		$expire = array();
		if($GLOBALS['user_info']){
			$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    	$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		}
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/load_user_tip.html");
}
//动态加载白条用户提示
function insert_load_debit_user_tip()
{
	if(intval($GLOBALS['user_info']['id']) > 0){
		//输出未读的消息数
		$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
		$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
		$expire = array();
		if($GLOBALS['user_info']){
			$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    	$GLOBALS['tmpl']->assign("credit_file",$credit_file);
		}
	}
	return $GLOBALS['tmpl']->fetch("debit/debit_load_user_tip.html");
}

//动态加载用户提示
function insert_load_user_tip_index()
{

	//输出未读的消息数
	$msg_count = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."msg_box where to_user_id = ".intval($GLOBALS['user_info']['id'])." and is_read = 0 and is_delete = 0 and type = 0");
	$GLOBALS['tmpl']->assign("msg_count",intval($msg_count));
	$expire = array();
	if($GLOBALS['user_info']){
		$credit_file = get_user_credit_file($GLOBALS['user_info']['id'],$GLOBALS['user_info']);
	    $GLOBALS['tmpl']->assign("credit_file",$credit_file);
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/load_user_tip_index.html");
}

/**
 * 动态输出成功案例， 不受缓存限制
 */
function insert_success_deal_list(){
	//输出成功案例
	$GLOBALS['tmpl']->caching = true;
	$GLOBALS['tmpl']->cache_lifetime = 120;  //首页缓存10分钟
	$cache_id  = md5("success_deal_list");	
	if (!$GLOBALS['tmpl']->is_cached("inc/insert/success_deal_list.html", $cache_id))
	{	
		$suc_deal_list =  get_deal_list(11,0,"deal_status in(4,5) "," success_time DESC,sort DESC,id DESC");
		$GLOBALS['tmpl']->assign("succuess_deal_list",$suc_deal_list['list']);
	}
	return $GLOBALS['tmpl']->fetch("inc/insert/success_deal_list.html",$cache_id);
}


//动态加载商品分类页的产品列表
function insert_load_filter_goods_list()
{
	convert_req($_REQUEST);	
	//获取当前页的团购商品列表
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");	
				
			
			$id = intval($_REQUEST['id']);
			if($id==0)
			$uname = addslashes(trim($_REQUEST['id']));
			$cate_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where id = ".$id." or (uname = '".$uname."' and uname <> '')");
					
			$condition = " d.buy_type<>1 ";  //条件
			
			$url_param = array(
				"id"	=> addslashes(trim($_REQUEST['id'])),
				"b"	=>	intval($_REQUEST['b']),
				"min_price" => doubleval($_REQUEST['min_price']),
				"max_price"	=> doubleval($_REQUEST['max_price'])
			);			
			$filter_req = $_REQUEST['f'];		//筛选数组	
			
			if(count($filter_req)>0)
			{		
				foreach($filter_req as $k=>$v)
				{
					$url_param['f['.$k.']'] = $v;
				}	
			}		

			if(intval($_REQUEST['is_redirect'])==1)
			{
				app_redirect(url("shop","cate",$url_param));
			}
			$GLOBALS['tmpl']->assign("url_param",$url_param); //将变量输出到模板
			
			$ids = load_auto_cache("shop_sub_parent_cate_ids",array("cate_id"=>intval($cate_item['id'])));
			
			$add_title = "";
			
			//输出品牌	
			$brand_id = intval($_REQUEST['b']);	
			if($brand_id>0)
			{
				$condition .= " and d.brand_id = ".$brand_id;
				$add_title.=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."brand where id = ".$brand_id);
			}
			$brand_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."brand where shop_cate_id in (".implode(",",$ids).")");
			$brand_list[]	=	array("name"=>$GLOBALS['lang']['ALL'],"id"=>0);
			foreach($brand_list as $k=>$v)
			{
				if($brand_id==$v['id'])
				{
					$brand_list[$k]['act'] = 1;
				}
				$tmp_url_param = $url_param;
				$tmp_url_param['b'] = $v['id'];
				$brand_list[$k]['url'] = url("shop","cate#index",$tmp_url_param);	
			}		
			$GLOBALS['tmpl']->assign("brand_list",$brand_list);
		
			
			//输出属性筛选						
			$join_str = "";
			$unicode_tags = array();
			if($filter_req)
			{
				foreach($filter_req as $k=>$v)
				{
					$k = intval($k);
					$filter_req[$k] = trim(addslashes(urldecode($v)));
					if($filter_req[$k]!=''&&$filter_req[$k]!='all')
					{
						if($add_title!='')$add_title.=" - ";
						$add_title.= $filter_req[$k];
												
						$unicode_tags[] = "+".str_to_unicode_string($filter_req[$k]);						
					}	
				}
			}
			if(count($unicode_tags)>0)
			{
				$kw_unicode = implode(" ", $unicode_tags);
				//有筛选
				$condition .=" and (match(d.tag_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			}			
			
			$filter_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter_group where is_effect = 1 and cate_id in (".implode(",",$ids).") order by sort desc");
			foreach($filter_group as $k=>$v)
			{
				$filter_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter where filter_group_id = ".$v['id']." limit 20");
				$filter_list[]	=	array("name"=>$GLOBALS['lang']['ALL'],"id"=>0);
				foreach($filter_list as $kk=>$vv)
				{
					if($filter_req[$v['id']]==$vv['name'])
					{
						$filter_list[$kk]['act'] = 1;
					}
					if($vv['id']==0)
					$url_name = 'all';
					else
					$url_name = $vv['name'];
					
					if(($filter_req[$v['id']]=='all'||$filter_req[$v['id']]=='')&&$url_name == 'all')
					{
						$filter_list[$kk]['act'] = 1;
					}
					
					$tmp_url_param = $url_param;
					$tmp_url_param["f[".$v['id']."]"] = $url_name;	
									
					$filter_list[$kk]['url'] = url("shop","cate#index",$tmp_url_param);
				}
				$filter_group[$k]['filter_list'] = $filter_list;
			}
			$GLOBALS['tmpl']->assign("filter_group",$filter_group);
			
			//输出价格区间
			$min_price = doubleval($_REQUEST['min_price']);
			$max_price = doubleval($_REQUEST['max_price']);
			
			$GLOBALS['tmpl']->assign("min_price",$min_price);
			$GLOBALS['tmpl']->assign("max_price",$max_price);
			if($min_price>0)
			{
				$condition.=" and d.current_price >= ".$min_price;
			}
			if($max_price>0)
			{
				$condition.=" and d.current_price <= ".$max_price;
			}
			
			$sort_field = es_cookie::get("shop_sort_field")?es_cookie::get("shop_sort_field"):"sort";
			$sort_type = es_cookie::get("shop_sort_type")?es_cookie::get("shop_sort_type"):"desc";
			$GLOBALS['tmpl']->assign('sort_field',$sort_field);
			$GLOBALS['tmpl']->assign('sort_type',$sort_type);
			
			$result = search_goods_list($limit,intval($cate_item['id']),$condition,"d.".$sort_field." ".$sort_type,false,$join_str);			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign("cate_id",$cate_item['id']);
			
			if(es_cookie::get("list_type")===null)
				$list_type = app_conf("LIST_TYPE");
			else
				$list_type = intval(es_cookie::get("list_type"));
			
			$GLOBALS['tmpl']->assign("list_type",$list_type);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_filter_goods_list.html");
}


/* 弃用的联表查询
function insert_load_filter_goods_list()
{
	convert_req($_REQUEST);	
	//获取当前页的团购商品列表
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");		
			
			$id = intval($_REQUEST['id']);
			if($id==0)
			$uname = addslashes(trim($_REQUEST['id']));
			$cate_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."shop_cate where id = ".$id." or (uname = '".$uname."' and uname <> '')");
					
			$condition = " d.buy_type<>1 ";  //条件
			
			$ids = $GLOBALS['cache']->get("DEAL_SHOP_CATE_BELONE_IDS_".intval($cate_item['id']));
			if($ids === false)
			{
				$ids_util = new ChildIds("shop_cate");
				$ids = $ids_util->getChildIds(intval($cate_item['id']));
				$ids[] = intval($cate_item['id']);
				$GLOBALS['cache']->set("DEAL_SHOP_CATE_BELONE_IDS_".intval($cate_item['id']),$ids);
			}
			
			$add_title = "";
			
			//输出品牌	
			$brand_id = intval($_REQUEST['b']);	
			if($brand_id>0)
			{
				$condition .= " and d.supplier_id = ".$brand_id;
				$add_title.=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."supplier where id = ".$brand_id);
			}
			$brand_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."supplier where is_effect = 1 and cate_id in (".implode(",",$ids).")");
			$brand_list[]	=	array("name"=>$GLOBALS['lang']['ALL'],"id"=>0);
			foreach($brand_list as $k=>$v)
			{
				if($brand_id==$v['id'])
				{
					$brand_list[$k]['act'] = 1;
				}
				if(preg_match("/b=/",$_SERVER['REQUEST_URI']))
				{
					$brand_list[$k]['url'] = preg_replace("/b=\d+/","b=".$v['id'],$_SERVER['REQUEST_URI']);
				}
				else
				{
					if(preg_match("/\?/", $_SERVER['REQUEST_URI'])) 
					{				
						$brand_list[$k]['url'] = $_SERVER['REQUEST_URI']."&b=".$v['id'];
					}
					else
					{
						$brand_list[$k]['url'] = $_SERVER['REQUEST_URI']."?b=".$v['id'];
					}	
				}		
			}		
			$GLOBALS['tmpl']->assign("brand_list",$brand_list);
		
			
			//输出属性筛选
			$filter_req = $_REQUEST['f'];					
			$join_str = "";
			if($filter_req)
			{
				foreach($filter_req as $k=>$v)
				{
					$k = intval($k);
					$filter_req[$k] = trim(addslashes(urldecode($v)));
					if($filter_req[$k]!=''&&$filter_req[$k]!='all')
					{
						if($add_title!='')$add_title.=" - ";
						$add_title.= $filter_req[$k];
						//联表及条件
						$join_str.=" left join ".DB_PREFIX."deal_filter as df_".$k." on d.id=df_".$k.".deal_id and df_".$k.".filter_group_id = ".$k;
						$condition.=" and df_".$k.".filter like '%".$filter_req[$k]."%' ";
					}	
				}
			}			
			
			$filter_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter_group where is_effect = 1 and cate_id in (".implode(",",$ids).") order by sort desc");
			foreach($filter_group as $k=>$v)
			{
				$filter_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter where filter_group_id = ".$v['id']." limit 20");
				$filter_list[]	=	array("name"=>$GLOBALS['lang']['ALL'],"id"=>0);
				foreach($filter_list as $kk=>$vv)
				{
					if($filter_req[$v['id']]==$vv['name'])
					{
						$filter_list[$kk]['act'] = 1;
					}
					if($vv['id']==0)
					$url_name = 'all';
					else
					$url_name = $vv['name'];
					
					if(($filter_req[$v['id']]=='all'||$filter_req[$v['id']]=='')&&$url_name == 'all')
					{
						$filter_list[$kk]['act'] = 1;
					}
									
					if(preg_match("/f\[".$v['id']."\]=[^&]+/",$_SERVER['REQUEST_URI']))
					{
						$filter_list[$kk]['url'] = preg_replace("/f\[".$v['id']."\]=[^&]+/","f[".$v['id']."]=".$url_name,$_SERVER['REQUEST_URI']);
					}
					else
					{
						if(preg_match("/\?/", $_SERVER['REQUEST_URI'])) 
						{				
							$filter_list[$kk]['url'] = $_SERVER['REQUEST_URI']."&f[".$v['id']."]=".$url_name;
						}
						else
						{
							$filter_list[$kk]['url'] = $_SERVER['REQUEST_URI']."?f[".$v['id']."]=".$url_name;
						}	
					}	
				}
				$filter_group[$k]['filter_list'] = $filter_list;
			}
			$GLOBALS['tmpl']->assign("filter_group",$filter_group);
			
			//输出价格区间
			$min_price = doubleval($_REQUEST['min_price']);
			$max_price = doubleval($_REQUEST['max_price']);
			
			$GLOBALS['tmpl']->assign("min_price",$min_price);
			$GLOBALS['tmpl']->assign("max_price",$max_price);
			if($min_price>0)
			{
				$condition.=" and d.current_price >= ".$min_price;
			}
			if($max_price>0)
			{
				$condition.=" and d.current_price <= ".$max_price;
			}
			
			$sort_field = es_cookie::get("shop_sort_field")?es_cookie::get("shop_sort_field"):"sort";
			$sort_type = es_cookie::get("shop_sort_type")?es_cookie::get("shop_sort_type"):"desc";
			$GLOBALS['tmpl']->assign('sort_field',$sort_field);
			$GLOBALS['tmpl']->assign('sort_type',$sort_type);
			
			$result = search_goods_list($limit,intval($cate_item['id']),$condition,"d.".$sort_field." ".$sort_type,false,$join_str);			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign("cate_id",$cate_item['id']);
			
			if(es_cookie::get("list_type")===null)
				$list_type = app_conf("LIST_TYPE");
			else
				$list_type = intval(es_cookie::get("list_type"));
			
			$GLOBALS['tmpl']->assign("list_type",$list_type);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_filter_goods_list.html");
}
*/

//载入文章点击数
function insert_load_article_click($para)
{
	if(check_ipop_limit(CLIENT_IP,"article",60,intval($para['article_id'])))
	{
					//每一分钟访问更新一次点击数
		$GLOBALS['db']->query("update ".DB_PREFIX."article set click_count = click_count + 1 where id =".intval($para['article_id']));
	}
	return intval($GLOBALS['db']->getOne("select click_count from ".DB_PREFIX."article where id = ".intval($para['article_id'])));
}

//加载购物车列表
function insert_load_cart_index()
{
	//增加输出购物车中产品是否参加抽奖
			$is_lottery = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."deal as d on dc.deal_id = d.id where d.is_lottery = 1 and session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id']));
			$GLOBALS['tmpl']->assign("is_lottery",$is_lottery);
		
			if(!$GLOBALS['user_info']&&$is_lottery>0) //购物车中有抽奖商品时必需先登录
			{
				showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax,url("shop","user#login"));
			}
		
			$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set update_time=".TIME_UTC.",user_id = ".intval($GLOBALS['user_info']['id'])." where session_id = '".es_session::id()."'");
			$cart_list = $GLOBALS['db']->getAll("select c.*,d.icon from ".DB_PREFIX."deal_cart as c left join ".DB_PREFIX."deal as d on c.deal_id = d.id where c.session_id = '".es_session::id()."' and c.user_id = ".intval($GLOBALS['user_info']['id']));
	
			$GLOBALS['tmpl']->assign("cart_list",$cart_list);
			$GLOBALS['tmpl']->assign('total_price',$GLOBALS['db']->getOne("select sum(total_price) from ".DB_PREFIX."deal_cart where session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id'])));
				
			//输出抽奖验证过的用户手机号
			$lottery_mobile = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".intval($GLOBALS['user_info']['id']));
			$GLOBALS['tmpl']->assign("lottery_mobile",$lottery_mobile['lottery_mobile']);
			$GLOBALS['tmpl']->assign("is_verify",$lottery_mobile['lottery_verify']==''?true:false);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_cart_index.html");
}

//加载产品的剩余库存
function insert_get_goods_stock($para)
{
	return intval($GLOBALS['db']->getOne("select (max_bought - buy_count) from ".DB_PREFIX."deal where id =".intval($para['id'])));
}

function insert_get_goods_attr_stock_json($para)
{
	$goods['id'] = intval($para['id']);
	//输出规格库存的配置
			$attr_stock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."attr_stock where deal_id = ".$goods['id']." order by id asc");
			$attr_cfg_json = "{";
			$attr_stock_json = "{";
			
			foreach($attr_stock as $k=>$v)
			{
				$attr_cfg_json.=$k.":"."{";
				$attr_stock_json.=$k.":"."{";
				foreach($v as $key=>$vvv)
				{
					if($key!='attr_cfg')
					$attr_stock_json.="\"".$key."\":"."\"".$vvv."\",";
				}
				$attr_stock_json = substr($attr_stock_json,0,-1);
				$attr_stock_json.="},";	
				
				$attr_cfg_data = unserialize($v['attr_cfg']);	
				foreach($attr_cfg_data as $attr_id=>$vv)
				{
					$attr_cfg_json.=$attr_id.":"."\"".$vv."\",";
				}	
				$attr_cfg_json = substr($attr_cfg_json,0,-1);
				$attr_cfg_json.="},";		
			}
			if($attr_stock)
			{
				$attr_cfg_json = substr($attr_cfg_json,0,-1);
				$attr_stock_json = substr($attr_stock_json,0,-1);
			}
			
			$attr_cfg_json .= "}";
			$attr_stock_json .= "}";
			return "var attr_cfg_json = ".$attr_cfg_json."; var attr_stock_json = ".$attr_stock_json.";";
}

function insert_load_msg_list()
{
	$rel_table = strim($_REQUEST['act']);
			$message_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message_type where type_name='".$rel_table."' ");
			if(!$message_type||$message_type['is_fix']==0)
			{
				$message_type_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."message_type where is_fix = 0 order by sort desc");
				if(!$message_type_list)
				{
					showErr($GLOBALS['lang']['INVALID_MESSAGE_TYPE']);
				}
				else
				{
					if(!$message_type)
					$message_type = $message_type_list[0];
					foreach($message_type_list as $k=>$v)
					{
						if($v['type_name'] == $message_type['type_name'])
						{
							$message_type_list[$k]['current'] = 1;
						}
						else
						{
							$message_type_list[$k]['current'] = 0;
						}
					}
					$GLOBALS['tmpl']->assign("message_type_list",$message_type_list);
				}
			}
			$rel_table = $message_type['type_name'];
			$condition = '';	
			$id = intval($_REQUEST['id']);
			if($rel_table == 'deal')
			{
				$deal = get_deal($id);
				if($deal['buy_type']!=1)
				$GLOBALS['tmpl']->assign("deal",$deal);
				$id = $deal['id'];
			}
			//require './app/Lib/side.php'; 
			if($id>0)
			$condition = "rel_table = '".$rel_table."' and rel_id = ".$id;
			else
			$condition = "rel_table = '".$rel_table."'";
		
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
			
			$condition.=" and is_buy = ".intval($_REQUEST['is_buy']);
			//message_form 变量输出
			
			//开始输出当前的site_nav					
			$site_nav[] = array('name'=>$GLOBALS['lang']['HOME_PAGE'],'url'=>APP_ROOT."/");
			$site_nav[] = array('name'=>$message_type['show_name'],'url'=>url("shop","msg#".$message_type['type_name']));
			$GLOBALS['tmpl']->assign("site_nav",$site_nav);
			//输出当前的site_nav
					
					
			$GLOBALS['tmpl']->assign("post_title",$message_type['show_name']);
			$GLOBALS['tmpl']->assign("page_title",$message_type['show_name']);
			$GLOBALS['tmpl']->assign('rel_id',$id);
			$GLOBALS['tmpl']->assign('rel_table',$rel_table);
			$GLOBALS['tmpl']->assign('is_buy',intval($_REQUEST['is_buy']));
			
			if(intval($_REQUEST['is_buy'])==1)
			{
				$GLOBALS['tmpl']->assign("post_title",$GLOBALS['lang']['AFTER_BUY']);
				$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['AFTER_BUY']);		
			}
			
			if(!$GLOBALS['user_info'])
			{
				$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
			}
			
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");
		
			
			$message = get_message_list_shop($limit,$condition);
			
			$page = new Page($message['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			$GLOBALS['tmpl']->assign("user_auth",get_user_auth());
			$GLOBALS['tmpl']->assign("message_list",$message['list']);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_msg_list.html");
}

function insert_load_rec_goods($para)
{
			$GLOBALS['tmpl']->assign("hide_filter",true);		
			
			$sort_field = es_cookie::get("shop_sort_field")?es_cookie::get("shop_sort_field"):"sort";
			$sort_type = es_cookie::get("shop_sort_type")?es_cookie::get("shop_sort_type"):"desc";			
			if(es_cookie::get("list_type")===null)
				$list_type = app_conf("LIST_TYPE");
			else
				$list_type = intval(es_cookie::get("list_type"));
			$GLOBALS['tmpl']->assign("list_type",$list_type);
			//分页
			$page = intval($_REQUEST['p']);
			if($page==0)
			$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE");		
			
			if($para['r']=='rhot')
			{
				$GLOBALS['tmpl']->assign('sort_field',$sort_field);
				$GLOBALS['tmpl']->assign('sort_type',$sort_type);
				$result = search_goods_list($limit,0,'d.is_hot = 1 and buy_type <> 1',"d.".$sort_field." ".$sort_type,false);							
			}
			elseif($para['r']=='rbest')
			{
				$GLOBALS['tmpl']->assign('sort_field',$sort_field);
				$GLOBALS['tmpl']->assign('sort_type',$sort_type);
				$result = search_goods_list($limit,0,'d.is_best = 1 and buy_type <> 1',"d.".$sort_field." ".$sort_type,false);
			}
			elseif($para['r']=='rnew')
			{
				$GLOBALS['tmpl']->assign('sort_field',$sort_field);
				$GLOBALS['tmpl']->assign('sort_type',$sort_type);
				$result = search_goods_list($limit,0,'d.is_new = 1 and buy_type <> 1',"d.".$sort_field." ".$sort_type,false);
			}
			elseif($para['r']=='rsale')
			{
				$sort_field = "buy_count";
				$sort_type = es_cookie::get("shop_sort_type")?es_cookie::get("shop_sort_type"):"desc";
				$GLOBALS['tmpl']->assign('sort_field',$sort_field);
				$GLOBALS['tmpl']->assign('sort_type',$sort_type);
				$result = search_goods_list($limit,0,' buy_type <> 1 ',"d.".$sort_field." ".$sort_type,false);
			}
			
			$GLOBALS['tmpl']->assign("list",$result['list']);
			$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			return $GLOBALS['tmpl']->fetch("inc/insert/load_filter_goods_list.html");
}

function insert_load_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_login_form.html");
}
function insert_load_debit_login_form()
{
	return $GLOBALS['tmpl']->fetch("debit/debit_login_form.html");
}
function insert_load_unit_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_unit_login_form.html");
}

function insert_load_authorized_login_form()
{
	return $GLOBALS['tmpl']->fetch("inc/page_authorized_login_form.html");
}

function insert_agency_info()
{
	$agency_info  = es_session::get("manageagency_info");
	$GLOBALS['tmpl']->assign('agency_info',$agency_info);
	return $GLOBALS['tmpl']->fetch("manageagency/agency_login_info.html");
}
function insert_authorized_info()
{
	$authorized_info  = es_session::get("authorized_info");
	$GLOBALS['tmpl']->assign('authorized_info',$authorized_info);
	return $GLOBALS['tmpl']->fetch("authorized/authorized_login_info.html");
}
//动态获取可同步登录的API大图
function insert_get_app_login($type)
{
		//0:小登录图标 1:大登录图标 2:绑定图标
		if(!isset($type['v'])){
			$type['v'] = 1;
		}
		$apis = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."api_login");
		if(intval($type["r"])==1)
			$str = "<h3>或使用这些帐号登录</h3>";
		else
			$str = "<h3>合作网站账号登录</h3>";
		foreach($apis as $k=>$api)
		{					
			$str .= $url."<span id='api_".$api['class_name']."_".$type['v']."'><script type='text/javascript'>load_api_url('".$api['class_name']."',".$type['v'].");</script></span>";
			
		}
		return $str;

}

function insert_load_goods_comment_tip($para)
{
	$goods_id = intval($para['goods_id']);
	$buy_comment = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_table = 'deal' and rel_id = ".$goods_id." and is_buy = 1");
	$good_comment = $GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."message where rel_table = 'deal' and rel_id = ".$goods_id." and is_buy = 1 and point = 5");
	if($buy_comment>0)
	$percent_comment = round($good_comment/$buy_comment*100);
	else
	$percent_comment = 0;
	$msg =  sprintf($GLOBALS['lang']['TOTAL_COMMENT_BUY'],$buy_comment).sprintf($GLOBALS['lang']['GOOD_COMMENT_PERCENT'],$percent_comment);
	return $msg;
}

//动态加载优惠券点评
function insert_load_youhui_comment()
{
	require_once APP_ROOT_PATH."app/Lib/message.php";
	require_once APP_ROOT_PATH.'app/Lib/page.php';
		$goods_id = intval($_REQUEST['id']);
		$uname = addslashes(trim($_REQUEST['id']));
		if($goods_id==0&&$uname!='')
		{
				$goods_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal where uname = '".$uname."'"); 
		}
		
		
		$is_buy = intval($_REQUEST['is_buy']);	
		$GLOBALS['tmpl']->assign("goods_id",$goods_id);
		$GLOBALS['tmpl']->assign("is_buy",$is_buy);
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");			
		$result = get_message_list_shop($limit," rel_table='deal' and rel_id = ".$goods_id." and is_buy = ".$is_buy);		
		$GLOBALS['tmpl']->assign("message_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign('is_buy',$is_buy);
		$GLOBALS['tmpl']->assign("user_auth",get_user_auth());	
		if(!$GLOBALS['user_info'])
		{
			$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
		}
		return $GLOBALS['tmpl']->fetch("inc/inc_youhui_comment_list.html");
}
//动态加载商品点评
function insert_load_goods_comment()
{
	require_once APP_ROOT_PATH."app/Lib/message.php";
	require_once APP_ROOT_PATH.'app/Lib/page.php';
		$goods_id = intval($_REQUEST['id']);
		$uname = addslashes(trim($_REQUEST['id']));
		if($goods_id==0&&$uname!='')
		{
				$goods_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."deal where uname = '".$uname."'"); 
		}
		
		$is_buy = intval($_REQUEST['is_buy']);	
		$GLOBALS['tmpl']->assign("goods_id",$goods_id);
		$GLOBALS['tmpl']->assign("is_buy",$is_buy);
		//分页
		$page = intval($_REQUEST['p']);
		if($page==0)
		$page = 1;
		$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");			
		$result = get_message_list_shop($limit," rel_table='deal' and rel_id = ".$goods_id." and is_buy = ".$is_buy);		
		$GLOBALS['tmpl']->assign("message_list",$result['list']);
		$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign('is_buy',$is_buy);
		$GLOBALS['tmpl']->assign("user_auth",get_user_auth());	
		if(!$GLOBALS['user_info'])
		{
			$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
		}
		return $GLOBALS['tmpl']->fetch("inc/inc_goods_comment_list.html");
}
function insert_load_goods_tab($p)
{
	$idx = intval($p['param']);
	if($idx==2&&trim($_REQUEST['type'])=='comment')
	{
		return "class='act'";
	}
	elseif($idx==1&&(!isset($_REQUEST['type'])||trim($_REQUEST['type'])!='comment')) 
	return "class='act'";
	else
	return "class=''";
}

//动态加载不同模块的点评
function insert_load_comment($param)
{
	
	require_once APP_ROOT_PATH."app/Lib/message.php";
	require_once APP_ROOT_PATH.'app/Lib/page.php';
	$rel_id = intval($_REQUEST['id']); //关联数据的ID
	$rel_table = $param['rel_table'];
	$is_effect = $param['is_effect'];
	$is_image = $param['is_image'];
	$width = $param['width'];
	$height = $param['height'];
	
	$GLOBALS['tmpl']->assign("height",$height);
	$GLOBALS['tmpl']->assign("width",$width);
	$GLOBALS['tmpl']->assign("rel_id",$rel_id);
	$GLOBALS['tmpl']->assign("rel_table",$rel_table);
	$GLOBALS['tmpl']->assign("is_effect",$is_effect);
	$GLOBALS['tmpl']->assign("is_image",$is_image);
	
	//分页
	$page = intval($_REQUEST['p']);
	if($page==0)
	$page = 1;
	$limit = (($page-1)*app_conf("PAGE_SIZE")).",".app_conf("PAGE_SIZE");			
	$result = get_message_list_shop($limit," rel_table='".$rel_table."' and rel_id = ".$rel_id." and is_effect = 1");		

	$GLOBALS['tmpl']->assign("message_list",$result['list']);
	$page = new Page($result['count'],app_conf("PAGE_SIZE"));   //初始化分页对象 		
	$p  =  $page->show();
	$GLOBALS['tmpl']->assign('pages',$p);
	$GLOBALS['tmpl']->assign("user_auth",get_user_auth());
			
	if(!$GLOBALS['user_info'])
	{
			$GLOBALS['tmpl']->assign("message_login_tip",sprintf($GLOBALS['lang']['MESSAGE_LOGIN_TIP'],url("shop","user#login"),url("shop","user#register")));
	}
	
	return $GLOBALS['tmpl']->fetch("inc/inc_comment_list.html");
}

function insert_load_keyword()
{
	$keyword = addslashes(htmlspecialchars(trim($_REQUEST['keyword'])));
	if($keyword=='')
	$keyword = $GLOBALS['lang']['HEAD_KEYWORD_EMPTY_TIP'];
	return $keyword;
}

function insert_get_syn_class()
{
	$apis = $GLOBALS['db']->getAll("select class_name from ".DB_PREFIX."api_login where is_weibo = 1");
	$str = "";
	foreach($apis as $k=>$v)
	{
		if($GLOBALS['user_info']['is_syn_'.strtolower($v['class_name'])]==1)
		{
			$str.="<input type='hidden' class='syn_class' value='".$v['class_name']."' />";
		}
	}
	return $str;
}

function insert_artile_list($param){
	if($param['cate']=="" || $param['tpl']=="")
		return "";
	if($param['limit']=="")
		$param['limit']= 5;
		
	$cate_id =  $GLOBALS['db']->getOne("SELECT id FROM ".DB_PREFIX."article_cate where title='".$param['cate']."'",false);
	
	if($cate_id > 0){
		$article_list  = get_article_list($param['limit'],$cate_id);
		if($article_list){
			$GLOBALS['tmpl']->assign($param['datakey']."_id",$cate_id);
			$GLOBALS['tmpl']->assign($param['datakey']."_list",$article_list['list']);	
		}
	}
	
	return $GLOBALS['tmpl']->fetch($param['tpl']);
}


function insert_get_login_key(){
	return LOGIN_DES_KEY();
}

function insert_get_hash_key(){
	return HASH_KEY();
}

function insert_is_mobile(){
	if(isMobile()){
		return 1;
	}
	else{
		return 0;
	}
}
?>