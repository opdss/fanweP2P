<a href="#top" target="_self" id="go_top"></a> 
<script>
$(document).ready(function(){
	//截断进步比的小数点后两位
	$(".content_pic h1").each(function(){
		var thisvalue=$(this).html();
		$(this).html(Math.round(thisvalue*100)/100+"%")
		
	});	
});
</script>
<?php if ($this->_var['pages']): ?>
	<div class="fy">
		<?php echo $this->_var['pages']; ?>
	</div>
<?php endif; ?>

<footer>
	<div class="footer">
		<div class="footer-t">
					<div class="f_user">
						<?php if ($this->_var['is_login'] == 0): ?>
						<div class="f_login"><a href="<?php
echo parse_wap_url_tag("u:index|login|"."".""); 
?>">登录</a></div>
						<div class="f_register"><a href="<?php
echo parse_wap_url_tag("u:index|register|"."".""); 
?>">注册</a></div>
						<?php else: ?>
						<span class="my_account"><a href="<?php
echo parse_wap_url_tag("u:index|uc_center|"."".""); 
?>">我的帐户</a></span>&nbsp;&nbsp;<i style="color: #6E7D8B;">|</i>&nbsp;&nbsp;
						<span class="f_login_out"><a href="<?php
echo parse_wap_url_tag("u:index|login_out|"."".""); 
?>">退出</a></span>
						<?php endif; ?>
					</div>
		</div>
		<div class="footer-sort">
					<ul>
						<li><a href="<?php
echo parse_wap_url_tag("u:index|init|"."".""); 
?>">首页</a></li>
						<li><a href="../index.php?is_pc=1">电脑版</a></li>
						<li><a href="<?php
echo parse_wap_url_tag("u:index|deals|"."".""); 
?>">投资</a></li>
						<li><a href="<?php
echo parse_wap_url_tag("u:index|transfer|"."".""); 
?>">债权</a></li>
						<li><a href="<?php
echo parse_wap_url_tag("u:index|article_list|"."".""); 
?>">帮助</a></li>
					</ul>
		</div>
		
		<div class="footer_num">
				<div class="footer_bor">
					<div class="footer_text">方维p2p商业系统</div>
				</div>
		</div>
	</div>
</footer>

</body>
</html>