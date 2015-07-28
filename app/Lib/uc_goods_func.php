<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
/**
 * 查询某件商品信息及用户可购买数量
 * @param 商品编号 $id
 * @param 会员编号 $user_id
 */
function get_goods_info($id,$user_id)
{
	$id = intval($id);
	$user_id = intval($user_id);
	$sql = "SELECT g.*,gc.name as cate_name from ".DB_PREFIX."goods g LEFT JOIN ".DB_PREFIX."goods_cate gc ON g.cate_id = gc.id where g.id = ".$id ;
	$goods = $GLOBALS['db']->getRow($sql);
	
	$goods['max_bought_format'] = $goods['max_bought'] - $goods['buy_number'];
	
	//如果有限制购买数量
	if($goods['user_max_bought'] > 0 ){
		$goods['user_can_buy_number'] = $goods['user_max_bought'];
		if($GLOBALS['user_info']){
			$goods['user_can_buy_number'] = $goods['user_max_bought'] - $GLOBALS['db']->getOne("SELECT sum(number) WHERE goods_id=".$id." AND user_id=".$user_id);
			if($goods['user_can_buy_number'] > $goods['max_bought_format']){
				$goods['user_can_buy_number'] = $goods['max_bought_format'];
			}
		}
	}
	else{
		$goods['user_can_buy_number'] = $goods['max_bought_format'];
	}
	
	
	return $goods;
}

/**
 * 查询某件商品的商品属性及库存
 * @param 商品编号 $id
 * @param 商品类型编号 $goods_type_id
 */
function get_goods_attr($goods_type_id,$id)
{
	$has_stock = 0;
	$goods_type_attr = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."goods_type_attr where  goods_type_id = ".$goods_type_id,false);
	$tmp_goods_attr =  $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_attr where  goods_id = ".$id);
	$goods_attr= array();
	foreach($tmp_goods_attr as $k=>$v){
		$goods_attr[$v['goods_type_attr_id']][] = $v;
	}
		
	foreach ($goods_type_attr as $k=>$v)
	{
		if(!$goods_attr[$v['id']]){
			unset($goods_type_attr[$k]);
		}
		else{
			$goods_type_attr[$k]['list'] = $goods_attr[$v['id']];
			foreach ($goods_type_attr[$k]['list'] as $kk=>$vv)
			{
				$goods_type_attr[$k]['list'][$kk]['score']=intval($vv['score']);
				if(intval($vv['is_checked']) == 1){
					$has_stock = 1;
				}
			}
		}
	}
		
	if($has_stock == 1){
		//库存
		$attr_stock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_attr_stock where  goods_id = ".$id);
		foreach($attr_stock as $k=>$v){
			$attr_stock[$k]['attr_cfg'] = unserialize($v['attr_cfg']);
		}
	}
	$result['goods_type_attr'] = $goods_type_attr;  //商品属性
	$result['has_stock'] = $has_stock; //是否有库存
	$result['attr_stock'] = $attr_stock;  //某种属性类型的库存
	return $result;
}

/**
 * 判断是否有该属性库存
 */
function get_attr_stock($goods,$number,$goods_id,$attr)
{
	$goods_attr_scoresum = 0;
	$attr_str = "";
	//判断是否选择了属性
	if($goods['goods_type_id'] > 0){
		$goods_attr =  $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_attr where  goods_id = ".$goods_id);
			
		if($goods_attr && !$attr){
			$result['info']="请选择属性";
			$result['status'] = 0;
			return $result;
		}
			
		//库存
		$attr_stock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."goods_attr_stock where  goods_id = ".$goods_id);
		$temp_attr_stock = array();
		foreach($attr_stock as $k=>$v){
			$temp_attr_stock[$v['attr_str']] = $v;
		}
			
		$temp_attr_list = $GLOBALS['db']->getAll("select *  from ".DB_PREFIX."goods_attr where  goods_id = ".$goods_id);
			
		//用户所选择的该商品的属性列表
		$attr_list = array();
		foreach($temp_attr_list as $k=>$v){
			$attr_list[$v['goods_type_attr_id']][$v['id']] = $v;
		}
		foreach($attr as $k=>$v){
			$attr_str .= $attr_list[$k][$v]['name'];
			$goods_attr_scoresum +=$attr_list[$k][$v]['score'];
		}
			
			
		if(isset($temp_attr_stock[$attr_str])){
			if($number > (intval($temp_attr_stock[$attr_str]['stock_cfg']) - intval($temp_attr_stock[$attr_str]['buy_count']))){
				$result['info']="该商品属性库存不足，无法兑换，请重新选择";
				$result['status'] = 0;
				return $result;
			}
		}
		
		$result['status'] = 1;
		$result['temp_attr_stock'] = $temp_attr_stock;
		$result['attr_str'] = $attr_str;
		
	}
	elseif($number > $goods['max_bought_format']){
		$result['info']="库存不足";
		$result['status'] = 0;
		
	}
	//return $result;
	return $result;
}

/**
 * 获取订单列表
 */
function get_order($limit,$user_id,$condition){
	$count_sql = "SELECT count(*) from ".DB_PREFIX."goods_order go where $condition and go.user_id = ".$user_id;
	$count = $GLOBALS['db']->getOne($count_sql);
	
	if($count > 0){
		$sql = "SELECT go.* from ".DB_PREFIX."goods_order go where $condition and go.user_id = ".$user_id."  order by go.id  DESC LIMIT ".$limit;
	
		$order_info = $GLOBALS['db']->getAll($sql);
		$attr_str = "";
		foreach($order_info as $k=>$v){
			$order_info[$k]['attr_format'] = unserialize($v['attr']);
			foreach($order_info[$k]['attr_format'] as $kk=>$vv){
				$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_type_attr where id =".$kk );
				$attr_str .=":";
				$attr_str .= $GLOBALS['db']->getOne("select name from ".DB_PREFIX."goods_attr where id =".$vv );
				$attr_str .="  ";
			}
			$order_info[$k]['attr_format'] = $attr_str;
			$attr_str = "";
			if($order_info[$k]['is_delivery'] == 0)
			{	$order_info[$k]['is_delivery_format'] = "否";}
			else{
				$order_info[$k]['is_delivery_format'] = "是";
			}
			if($order_info[$k]['order_status'] == 0){
				$order_info[$k]['order_status_format'] = "未发货";
			}elseif($order_info[$k]['order_status'] == 1){
				$order_info[$k]['order_status_format'] = "已发货";
			}elseif($order_info[$k]['order_status'] == 2){
				$order_info[$k]['order_status_format'] = "无效订单";
			}elseif($order_info[$k]['order_status'] == 3){
				$order_info[$k]['order_status_format'] = "用户取消";
			}
	
	
			$a=get_goods($order_info[$k]['goods_id']);
			$order_info[$k]['img'] = $a['img'];
			$order_info[$k]['ex_time'] = to_date( $v['ex_time'],"Y-m-d H:i:s");
			$order_info[$k]['delivery_time'] = to_date( $v['delivery_time'],"Y-m-d H:i:s");
		}
	}
	
	return array("list"=>$order_info,'count'=>$count);
}
	
?>