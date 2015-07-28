<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

$lang = array(
	'name'	=>	'方维贷款内部数据分享接口',
	'show_name'	=>	'站内分享',
);
$config = array(

);

/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Fanwe';

    /* 名称 */
    $module['name']    = $lang['name'];
    $module['show_name']    = $lang['show_name'];
    


	$module['lang'] = $config;
    $module['lang'] = $lang;
    return $module;
}

// 方维oso内部数据分享接口
require_once(APP_ROOT_PATH.'system/libs/fetch_topic.php');
class Fanwe_fetch_topic implements fetch_topic {
	
	//通过url解析并生成相应的序列化内容
	// 返回: "status"=>"","info"=>"","group_data"=>"","content"=>"","tags"=>"","images"=>array("id"=>"","url"=>"")
	public function fetch($url_str)
	{
		//tuan.php?ctl=deal&id=39 | tuan/deal/id-39 //团购
		//shop.php?ctl=goods&id=48 | goods/id-48  //商品
		//youhui.php?ctl=ydetail&id=53|youhui/ydetail/id-53  //优惠
		//youhui.php?ctl=fdetail&id=15 | youhui/fdetail/id-15  //免费优惠
		//youhui.php?ctl=edetail&id=1 | youhui/edetail/id-1  //活动
		
		$preg[] = $preg_tuan_o = "/tuan\.php\?ctl=(deal)\&id=(\w+)/i";
		$preg[] = $preg_tuan_r = "/tuan\/(deal)\/id\-(\w+)/i";
		
		$preg[] = $preg_shop_o = "/shop\.php\?ctl=(goods)\&id=(\w+)/i";
		$preg[] = $preg_shop_o2 = "/index\.php\?ctl=(goods)\&id=(\w+)/i";
		$preg[] = $preg_shop_r = "/(goods)\/id\-(\w+)/i";
		
		$preg[] = $preg_fyouhui_o = "/store\.php\?ctl=(fdetail)\&id=(\d+)/i";
		$preg[] = $preg_fyouhui_r = "/store\/(fdetail)\/id\-(\d+)/i";
		
		$preg[] = $preg_byouhui_o = "/store\.php\?ctl=(ydetail)\&id=(\d+)/i";
		$preg[] = $preg_byouhui_r = "/store\/(ydetail)\/id\-(\d+)/i";
		
		$preg[] = $preg_event_o = "/store\.php\?ctl=(edetail)\&id=(\d+)/i";
		$preg[] = $preg_event_r = "/store\/(edetail)\/id\-(\d+)/i";
		
		$is_match = false;
		foreach($preg as $preg_item)
		{
			if(preg_match_all($preg_item,$url_str,$matches))
			{
				$is_match = true;
				break;
			}
		}
		
		if($is_match)
		{
			$ctl = $matches[1][0];
			$data_id = trim(addslashes($matches[2][0]));
			switch($ctl)
			{
				case "deal":  //团购分享
					$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where (uname = '".$data_id."' or id = ".$data_id.") and is_effect = 1 and is_delete = 0");
					if($deal)
					{
						$result['status'] = 1;
						$result['content'] = "团购推荐:".$deal['sub_name'];
						if($deal['brief'])
						$result['content'].="[".$deal['brief']."]";		
						$result['type'] = "sharetuan";
						require_once APP_ROOT_PATH."system/libs/words.php";
						$tags = words::segment($deal['name']);						
						$result['tags'] = implode(" ",$tags);						
						$group_data['url']['app_index'] =  "tuan";
						$group_data['url']['route'] =  "deal";
						$group_data['url']['param'] =  "id=".$data_id;
						$group_data['data'] = $deal;					
						$result['group_data'] =  base64_encode(serialize($group_data));
						$deal_gallery = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id = ".$deal['id']." order by sort asc");
						foreach($deal_gallery as $row)
						{
							$result['images'][] = syn_image_to_topic($row['img']);
						}
					}
					else
					{
						$result['status'] = 0;
						$result['info'] = "团购商品不存在";
					}
					break;
				case "goods":
					$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where (uname = '".$data_id."' or id = ".$data_id.") and is_effect = 1 and is_delete = 0");
					if($deal)
					{
						$result['status'] = 1;
						$result['content'] = "商品推荐:".$deal['sub_name'];
						if($deal['brief'])
						$result['content'].="[".$deal['brief']."]";		
						$result['type'] = "sharegoods";
						require_once APP_ROOT_PATH."system/libs/words.php";
						$tags = words::segment($deal['name']);						
						$result['tags'] = implode(" ",$tags);						
						$group_data['url']['app_index'] =  "shop";
						$group_data['url']['route'] =  "goods";
						$group_data['url']['param'] =  "id=".$data_id;
						$group_data['data'] = $deal;					
						$result['group_data'] =  base64_encode(serialize($group_data));
						$deal_gallery = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id = ".$deal['id']." order by sort asc");
						foreach($deal_gallery as $row)
						{
							$result['images'][] = syn_image_to_topic($row['img']);
						}
					}
					else
					{
						$result['status'] = 0;
						$result['info'] = "商品不存在";
					}
					break;
				case "fdetail":
					$youhui = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."youhui where id=".intval($data_id)." and is_effect = 1");
					if($youhui)
					{
						$result['status'] = 1;
						$result['content'] = "优惠券推荐:".$youhui['name'];
						$result['type'] = "sharefyouhui";
						require_once APP_ROOT_PATH."system/libs/words.php";
						$tags = words::segment($youhui['name']);						
						$result['tags'] = implode(" ",$tags);						
						$group_data['url']['app_index'] =  "store";
						$group_data['url']['route'] =  "fdetail";
						$group_data['url']['param'] =  "id=".$data_id;
						$group_data['data'] = $youhui;
						$result['group_data'] =  base64_encode(serialize($group_data));
						$result['images'][] = syn_image_to_topic($youhui['icon']);						
					}
					else
					{
						$result['status'] = 0;
						$result['info'] = "优惠券不存在";
					}
					break;
				case "ydetail":
					$deal = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where (uname = '".$data_id."' or id = ".$data_id.") and is_effect = 1 and is_delete = 0");
					if($deal)
					{
						$result['status'] = 1;
						$result['content'] = "代金券推荐:".$deal['sub_name'];
						if($deal['brief'])
						$result['content'].="[".$deal['brief']."]";		
						$result['type'] = "sharebyouhui";
						require_once APP_ROOT_PATH."system/libs/words.php";
						$tags = words::segment($deal['name']);						
						$result['tags'] = implode(" ",$tags);						
						$group_data['url']['app_index'] =  "store";
						$group_data['url']['route'] =  "ydetail";
						$group_data['url']['param'] =  "id=".$data_id;
						$group_data['data'] = $deal;					
						$result['group_data'] =  base64_encode(serialize($group_data));
						$deal_gallery = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id = ".$deal['id']." order by sort asc");
						foreach($deal_gallery as $row)
						{
							$result['images'][] = syn_image_to_topic($row['img']);
						}
					}
					else
					{
						$result['status'] = 0;
						$result['info'] = "代金券不存在";
					}
					break;
				case "edetail":
					$event = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."event where id=".intval($data_id)." and is_effect = 1");
					if($event)
					{
						$result['status'] = 1;
						$result['content'] = "活动推荐:".$event['name'];
						$result['type'] = "shareevent";
						require_once APP_ROOT_PATH."system/libs/words.php";
						$tags = words::segment($event['name']);						
						$result['tags'] = implode(" ",$tags);						
						$group_data['url']['app_index'] =  "store";
						$group_data['url']['route'] =  "edetail";
						$group_data['url']['param'] =  "id=".$data_id;
						$group_data['data'] = $event;
						$result['group_data'] =  base64_encode(serialize($group_data));
						$result['images'][] = syn_image_to_topic($event['icon']);						
					}
					else
					{
						$result['status'] = 0;
						$result['info'] = "活动不存在";
					}
					break;
				default:
					$result['status'] = 0;
					$result['info'] = "URL地址错误，该地址的数据无法分享"; 
					break;
			}
		}
		else
		{
			$result['status'] = 0;
			$result['info'] = "URL地址错误，该地址的数据无法分享"; 
		}
//		$result['status'] = 0;
//		$result['info'] = print_r($matches,1);
//		$result['group_data'] = "";
//		$result['content'] = "你好";
//		$result['tags'] = "好 一般 好不好";
//		$result['images'] = array(array("id"=>108,"url"=>"./public/comment/201202/04/16/4c5971b0370e739c71ea9d0f5e2e35e257_100x100.jpg"));
		
		return $result;
	}
	
	public function decode($topic)
	{
		$group_data =  unserialize(base64_decode($topic['group_data']));
		$url_tag = "u:".$group_data['url']['app_index']."|".$group_data['url']['route']."|".$group_data['url']['param'];		
		$topic['content'].= " - <a href='".parse_url_tag($url_tag)."' target='_blank' style='color:#f30;'>[去看看]</a>";
		return $topic;
	}
	
	//返回 $topic['content'] $topic['img']
	public function decode_weibo($topic)
	{
		$data['content'] = $topic['content'];
		$data['content'] = msubstr($data['content'],0,120);
		$group_data =  unserialize(base64_decode($topic['group_data']));
		$url_tag = "u:".$group_data['url']['app_index']."|".$group_data['url']['route']."|".$group_data['url']['param'];
		$url = SITE_DOMAIN.parse_url_tag($url_tag);
		$data['content'].=" ".$url;  //内容
		
		//图片
		$topic_image = $GLOBALS['db']->getRow("select o_path from ".DB_PREFIX."topic_image where topic_id = ".$topic['id']);
		if($topic_image)
		$data['img'] = SITE_DOMAIN.APP_ROOT."/".$topic_image['o_path'];
		
		return $data;
	}
	public function decode_mobile($topic)
	{
		$topic['group_data'] = unserialize(base64_decode($topic['group_data']));
		return $topic;
	}
}
?>