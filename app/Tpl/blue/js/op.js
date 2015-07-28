function op_dp_setbest(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=dp&a_name=setbest&id="+id, {contentType:'ajax',showButton:false,title:"推荐点评",width:570,type:'wee'});	
}

function op_dp_del(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=dp&a_name=del&id="+id, {contentType:'ajax',showButton:false,title:"删除点评",width:570,type:'wee'});
}

function op_dp_replydel(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=dp&a_name=replydel&id="+id, {contentType:'ajax',showButton:false,title:"删除点评回应",width:570,type:'wee'});
}

function op_topic_del(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=topic&a_name=del&id="+id, {contentType:'ajax',showButton:false,title:"删除主题",width:570,type:'wee'});
}

function op_topic_replydel(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=topic&a_name=replydel&id="+id, {contentType:'ajax',showButton:false,title:"删除主题回应",width:570,type:'wee'});
}

function op_group_del(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=group&a_name=del&id="+id, {contentType:'ajax',showButton:false,title:"删除主题",width:570,type:'wee'});
}

function op_group_replydel(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=group&a_name=replydel&id="+id, {contentType:'ajax',showButton:false,title:"删除主题回应",width:570,type:'wee'});
}

function op_group_setbest(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=group&a_name=setbest&id="+id, {contentType:'ajax',showButton:false,title:"推荐主题",width:570,type:'wee'});
}

function op_group_settop(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=group&a_name=settop&id="+id, {contentType:'ajax',showButton:false,title:"置顶主题",width:570,type:'wee'});
}

function op_group_setmemo(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=setmemo&id="+id, {contentType:'ajax',showButton:false,title:"编辑小组说明",width:570,type:'wee'});
}

function op_msg_del(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=op&act=index&m_name=msg&a_name=del&id="+id, {contentType:'ajax',showButton:false,title:"删除留言",width:570,type:'wee'});
}
function do_submit_opform()
{
	var query = $("form[name='opform']").serialize();
	
	var ajaxurl = $("form[name='opform']").attr("action");
	$.ajax({ 
		url: ajaxurl,
		data:query,
		type: "POST",
		dataType: "json",
		success: function(o){
			if(o.status==1)
				location.reload();
			else
				$.showErr(o.info);
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});	
	
}


function user_sign()
{
	var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=signin";
	$.ajax({ 
		url: ajaxurl,
		type: "POST",
		dataType: "json",
		success: function(o){
			if(o.status==2)
			{
				ajax_login();
			}
			else
			{
				$.showSuccess(o.info,function(){location.reload();});
			}
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});	
}