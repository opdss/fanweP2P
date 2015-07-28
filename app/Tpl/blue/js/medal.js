$(document).ready(function(){
	$(".medal_item").find("a").bind("click",function(){
		get_medal($(this).attr("rel"));
	});
});
function get_medal(id)
{
	$.weeboxs.open(APP_ROOT+"/index.php?ctl=uc_medal&act=load_medal&id="+id, {contentType:'ajax',showButton:false,title:"获取勋章",width:400,type:'wee'});	
}

function imp_get_medal(id)
{
	var ajaxurl = APP_ROOT+"/index.php?ctl=uc_medal&act=get_medal&id="+id;
	$.ajax({ 
		url: ajaxurl,
		type: "POST",
		dataType: "json",
		success: function(data){
			if(data.status==0)
			{
				$.showErr(data.info,function(){
					location.href = data.jump;
				});
				
			}
			else if(data.status==1)
			{
				$.showSuccess("领取成功",function(){location.reload();});
			}
			else
			{
				$.showErr(data.info);
			}
		},
		error:function(ajaxobj)
		{			
			alert(ajaxobj.responseText);
		}
	});	
}