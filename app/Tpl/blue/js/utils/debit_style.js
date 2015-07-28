/*index*/
$(document).ready(function(){
		$(".select_this").click(function(){
			if(!$(".select_list").hasClass("y"))
			{
				$(".select_list").show();
				$(".select_list").addClass("y");
				$(".select_this").addClass("y");
				
				$(".select_list").hover(
			function(){
				$(this).show();
				$(".select_list li").hover(
			function(){
				$(this).addClass("y");
				$(this).siblings().removeClass("y");
				$(this).click(function(){
					var value_0=$(this).html();
					$(".select_this").html(value_0);
					$(".select_list").hide();
				});
			});
			
			},
			function(){
				$(this).hide();
				$(".select_list").removeClass("y");
				$(".select_this").removeClass("y");
			});
			}
			else{
				$(".select_list").hide();
				$(".select_list").removeClass("y");
				$(".select_this").removeClass("y");
			}
		});
		
			$(".type_choose label").click(function(){
				$(this).addClass("label_y");
				$(this).siblings().removeClass("label_y");
			});
			
			$(".Process ul li").hover(function(){
				$(this).addClass("y");
				$(this).siblings().removeClass("y");
			},
			function(){
				$(this).removeClass("y");
				
				
			})
	});
	
	/*#scrollDiv轮播*/
	function AutoScroll(obj)
{
	$(obj).find("ul:first").animate(
	{
		marginTop:"-93px"
	},400,function()
	{
		$(this).css({marginTop:"0px"}).find("li:first").appendTo(this);
	});
}
$(document).ready(function(){
	setInterval('AutoScroll("#scrollDiv")',3000)
});

/*help_center.html*/
$(document).ready(function(){
	$(".help_center .nav_list li").click(function(){
		$(this).addClass("y");
		$(this).siblings().removeClass("y");
		var x=$(this).index();
		
		$(".content_list li").eq(x).siblings().hide();
	    $(".content_list li").eq(x).show();
		
		//alert(x);
	});
	
	$("button.type_but").click(
	function(){
		if($("input[type='radio'][name='type']:checked").length == 0)
		{
			alert("请选择白条类型");
			return false;
		};
		
		if($("input[type='radio'][name='debit_money']:checked").length == 0)
		{
			alert("请选择白条金额");
			return false;
		};
		
		$("#repaytime").val($(".select_this").html().replace("个月",""));

		//location.href=$("#form1").attr("action")+'&type='+$("input[type='radio'][name='type']:checked").val()+"&money="+$("input[type='radio'][name='debit_money']:checked").val();
		
		return true;
	});
});
