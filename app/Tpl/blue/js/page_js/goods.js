var doexchange_lock = false;
$(document).ready(function(){
	$("#sku label").click(function(){
		$(this).siblings("label").removeClass("this_Property");
		$(this).addClass("this_Property");
		
	});
	
	$("#quxiao").live("click",function(){
		$.weeboxs.close("WB_ADDRESS");
	});
	
	$("#sku .Other_Property").click(function(){
		var t_score = parseFloat(sum_score);
		$("#sku .this_Property .mt").each(function(){
			if($(this).attr("checked")=="checked" || $(this).attr("checked")==true){
				t_score += parseFloat($(this).attr("rel"));
			}
		});
		$(".score").html(t_score);
		eachattr();
	});
	
	$("#add_newaddress").live("click",function(){
		$("#add_newaddress").removeClass("reset_btn");
		$("#add_newaddress").addClass("true_btn");
		$("#old_newaddress").removeClass("true_btn");
		$("#old_newaddress").addClass("reset_btn");
		$(".old_addr_box").addClass("hide");
		$(".new_addr_box").removeClass("hide");
		$("#add_addr").val(1);
	});
	
	$("#old_newaddress").live("click",function(){
		$("#old_newaddress").removeClass("reset_btn");
		$("#old_newaddress").addClass("true_btn");
		$("#add_newaddress").removeClass("true_btn");
		$("#add_newaddress").addClass("reset_btn");
		$(".new_addr_box").addClass("hide");
		$(".old_addr_box").removeClass("hide");
		$("#add_addr").val(0);
	});

});

function eachattr(){
	if(json_attr_stock!=null){
		var attr_str = "";
		var stock_number = 0;
		$("#sku input.mt:checked").each(function(){
			attr_str += $(this).attr("attrstr");
		});
		
		$.each(json_attr_stock,function(i,v){
			if(v.attr_str==attr_str){
				stock_number = v.stock_cfg - v.buy_count;
			}
		});
		$("#stock_number").html(stock_number);
	}
}


function doexchange(id,delivery,max_bought,user_can_buy_number)
{
	var stock_number = $.trim($("#stock_number").html());
	var number =  $.trim($("#number").val());
	var user_can_buy_number =  user_can_buy_number;
	if(number=="" || parseInt(number) <= 0){
		$.showErr("请输入正确的兑换数量",function(){
			$("#number").focus();
		});
		return false;
	}
	
	var select_attr = true;
	var  attr_tip = "";
	$("#sku .rows").each(function(){
		if($(this).find("input.mt:checked").length ==0){
			attr_tip +="请选择"+$(this).find(".t").attr("rel")+"<br>";
			select_attr =  false;
		}
	});
	
	if(!select_attr){
		$.showErr(attr_tip);
		return false;
	}
	
	if(parseInt(number) > user_can_buy_number){
		$.showErr("超出你所能兑换的数量");
		return false;
	}
	
	if(parseInt(number) > parseInt(stock_number)){
		$.showErr("库存不足");
		return false;
	}
	
	
	if(delivery == 1){
		isdelivery(id,number);
	}else{
		var query = $("#J_score_goods_form").serialize();
		doExchange(id,number,query);
	}
	
}

function isdelivery(id,number)
{
	doexchange_lock = true;
	var ajaxurl = APP_ROOT+"/index.php?ctl=goods_information&act=address";
	var query = $("#J_score_goods_form").serialize();
	$.ajax({
		url:ajaxurl,
		data:query+"&id="+id+"&number="+number,
		type: "POST",
		dataType:"json",
		success:function(result){
			if(result.status==1){
				$.weeboxs.open(result.info,{boxid:"WB_ADDRESS",contentType:'text',showButton:false,title:"收货地址",width:500,height:280,type:'wee',onopen:function(){
					init_ui_radiobox();
					init_ui_textbox();
				}});
			}
			else if(result.status=2){
				$.showErr(result.info,function(){
					ajax_login();
				});
			}
			else{
				$.showErr(result.info);
			}
		}
	});

}


function  doExchange(id,number,query){
	
	if(doexchange_lock){
		return false;
	}
	
	var ajaxurl = APP_ROOT+"/index.php?ctl=goods_information&act=doexchange";
	$.ajax({ 
		url: ajaxurl,
		data:query+"&goods_id="+id+"&number="+number,
		type: "POST",
		dataType: "json",
		success: function(result){
			doexchange_lock = false;
			if(result.status==1)
			{
				$.showSuccess(result.info,function(){
					window.location.href= result.jump;
				});
			}
			else
			{	
				$.showErr(result.info);
				return false;
			}
		}
	});	
}
