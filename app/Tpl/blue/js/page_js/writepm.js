function set_to_user(user_name)
{
	$("input[name='user_name']").val(user_name);
}

$(document).ready(function(){
	$("input[name='user_name']").bind("blur",function(){
		check_send($(this));
	});
});

function do_send_pm()
{
	var uname = $("input[name='user_name']").val();
	var content = $("textarea[name='content']").val();
	if($.trim(uname)=='')
	{
		$.showErr("请选择收件人");
		return;
	}
	if($.trim(content)=='')
	{
		$.showErr("请输出消息内容");
		return;
	}
	if(content.length>200)
	{
		$.showErr("消息长度不能超过200个字");
		return;
	}
	
	
	//验证收件人
	var query = new Object();
	query.user_name = uname;
	var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=check_send";
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		type: "POST",
		data:query,
		success: function(ajaxobj){
			if(ajaxobj.status==0)
			{
				formError($("input[name='user_name']"),LANG['FANS_ONLY']);
			}
			else
			{
				//验证通过
				send_pm(uname,content);
			}
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});	
	
}


function do_reply_pm()
{
	var uname = $("input[name='user_name']").val();
	var content = $("textarea[name='content']").val();
	if($.trim(uname)=='')
	{
		$.showErr("请选择收件人");
		return;
	}
	if($.trim(content)=='')
	{
		$.showErr("请输出消息内容");
		return;
	}
	if(content.length>200)
	{
		$.showErr("消息长度不能超过200个字");
		return;
	}
	
	
	send_pm(uname,content);	
	
}

function send_pm(uname,content)
{
	var query = new Object();
	query.user_name = uname;
	query.content = content;
	var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=send_pm";
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		type: "POST",
		data:query,
		success: function(ajaxobj){
			if(ajaxobj.status==0)
			{
				$.showErr(ajaxobj.info);
			}
			else
			{
				location.href = ajaxobj.info;
			}
		},
		error:function(ajaxobj)
		{
//			if(ajaxobj.responseText!='')
//			alert(ajaxobj.responseText);
		}
	});	
}

function check_send(obj)
{
	var uname = $(obj).val();
	if(uname!="")
	{
		var query = new Object();
		query.user_name = uname;
		var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=check_send";
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			type: "POST",
			data:query,
			success: function(ajaxobj){
				if(ajaxobj.status==0)
				{
					formError(obj,"只能发件给粉丝");
				}
				else
				{
					$(obj).parent().find(".f-input-tip").html("");
				}
			},
			error:function(ajaxobj)
			{
//				if(ajaxobj.responseText!='')
//				alert(ajaxobj.responseText);
			}
		});	
	}	
}









//删除功能
$(document).ready(function(){
	$("input[name='checkall']").bind("click",function(){
		$("input[name='id[]']").attr("checked",$("input[name='checkall']").attr("checked"));
	});
	$("input[name='del_pm']").bind("click",function(){
		drop_pm_item();
	});
});

function drop_pm_item()
{
	var cbos = $("input[name='id[]']:checked");
	if(cbos.length==0)
	{
		$.showErr(LANG['PLEASE_SELECT_PM']);
	}
	else
	{
		if(confirm(LANG['CONFIRM_DELETE_PMGROUP']))
		{
			var query = $("form[name='pm_list']").serialize();
			var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=drop_pm_item";
			$.ajax({ 
				url: ajaxurl,
				dataType: "json",
				type: "POST",
				data:query,
				success: function(ajaxobj){
					if(ajaxobj.status==1)
					{
						$("input[type='checkbox']").attr("checked",false);
						$.showSuccess(ajaxobj.info,function(){							
							location.reload();
						});
					}
					else
					{
						$.showErr(ajaxobj.info);
					}
				},
				error:function(ajaxobj)
				{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
				}
			});	
		}
		
	}
}