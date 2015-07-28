var Jcash_From_Lock = false;
jQuery(function(){
	
	$("#Jcash_score").keyup(function(){
		setCashResult()
	});
	$("#Jcash_score").blur(function(){
		setCashResult()
	});
	
	$("#Jcash_From").submit(function(){
		if(Jcash_From_Lock){
			return false;
		}
		Jcash_From_Lock = true;
		if($.trim($("#Jcash_score").val())=="" || !$.checkNumber($("#Jcash_score").val()) || parseFloat($("#Jcash_score").val())<=0){
			Jcash_From_Lock = false;
			$.showErr(LANG.CARRY_MONEY_NOT_TRUE,function(){
				$("#Jcash_score").focus();
			});
			return false;
		}
		
		return true;
	});
	
	
});



function setCashResult(){
	var cash_score = 0;
	
	var total_score =  parseFloat($("#Jcash_totalScore").val());
	if ($.trim($("#Jcash_score").val()).length > 0) {
		if ($("#Jcash_score").val() == "-") {
			cash_score = "-0";
		}
		else{
			cash_score = parseFloat($("#Jcash_score").val());
		}
	}
	cash_score = parseFloat(cash_score);
	
	if(cash_score < 0){
		$("#Jcash_balance").html(LANG.CARRY_MONEY_NOT_TRUE);
	}
	else if(cash_score > total_score){
		$("#Jcash_balance").html("您的积分不足！");
	}
	else if(cash_score < 1000){
		$("#Jcash_balance").html("兑现最低只能1000积分");
	}
	else{
		if(cash_score%1000!=0){
			$("#Jcash_balance").html("兑现值必须为 1000 的倍数");	
		} 
		else
		{
			$("#Jcash_balance").html("");
		}
		
	}
	
	var coefficient =  parseFloat($("#Jcash_coefficient").val());
	
	var realAmount = cash_score*coefficient*0.01;
	$("#Jcash_realAmount").html(foramtmoney(realAmount,2)+" 元");
	

}


