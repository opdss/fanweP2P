var timer;
var c_idx = 1;
var total = 0;
var is_has_show = false;
$(document).ready(function(){
	$("#main_adv_box").find("span").each(function(){
		if($.trim($(this).html())!=""){
			if (!is_has_show) {
				$(this).show();
				is_has_show = true;
			}
			total ++;
		}

	});
	if (total > 1) {
		$("#main_adv_ctl li").hide();
		init_main_adv();
		for(i=1;i<=total;i++){
			$("#main_adv_ctl li[rel='"+i+"']").show();
		}
		$("#main_adv_ctl ul").css({"width":35*total+"px"});
	}
	else {
		if(total==0)
			$("#main_adv_box").hide();
                else{
                    $("img","#main_adv_img ").each(function(){
                        var img_str = $(this).attr("src");
                        $(this).hide();

                        $(this).parent().css({"background":"url("+img_str+") no-repeat center 0","width":"100%","height":"100%"});
                     });
                }
		$("#main_adv_ctl").hide();
	}	
});

function init_main_adv()
{
	$("#main_adv_box").find("span[rel='1']").show();
	$("#main_adv_box").find("li[rel='1']").addClass("act");
	$("img","#main_adv_img ").each(function(){
           var img_str = $(this).attr("src");
           $(this).hide();
           
           $(this).parent().css({"background":"url("+img_str+") no-repeat center 0","width":"100%","height":"100%"});
        });
	timer = window.setInterval("auto_play()", 5000);
	$("#main_adv_box").find("li").hover(function(){
		show_current_adv($(this).attr("rel"));		
	});
	
	$("#main_adv_box").hover(function(){
		clearInterval(timer);
	},function(){
		timer = window.setInterval("auto_play()", 5000);
	});
	init_success_play();
}

function auto_play()
{	
	if(c_idx == total)
	{
		c_idx = 1;
	}
	else
	{
		c_idx++;
	}
	show_current_adv(c_idx);
}

function show_current_adv(idx)
{	
	$("#main_adv_box").find("span[rel!='"+idx+"']").hide();
	$("#main_adv_box").find("li").removeClass("act");
	$("#main_adv_box").find("li").find("div div div div").css("background-color","#fff");
	if($("#main_adv_box").find("span[rel='"+idx+"']").css("display")=='none')
	$("#main_adv_box").find("span[rel='"+idx+"']").fadeIn();
	$("#main_adv_box").find("li[rel='"+idx+"']").addClass("act");
	$("#main_adv_box").find("li[rel='"+idx+"']").find("div div div div").css("background-color","#f60");
	c_idx = idx;
	
	
}

function init_success_play(){
	var a = function() {
		this.h = 50,
		this.speed = 50,
		this.ul = $("#examIndex ul"),
		this.timer = null,
		this.isPasue = !1,
		this.isLoop = !0,
		this.play = function() {
			if (this.ul[0] == undefined || this.ul.find("li").length <= 5) return;
			var a = this.ul.find("li:first"),
			b = this.ul.find("li:last"),
			c = document.createElement("li");
			c.style.height = "0px",
			a.before(c);
			var d = b.html(),
			e = 0,
			f = this.h,
			g = this;
			g.timer = setInterval(function() {
				if (g.isPasue) return ! 1;
				c.style.height = e + "px",
				e += 4,
				e >= f && (clearInterval(g.timer), b.remove(), $(c).css("opacity", 0), $(c).html(d).animate({
					opacity: 1
				}), g.isLoop && setTimeout(function() {
					g.play()
				},
				3e3))
			},
			this.speed)
		},
		this.pause = function() {
			this.isPasue = !0
		},
		this.replay = function() {
			this.isPasue = !1
		}
	},
	b = new a;
	b.play(),
	$("#examIndex ul").hover(function() {
		b.pause()
	},
	function() {
		b.replay()
	})
}
