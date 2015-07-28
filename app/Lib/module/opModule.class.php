<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

require APP_ROOT_PATH.'app/Lib/page.php';
require APP_ROOT_PATH."system/libs/user.php";
class opModule extends SiteBaseModule
{
	public function index()
	{	
		
		$m_name = addslashes(htmlspecialchars(trim($_REQUEST['m_name'])));
		$a_name = addslashes(htmlspecialchars(trim($_REQUEST['a_name'])));
		$id = intval($_REQUEST['id']);
		$func = "op_".$m_name."_".$a_name;	
		$res = $this->$func($id,0,"",true);  //检测是否需要操作（推荐/置顶）
		if($res['status'])
		{
			$GLOBALS['tmpl']->assign("m_name",$m_name);
			$GLOBALS['tmpl']->assign("a_name",$a_name);
			$GLOBALS['tmpl']->assign("id",$id);		
			$GLOBALS['tmpl']->display("op_index.html");
		}
		else
		{
			$GLOBALS['tmpl']->assign("cancel",true);
			$GLOBALS['tmpl']->assign("m_name",$m_name);
			$GLOBALS['tmpl']->assign("a_name",$a_name);
			$GLOBALS['tmpl']->assign("id",$id);		
			$GLOBALS['tmpl']->display("op_index.html");
		}
	}
	
	public function setmemo()
	{
		$id = intval($_REQUEST['id']);
		$group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_group where id = ".$id);
		$GLOBALS['tmpl']->assign("group",$group);	
		$GLOBALS['tmpl']->display("op_setmemo.html");
	}
	
	public function submit()
	{
		$m_name = addslashes(htmlspecialchars(trim($_REQUEST['m_name'])));
		$a_name = addslashes(htmlspecialchars(trim($_REQUEST['a_name'])));
		$id = intval($_REQUEST['id']);
		$change = intval($_REQUEST['op_change']);		
		$reason = addslashes(htmlspecialchars(trim($_REQUEST['reason'])));
		$func = "op_".$m_name."_".$a_name;		
		$res = $this->$func($id,$change,$reason);		
		ajax_return($res);
	}
	
	//推荐点评
	function op_dp_setbest($id,$change,$reason,$check=false)
	{
		$dp = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location_dp where id = ".$id);
		if($check)
		{
			//检测是否可以推荐
			if($dp['is_best'] == 1)
			{
				$result['status'] = 0;
				return $result;
			}	
			else
			{
				$result['status'] = 1;
				return $result;
			}		
		}
		if(check_user_auth("dp","setbest"))
		{
			if($dp['is_best']==1)
			{
				$sql = "update ".DB_PREFIX."supplier_location_dp set is_best = 0 where id = ".$id;		
				$GLOBALS['db']->query($sql);					
			}
			else
			{
				$sql = "update ".DB_PREFIX."supplier_location_dp set is_best = 1 where id = ".$id;	
				$GLOBALS['db']->query($sql);
				$o_reason = $reason;
				$reason .= " 商户点评(".$dp['title'].")被推荐 ";			
				if($change==1)
				{
					modify_account(get_op_change("dp","setbest"),$dp['user_id'],$reason);
					$reason .= get_op_change_show("dp","setbest");
				}
				if($change==1||$o_reason!="")
				send_user_msg("商户点评被推荐",$reason,intval($GLOBALS['user_info']['id']),$dp['user_id'],TIME_UTC,0,true,true);
			}			
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_dp_del($id,$change,$reason,$check=false)
	{
		$dp = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location_dp where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("dp","del"))
		{
			
			$sql = "delete from ".DB_PREFIX."supplier_location_dp where id = ".$id;	
			$GLOBALS['db']->query($sql);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_dp_reply where dp_id = ".$id);
			
			if($dp['status']==1)
			{
				$merchant_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where id = ".$dp['supplier_location_id']);
				syn_supplier_locationcount($merchant_info);
			}
			$GLOBALS['db']->query("update ".DB_PREFIX."user set dp_count = dp_count - 1 where id = ".intval($dp['user_id']));			
			$GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_dp_point_result where dp_id = ".$id);	
			$GLOBALS['db']->query("delete from ".DB_PREFIX."supplier_location_dp_tag_result where dp_id = ".$id);	
			$o_reason = $reason;	
			$reason .= "商户点评(".$dp['title'].")被删除";			
			if($change==1)
			{
				modify_account(get_op_change("dp","del"),$dp['user_id'],$reason);
				$reason .= get_op_change_show("dp","del");
			}
			
			if($change==1||$o_reason!="")
			send_user_msg("商户点评被删除",$reason,intval($GLOBALS['user_info']['id']),$dp['user_id'],TIME_UTC,0,true,true);		
			$cache_id  = md5("store"."view".$dp['supplier_location_id']);		
			$GLOBALS['tmpl']->clear_cache('store_view.html', $cache_id);	
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_dp_replydel($id,$change,$reason,$check=false)
	{
		$dp_reply = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location_dp_reply where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("dp","replydel"))
		{
			
			$sql = "delete from ".DB_PREFIX."supplier_location_dp_reply where id = ".$id;	
			$GLOBALS['db']->query($sql);
			$GLOBALS['db']->query("update ".DB_PREFIX."supplier_location_dp set reply_count = reply_count - 1 where id = ".intval($dp_reply['dp_id']));			

			$o_reason = $reason;	
			$reason .= "点评回应被删除";			
			if($change==1)
			{
				modify_account(get_op_change("dp","replydel"),$dp_reply['user_id'],$reason);
				$reason .= get_op_change_show("dp","replydel");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$dp_reply['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_topic_del($id,$change,$reason,$check=false)
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("topic","del"))
		{
			
			$GLOBALS['db']->query("update ".DB_PREFIX."topic_group set topic_count = topic_count - 1 where id = ".$topic['group_id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic where id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_cate_link where topic_id = ".$id);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set relay_count = relay_count - 1 where id = ".$topic['relay_id']);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set fav_count = fav_count - 1 where id = ".$topic['fav_id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_image where topic_id = ".$id);			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."topic_title set count = count - 1 where name = '".$topic['title']."'");
			$GLOBALS['db']->query("update ".DB_PREFIX."user set topic_count = topic_count - 1 where id = ".intval($topic['user_id']));
			if($topic['fav_id']>0)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set fav_count = fav_count - 1 where id = ".intval($topic['user_id']));
				$fav_topic = $GLOBALS['db']->getRow("select user_id,id,origin_id from ".DB_PREFIX."topic where id = ".$topic['fav_id']);
				
				$GLOBALS['db']->query("update ".DB_PREFIX."user set faved_count = faved_count - 1 where id = ".intval($fav_topic['user_id']));
				if($fav_topic['id']!=$fav_topic['origin_id'])
				{
					$fav_origin_topic = $GLOBALS['db']->getRow("select user_id,id,origin_id from ".DB_PREFIX."topic where id = ".$fav_topic['origin_id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set faved_count = faved_count - 1 where id = ".intval($fav_origin_topic['user_id']));
				}
			}
			if($topic['group']=='Fanwe')
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set insite_count = insite_count - 1 where id = ".intval($topic['user_id']));
			}
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_reply where topic_id = ".intval($topic['id']));			
			$o_reason = $reason;
			$reason .= "分享被删除";			
			if($change==1)
			{
				modify_account(get_op_change("topic","del"),$topic['user_id'],$reason);
				$reason .= get_op_change_show("topic","del");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_topic_replydel($id,$change,$reason,$check=false)
	{
		$topic_reply = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_reply where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("topic","replydel"))
		{			
			$sql = "delete from ".DB_PREFIX."topic_reply where id = ".$id;	
			$GLOBALS['db']->query($sql);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set reply_count = reply_count - 1 where id = ".intval($topic_reply['topic_id']));							
			$o_reason = $reason;
			$reason .= "分享回应被删除";			
			if($change==1)
			{
				modify_account(get_op_change("topic","replydel"),$topic_reply['user_id'],$reason);
				$reason .= get_op_change_show("topic","replydel");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic_reply['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_group_del($id,$change,$reason,$check=false)
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("group","del",$topic['group_id']))
		{			
			$GLOBALS['db']->query("update ".DB_PREFIX."topic_group set topic_count = topic_count - 1 where id = ".$topic['group_id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic where id = ".$id);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_cate_link where topic_id = ".$id);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set relay_count = relay_count - 1 where id = ".$topic['relay_id']);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set fav_count = fav_count - 1 where id = ".$topic['fav_id']);
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_image where topic_id = ".$id);			
			
			$GLOBALS['db']->query("update ".DB_PREFIX."topic_title set count = count - 1 where name = '".$topic['title']."'");
			$GLOBALS['db']->query("update ".DB_PREFIX."user set topic_count = topic_count - 1 where id = ".intval($topic['user_id']));
			if($topic['fav_id']>0)
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set fav_count = fav_count - 1 where id = ".intval($topic['user_id']));
				$fav_topic = $GLOBALS['db']->getRow("select user_id,id,origin_id from ".DB_PREFIX."topic where id = ".$topic['fav_id']);
				
				$GLOBALS['db']->query("update ".DB_PREFIX."user set faved_count = faved_count - 1 where id = ".intval($fav_topic['user_id']));
				if($fav_topic['id']!=$fav_topic['origin_id'])
				{
					$fav_origin_topic = $GLOBALS['db']->getRow("select user_id,id,origin_id from ".DB_PREFIX."topic where id = ".$fav_topic['origin_id']);
					$GLOBALS['db']->query("update ".DB_PREFIX."user set faved_count = faved_count - 1 where id = ".intval($fav_origin_topic['user_id']));
				}
			}
			if($topic['group']=='Fanwe')
			{
				$GLOBALS['db']->query("update ".DB_PREFIX."user set insite_count = insite_count - 1 where id = ".intval($topic['user_id']));
			}
			$GLOBALS['db']->query("delete from ".DB_PREFIX."topic_reply where topic_id = ".intval($topic['id']));			
			$o_reason = $reason;
			$reason .= "分享被删除";			
			if($change==1)
			{
				modify_account(get_op_change("group","del"),$topic['user_id'],$reason);
				$reason .= get_op_change_show("group","del");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_group_replydel($id,$change,$reason,$check=false)
	{
		$topic_reply = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_reply where id = ".$id);
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$topic_reply['topic_id']);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("group","replydel",$topic['group_id']))
		{			
			$sql = "delete from ".DB_PREFIX."topic_reply where id = ".$id;	
			$GLOBALS['db']->query($sql);
			$GLOBALS['db']->query("update ".DB_PREFIX."topic set reply_count = reply_count - 1 where id = ".intval($topic_reply['topic_id']));							
			$o_reason = $reason;
			$reason .= "分享回应被删除";			
			if($change==1)
			{
				modify_account(get_op_change("group","replydel"),$topic_reply['user_id'],$reason);
				$reason .= get_op_change_show("group","replydel");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic_reply['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_group_setbest($id,$change,$reason,$check=false)
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$id);
		if($check)
		{
			if($topic['is_best']==0)
			{
				$result['status'] = 1;
				return $result;		
			}
			else
			{
				$result['status'] = 0;
				return $result;	
			}			
		}
		if(check_user_auth("group","setbest",$topic['group_id']))
		{			
			
			if($topic['is_best'] == 0)
			{
				$sql = "update ".DB_PREFIX."topic set is_best = 1 where id = ".$id;	
				$GLOBALS['db']->query($sql);
				$o_reason = $reason;
				$reason .= "分享被推荐";			
				if($change==1)
				{
					modify_account(get_op_change("group","setbest"),$topic['user_id'],$reason);
					$reason .= get_op_change_show("group","setbest");
				}
				if($change==1||$o_reason!="")
				send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic['user_id'],TIME_UTC,0,true,true);	
			}	
			else
			{
				$sql = "update ".DB_PREFIX."topic set is_best = 0 where id = ".$id;	
				$GLOBALS['db']->query($sql);
			}
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_group_settop($id,$change,$reason,$check=false)
	{
		$topic = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic where id = ".$id);
		if($check)
		{
			if($topic['is_top']==0)
			{
				$result['status'] = 1;
				return $result;		
			}
			else
			{
				$result['status'] = 0;
				return $result;	
			}			
		}
		if(check_user_auth("group","settop",$topic['group_id']))
		{			
			
			if($topic['is_top'] == 0)
			{
				$sql = "update ".DB_PREFIX."topic set is_top = 1 where id = ".$id;	
				$GLOBALS['db']->query($sql);
				$o_reason = $reason;
				$reason .= "分享被置顶";			
				if($change==1)
				{
					modify_account(get_op_change("group","settop"),$topic['user_id'],$reason);
					$reason .= get_op_change_show("group","settop");
				}
				if($change==1||$o_reason!="")
				send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$topic['user_id'],TIME_UTC,0,true,true);	
			}	
			else
			{
				$sql = "update ".DB_PREFIX."topic set is_top = 0 where id = ".$id;	
				$GLOBALS['db']->query($sql);
			}
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_group_setmemo($id,$change,$reason,$check=false)
	{
		$group = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."topic_group where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("group","setmemo",$group['id']))
		{
			
			$sql = "update ".DB_PREFIX."topic_group set memo = '".$reason."' where id = ".$id;	
			$GLOBALS['db']->query($sql);
			
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	function op_msg_del($id,$change,$reason,$check=false)
	{
		$message = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."message where id = ".$id);
		if($check)
		{
			$result['status'] = 1;
			return $result;					
		}
		if(check_user_auth("msg","del"))
		{			
			$sql = "delete from ".DB_PREFIX."message where id = ".$id;	
			$GLOBALS['db']->query($sql);
								
			$o_reason = $reason;
			$reason .= "留言被删除";			
			if($change==1)
			{
				modify_account(get_op_change("msg","del"),$message['user_id'],$reason);
				$reason .= get_op_change_show("msg","del");
			}
			if($change==1||$o_reason!="")
			send_user_msg($reason,$reason,intval($GLOBALS['user_info']['id']),$message['user_id'],TIME_UTC,0,true,true);		
			$result['status'] = 1;			
		}	
		else
		{
			$result['status'] = 0;
			$result['info'] = "没有权限";
		}
		return $result;
	}
	
	
	
}
?>