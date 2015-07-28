function account(user_id)
{
	$.weeboxs.open(ROOT+'?m=User&a=account&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_ACCOUNT'],width:600,height:260});
}
function account_detail(user_id)
{
	location.href = ROOT + '?m=User&a=account_detail&id='+user_id;
}
function agencies_account_detail(user_id)
{
	location.href = ROOT + '?m=User&a=agencies_account_detail&id='+user_id;
}

function user_passed(user_id)
{
	window.location.href = ROOT+'?m=User&a=passed&id='+user_id;
	/*$.weeboxs.open(ROOT+'?m=User&a=passed&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_PASSED'],width:600,height:400});*/
}

function agency_passed(user_id)
{
	window.location.href = ROOT+'?m=DealAgency&a=passed&id='+user_id;
	/*$.weeboxs.open(ROOT+'?m=User&a=passed&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_PASSED'],width:600,height:400});*/
}

function agencies_passed(user_id)
{
	window.location.href = ROOT+'?m=User&a=agencies_passed&id='+user_id;
	/*$.weeboxs.open(ROOT+'?m=User&a=passed&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_PASSED'],width:600,height:400});*/
}

function eidt_lock_money(user_id){
	$.weeboxs.open(ROOT+'?m=User&a=lock_money&id='+user_id, {contentType:'ajax',showButton:false,title:LANG['USER_LOCK_MONEY'],width:600,height:400});
}

function info_down(user_id){
	$.weeboxs.open(ROOT+'?m=User&a=info_down&id='+user_id, {contentType:'ajax',showButton:false,title:"资料",width:600,height:400});
}

function view_info(user_id){
	$.weeboxs.open(ROOT+'?m=User&a=view_info&id='+user_id, {contentType:'ajax',showButton:false,title:"证件预览",width:1000,height:500});
}


function bank_manage(user_id){
	window.location.href=ROOT+'?m=User&a=bank_manage&id='+user_id;
}

function load_manage(user_id){
	window.location.href=ROOT+'?m=Loads&a=index&user_id='+user_id;
}
function user_company(user_id){
	$.weeboxs.open(ROOT+'?m=User&a=company&id='+user_id, {contentType:'ajax',showButton:false,title:"公司信息",width:800,height:400});
}

//改变状态
function set_black(id, domobj){
	var do_ajax = false;
	var data = $(domobj).attr("data");
	if (data == "0" && confirm("是否要设置成黑名单?\n设置成黑名单将无法 贷款 和 提现 ！")) {
		do_ajax = true;
	}
	
	if (data == "1" && confirm("是否要取消黑名单?")) {
		do_ajax = true;
	}
	
	if (do_ajax) {
		$.ajax({
			url: ROOT + "?" + VAR_MODULE + "=" + MODULE_NAME + "&" + VAR_ACTION + "=set_black&id=" + id,
			data: "ajax=1",
			dataType: "json",
			success: function(obj){
			
				if (obj.data == '1') {
					$(domobj).html("是");
					$(domobj).attr("data",obj.data);
				}
				else 
					if (obj.data == '0') {
						$(domobj).html("否");
						$(domobj).attr("data",obj.data);
					}
					else 
						if (obj.data == '') {
						
						}
				$("#info").html(obj.info);
			}
		});
	}
}