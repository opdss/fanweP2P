<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------

class DealOrderAction extends CommonAction{
	public function incharge_index()
	{
		$reminder = M("RemindCount")->find();
		$reminder['incharge_count_time'] = TIME_UTC;
		M("RemindCount")->save($reminder);
		
		$condition['is_delete'] = 0;
		$condition['type'] = 1;
		if(trim($_REQUEST['user_name'])!='')
		{		
			$ids = M("User")->where(array("user_name"=>array('like','%'.trim($_REQUEST['user_name']).'%')))->field("id")->findAll();
			$ids_arr = array();
			foreach($ids as $k=>$v)
			{
				array_push($ids_arr,$v['id']);
			}	
			$condition['user_id'] = array("in",$ids_arr);
		}
		
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $condition );
		}
		$name=$this->getActionName();
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $condition );
		}
		
		$this->display ();
		
	}
	public function incharge_trash()
	{
		$condition['is_delete'] = 1;
		$condition['type'] = 1;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function deal_index()
	{
		$reminder = M("RemindCount")->find();
		$reminder['order_count_time'] = TIME_UTC;
		$reminder['refund_count_time'] = TIME_UTC;
		$reminder['retake_count_time'] = TIME_UTC;
		M("RemindCount")->save($reminder);
		
		//处理-1情况的select
		if(!isset($_REQUEST['pay_status']))
		{
			$_REQUEST['pay_status'] = -1;
		}
		if(!isset($_REQUEST['delivery_status']))
		{
			$_REQUEST['delivery_status'] = -1;
		}
		if(!isset($_REQUEST['extra_status']))
		{
			$_REQUEST['extra_status'] = -1;
		}
		if(!isset($_REQUEST['after_sale']))
		{
			$_REQUEST['after_sale'] = -1;
		}
		
		
		$where = " 1=1 ";
		if(intval($_REQUEST['id'])>0)
		$where .= " and id = ".intval($_REQUEST['id']);
		//定义条件
		if(isset($_REQUEST['referer'])&&trim($_REQUEST['referer'])!='')
		{
			$where.=" and ".DB_PREFIX."deal_order.referer = '".trim($_REQUEST['referer'])."'";
		}
		if(trim($_REQUEST['user_name'])!='')
		$where.=" and ".DB_PREFIX."deal_order.user_name like '%".trim($_REQUEST['user_name'])."%'";
		if(intval($_REQUEST['deal_id'])>0)		
		$where.=" and (".DB_PREFIX."deal_order.deal_ids = ".intval($_REQUEST['deal_id'])." or deal_ids like '%".intval($_REQUEST['deal_id']).",%' or deal_ids like '%,".intval($_REQUEST['deal_id'])."' or deal_ids like '%,".intval($_REQUEST['deal_id']).",%')";
		
		
		$where.= " and ".DB_PREFIX."deal_order.is_delete = 0 ";
		$where.= " and ".DB_PREFIX."deal_order.type = 0 ";

		if(trim($_REQUEST['order_sn'])!='')
		{
			$where.= " and ".DB_PREFIX."deal_order.order_sn like '%".trim($_REQUEST['order_sn'])."%' ";
		}
		if(intval($_REQUEST['pay_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.pay_status = ".intval($_REQUEST['pay_status']);
		}
		if(intval($_REQUEST['delivery_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.delivery_status = ".intval($_REQUEST['delivery_status']);
		}
		if(intval($_REQUEST['extra_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.extra_status = ".intval($_REQUEST['extra_status']);
		}
		if(intval($_REQUEST['after_sale'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.after_sale = ".intval($_REQUEST['after_sale']);
		}

	
		
		//关于列表数据的输出
		if (isset ( $_REQUEST ['_order'] )) {
			$order = DB_PREFIX.'deal_order.'.$_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : DB_PREFIX.'deal_order.id';
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		
		
		
		$count = M("DealOrder")
				->where($where)
				->count();
		
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据

			$voList = M("DealOrder")
				->where($where)				
				->field(DB_PREFIX.'deal_order.*')
				->order( $order ." ". $sort)
				->limit($p->firstRow . ',' . $p->listRows)->findAll ( );
			

			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $_REQUEST ['_order']?$_REQUEST ['_order']:'id' );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		
		
		//输出快递接口
		$express_list = M("Express")->where("is_effect = 1")->findAll();
		$this->assign("express_list",$express_list);
		//end 
		$this->display ();
		return;
	}
	
	
	public function export_csv($page = 1)
	{
		set_time_limit(0);
		$limit = (($page - 1)*intval(app_conf("BATCH_PAGE_SIZE"))).",".(intval(app_conf("BATCH_PAGE_SIZE")));
		
		//处理-1情况的select
		if(!isset($_REQUEST['pay_status']))
		{
			$_REQUEST['pay_status'] = -1;
		}
		if(!isset($_REQUEST['delivery_status']))
		{
			$_REQUEST['delivery_status'] = -1;
		}
		if(!isset($_REQUEST['extra_status']))
		{
			$_REQUEST['extra_status'] = -1;
		}
		if(!isset($_REQUEST['after_sale']))
		{
			$_REQUEST['after_sale'] = -1;
		}
		
		$where = " 1=1 ";
		//定义条件
		if(isset($_REQUEST['referer'])&&trim($_REQUEST['referer'])!='')
		{
			$where.=" and ".DB_PREFIX."deal_order.referer = '".trim($_REQUEST['referer'])."'";
		}
		if(trim($_REQUEST['user_name'])!='')
		$where.=" and ".DB_PREFIX."deal_order.user_name like '%".trim($_REQUEST['user_name'])."%'";
		if(intval($_REQUEST['deal_id'])>0)		
		$where.=" and (".DB_PREFIX."deal_order.deal_ids = ".intval($_REQUEST['deal_id'])." or deal_ids like '%".intval($_REQUEST['deal_id']).",%' or deal_ids like '%,".intval($_REQUEST['deal_id'])."' or deal_ids like '%,".intval($_REQUEST['deal_id']).",%')";
		
		
		$where.= " and ".DB_PREFIX."deal_order.is_delete = 0 ";
		$where.= " and ".DB_PREFIX."deal_order.type = 0 ";

		if(trim($_REQUEST['order_sn'])!='')
		{
			$where.= " and ".DB_PREFIX."deal_order.order_sn like '%".trim($_REQUEST['order_sn'])."%' ";
		}
		if(intval($_REQUEST['pay_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.pay_status = ".intval($_REQUEST['pay_status']);
		}
		if(intval($_REQUEST['delivery_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.delivery_status = ".intval($_REQUEST['delivery_status']);
		}
		if(intval($_REQUEST['extra_status'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.extra_status = ".intval($_REQUEST['extra_status']);
		}
		if(intval($_REQUEST['after_sale'])>=0)
		{
			$where.= " and ".DB_PREFIX."deal_order.after_sale = ".intval($_REQUEST['after_sale']);
		}

	
		
		$list = M("DealOrder")
				->where($where)
				->field(DB_PREFIX.'deal_order.*')
				->limit($limit)->findAll ( );
			
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
			
			$order_value = array('sn'=>'""', 'user_name'=>'""', 'deal_name'=>'""','number'=>'""', 'pay_status'=>'""', 'delivery_status'=>'""','extra_status'=>'""','after_sale'=>'""', 'create_time'=>'""', 'total_price'=>'""', 'pay_amount'=>'""', 'consignee'=>'""', 'address'=>'""','zip'=>'""','email'=>'""', 'mobile'=>'""', 'memo'=>'""');
	    	if($page == 1)
	    	{
		    	$content = iconv("utf-8","gbk","订单编号,用户名,团购名称,订购数量,支付状态,发货状态,额外状态,售后,下单时间,订单总额,已收金额,收货人,发货地址,邮编,用户邮件,手机号码,订单留言");	    		    	
		    	$content = $content . "\n";
	    	}
	    	
			foreach($list as $k=>$v)
			{
				
				$order_value['sn'] = '"' . "sn:".iconv('utf-8','gbk',$v['order_sn']) . '"';
				$user_info = M("User")->getById($v['user_id']);
				$order_value['user_name'] = '"' . iconv('utf-8','gbk',$user_info['user_name']) . '"';
				$order_items = M("DealOrderItem")->where("order_id=".$v['id'])->findAll();
				$names = "";
				foreach($order_items as $key => $row)
				{
					$names.=  addslashes($row['name'])."[".$row['number']."]";
					if($key<count($order_items)-1)
					$names.="\n";
				}
			
				$order_value['deal_name'] = '"' . iconv('utf-8','gbk',$names) . '"';
				$number = M("DealOrderItem")->where("order_id=".$v['id'])->sum("number");
				$order_value['number'] = '"' . iconv('utf-8','gbk',$number) . '"';
				$order_value['pay_status'] = '"' . iconv('utf-8','gbk',l("PAY_STATUS_".$v['pay_status'])) . '"';
				$order_value['delivery_status'] = '"' . iconv('utf-8','gbk',l("ORDER_DELIVERY_STATUS_".$v['delivery_status'])) . '"';
				$order_value['extra_status'] = '"' . iconv('utf-8','gbk',l("EXTRA_STATUS_".$v['extra_status'])) . '"';
				$order_value['after_sale'] = '"' . iconv('utf-8','gbk',l("AFTER_SALE_".$v['after_sale'])) . '"';
				$order_value['create_time'] = '"' . iconv('utf-8','gbk',to_date($v['create_time'])) . '"';
				$order_value['total_price'] = '"' . iconv('utf-8','gbk',format_price($v['total_price'])) . '"';
				$order_value['pay_amount'] = '"' . iconv('utf-8','gbk',format_price($v['pay_amount'])) . '"';
				$order_value['consignee'] = '"' . iconv('utf-8','gbk',$v['consignee']) . '"';
				
				$region_lv1_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv1']);
				$region_lv2_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv2']);
				$region_lv3_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv3']);
				$region_lv4_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."delivery_region where id = ".$v['region_lv4']);
				$address = $region_lv1_name.$region_lv2_name.$region_lv3_name.$region_lv4_name.$v['address'];
				$order_value['address'] = '"' . iconv('utf-8','gbk',$address) . '"';
				$order_value['zip'] = '"' . iconv('utf-8','gbk',$v['zip']) . '"';
				$order_value['email'] = '"' . iconv('utf-8','gbk',$user_info['email']) . '"';
				if($v['mobile']!='')
				$mobile = $v['mobile'];
				else
				$mobile = $user_info['mobile'];
				$order_value['mobile'] = '"' . iconv('utf-8','gbk',$mobile) . '"';
				$order_value['memo'] = '"' . iconv('utf-8','gbk',$v['memo']) . '"';
				
				
				$content .= implode(",", $order_value) . "\n";
			}	
			
			
			header("Content-Disposition: attachment; filename=order_list.csv");
	    	echo $content;  
		}
		else
		{
			if($page==1)
			$this->error(L("NO_RESULT"));
		}	
		
	}
	
	public function deal_trash()
	{
		$condition['is_delete'] = 1;
		$condition['type'] = 0;
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function pay_incharge()
	{
		$id = intval($_REQUEST['id']);
		//开始由管理员手动收款
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id);
		if($order_info['pay_status'] != 2)
		{
			require_once APP_ROOT_PATH."system/libs/cart.php";
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid = 0");
			if(!$payment_notice)
			{
				make_payment_notice($order_info['total_price'],$order_info['id'],$order_info['payment_id']);
				$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_info['id']." and payment_id = ".$order_info['payment_id']." and is_paid = 0");
			}
			$adm_session = es_session::get(md5(conf("AUTH_KEY")));
			payment_paid(intval($payment_notice['id']),l("ADMIN_PAYMENT_PAID").':'.intval($adm_session['adm_id'])."后台收款");	//对其中一条款支付的付款单付款					
			$msg = sprintf(l("ADMIN_PAYMENT_PAID"),$payment_notice['notice_sn']);
			save_log($msg,1);
			$rs = order_paid($order_info['id']);
			
			if($rs)
			{
				$msg = sprintf(l("ADMIN_ORDER_PAID"),$order_info['order_sn']);
				save_log($msg,1);
				$this->success(l("ORDER_PAID_SUCCESS"));
			}
			else
			{
				$msg = sprintf(l("ADMIN_ORDER_PAID"),$order_info['order_sn']);
				save_log($msg,0);
				$this->error(l("ORDER_PAID_FAILED"));
			}
		}
		else 
		{
			$this->error(l("ORDER_PAID_ALREADY"));
		}
	}	
	public function delete() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['order_sn'];	
					if($data['order_status']==0&&$data['type']==0)
					{
						$this->error (l("ORDER_DELETE_FAILED"),$ajax);						
					}
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 1 );
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	public function restore() {
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['order_sn'];						
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->setField ( 'is_delete', 0 );
				if ($list!==false) {
					save_log($info.l("RESTORE_SUCCESS"),1);
					$this->success (l("RESTORE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("RESTORE_FAILED"),0);
					$this->error (l("RESTORE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}		
	}
	
	
	public function foreverdelete() {
		//彻底删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M(MODULE_NAME)->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['order_sn'];
					if($data['order_status']==0&&$data['type']==0)
					{
						$this->error (l("ORDER_DELETE_FAILED"),$ajax);						
					}	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
		
				if ($list!==false) {
					//删除关联数据
					M("PaymentNotice")->where(array ('order_id' => array ('in', explode ( ',', $id ) ) ))->delete(); //删除相关收款单
					M("DealOrderLog")->where(array ('order_id' => array ('in', explode ( ',', $id ) ) ))->delete(); //删除相关日志
					M("DealCoupon")->where(array ('order_id' => array ('in', explode ( ',', $id ) ) ))->delete(); //删除相关团购券
					M("DealOrderItem")->where(array ('order_id' => array ('in', explode ( ',', $id ) ) ))->delete(); //删除相关订单商品
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
	
	public function view_order()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$id." and is_delete = 0 and type = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		$order_deal_items = M("DealOrderItem")->where("order_id=".$order_info['id'])->findAll();
		foreach($order_deal_items as $k=>$v)
		{
			$order_deal_items[$k]['is_delivery'] = M("Deal")->where("id=".$v['deal_id'])->getField("is_delivery");
		}
		$this->assign("order_deals",$order_deal_items);
		$this->assign("order_info",$order_info);
		
		$payment_notice = M("PaymentNotice")->where("order_id = ".$order_info['id']." and is_paid = 1")->order("pay_time desc")->findAll();
		$this->assign("payment_notice",$payment_notice);
		
		
		
		//输出订单留言
		$map['rel_table'] = 'deal_order';
		$map['rel_id'] = $order_info['id'];
		
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name= "Message"; 
		$model = D ($name);
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		
		//输出订单相关的团购券
		$coupon_list = M("DealCoupon")->where("order_id = ".$order_info['id']." and is_delete = 0")->findAll();
		$this->assign("coupon_list",$coupon_list);
		
		//输出订单日志
		$log_list = M("DealOrderLog")->where("order_id=".$order_info['id'])->order("log_time desc")->findAll();
		$this->assign("log_list",$log_list);
		
		$this->display();
	}
	
	public function delivery()
	{
		$id = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$id." and is_delete = 0 and type = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		$order_deal_items = M("DealOrderItem")->where("order_id=".$order_info['id'])->findAll();
		foreach($order_deal_items as $k=>$v)
		{
			if(M("Deal")->where("id=".$v['deal_id'])->getField("is_delivery")==0) //无需发货的商品
			{
				unset($order_deal_items[$k]);
			}
		}
		
		//输出快递接口
		$express_list = M("Express")->where("is_effect = 1")->findAll();
		$this->assign("express_list",$express_list);
		$this->assign("order_deals",$order_deal_items);
		$this->assign("order_info",$order_info);
		$this->display();
	}
	
	//批量发货
	public function do_batch_delivery()
	{
		$delivery_sn = doubleval($_REQUEST['begin_sn']);
		$order_ids = $_REQUEST['ids'];
		$order_ids = explode(",",$order_ids);
		$_REQUEST['silent'] = 1;	

		foreach($order_ids as $k=>$order_id)
		{
			$_REQUEST['order_id'] = $order_id;
			$_REQUEST['delivery_sn'] = $delivery_sn + $k;
			$order_items = $GLOBALS['db']->getAll("select doi.* from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."deal as d on doi.deal_id = d.id where doi.order_id = ".$order_id." and d.is_delivery = 1");
			$order_deals = array();
			foreach($order_items as $kk=>$vv)
			{
				array_push($order_deals,$vv['id']);
			}
			$_REQUEST['order_deals'] = $order_deals;
			$_REQUEST['express_id'] = intval($_REQUEST['express_id']);
			$this->do_delivery();
		}
		
		$this->assign("jumpUrl",U("DealOrder/deal_index"));
		$this->success(l("BATCH_DELIVERY_SUCCESS"));	
	}
	public function load_batch_delivery()
	{
		$ids = trim($_REQUEST['ids']);
		$express_id = intval($_REQUEST['express_id']);
		if($express_id==0)
		{
			header("Content-Type:text/html; charset=utf-8");
			echo l("SELECT_EXPRESS_WARNING");
			exit;
		}
		$this->assign("ids",$ids);
		$this->assign("express_id",$express_id);
		$this->display();
	}

	public function do_delivery()
	{
		$silent = intval($_REQUEST['silent']);
		$order_id = intval($_REQUEST['order_id']);
		$order_deals = $_REQUEST['order_deals'];
		$delivery_sn = $_REQUEST['delivery_sn'];
		$express_id = intval($_REQUEST['express_id']);
		$memo = $_REQUEST['memo'];
		if(!$order_deals)
		{
			if($silent==0)
			$this->error(l("PLEASE_SELECT_DELIVERY_ITEM"));
		}
		else
		{
			$deal_names = array();
			foreach($order_deals as $order_deal_id)
			{
				$deal_name =$GLOBALS['db']->getOne("select d.sub_name from ".DB_PREFIX."deal as d left join ".DB_PREFIX."deal_order_item as doi on doi.deal_id = d.id where doi.id = ".$order_deal_id);
				array_push($deal_names,$deal_name);
				$rs = make_delivery_notice($order_id,$order_deal_id,$delivery_sn,$memo,$express_id);
				if($rs)
				{
					$GLOBALS['db']->query("update ".DB_PREFIX."deal_order_item set delivery_status = 1 where id = ".$order_deal_id);
				}
			}
			$deal_names = implode(",",$deal_names);
			
			send_delivery_mail($delivery_sn,$deal_names,$order_id);
			send_delivery_sms($delivery_sn,$deal_names,$order_id);
			//开始同步订单的发货状态
			$order_deal_items = M("DealOrderItem")->where("order_id=".$order_id)->findAll();
			foreach($order_deal_items as $k=>$v)
			{
				if(M("Deal")->where("id=".$v['deal_id'])->getField("is_delivery")==0) //无需发货的商品
				{
					unset($order_deal_items[$k]);
				}				
			}
			$delivery_deal_items = $order_deal_items;
			foreach($delivery_deal_items as $k=>$v)
			{
				if($v['delivery_status']==0) //未发货去除
				{
					unset($delivery_deal_items[$k]);
				}				 
			}
			

			if(count($delivery_deal_items)==0&&count($order_deal_items)!=0)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 0 where id = ".$order_id); //未发货
			}
			elseif(count($delivery_deal_items)>0&&count($order_deal_items)!=0&&count($delivery_deal_items)<count($order_deal_items))
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 1 where id = ".$order_id); //部分发
			}
			else
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."deal_order set delivery_status = 2 where id = ".$order_id); //全部发
			}		
			M("DealOrder")->where("id=".$order_id)->setField("update_time",TIME_UTC);
			
			
			
			$msg = l("DELIVERY_SUCCESS");
			//发货完毕，开始同步相应支付接口中的发货状态
			if(intval($_REQUEST['send_goods_to_payment'])==1)
			{
				$payment_notices = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."payment_notice where order_id = ".$order_id);
				foreach($payment_notices as $k=>$v)
				{
					$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$v['payment_id']);
					if($v['outer_notice_sn']!='')
					{						
						require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
						$payment_class = $payment_info['class_name']."_payment";
						$payment_object = new $payment_class();
						if(method_exists ($payment_object,"do_send_goods"))
						{
							$result = $payment_object->do_send_goods($v['id'],$delivery_sn);
							$msg = $msg."[".$payment_info['name'].$result."]";							
						}
						else 
						{
							$msg = $msg."[".$payment_info['name'].l("NOT_SUPPORT_SEND_GOODS")."]";							
						}						
					}
					else
					{
						$msg = $msg."[".$payment_info['name'].l("NOT_TRADE_SN")."]";	
					}
				}
			}
						
			$this->assign("jumpUrl",U("DealOrder/view_order",array("id"=>$order_id)));		

			//查询快递名
			$express_name = M("Express")->where("id=".$express_id)->getField("name");
			
			order_log(l("DELIVERY_SUCCESS").$express_name.$delivery_sn.$_REQUEST['memo'],$order_id);
			
			if($silent==0)
			$this->success($msg);
		}
	}
	
	public function over_order()
	{
		$order_id  = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0 and order_status = 0 and (pay_status = 2 and ((delivery_status = 2 or delivery_status = 5)) or (pay_amount = refund_money))")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0 and order_status = 0 and (pay_status = 2 and ((delivery_status = 2 or delivery_status = 5)) or (pay_amount = refund_money))")->setField("order_status",1);
		M("DealOrder")->where("id=".$order_id)->setField("update_time",TIME_UTC);
		save_log($order_info['order_sn'].l("OVER_ORDER_SUCCESS"),1);
		order_log($order_info['order_sn'].l("OVER_ORDER_SUCCESS"),$order_id);
		
		$this->assign("jumpUrl",U("DealOrder/view_order",array("id"=>$order_id)));
		$this->success(l("OVER_ORDER_SUCCESS"));
	}
	
	public function open_order()
	{
		$order_id  = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0 and order_status = 1 and (pay_status = 2 and ((delivery_status = 2 or delivery_status = 5)) or (pay_amount = refund_money))")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0 and order_status = 1 and (pay_status = 2 and ((delivery_status = 2 or delivery_status = 5)) or (pay_amount = refund_money))")->setField("order_status",0);
		M("DealOrder")->where("id=".$order_id)->setField("update_time",TIME_UTC);
		save_log($order_info['order_sn'].l("OPEN_ORDER_SUCCESS"),1);
		order_log($order_info['order_sn'].l("OPEN_ORDER_SUCCESS"),$order_id);
		
		$this->assign("jumpUrl",U("DealOrder/view_order",array("id"=>$order_id)));
		$this->success(l("OPEN_ORDER_SUCCESS"));
	}
	
	public function admin_memo()
	{
		$order_id  = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		if($order_info['order_status'] == 1)
		{
			$this->error(l("ORDER_OVERED"));
		}
		$admin_memo = $_REQUEST['admin_memo'];
		$after_sale_r = $_REQUEST['after_sale'];
		$after_sale = 0;
		foreach($after_sale_r as $k=>$v)
		{
			$after_sale+=intval($v);
		}
		$refund_money = floatval($_REQUEST['refund_money']);
		if($refund_money == $order_info['refund_money'])
		{
			$log_info = $admin_memo;
		}
		else
		{
			//退款金额有变动
			if($refund_money>$order_info['refund_money'])
			{
				$current_refund_money = $refund_money - floatval($order_info['refund_money']);
				//增加退款
				if(intval($_REQUEST['refund_to_user'])==1)
				{
					$data = array("money"=>$current_refund_money);
					require_once APP_ROOT_PATH."system/libs/user.php";
					modify_account($data,$order_info['user_id'],"来自".$order_info['order_sn']."的退款 ".$admin_memo,13);
				}
			}
			$log_info = sprintf(L("CHANGE_REFUND_AMOUNT"),format_price($refund_money)).$admin_memo;
		}
		
		
		order_log($log_info,$order_id);
		if($after_sale==1||$after_sale==2||$after_sale==3)
		{
			if($after_sale==1||$after_sale==3)
			{
				M("DealOrder")->where("id=".$order_id)->setField("refund_status",2);
			}
			if($after_sale==2||$after_sale==3)
			{
				M("DealOrder")->where("id=".$order_id)->setField("retake_status",2);
			}
		}
		else
		{
			M("DealOrder")->where("id=".$order_id)->setField("refund_status",0);
			M("DealOrder")->where("id=".$order_id)->setField("retake_status",0);
		}
		M("DealOrder")->where("id=".$order_id)->setField("refund_money",$refund_money);
		M("DealOrder")->where("id=".$order_id)->setField("admin_memo",$admin_memo);
		M("DealOrder")->where("id=".$order_id)->setField("update_time",TIME_UTC);
		M("DealOrder")->where("id=".$order_id)->setField("after_sale",$after_sale);
		save_log($order_info['order_sn'].l("ORDER_MEMO_MODIFY").l("AFTER_SALE").":".l("AFTER_SALE_".$after_sale),1);
		$this->success(l("SAVE_SUCCESS"));
	}
	
	public function order_incharge()
	{
		$order_id  = intval($_REQUEST['id']);
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0")->find();
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		
		
		if($order_info['region_lv4']>0)
		$region_id = $order_info['region_lv4'];
		elseif($order_info['region_lv3']>0)
		$region_id = $order_info['region_lv3'];
		elseif($order_info['region_lv2']>0)
		$region_id = $order_info['region_lv2'];
		else
		$region_id = $order_info['region_lv1'];
		
		$delivery_id = $order_info['delivery_id'];
		$payment_id = 0;		
		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);		
		$GLOBALS['user_info']['id'] = $order_info['user_id'];
		require_once APP_ROOT_PATH."system/libs/cart.php";
		$result = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=0,$ecvsn,$ecvpassword,$goods_list,$order_info['account_money'],$order_info['ecv_money'],$order_info['bank_id']);
		
		$this->assign("result",$result);
		
	
		
		
		$payment_list = M("Payment")->where("is_effect = 1 and class_name <> 'Voucher'")->findAll();
		$this->assign("payment_list",$payment_list);
		$this->assign("user_money",M("User")->where("id=".$order_info['user_id'])->getField("money"));
		$this->assign("order_info",$order_info);
		$this->display();
	}
	
	public function do_incharge()
	{
		$order_id  = intval($_REQUEST['order_id']);
		$payment_id = intval($_REQUEST['payment_id']);
		$payment_info = M("Payment")->getById($payment_id);
		$memo = $_REQUEST['memo'];
		$order_info = M("DealOrder")->where("id=".$order_id." and is_delete = 0 and type = 0")->find();		
		if(!$order_info)
		{
			$this->error(l("INVALID_ORDER"));
		}
		
		if($order_info['region_lv4']>0)
		$region_id = $order_info['region_lv4'];
		elseif($order_info['region_lv3']>0)
		$region_id = $order_info['region_lv3'];
		elseif($order_info['region_lv2']>0)
		$region_id = $order_info['region_lv2'];
		else
		$region_id = $order_info['region_lv1'];
		
		$delivery_id = $order_info['delivery_id'];
		$payment_id = intval($_REQUEST['payment_id']);		
		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_id);		
		$GLOBALS['user_info']['id'] = $order_info['user_id'];
		require_once APP_ROOT_PATH."system/libs/cart.php";
		$result = count_buy_total($region_id,$delivery_id,$payment_id,$account_money=0,$all_account_money=0,$ecvsn,$ecvpassword,$goods_list,$order_info['account_money'],$order_info['ecv_money'],$order_info['bank_id']);
		

		$user_money = M("User")->where("id=".$order_info['user_id'])->getField("money");
		//$pay_amount = $order_info['deal_total_price']+ $order_info['delivery_fee']-$order_info['account_money']-$order_info['ecv_money']+$payment_info['fee_amount'];
		$pay_amount = $result['pay_price'];
		
		
		if($payment_info['class_name']=='Account'&&$user_money<$pay_amount) 
		$this->error(l("ACCOUNT_NOT_ENOUGH"));

		$notice_id = make_payment_notice($pay_amount,$order_id,$payment_id,$memo);
		
		$order_info['total_price'] = $result['pay_total_price'];
		$order_info['payment_fee'] = $result['payment_fee'];  
		$order_info['payment_id'] = $payment_info['id'];
		$order_info['update_time'] = TIME_UTC;
		M("DealOrder")->save($order_info);
		
		$payment_notice = M("PaymentNotice")->getById($notice_id);
		$adm_session = es_session::get(md5(conf("AUTH_KEY")));
		$rs = payment_paid($payment_notice['id'],l("ADMIN_PAYMENT_PAID").':'.intval($adm_session['adm_id'])."后台收款");	
		if($rs&&$payment_info['class_name']=='Account')
		{
			//余额支付
			require_once APP_ROOT_PATH."system/payment/Account_payment.php";				
			require_once APP_ROOT_PATH."system/libs/user.php";
			$msg = sprintf($payment_lang['USER_ORDER_PAID'],$order_info['order_sn'],$payment_notice['notice_sn']);			
			modify_account(array('money'=>"-".$payment_notice['money'],'score'=>0),$payment_notice['user_id'],$msg,13);
		}

		
		if($rs)
		{	
			order_paid($order_id);
			$msg = sprintf(l("MAKE_PAYMENT_NOTICE_LOG"),$order_info['order_sn'],$payment_notice['notice_sn']);
			save_log($msg,1);
			order_log($msg.$_REQUEST['memo'],$order_id);
			$this->assign("jumpUrl",U("DealOrder/view_order",array("id"=>$order_id)));
			$this->success(l("ORDER_INCHARGE_SUCCESS"));
		}
		else
		{
			$this->assign("jumpUrl",U("DealOrder/view_order",array("id"=>$order_id)));
			$this->success(l("ORDER_INCHARGE_FAILED"));
		}
	}
	
	public function lottery_index()
	{
		if(trim($_REQUEST['user_name'])!='')
		{		
			$ids = M("User")->where(array("user_name"=>array('like','%'.trim($_REQUEST['user_name']).'%')))->field("id")->findAll();
			$ids_arr = array();
			foreach($ids as $k=>$v)
			{
				array_push($ids_arr,$v['id']);
			}	
			$map['user_id'] = array("in",$ids_arr);
		}
		
		if(intval($_REQUEST['deal_id'])>0)
		$map['deal_id'] = intval($_REQUEST['deal_id']);
		
		if(trim($_REQUEST['lottery_sn'])!='')
		$map['lottery_sn'] = trim($_REQUEST['lottery_sn']);

		$model = D ("Lottery");
		if (! empty ( $model )) {
			$this->_list ( $model, $map );
		}
		$this->display ();
		return;
	}
	
	public function del_lottery()
	{
		//删除指定记录
		$ajax = intval($_REQUEST['ajax']);
		$id = $_REQUEST ['id'];
		if (isset ( $id )) {
				$condition = array ('id' => array ('in', explode ( ',', $id ) ) );
				$rel_data = M("Lottery")->where($condition)->findAll();				
				foreach($rel_data as $data)
				{
					$info[] = $data['lottery_sn'];						
				}
				if($info) $info = implode(",",$info);
				$list = M("Lottery")->where ( $condition )->delete();
				if ($list!==false) {
					save_log($info.l("DELETE_SUCCESS"),1);
					$this->success (l("DELETE_SUCCESS"),$ajax);
				} else {
					save_log($info.l("DELETE_FAILED"),0);
					$this->error (l("DELETE_FAILED"),$ajax);
				}
			} else {
				$this->error (l("INVALID_OPERATION"),$ajax);
		}
	}
	
	public function referer()
	{
		if(isset($_REQUEST['referer'])&&trim($_REQUEST['referer'])!='')
		{
			$where = "referer like '%".trim($_REQUEST['referer'])."%' and is_delete = 0";
			$map['referer'] = array("like","%".trim($_REQUEST['referer'])."%");
			$map['is_delete'] = 0;
		}
		else
		{
			$where = "referer <> '' and is_delete = 0";
			$map['referer'] = array("neq","");
			$map['is_delete'] = 0;
		}
		$where.=" and type <> 1";
		$map['type'] = array("neq",1);
		$begin_time  = trim($_REQUEST['begin_time'])==''?0:to_timespan($_REQUEST['begin_time']);
		$end_time  = trim($_REQUEST['end_time'])==''?0:to_timespan($_REQUEST['end_time']);
		if($end_time==0)
		{
			$where.=" and create_time > ".$begin_time;			
			$map['create_time'] = array("gt",$begin_time);
		}
		else
		{
			$where.=" and create_time between ".$begin_time." and ".$end_time;	
			$map['create_time'] = array("between",array($begin_time,$end_time));
		}	
		$sql = "select referer,count(id) as ct from ".DB_PREFIX."deal_order where ".$where." group by referer having count(id) > 0 ";
		$sql_count = "select referer from ".DB_PREFIX."deal_order where ".$where." group by referer having count(id) > 0 ";
		
		$count = $GLOBALS['db']->getAll($sql_count);
		
		//开始list
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : "ct";
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$count = count($count);
		if ($count > 0) {
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//分页查询数据
			$sql .= "order by `" . $order . "` " . $sort;
			$sql .= " limit ".$p->firstRow . ',' . $p->listRows;

			$voList = $GLOBALS['db']->getAll($sql);
			
//			echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示

			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
			$this->assign ( "nowPage",$p->nowPage);
		}
		$this->display ();
	}
}
?>