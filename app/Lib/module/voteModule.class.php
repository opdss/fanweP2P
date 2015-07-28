<?php
// +----------------------------------------------------------------------
// | Fanwe 方维p2p借贷系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class voteModule extends SiteBaseModule
{
	public function index()
	{
		$GLOBALS['tmpl']->caching = true;
		$cache_id  = md5(MODULE_NAME.ACTION_NAME.$GLOBALS['deal_city']['id']);		
		if (!$GLOBALS['tmpl']->is_cached('page/vote_index.html', $cache_id))	
		{			 
			$now = TIME_UTC;
			$vote = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote where is_effect = 1 and begin_time < ".$now." and (end_time = 0 or end_time > ".$now.") order by sort desc limit 1");
	
			if($vote)
			{
				$vote_ask = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."vote_ask where vote_id = ".intval($vote['id'])." order by sort asc");
				
				foreach($vote_ask as $k=>$v)
				{
					$vote_ask[$k]['val_scope'] = preg_split("/[ ,]/i",$v['val_scope']);
				}			
				
				$GLOBALS['tmpl']->assign("vote",$vote);
				$GLOBALS['tmpl']->assign("vote_ask",$vote_ask);
				$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['VOTE']);
				
			}
			else
			{
				showErr($GLOBALS['lang']['NO_VOTE'],0,APP_ROOT);
			}
		}
		$GLOBALS['tmpl']->display("page/vote_index.html",$cache_id);
	}
	
	public function dovote()
	{
		$ok = false;
		foreach($_REQUEST['name'] as $vote_ask_id=>$names)
		{			
				foreach($names as $kk=>$name)
				{
					if($name!='')
					{
						$ok = true;
					}
				}
		}
		if(!$ok)
		{
			showErr($GLOBALS['lang']['YOU_DONT_CHOICE']);
		}
		$vote_id = intval($_REQUEST['vote_id']);
		if(check_ipop_limit(CLIENT_IP,"vote",3600,$vote_id))
		{
			foreach($_REQUEST['name'] as $vote_ask_id=>$names)
			{
				
				foreach($names as $kk=>$name)
				{
					$name = htmlspecialchars(addslashes(trim($name)));
					
					$result = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."vote_result where name = '".$name."' and vote_id = ".$vote_id." and vote_ask_id = ".$vote_ask_id);
					$is_add = true;
					if($result)
					{
						$GLOBALS['db']->query("update ".DB_PREFIX."vote_result set count = count + 1 where name = '".$name."' and vote_id = ".$vote_id." and vote_ask_id = ".$vote_ask_id);
						if(intval($GLOBALS['db']->affected_rows())!=0)
						{
							$is_add = false;
						}
					}
					
					if($is_add)
					{
						if($name!='')
						{
							$result = array();
							$result['name'] = $name;
							$result['vote_id'] = $vote_id;
							$result['vote_ask_id'] = $vote_ask_id;
							$result['count'] = 1;
							$GLOBALS['db']->autoExecute(DB_PREFIX."vote_result",$result);
						}
					}
				}
			}
		
			showSuccess($GLOBALS['lang']['VOTE_SUCCESS']);
		}
		else
		{
			showErr($GLOBALS['lang']['YOU_VOTED']);
		}
	}
}
?>