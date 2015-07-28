<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class GoodsAction extends CommonAction{
	public function index()
	{	
		$this->assign("default_map",$condition);
		
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
		$map = array_merge($map,$this->get("default_map"));
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		$model = D ("Goods");
		
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		foreach($list as $k=>$v)
		{
			$list[$k]['cate_name'] = M("Goods_cate")->where("id=".$list[$k]['cate_id'])->getField("name");  
			if ($list[$k]['is_delivery']){$list[$k]['is_delivery_format'] = "是"; }else{$list[$k]['is_delivery_format'] = "否";}
			if ($list[$k]['is_hot']){$list[$k]['is_hot_format'] = "是"; }else{$list[$k]['is_hot_format'] = "否";}
			if ($list[$k]['is_new']){$list[$k]['is_new_format'] = "是"; }else{$list[$k]['is_new_format'] = "否";}
			if ($list[$k]['is_recommend']){$list[$k]['is_recommend_format'] = "是"; }else{$list[$k]['is_recommend_format'] = "否";}
		}
		//dump($result);exit;
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	public function goods_cate()
	{
		$condition['is_delete'] = 0;
		$condition['pid'] = 0;
		$this->assign("default_map",$condition);
	
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		//追加默认参数
		if($this->get("default_map"))
			$map = array_merge($map,$this->get("default_map"));
	
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$list = $this->get("list");
		print_r($list);
		$result = array();
		$row = 0;
		foreach($list as $k=>$v)
		{
			$v['level'] = -1;
			$v['name'] = $v['name'];
			$result[$row] = $v;
			$row++;
			$sub_cate = M(MODULE_NAME)->where(array("id"=>array("in",D(MODULE_NAME)->getChildIds($v['id'])),'is_delete'=>0))->findAll();
			$sub_cate = D(MODULE_NAME)->toFormatTree($sub_cate,'name');
			foreach($sub_cate as $kk=>$vv)
			{
				$vv['name']	=	$vv['title_show'];
				$result[$row] = $vv;
				$row++;
			}
		}
		//dump($result);exit;
		$this->assign("list",$result);
		$this->display ();
		return;
	}
	

	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		
		$condition['id'] = $id;
		$vo = M("Goods")->where($condition)->find();
		$this->assign ( 'vo', $vo );
		//商品分类
		$cate = M("GoodsCate")->where(' is_delete= 0 and is_effect=1 ')->findAll();
		$this->assign ( 'cate', $cate );
		
		//商品类型
		$goods_type_list = M("GoodsType")->where(' is_effect=1 ')->findAll();
		$this->assign ( 'goods_type_list', $goods_type_list );
	
		//输出规格库存的配置
		$attr_stock = M("GoodsAttrStock")->where("goods_id=".intval($vo['id']))->order("id asc")->findAll();
	
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
		
		$this->assign("attr_cfg_json",$attr_cfg_json);
		$this->assign("attr_stock_json",$attr_stock_json);
		//goods_type_attr
		$this->display ();
		
	}
	
	public function attr_html()
	{
		$goods_type_id = intval($_REQUEST['goods_type_id']);
		$goods_id = intval($_REQUEST['goods_id']);
		
		if( $goods_id>0 && M("Goods")->where("id=".$goods_id)->getField("goods_type_id")==$goods_type_id)
		{
			
			$goods_type_attr = M()->query("select a.name as attr_name,a.is_checked as is_checked,a.score,b.*
					from ".conf("DB_PREFIX")."goods_attr as a
					left join ".conf("DB_PREFIX")."goods_type_attr as b on a.goods_type_attr_id = b.id
					where a.goods_id=".$goods_id." order by a.id asc");
			
			$goods_type_attr_id = 0;
			if($goods_type_attr)
			{
				foreach($goods_type_attr as $k=>$v)
				{
					if($goods_type_attr_id!=$v['id'])
					{
						$goods_type_attr[$k]['is_first'] = 1;
					}
					else
					{
						$goods_type_attr[$k]['is_first'] = 0;
					}
					$goods_type_attr_id = $v['id'];
				}	
			}
			else 
			{
				$goods_type_attr = M("GoodsTypeAttr")->where("goods_type_id=".$goods_type_id)->findAll();
				foreach($goods_type_attr as $k=>$v)
				{
					$goods_type_attr[$k]['is_first'] = 1;
				}
			}
		}
		else
		{
			$goods_type_attr = M("GoodsTypeAttr")->where("goods_type_id=".$goods_type_id)->findAll();
			foreach($goods_type_attr as $k=>$v)
			{
				$goods_type_attr[$k]['is_first'] = 1;
			}	
		}
	
		$this->assign("goods_type_attr",$goods_type_attr);
		$this->display();
	}
	
	
	public function update()
	{
		$data = M("Goods")->create ();
		
		// 更新数据
		$list=M("Goods")->save ($data);
		if (false !== $list) {
			M("GoodsAttr")->where("goods_id=".$data['id'])->delete();
			M("GoodsAttrStock")->where("goods_id=".$data['id'])->delete();
			
			if($data['goods_type_id'] > 0){
				$goods_attr = $_REQUEST['goods_attr'];
				$goods_attr_score = $_REQUEST['goods_attr_score'];
				$goods_attr_stock_hd = $_REQUEST['goods_attr_stock_hd'];
				
				foreach($goods_attr as $goods_type_attr_id=>$arr)
				{
					foreach($arr as $k=>$v)
					{
						if($v!='')
						{
							$deal_attr_item['goods_id'] = $data['id'];
							$deal_attr_item['goods_type_attr_id'] = $goods_type_attr_id;
							$deal_attr_item['name'] = $v;
							$deal_attr_item['score'] = $goods_attr_score[$goods_type_attr_id][$k];
							$deal_attr_item['is_checked'] = intval($goods_attr_stock_hd[$goods_type_attr_id][$k]);
							M("GoodsAttr")->add($deal_attr_item);
						}
					}
				}
				
				//开始创建属性库存
				$stock_cfg = $_REQUEST['stock_cfg_num'];
				$attr_cfg = $_REQUEST['stock_attr'];
				$attr_str = $_REQUEST['stock_cfg'];
				foreach($stock_cfg as $row=>$v)
				{
					$stock_data = array();
					$stock_data['goods_id'] = $data['id'];
					$stock_data['stock_cfg'] = $v;
					$stock_data['attr_str'] = $attr_str[$row];
					$attr_cfg_data = array();
					foreach($attr_cfg as $attr_id=>$cfg)
					{
						$attr_cfg_data[$attr_id] = $cfg[$row];
					}
					$stock_data['attr_cfg'] = serialize($attr_cfg_data);
					
					M("GoodsAttrStock")->add($stock_data);
				}
			}
			//成功提示
			save_log($data['name'].L("UPDATE_SUCCESS"),1);
			$this->success(L("UPDATE_SUCCESS"));
		} else {
			//错误提示
			save_log($data['name'].L("UPDATE_FAILED"),0);
			$this->error(L("UPDATE_FAILED"),0,$data['name'].L("UPDATE_FAILED"));
		}
		
	
	}
	
	
	
	public function delete(){
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
			$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
			//删除的验证
			$list = M("Goods")->where ( $condition )->delete();
			if ($list!==false) {
				save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
				$this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
			} else {
				save_log($info.l("FOREVER_DELETE_FAILED"),0);
				$this->error (l("FOREVER_DELETE_FAILED"),$ajax);
			}
		} else {
			$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function add() {
		
		//商品分类
		$cate = M("GoodsCate")->where(' is_delete= 0 and is_effect=1 ')->findAll();
		$this->assign ( 'cate', $cate );
		
		//商品类型
		$goods_type_list = M("GoodsType")->where(' is_effect=1 ')->findAll();
		$this->assign ( 'goods_type_list', $goods_type_list );
		
		$this->display ();
	}
	
	public function insert()
	{
		
		$data = M("Goods")->create ();
		
		// 更新数据
		
		$list = M("Goods")->add ($data); 
		$goods_id = $list;
		
		if (false !== $list){
		
			if($data['goods_type_id'] > 0){
				//开始处理属性
				$deal_attr = $_REQUEST['goods_attr'];
				$goods_attr_score = $_REQUEST['goods_attr_score'];	
				$goods_attr_stock_hd = $_REQUEST['goods_attr_stock_hd'];			
				
				foreach($deal_attr as $goods_type_attr_id=>$arr)
				{
					foreach($arr as $k=>$v)
					{
						if($v!='')
						{
							$deal_attr_item['goods_id'] = $list;
							$deal_attr_item['goods_type_attr_id'] = $goods_type_attr_id;
							$deal_attr_item['name'] = $v;
							$deal_attr_item['score'] = $goods_attr_score[$goods_type_attr_id][$k];
							$deal_attr_item['is_checked'] = intval($goods_attr_stock_hd[$goods_type_attr_id][$k]);
							M("GoodsAttr")->add($deal_attr_item);
						}
					}
				}
				
				//开始创建属性库存
				$stock_cfg = $_REQUEST['stock_cfg_num']; //库存数量
				$attr_cfg = $_REQUEST['stock_attr']; 	//库存属性
				$attr_str = $_REQUEST['stock_cfg'];
				foreach($stock_cfg as $row=>$v)
				{
					$stock_data = array();
					$stock_data['goods_id'] = $list;
					$stock_data['stock_cfg'] = $v;
					$stock_data['attr_str'] = $attr_str[$row];
					$attr_cfg_data = array();
					foreach($attr_cfg as $attr_id=>$cfg)
					{
						$attr_cfg_data[$attr_id] = $cfg[$row];
					}
					$stock_data['attr_cfg'] = serialize($attr_cfg_data);
					M("GoodsAttrStock")->add($stock_data);
				}
				
				M("GoodsAttr")->add ($data);
			}
			
			//错误提示
			$dbErr = M()->getDbError();
			$this->success ("添加成功");
		}else{
			$this->error(L("INSERT_FAILED"));
		}
	}
	
}
?>