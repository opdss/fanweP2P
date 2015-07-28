<?php echo $this->fetch('./inc/header.html'); ?>
<?php
	$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/integral_mall.css";	
	$this->_var['pagecss'][] = $this->_var['TMPL_REAL']."/css/public.css";	
?>
<link rel="stylesheet" type="text/css" href="<?php 
$k = array (
  'name' => 'parse_css',
  'v' => $this->_var['pagecss'],
);
echo $k['name']($k['v']);
?>" />
<!--积分商城-->
<ul class="integral_nav_0">
	<li class="type_0">所有类别</li>
	<li class="type_1">积分范围</li>
</ul>
<div class="blank15"></div>

<div class="integral_nav_1">
	<table style=" width:100%;">
	<tr>
		<?php $_from = $this->_var['data']['sort_url']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'sorts');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['sorts']):
?>
		<th <?php if ($this->_var['sorts']['id'] == $this->_var['data']['sort']): ?> class="y"<?php endif; ?> ><a href="<?php
echo parse_wap_url_tag("u:index|integral_mall|"."status=".$this->_var['sorts']['url']."".""); 
?>"><?php echo $this->_var['sorts']['name']; ?></a></th>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</tr>
</table>
</div>

<div class="blank15"></div>
<div>
	<ul class="integral_goods">
		<?php $_from = $this->_var['data']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'goods');$this->_foreach['goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['goods']):
        $this->_foreach['goods']['iteration']++;
?>
		<li>
			<a href="<?php
echo parse_wap_url_tag("u:index|goods_information|"."id=".$this->_var['goods']['id']."".""); 
?>" >
			<div class="left_img"><img src="/<?php echo $this->_var['goods']['img']; ?>" /></div>
		    <div class="right_p">
		    	<h5><?php echo $this->_var['goods']['name']; ?></h5>
				<p >所需积分：<span class="c_ff4a4a"><?php echo $this->_var['goods']['score']; ?></span>分</p>
				<p>购买人数：
				<?php if ($this->_var['goods']['invented_number'] > 0): ?>
				<?php echo $this->_var['goods']['invented_number']; ?>
				<?php else: ?>
				0
				<?php endif; ?>人
				</p>
		    </div>
		    </a>
		</li>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	</ul>
</div>
<div class="blank15"></div>


	
<div class="float_block Object_0" style="display:none;">
	<div class="float_background"></div>
	<div class="integral_mall_nav">
		<h5>所有类别<span class="close">关闭</span></h5>
		<div class="b_blank"></div>
		<ul>
			<?php $_from = $this->_var['data']['cates_url']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'cates');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['cates']):
?>
			<li <?php if ($this->_var['cates']['id'] == $this->_var['data']['cates']): ?> class="y"<?php endif; ?>>
				<a href="<?php
echo parse_wap_url_tag("u:index|integral_mall|"."status=".$this->_var['cates']['url']."".""); 
?>">
				<span class="name f_l"><?php echo $this->_var['cates']['name']; ?></span>
				<span class="ico f_r"><i class="fa fa-check"></i></span>
				</a>
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
		</div>
	</div>

	<div class="float_block Object_1" style="display:none;">
	<div class="float_background"></div>
	<div class="integral_mall_nav">
		<h5>积分范围<span class="close">关闭</span></h5>
		<div class="b_blank"></div>
		<ul>
			<?php $_from = $this->_var['data']['integral_url']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'integral');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['integral']):
?>
			<li <?php if ($this->_var['integral']['id'] == $this->_var['data']['integral']): ?> class="y"<?php endif; ?>>
				<a href="<?php
echo parse_wap_url_tag("u:index|integral_mall|"."status=".$this->_var['integral']['url']."".""); 
?>">
				<span class="name f_l"><?php echo $this->_var['integral']['name']; ?></span>
				<span class="ico f_r"><i class="fa fa-check"></i></span>
			</a>
			</li>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</ul>
		</div>
	</div>

	<script>
		$(document).ready(function(){
			var height=$("body").height();
			
			$(".integral_mall_nav").height(height);
			$(".float_background").height(height);
              

            $(".close").click(function(){
            	$(this).parents(".float_block").hide();
            });  

                

                $('.integral_nav_0 li').each(function() 
			        { 
			            $(this).click( 
			            function() 
			            { 
			                var iIndex = $('.integral_nav_0 li').index($(this)); 
			                var Object_iIndex='.Object_'+iIndex;
			                $(Object_iIndex).show();
			            }); 
			        }) 
  
     

		});
	</script>
<?php echo $this->fetch('./inc/footer.html'); ?>
