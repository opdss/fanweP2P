<?php
// +----------------------------------------------------------------------
// | easethink 方维借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class MyCustomerAction extends CommonAction{
	public function index()
	{	
		$this->assign("main_title",L("DEAL_THREE"));
		
		
		if (isset ( $_REQUEST ['_order'] )) {
			$sorder = $_REQUEST ['_order'];
		}
		else{
			$sorder = "id";
		}
		
		switch($sorder){
			case "adm_name":
					$order ="u.admin_id";
				break;
			case "cname":
				$order ="u.customer_id";
				break;
			default : 
				$order =$sorder;
				break;
		}
		
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		}
		else{
			$sort = "desc";
		}
		
		//开始加载搜索条件
		$condition =" 1=1 ";
		$condition .= " and u.is_effect = 1 and u.is_delete = 0 and (user_type = 0 || user_type = 1) ";
		
		$admin_names  = !isset($_REQUEST['adm_names'])? -3 : intval($_REQUEST['adm_names']);
		$cnames  = !isset($_REQUEST['cnames'])? -3 : intval($_REQUEST['cnames']);
		$name = !isset($_REQUEST['name'])? -3 : strim($_REQUEST['name']) =="" ? -3 : strim($_REQUEST['name']);
		
		// -2：未分配 ，  -1：已分配
		if($admin_names == -3 && $cnames == -3 && $name == -3)
		{
			$condition .= " and ( customer_id = 0  or admin_id = 0 )";
		}
		else{		
			if($admin_names == -2 && $cnames == -2)	 	 //管理员未分配客服未分配
				if($name == -3){
					$condition .= " and ( customer_id = 0  and admin_id = 0 )  or  u.user_name = '". $name ." ' ";
				}else{
					$condition .= " and u.user_name = '". $name ." ' ";
				}
			elseif ($admin_names == -2 && $cnames != -2) //管理员未分配客服已分配	
				if($cnames == -1){
					if($name == -3){
						$condition .= " and  customer_id != 0  and admin_id = 0 or  u.user_name = '". $name ." ' ";
					}else{
						$condition .= " and  u.user_name = '". $name ." ' ";
					}
					
				}else{
				
					if($name == -3){
						$condition .= " and  customer_id = ".$cnames."  and admin_id = 0  or  u.user_name = '". $name ." '";
					}else{
						$condition .= " and  u.user_name = '". $name ." '";
					}
				}
			elseif ($admin_names != -2 && $cnames == -2) //管理员已分配客服未分配	
				if($admin_names == -1){
					
					if($name == -3){
						$condition .= " and customer_id = 0  and admin_id != 0 or u.user_name = '". $name ." ' ";
					}else{
						$condition .= " and u.user_name = '". $name ." ' ";
					}
				}else{
					if($name == -3){
						$condition .= " and  admin_id = ".$admin_names." and customer_id = 0 or  u.user_name = '". $name ." ' ";
					}else{
						$condition .= " and   u.user_name = '". $name ." ' ";
					}
				}
			elseif ($admin_names != -2 && $cnames != -2) //都已分配
				if($admin_names == -1 && $cnames == -1) 
					if($name == -3){
						$condition .= " and customer_id != 0  and admin_id != 0 or u.user_name = '". $name ." ' ";
					}else{
						$condition .= " u.user_name = '". $name ." ' ";
					}
					
				elseif($admin_names == -1 && $cnames != -1)
					
					if($name == -3){
						$condition .= " and customer_id = ".$cnames." and admin_id != 0  or u.user_name = '". $name ." ' ";
					}else{
						$condition .= " and  u.user_name = '". $name ." ' ";
					}
				elseif($admin_names != -1 && $cnames == -1)
					
					if($name == -3){
						$condition .= " and admin_id = ".$admin_names." and customer_id != 0  or u.user_name = '". $name ." ' ";
					}else{
						$condition .= "  and u.user_name = '". $name ." ' ";
					}
				elseif($admin_names != -1 && $cnames != -1)
					
					if($name == -3){
						$condition .= " and admin_id = ".$admin_names." and customer_id = ".$cnames." or u.user_name = '". $name ." ' ";
					}else{
						$condition .= " and  u.user_name = '". $name ." ' ";
					}
		}
		
		$sql_count = " SELECT count(*) FROM ".DB_PREFIX."user u left join ".DB_PREFIX."admin a on u.admin_id = a.id left join ".DB_PREFIX."customer c on c.id = u.customer_id WHERE $condition ";

		$rs_count = $GLOBALS['db']->getOne($sql_count);
		$list = array();
		
		if($rs_count > 0){
			
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $rs_count, $listRows );
			
			$sql_list =  " SELECT u.*,a.adm_name,c.name as cname FROM ".DB_PREFIX."user u left join ".DB_PREFIX."admin a on u.admin_id = a.id left join ".DB_PREFIX."customer c on c.id = u.customer_id WHERE $condition ORDER BY $order $sort LIMIT ".$p->firstRow . ',' . $p->listRows;
			
			$list = $GLOBALS['db']->getAll($sql_list);
			$page = $p->show();
			$this->assign ( "page", $page );
			
		}

		$admin_cate = $GLOBALS['db']->getAll("select id,adm_name from ".DB_PREFIX."admin where is_effect = 1 and is_delete = 0 and is_department = 0");
		$this->assign ( 'admin_cate', $admin_cate );
		$customer_cate = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."customer where is_effect = 1 and is_delete = 0");
		$this->assign ( 'customer_cate', $customer_cate );
		
		$sortImg = $sort; //排序图标
		$sortAlt = $sort == 'desc' ? l("ASC_SORT") : l("DESC_SORT"); //排序提示
		$sort = $sort == 'desc' ? 1 : 0; //排序方式
		
		$this->assign ( 'sort', $sort );
		$this->assign ( 'order', $sorder );
		$this->assign ( 'sortImg', $sortImg );
		$this->assign ( 'sortType', $sortAlt );
		
		$this->assign("list",$list);
		$this->display ();
		return;
	}
	
	
	public function edit()
	{
		$id = intval($_REQUEST ['id']);
		
		require_once APP_ROOT_PATH."app/Lib/common.php";
		$user_info = get_user( "*",$id);
		$this->assign ( 'user_info', $user_info );
		
		//客服列表
		$customer_sql =  " SELECT * FROM ".DB_PREFIX."customer WHERE is_delete= 0 and is_effect=1";
		$customer_list = $GLOBALS['db']->getAll($customer_sql);
		$this->assign ( 'customers', $customer_list );
		
		//管理员列表
		$adm_sql =  " SELECT * FROM ".DB_PREFIX."admin WHERE is_delete= 0 and is_effect=1 and is_department = 0";
		$adm_list = $GLOBALS['db']->getAll($adm_sql);
		$this->assign ( 'admins', $adm_list );
		
		$this->display ();
		
	}
	
	public function update()
	{
		$id = intval($_REQUEST ['id']);
		$admin_id = intval($_REQUEST ['admin_id']);
		//$customer_id = intval($_REQUEST ['customer_id']);
		
		$user_info = array();
		$user_info['admin_id'] = $admin_id;
		//$user_info['customer_id'] = $customer_id;
		$list = $GLOBALS['db']->autoExecute(DB_PREFIX."user",$user_info,"UPDATE","id=".$id);

		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	public function updates()  //更新标所属的客服/管理员
	{
		$id = intval($_REQUEST ['id']);
		$admin_id = intval($_REQUEST ['admin_id']);
		$customers_id = intval($_REQUEST ['customers_id']);
	
		$deal_info = array();
		if($admin_id)
		{$deal_info['admin_id'] = $admin_id;}
		if($customers_id)
		{$deal_info['customers_id'] = $customers_id;}
		
		$list = $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$deal_info,"UPDATE","id=".$id);
	
		if (false !== $list) {
			//成功提示
			save_log($log_info.L("INSERT_SUCCESS"),1);
			$this->success(L("INSERT_SUCCESS"));
		} else {
			//错误提示
			save_log($log_info.L("INSERT_FAILED"),0);
			$this->error(L("INSERT_FAILED"));
		}
	}
	
	
	
	
}
?>