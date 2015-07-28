<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/deal.php';
class scoreModule extends SiteBaseModule
{
	public function index(){
		$GLOBALS['tmpl']->caching = true;
		$GLOBALS['tmpl']->cache_lifetime = 60;  //首页缓存10分钟
		$field = es_cookie::get("shop_sort_field"); 
		$field_sort = es_cookie::get("shop_sort_type"); 
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.implode(",",$_REQUEST).$field.$field_sort);	
		if (!$GLOBALS['tmpl']->is_cached("page/score.html", $cache_id))
		{	
			require APP_ROOT_PATH.'app/Lib/page.php';
			
			$cates = intval($_REQUEST['cates']);
			$GLOBALS['tmpl']->assign("cates",$cates);
			
			$integral = intval($_REQUEST['integral']);
			$GLOBALS['tmpl']->assign("integral",$integral);
			
			$sort = intval($_REQUEST['sort']);   //1.最新  2.热门  3.积分
			$GLOBALS['tmpl']->assign("sort",$sort);
			
		
			//输出投标列表
			$page = intval($_REQUEST['p']);
			if($page==0)
				$page = 1;
			$limit = (($page-1)*app_conf("DEAL_PAGE_SIZE")).",".app_conf("DEAL_PAGE_SIZE"); 
			$condition = " 1=1";
		    if($sort == 1){
				$condition .= " AND is_new = 1";
			}elseif($sort == 2)
			{
				$condition .= " AND is_hot = 1 ";
			}elseif ($sort == 3)
			{
				$orderby = " score desc";
			}
			
			if($cates>0){
				$cates_id = $GLOBALS['db']->getAll("select id from ".DB_PREFIX."goods_cate where pid = ".$cates);
				$flatmap = array_map("array_pop",$cates_id);
				$cates_ids=implode(',',$flatmap);
				if($cates_ids=="") 
				{
					$condition .= " AND cate_id in (".$cates.") ";
				}else{
					$condition .= " AND cate_id in (".$cates.",".$cates_ids.") ";
				}
				
			}
			
			if($integral==0){
				$condition .= "";
			}elseif ($integral==1){
				$condition .= " AND score  <= 500";
			}elseif ($integral==2){
				$condition .= " AND score  between 500 and 1000";
			}elseif ($integral==3){
				$condition .= " AND score  between 1000 and 3000";
			}elseif ($integral==4){
				$condition .= " AND score  between 3000 and 5000";
			}else{
				$condition .= " AND score  >= 5000";
			}
			
			$result = get_goods_list($limit,$condition,$orderby);
			$GLOBALS['tmpl']->assign("goods_list",$result['list']);
			$page_args['cates'] =  $cates;
			$page_args['integral'] =  $integral;
			$page_args['sort'] =  $sort;
			
			//商品类别
			$cates_urls =load_auto_cache("score_cates");
			
			//$cates_urls = $GLOBALS['db']->getAll("SELECT id,name FROM ".DB_PREFIX."goods_cate WHERE is_effect=1 and is_delete = 0 and pid= 0");	
			$cates_url = array();
			
			$cates_url[0]['id'] = 0;
			$cates_url[0]['name'] = "不限";
			$tmp_args = $page_args;
			$tmp_args['cates'] = 0;
			$cates_url[0]['url'] = url("index","score#index",$tmp_args);
			
			
			foreach($cates_urls as $k=>$v){
				$cates_url[$k+1]['id'] = $v['id'];
				$cates_url[$k+1]['name'] = $v['name'];
				$tmp_args = $page_args;
				$tmp_args['cates'] = $v['id'];
				$cates_url[$k+1]['url'] = url("index","score#index",$tmp_args);
			}
			$GLOBALS['tmpl']->assign('cates_url',$cates_url);
			
			//积分范围
			$integral_url = array(
					array(
							"name" => "不限",
					),
					array(
							"name" => "500积分以下",
					),
					array(
							"name" => "500-1000积分",
					),
					array(
							"name" => "1000-3000积分",
					),
					array(
							"name" => "3000-5000积分",
					),
					array(
							"name" => "5000积分以上",
					),
			);
			foreach($integral_url as $k=>$v){
				$tmp_args = $page_args;
				$tmp_args['integral'] = $k;
				$integral_url[$k]['url'] = url("index","score#index",$tmp_args);
			}
			$GLOBALS['tmpl']->assign('integral_url',$integral_url);
			
			//排序
			$sort_url = array(
					array(
							"name" => "默认排序",
					),
					array(
							"name" => "最新",
					),
					array(
							"name" => "热门",
					),
					array(
							"name" => "积分",
					),
			);
			foreach($sort_url as $k=>$v){
				$tmp_args = $page_args;
				$tmp_args['sort'] = $k;
				$sort_url[$k]['url'] = url("index","score#index",$tmp_args);
			}
			$GLOBALS['tmpl']->assign('sort_url',$sort_url);
			
			
			$page_pram = "";
			foreach($page_args as $k=>$v){
				$page_pram .="&".$k."=".$v;
			}
			
			$page = new Page($result['count'],app_conf("DEAL_PAGE_SIZE"),$page_pram);   //初始化分页对象 		
			$p  =  $page->show();
			$GLOBALS['tmpl']->assign('pages',$p);
			
			$GLOBALS['tmpl']->assign("page_args",$page_args);
			
			$GLOBALS['tmpl']->assign("field",$field); //??
			$GLOBALS['tmpl']->assign("field_sort",$field_sort); //??
		}
		
		$GLOBALS['tmpl']->display("page/score.html",$cache_id);
	}
}
?>
