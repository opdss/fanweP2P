<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class PromoteMsgListAction extends CommonAction{
	public function index()
	{
		if(trim($_REQUEST['dest'])!='')
		$condition['dest'] = array('like','%'.trim($_REQUEST['dest']).'%');
		if(trim($_REQUEST['content'])!='')
		$condition['content'] = array('like','%'.trim($_REQUEST['content']).'%');
		$this->assign("default_map",$condition);
		parent::index();
	}
	public function show_content()
	{
		$id = intval($_REQUEST['id']);
		header("Content-Type:text/html; charset=utf-8");
		echo M("PromoteMsgList")->where("id=".$id)->getField("content");
	}
	
//	private function url_pack_back($url,$id = 0)
//	{
//		$citys = $GLOBALS['db']->getAll("select id,name,is_open from ".DB_PREFIX."deal_city where is_effect = 1 and is_delete = 0");
//		$show_city = count($citys)>1?true:false;  //有多个城市时显示城市名称到url
//		$deal_city = $GLOBALS['db']->getRow("select id,name,uname from ".DB_PREFIX."deal_city where is_delete = 0 and is_effect = 1 and is_default = 1");
//		$arr = explode("#",$url);
//		$module = $arr[0];
//		$action = $arr[1];
//		if(app_conf("URL_MODEL")==0)
//			{
//				//原始			
//				$url = APP_ROOT."/".$module.".php?";
//				if($show_city)
//				{
//					$city_uname = $deal_city['uname'];
//					$url = $url."city=".$city_uname."&";
//				}			
//				
//				if($action&&$action!='')
//				$url .= "act=".$action."&";
//				if(intval($id)!=0)
//				$url .= "id=".$id."&";
//			}
//			else
//			{
//				//重写
//				if($show_city)
//				{
//					$city_uname = $deal_city['uname'];
//					$url = APP_ROOT."/".$city_uname;
//				}
//				else
//				{
//					$url = APP_ROOT;
//				}
//				if($module!='index')
//				$url = $url."/".$module;
//				if($action&&$action!=''&&$action!='index')
//				$url .= "/".$action;
//				if(intval($id)!=0)
//				$url .= "/".$id;
//			}
//			return $url;		
//	}
	public function send()
	{
		$id = intval($_REQUEST['id']);
		$msg_item = M("PromoteMsgList")->getById($id);
		if($msg_item)
		{
			if($msg_item['send_type']==0)
			{
				//短信
				require_once APP_ROOT_PATH."system/utils/es_sms.php";
				$sms = new sms_sender();
		
				$result = $sms->sendSms($msg_item['dest'],$msg_item['content']);
				$msg_item['result'] = $result['msg'];
				$msg_item['is_success'] = intval($result['status']);
				$msg_item['send_time'] = TIME_UTC;
				M("PromoteMsgList")->save($msg_item);
				if($result['status'])
				{					
					header("Content-Type:text/html; charset=utf-8");
					echo l("SEND_NOW").l("SUCCESS");
				}
				else
				{
					
					header("Content-Type:text/html; charset=utf-8");
					echo l("SEND_NOW").l("FAILED").$result['msg'];
				}
			}
			else
			{			
				//邮件
				require_once APP_ROOT_PATH."system/utils/es_mail.php";
				$mail = new mail_sender();
		
				$mail->AddAddress($msg_item['dest']);
				$mail->IsHTML($msg_item['is_html']); 				  // 设置邮件格式为 HTML
				$mail->Subject = $msg_item['title'];   // 标题
				

				if($msg_item['is_html'])
				$mail_content = $msg_item['content']."<br />".sprintf(app_conf("UNSUBSCRIBE_MAIL_TIP"),app_conf("SHOP_TITLE"),"<a href='".SITE_DOMAIN.APP_ROOT."/subscribe.php?act=unsubscribe&code=".base64_encode($msg_item['dest'])."'>".$GLOBALS['lang']['CANCEN_EMAIL']."</a>");
				else
				$mail_content = $msg_item['content']." \n ".sprintf(app_conf("UNSUBSCRIBE_MAIL_TIP"),app_conf("SHOP_TITLE"),$GLOBALS['lang']['CANCEN_EMAIL'].SITE_DOMAIN.APP_ROOT."/subscribe.php?act=unsubscribe&code=".base64_encode($msg_item['dest']));
								
				
				$mail->Body = $mail_content;  // 内容	
				$result = $mail->Send();
				
				$msg_item['result'] = $mail->ErrorInfo;
				$msg_item['is_success'] = intval($result);
				$msg_item['send_time'] = TIME_UTC;
				M("PromoteMsgList")->save($msg_item);
				if($result)
				{					
					header("Content-Type:text/html; charset=utf-8");
					echo l("SEND_NOW").l("SUCCESS");
				}
				else
				{
					
					header("Content-Type:text/html; charset=utf-8");
					echo l("SEND_NOW").l("FAILED").$mail->ErrorInfo;
				}
				
			}
		}
		else
		{
			header("Content-Type:text/html; charset=utf-8");
			echo l("SEND_NOW").l("FAILED");
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
					$info[] = $data['id'];	
				}
				if($info) $info = implode(",",$info);
				$list = M(MODULE_NAME)->where ( $condition )->delete();	
			
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
		
}
?>