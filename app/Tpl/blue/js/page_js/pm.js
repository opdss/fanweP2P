$(document).ready(function(){
	$("input[name='checkall']").bind("click",function(){
		$("input[name='pm_key[]']").attr("checked",$("input[name='checkall']").attr("checked"));
	});
	$("input[name='del_pm']").bind("click",function(){
		drop_pm();
	});
	$("input[name='del_pmxiaoxi']").bind("click",function(){
		drop_pmxiaoxi();
	});
});

function drop_pm()
{
	var cbos = $("input[name='pm_key[]']:checked");
	if(cbos.length==0)
	{
		$.showErr(LANG['PLEASE_SELECT_PMGROUP']);
	}
	else
	{
		if(confirm(LANG['CONFIRM_DELETE_PMGROUP']))
		{
			var query = $("form[name='pm_list']").serialize();
			var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=drop_pm";
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


function drop_pmxiaoxi()
{
	var cbos = $("input[name='pm_key[]']:checked");
	if(cbos.length==0)
	{
		$.showErr(LANG['PLEASE_SELECT_PMGROUP']);
	}
	else
	{
		if(confirm(LANG['CONFIRM_DELETE_PMGROUP']))
		{
			var query = $("form[name='pm_list']").serialize();
		
			var ajaxurl = APP_ROOT+"/index.php?ctl=ajax&act=drop_pmxiaoxi";
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