<?php echo $this->fetch('./inc/header.html'); ?>
<?php
	$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/login.css";		
?>
<link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['pagecss'],
);
echo $k['name']($k['v']);
?>" />

<div class="login">
	<form>
        <input class="logininput mainborder " id="email" type="text" placeholder="输入用户名/邮箱/手机" />
        <input class="logininput mainborder" type="password" id="pwd" placeholder="输入密码"/>
        <input class="ui-button_login Headerbackground_dark" type="button" value="登录"/>
        <a class="f_l forgetpw" href="<?php
echo parse_wap_url_tag("u:index|save_reset_pwd|"."".""); 
?>">忘记密码？</a>
        <a class="f_r rgst" href="<?php
echo parse_wap_url_tag("u:index|register|"."".""); 
?>">注册账号</a>
        <div class="blank"></div>
    </form>
</div>
<script type="text/javascript">
	check_email();
	check_pwd();
	
	function check_email(){
		$("#email").blur(function(){
		var vlaue=$(this).val();
	    var val1='用户名';
		 check_null(vlaue,val1);
	});
	}
	
	function check_pwd(){
		$("#pwd").blur(function(){
		var vlaue=$(this).val();
	    var val1='密码';
		 check_null(vlaue,val1);
	});
	}
	

	function check_null(val,val1){
		if(!val)
		{
			alert(val1+'不能为空');
			return false;
		}
	}
	
	
	$(".ui-button_login").click(function(){
		var email_val=$("#email").val();
		var pwd=$("#pwd").val();
		if(!email_val)
		{
			alert("请填写用户名");
			return false;
		}
		if(!pwd)
		{
			alert("请填写密码");
			return false;
		}
		var ajaxurl = '<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>';
		var query = new Object();
		query.email = email_val;
		query.pwd = pwd;
		query.post_type = "json";
		
		//alert(ajaxurl);
		
		$.ajax({
			url:ajaxurl,
			data:query,
			type:"Post",
			dataType:"json",
			success:function(data){
				alert(data.show_err);
				if(data.user_login_status == 1)
				{
					if(document.referrer.indexOf("login_out") > 0 || document.referrer.indexOf("register") > 0 ){
					window.location.href = '<?php
echo parse_wap_url_tag("u:index|init|"."".""); 
?>';
				    }else{
					location.replace(document.referrer);
				    }
				}
			}
			,error:function()
			{
				alert("服务器很忙");
			}
		});
		
	
	});
		
</script>
<?php echo $this->fetch('./inc/footer.html'); ?>







