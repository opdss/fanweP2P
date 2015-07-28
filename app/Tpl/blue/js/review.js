//关于点评页点评动态效果的jQ扩展
(function($) {      
	//主评分的控制
	$.fn.rating_main = function() {
		var level_data = new Array("差","一般","好","很好","非常好");
		var outBar = $(this);
		$(outBar).find("span").find("input").val(0);	
	    var total_width = $(outBar).width();
	    var sec_width = total_width / 5;	    
	    $(outBar).bind("mousemove mouseover",function(event){
	    	//绑定移动事件
	    	var pageX = event.pageX; //左移量
	    	var left = $(outBar).offset().left;
	    	var move_left = pageX - left;	    	
	    	var sector = Math.ceil(move_left/sec_width);
	    	var cssWidth = (sector * sec_width) + "px";
	    	var tip = level_data[sector - 1];
	    	$("#score_tips").find("span").html(tip);
	    	$(outBar).find("span").attr("sector",sector);
	    	$(outBar).find("span").css("width",cssWidth);	    	
	    });
	    $(outBar).bind("mouseout",function(){
	    	var current_sec = $(outBar).find("span").find("input").val();
	    	var cssWidth = (current_sec * sec_width) + "px";
	    	if(current_sec == 0 )
	    	{
	    		$("#score_tips").find("span").html("点击星星为商家打分，最高5颗星");
	    	}
	    	else
	    	{
	    		$("#dp_point_tips").hide();
	    		var tip = level_data[current_sec - 1];
		    	$("#score_tips").find("span").html(tip);		    	
	    	}
	    	
	    	$(outBar).find("span").css("width",cssWidth);	
	    });
	    $(outBar).bind("click",function(){
	    	var current_sec = $(outBar).find("span").attr("sector");
	    	$(outBar).find("span").find("input").val(current_sec);	
	    });
	    
	};   
	
	
	//子评分控制
	$.fn.rating_sub = function(){	
		var level_data = new Array("差","一般","好","很好","非常好");		
		var outBar = $(this).find(".data_bar");
		var tipBar = $(this).find(".data_bar_tags");
		var errorBar = $(this).find(".error_tips");
		$(outBar).find("span").find("input").val(0);	//初始化为0
	    var total_width = $(outBar).width();
	    var sec_width = total_width / 5;	    
	    $(outBar).bind("mousemove mouseover",function(event){
	    	//绑定移动事件
	    	var pageX = event.pageX; //左移量
	    	var left = $(outBar).offset().left;
	    	var move_left = pageX - left;	    	
	    	var sector = Math.ceil(move_left/sec_width);
	    	var cssWidth = (sector * sec_width) + "px";
	    	var tip = level_data[sector - 1];
	    	$(tipBar).html(tip);
	    	$(outBar).find("span").attr("sector",sector);
	    	$(outBar).find("span").css("width",cssWidth);	    	
	    });
	    $(outBar).bind("mouseout",function(){
	    	var current_sec = $(outBar).find("span").find("input").val();
	    	var cssWidth = (current_sec * sec_width) + "px";
	    	if(current_sec == 0 )
	    	{
	    		$(tipBar).html("");
	    	}
	    	else
	    	{
	    		var tip = level_data[current_sec - 1];
	    		$(tipBar).html(tip);
	    	}
	    	$(outBar).find("span").css("width",cssWidth);	
	    });
	    $(outBar).bind("click",function(){
	    	$(errorBar).hide();
	    	var current_sec = $(outBar).find("span").attr("sector");
	    	$(outBar).find("span").find("input").val(current_sec);	
	    	
	    });
		
		
	};
	
	//绑定点评标签点击
	$.fn.tag_dp = function(){
		var rows = $(this);

		for(k=0;k<rows.length;k++)
		{
			var tag_text = $(rows[k]).find(".tag_text");
			var tag_tips = $(rows[k]).find(".tag_tips").find("a");
			var tags_content = $(tag_text).val();
			//初始化选中事件
			tags_content = tags_content.replace(/[，|,| ]+/g," ");
			var input_arr = tags_content.split(" ");
			for(i=0;i<input_arr.length;i++)
			{
				for(j=0;j<tag_tips.length;j++)
				{
					if($.trim(input_arr[i]) == $.trim($(tag_tips[j]).html()))
					{
						$(tag_tips[j]).addClass("current");
					}
				}
			}
		}
	};
	
	/**
	 * type: good_count/bad_count  有用/没用
	 * rec_id: 对应数据ID
	 * rec_module:对应数据模型
	 * memo:内容
	 * func 回调
	 */
	$.Vote_Flower=function(obj,type,rec_id,memo,rec_module,fun)
	{
		var query = new Object();
		query.type = type;
		query.rec_id = rec_id;
		query.memo = memo;
		query.rec_module = rec_module;
		$.ajax({
			url: APP_ROOT+"/store.php?ctl=review&act=flower",
			type: "POST",
			data:query,
			dataType: "json",
			success: function(result){
				if(result.status==2)
				{
					ajax_login();
					return ;
				}
				if(fun != null)
					fun.call(this,obj,result);
			}
		});
	}
	
})(jQuery); 


function set_tag(obj)
{
	var input_value = $(obj).parent().parent().find(".tag_text").val();	
	var select_value = $(obj).html();
	input_value = input_value.replace(/[，|,| ]+/g," ");
	input_arr = input_value.split(" ");
	var find = false;
	for(i=0;i<input_arr.length;i++)
	{
		if(input_arr[i]==select_value)
		{
			input_arr[i] = '';
			find = true;
			break;
		}
	}
	
	if(!find)
	{
		input_arr.push(select_value);
		$(obj).addClass("current");
	}
	else
	{
		$(obj).removeClass("current");
	}
	var new_arr = new Array();
	for(i=0;i<input_arr.length;i++)
	{
		if($.trim(input_arr[i])!='')
		new_arr.push(input_arr[i]);
	}


	$(obj).parent().parent().find(".tag_text").val(new_arr.join(" "));	
}

function init_dp_form()
{
	$("#comment_btn").bind("click",function(){
		
		//验证总评		
		var dp_point = $("input[name='dp_point']").val();
		if(dp_point==0)
		{
			$("#dp_point_tips").show();
			return;
		}
		
		//验证子评分
		var dp_sub_bar = $(".dp_sub_bar");
		for(i=0;i<dp_sub_bar.length;i++)
		{
			var point = $(dp_sub_bar[i]).find("input[type='hidden']").val();
			if(point==0)
			{
				$(dp_sub_bar[i]).find(".error_tips").show();
				return;
			}
		}
		
		//验证内容
		if($("input[name='dp_title']").val()=='')
		{
			$.showErr("请输入点评的标题");
			return;
		}
		if($("#main_topic_form_textarea").val().length<20)
		{
			$.showErr("点评内容不能低于20个字");
			return;
		}
		
		var formdata = $("form[name='review_form']").serialize();
		var mid = $("input[name='supplier_location_id']").val();
		var deal_cate_id = $("input[name='deal_cate_id']").val();
	
		$.ajax({
			url: APP_ROOT+"/store.php?ctl=review&act=savereview",
			type: "POST",
			data:formdata,
			dataType: "json",
			success: function(result){
				if(result.status==1)
				{
					$.weeboxs.open('<div style="text-align:center; line-height:90px; font-size:14px;">成功发表一篇点评，谢谢您的参与。</div>', {contentType:'text',draggable:false,showButton:false,title:"点评提交成功",width:600,type:'wee'});			
					load_review_form(mid,deal_cate_id);
				}
				else if(result.status==2)
				{
					ajax_login();
				}
				else{
					$.showErr(result.message);
				}
			},
			error:function(o)
			{
//				alert(o.responseText);
			}
		});
		
	});
}

$(document).ready(function(){
	$(".starsBar").rating_main();
	var sub_bar = $(".dp_sub_bar");
	for(i=0;i<sub_bar.length;i++)
	{
		$(sub_bar[i]).rating_sub();
	}	
	$(".tag_bar").tag_dp();
	init_dp_form();
});