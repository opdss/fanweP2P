<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';

class ssModule extends SiteBaseModule
{
	public function index()
	{
		//输出商城分类
		$cate_tree = get_cate_tree();	
		$all_cate_tree = get_cate_tree(0,1);		
		$GLOBALS['tmpl']->assign("cate_tree",$cate_tree);
		$GLOBALS['tmpl']->assign("all_cate_tree",$all_cate_tree);
		//输出品牌
		$brand_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."brand  order by sort desc");
		$GLOBALS['tmpl']->assign("brand_list",$brand_list);
		
		$GLOBALS['tmpl']->assign("filter",true); //显示筛选框
		
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['ADVANCED_SEARCH']);
		$GLOBALS['tmpl']->assign("page_keyword",$GLOBALS['lang']['ADVANCED_SEARCH']);
		$GLOBALS['tmpl']->assign("page_description",$GLOBALS['lang']['ADVANCED_SEARCH']);
		$GLOBALS['tmpl']->display("ss_index.html");
	}
	public function pick()
	{		
		
			$url_param = array(
				"id"	=> addslashes(trim($_REQUEST['id'])),
				"b"	=>	intval($_REQUEST['b']),
				"min_price" => doubleval($_REQUEST['min_price']),
				"max_price"	=> doubleval($_REQUEST['max_price']),
				"keyword"	=>	addslashes(trim($_REQUEST['keyword']))
			
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
			app_redirect(url("shop","ss#pick",$url_param));
		}	
			
		//输出商城分类
		$cate_tree = get_cate_tree();	
		$all_cate_tree = get_cate_tree(0,1);		
		$GLOBALS['tmpl']->assign("cate_tree",$cate_tree);
		$GLOBALS['tmpl']->assign("all_cate_tree",$all_cate_tree);
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
		
		$ids = load_auto_cache("shop_sub_cate_ids",array("cate_id"=>intval($cate_item['id'])));
		
		$add_title = "";
		
		//输出品牌	
		$brand_id = intval($_REQUEST['b']);	
		if($brand_id>0)
		{
			$condition .= " and d.brand_id = ".$brand_id;
			$add_title.=$GLOBALS['db']->getOne("select name from ".DB_PREFIX."brand where id = ".$brand_id);
			$GLOBALS['tmpl']->assign("brand_id",$brand_id);
		}
		$brand_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."brand order by sort desc");
			
		$GLOBALS['tmpl']->assign("brand_list",$brand_list);
	
		
		//输出属性筛选
		$filter_req = $_REQUEST['f'];		
		$join_str = "";
		$unicode_tags = array();
		foreach($filter_req as $k=>$v)
		{
			$filter_req[$k] = trim(addslashes(urldecode($v)));
			if($filter_req[$k]!=''&&$filter_req[$k]!='all')
			{
				if($add_title!='')$add_title.=" - ";
				$add_title.= $filter_req[$k];
//				//联表及条件
//				$join_str.=" left join ".DB_PREFIX."deal_filter as df_".$k." on d.id=df_".$k.".deal_id and df_".$k.".filter_group_id = ".$k;
//				$condition.=" and df_".$k.".filter like '%".$filter_req[$k]."%' ";
				$unicode_tags[] = "+".str_to_unicode_string($filter_req[$k]);	
			}	
		}			
		
		if(count($unicode_tags)>0)
		{
				$kw_unicode = implode(" ", $unicode_tags);
				//有筛选
				$condition .=" and (match(d.tag_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
		}
		
		if(intval($_REQUEST['id'])!=0)
		{
			$filter_group = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."filter_group where is_effect = 1 and cate_id in (".implode(",",$ids).") order by sort desc");
			foreach($filter_group as $k=>$v)
			{
				$filter_group[$k]['value'] = $filter_req[$v['id']];
			}
			$GLOBALS['tmpl']->assign("filter_group",$filter_group);
		}
		
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
		if(es_cookie::get("list_type")===null)
			$list_type = app_conf("LIST_TYPE");
		else
			$list_type = intval(es_cookie::get("list_type"));
		$GLOBALS['tmpl']->assign("list_type",$list_type);
		
		
		$kw = addslashes(htmlspecialchars(trim($_REQUEST['keyword'])));
		if($kw!='')
		{
			$GLOBALS['tmpl']->assign('keyword',$kw);
			if($add_title!='')$add_title.="-";
			$add_title.=$kw;

			$kws_div = div_str($kw);
			foreach($kws_div as $k=>$item)
			{
				$kws[$k] = str_to_unicode_string($item);
			}
			$ukeyword = implode(" ",$kws);
			$condition .=" and (match(d.tag_match,d.name_match,d.locate_match,d.shop_cate_match) against('".$ukeyword."' IN BOOLEAN MODE))";
			//$condition.=" and (d.name like '%".$kw."%' or d.sub_name like '%".$kw."%' or d.brief like '%".$kw."%' or d.description like '%".$kw."%')";
		}
		if($add_title!='')$add_title.="-";
		$add_title.=$GLOBALS['lang']['GOODS_SEARCH'];
		$result = search_goods_list($limit,intval($cate_item['id']),$condition,"d.".$sort_field." ".$sort_type,false,$join_str);			
		$GLOBALS['tmpl']->assign("list",$result['list']);
		$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"));   //初始化分页对象 		
		$p  =  $page->show();
		$GLOBALS['tmpl']->assign('pages',$p);
		$GLOBALS['tmpl']->assign("cate_id",$cate_item['id']);
		$GLOBALS['tmpl']->assign("page_title",$add_title.$cate_item['name']);
		$GLOBALS['tmpl']->assign("page_keyword",$add_title.$cate_item['name'].",");
		$GLOBALS['tmpl']->assign("page_description",$add_title.$cate_item['name'].",");		
		
		
		$GLOBALS['tmpl']->assign("filter",true); //显示筛选框
		$GLOBALS['tmpl']->assign("show_list",true); 
		
		
		$GLOBALS['tmpl']->display("ss_index.html");
	}
}
?>