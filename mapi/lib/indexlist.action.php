<?php
//分享列表(从广告入口)
//接口名: indexlist
//参数: 
//id: 广告ID
//type: 广告类型  index/adv
//cid: 标签分类ID
//tag: 查询的标签
//is_hot: 最热:1
//is_new: 最新:1
//page: 当前分页数

class indexlist
{
	public function index()
	{		
		$id = intval($GLOBALS['request']['id']);
		$type = addslashes($GLOBALS['request']['type']);
		$cid = intval($GLOBALS['request']['cid']);
		$keyword = addslashes($GLOBALS['request']['tag']);
		$is_hot = intval($GLOBALS['request']['is_hot']);
		$is_new = intval($GLOBALS['request']['is_new']);
		$page = intval($GLOBALS['request']['page'])>0?intval($GLOBALS['request']['page']):1;

		
			$page_size = 20;
		
			$limit = ($page-1)*$page_size.",".$page_size;			
			$root = array();
			$root['response_code'] = 1;
			$root['tag'] = $tag;
			$root['cid'] = $cid;
			$root['id'] = $id;
			$root['type']  = $type;
			
			$condition = " 1 = 1 ";
			$sort = "";
			if($is_hot>0)
			{
				$condition .= " and t.is_recommend = 1 and t.has_image = 1 ";
				$sort .= " order by  t.fav_count desc,t.relay_count desc,t.reply_count desc,t.id desc  ";
			}
			
			if($is_new>0)
			{
				$condition .= " and t.has_image = 1 ";
				$sort .= " order by t.create_time desc,t.id desc  ";
			}
			if($cid>0)
			{
				$condition .=" and l.cate_id = ".$cid;
			}
			
			if($keyword)
			{			
				$kws_div = div_str($keyword);
				foreach($kws_div as $k=>$item)
				{
					$kw[$k] = str_to_unicode_string($item);
				}
				$ukeyword = implode(" ",$kw);
				$condition.=" and match(t.keyword_match) against('".$ukeyword."'  IN BOOLEAN MODE) ";
			}
			
			$sql = "select distinct(t.id) from ".DB_PREFIX."topic as t left join ".DB_PREFIX."topic_cate_link as l on l.topic_id = t.id where ".$condition.$sort." limit ".$limit;
			$sql_total = "select count(distinct(t.id)) from ".DB_PREFIX."topic as t left join ".DB_PREFIX."topic_cate_link as l on l.topic_id = t.id where ".$condition;
			
			
			$total = $GLOBALS['db']->getOne($sql_total);		
			$result = $GLOBALS['db']->getAll($sql);
	
			
			$share_list =array();
			foreach($result as $k=>$v)
			{
				$share_list[$k]['share_id'] = $v['id'];
				$image = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_image where topic_id = ".$v['id']." limit 1");
				$share_list[$k]['img'] = get_abs_img_root(get_spec_image($image['o_path'],200,0,0));			
				$share_list[$k]['height'] = floor($image['height'] * (200 / $image['width']));			
			}
			$root['item'] = $share_list;
			
			//分页
			$page_info['page'] = $page;
			$page_info['page_total'] = ceil($total/$page_size);
			$root['page'] = $page_info;
			
			
			switch ($type)
			{
				case "index":
					$data_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."m_index where id = ".$id." and city_id in (0,".intval($GLOBALS['city_id']).") and status = 1");
					break;
				case "adv":
					$data_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."m_adv where id = ".$id." and city_id in (0,".intval($GLOBALS['city_id']).") and status = 1");
					break;
			}
			if($data_item)
			{
				$data_item['data'] = unserialize($data_item['data']);
				foreach($data_item['data']['tags'] as $tag_item)
				$root['tags'][]['name'] = $tag_item;
			}

		output($root);
		
	}
}
?>