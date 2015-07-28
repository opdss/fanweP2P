function init_dealform()
{
	//绑定副标题20个字数的限制
	$("input[name='sub_name']").bind("keyup change",function(){
		if($(this).val().length>20)
		{
			$(this).val($(this).val().substr(0,20));
		}		
	});
}

jQuery(function(){
	$('#colorpickerField').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			if(hex!=""){
				$(el).css({"background":"#"+hex});
			}
			else{
				$(el).css({"background":"#FFFFFF"});
				$(el).val("");
			}
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
			if(this.value!=""){
				$(this).css({"background":"#"+this.value});
			}
			else{
				$(this).css({"background":"#FFFFFF"});
				$(this).val("");
			}
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
		if(this.value!=""){
			$(this).css({"background":"#"+this.value});
		}
		else{
			$(this).css({"background":"#FFFFFF"});
			$(this).val("");
		}
	});
	
	$('#colorpickerField').blur(function(){
		
		if($(this).val()!=""){
			$(this).css({"background":"#"+this.value});
		}
		else{
			$(this).css({"background":"#FFFFFF"});
		}
	});
	
	//绑定会员ID检测
	$("input[name='user_name']").bind("blur",function(){
		var user_id = parseInt($("input[name='user_id']").val());
		if(user_id > 0)
		{
			$.ajax({
				url:ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_user&id="+user_id, 
				dataType:"json",
				success:function(result){
					if(result.status ==1)
					{
						if(result.user.services_fee)
							$("input[name='services_fee']").val(parseFloat(result.user.services_fee));
						else
							$("input[name='services_fee']").val("5");
						
						var img_html ="";
						$.each(result.user.old_imgdata_str,function(i,v){
							img_html +='<p style="float:left">';
							img_html +='<input style=" margin-top: 12px;"  type="checkbox" name="key[]" value="'+i+'">';
							img_html +='<a href="'+v.img+'" target="_blank" title="'+v.name+'"><img width="35" height="35" style="float:left; border:#ccc solid 1px; margin-left:5px;" id="'+v.name+'" src="'+v.img+'"></a>';
							img_html +="</p>";
						});

						$("#view_user_img_box").html(img_html);
						
						$("#J_user_name").html(result.user.user_name);
						if(result.user.deal_info.manage_fee!=undefined){
							$("input[name='manage_fee']").val(result.user.deal_info.manage_fee);
						}
					}
					else{
						alert("会员不存在");
						$("input[name='user_id']").val("");
						$("input[name='user_user']").val("");
						$("#J_user_name").html("");
						$("#view_user_img_box").html("");
						$("input[name='services_fee']").val("");
					}
				}
			});
		
			
		}		
	});
	
	$("input[name='deal_status']").live("click",function(){
		$("input[name='is_delete']").attr("checked",false);
		$("#delele_msg_box").hide();
		deal_status_click(this);
	});
	
	$("select[name='agency_id']").change(function(){
		if($(this).val()==0){
			$("select[name='warrant']").val(0);
			$("#guarantor_margin_amt_box").hide();
			$("#guarantor_amt_box").hide();
			$("#guarantor_pro_fit_amt_box").hide();
		}
	});
	
	$("select[name='warrant']").change(function(){
		if($(this).val()!=0){
			$("#guarantor_margin_amt_box").show();
			$("#guarantor_amt_box").show();
			$("#guarantor_pro_fit_amt_box").show();
		}
		else{
			$("#guarantor_margin_amt_box").hide();
			$("#guarantor_amt_box").hide();
			$("#guarantor_pro_fit_amt_box").hide();
		}
	});
	
	$("select[name='loantype']").change(function(){
		BloadType($(this).val());
	});
	
	$("#repay_time_type").change(function(){
		var val = $(this).val();
		var select_rel = $("select[name='loantype'] option[value='"+$("select[name='loantype']").val()+"']").attr("rel");
		var select_rel_str = select_rel.split(",");
		var seleted = -1;
		for(var i=0;i<select_rel_str.length;i++){
			if(seleted == -1 && parseInt(val)==parseInt(select_rel_str[i])){
				seleted = 0;
			}
		}
		if(seleted==-1){
			$("select[name='loantype'] option").each(function(){
				var rel = $("select[name='loantype'] option[value='"+$(this).attr("value")+"']").attr("rel");
				var rel_str = rel.split(",");
				for(var i=0;i<rel_str.length;i++){
					if(seleted == -1 && parseInt(val)==parseInt(rel_str[i])){
						$("select[name='loantype']").val($(this).attr("value"));
						seleted = 0;
					}
				}
			});
		}
	});
	
	$("input[name='is_delete']").click(function(){
		if ($(this).val() == "3") {
			$("input[name='deal_status']").attr("checked",false);
			$("#delele_msg_box").show();
		}
		deal_status_click();
		return true;
	});
	
	$("#citys_box .item .bcity input").click(function(){
		var obj = $(this);
		if(obj.attr("checked") == true ||　obj.attr("checked") == "checked"){
			obj.parent().parent().find(".scity input").attr("checked","checked");
		}
		else{
			obj.parent().parent().find(".scity input").attr("checked","");
		}
	});
	
	$("select[name='uloadtype']").change(function(){
		var val = $(this).val();
		switch(val){
			case "0":
				$(".uloadtype_0").show();
				$(".uloadtype_1").hide();
				break;
			case "1":
				$(".uloadtype_0").hide();
				$(".uloadtype_1").show();
				break;
		}
	});
	
	$("input[name='min_loan_money'],input[name='portion'],input[name='max_loan_money'],input[name='max_portion'],input[name='borrow_amount']").change(function(){
		var uloadtype= parseInt($("select[name='uloadtype']").val());
		var borrow_amount= parseInt($("input[name='borrow_amount']").val());
		var min_loan_money = parseInt($("input[name='min_loan_money']").val());
		var max_loan_money = parseInt($("input[name='max_loan_money']").val());
		var portion = parseInt($("input[name='portion']").val());
		var max_portion = parseInt($("input[name='max_portion']").val());
		
		if(min_loan_money > 0 && uloadtype == 0){
			if(borrow_amount%min_loan_money == 0){
				 $("input[name='portion']").val(borrow_amount/min_loan_money);
			}
			else{
				$("input[name='portion']").val(0);
			}
		}
		
		if(portion > 0 && uloadtype == 1){
			if(borrow_amount%portion == 0){
				 $("input[name='min_loan_money']").val(borrow_amount/portion);
			}
			else{
				$("input[name='min_loan_money']").val(0);
			}
		}
		
		if(max_loan_money >0 && uloadtype == 0){
			if(min_loan_money > 0 && max_loan_money%min_loan_money==0 ){
				if(max_loan_money > borrow_amount){
					$("input[name='max_loan_money']").val(borrow_amount);
					$("input[name='max_portion']").val(borrow_amount / min_loan_money);
				}
				else
					$("input[name='max_portion']").val(max_loan_money / min_loan_money);
			}
			else{
				$("input[name='max_loan_money']").val(0);
				$("input[name='max_portion']").val(0);
			}
		}
		
		if(max_portion >0 && portion > 0 && uloadtype == 1){
			if(max_portion<=portion){
				 $("input[name='max_loan_money']").val(max_portion*(borrow_amount/portion));
			}
			else{
				$("input[name='max_portion']").val(portion);
				$("input[name='max_loan_money']").val(borrow_amount);
			}
		}
		
	});
	
	
	$("input[name='min_loan_money'],input[name='portion'],input[name='max_loan_money'],input[name='max_portion'],input[name='borrow_amount']").blur(function(){
		var uloadtype= parseInt($("select[name='uloadtype']").val());
		var borrow_amount= parseInt($("input[name='borrow_amount']").val());
		var min_loan_money = parseInt($("input[name='min_loan_money']").val());
		var max_loan_money = parseInt($("input[name='max_loan_money']").val());
		var portion = parseInt($("input[name='portion']").val());
		var max_portion = parseInt($("input[name='max_portion']").val());
		if(min_loan_money > 0 && uloadtype==0){
			if(borrow_amount%min_loan_money != 0){
				alert("无法整除");
				$("input[name='min_loan_money']").val(0)
			}
		}
		
		if(portion > 0 && uloadtype==1){
			if(borrow_amount%portion != 0){
				alert("无法整除");
				$("input[name='portion']").val(0)
			}
		}
		
		
		if(max_loan_money > 0 && uloadtype==0){
			if(max_loan_money <=borrow_amount){
				$("input[name='max_loan_money']").val(max_loan_money);
				if(min_loan_money > 0 && max_loan_money%min_loan_money==0){
					$("input[name='max_portion']").val(max_loan_money/min_loan_money);
				}
				else{
					$("input[name='max_portion']").val(0);
				}
			}
			else{
				$("input[name='max_loan_money']").val(borrow_amount);
				if(min_loan_money > 0)
					$("input[name='max_portion']").val(borrow_amount/min_loan_money);
				else
					$("input[name='max_portion']").val(0);
			}
		}
		
	});
	
	BloadType($("select[name='loantype']").val());
});

function BloadType(val){
	var seleted = -1;
	var select_rel = $("select[name='loantype'] option[value='"+val+"']").attr("rel");
	var select_rel_str = select_rel.split(",");
	var repay_time_type = $("#repay_time_type").val();
	for(var i=0;i<select_rel_str.length;i++){
		if(seleted == -1 && parseInt(select_rel_str[i]) == parseInt(repay_time_type)){
			seleted = 0;
		}
	}
	
	if(seleted==-1){
		$("#repay_time_type option").each(function(){
			for(var i=0;i<select_rel_str.length;i++){
				if(seleted ==-1 && parseInt(select_rel_str[i]) == parseInt($(this).val())){
					$("#repay_time_type").val($(this).val());
					seleted = 0;
				}
			}
		});
	}
}


function deal_status_click(obj){
	$("#start_time_box #start_time").removeClass("require");
	switch($(obj).val()){
		case "1":
			$("#start_time_box").show();
			$("#start_time_box #start_time").addClass("require");
			break;
		default :
			$("#start_time_box").hide();
			break;
	}	
};

