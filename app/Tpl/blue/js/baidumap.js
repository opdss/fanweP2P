var marker_array=[];
var preview_window;
function zoommap(data)
{
	var width = $(window).width() - 200;	
	var height = $(window).height() - 100;
	$.weeboxs.open("<div id='zoommap_container'><div id='zmap_info' style='float:left;'></div><div id='zstore_info' style='float:left;'></div></div>", {boxid:'fanwe_map_box',contentType:'text',showButton:false, showCancel:false, showOk:true,title:'查看详情',width:width,height:height,type:'wee'});
	
	$("#zmap_info").css({"width":(width-300),"height":height});
	$("#zstore_info").css({"width":220,"height":height,"padding-left":15});

	var map =new BMap.Map("zmap_info");
    map.addControl(new BMap.NavigationControl()); 
	map.addControl(new BMap.ScaleControl());   
	map.addControl(new BMap.OverviewMapControl());



    for(var i=0;i<data.length;i++)
    {
    	var item = data[i];
    	var marker = drawMarker(map,item.name,item.xpoint,item.ypoint);
    	marker_array[i] = marker;
    	var itemDom = $("<div style='cursor:pointer; padding:10px;' pos="+i+" class='itemdom'><span style='font-size:14px; font-weight:bolder; color:#268bd9;'>"+item.name+"</span><br />地址:"+item.address+"<br />电话:"+item.tel+"</div>");
    	  	
    	$(itemDom).hover(function(){
    			$(this).css("background","#f2f2f2");
    		},function(){
    			if($(this).attr("rel")!="1")
    			$(this).css("background","#ffffff");
    	});
    	$(itemDom).bind("click",function(){
    		$(".itemdom").css("background","#ffffff");
    		$(this).css("background","#f2f2f2");
    		$(".itemdom").attr("rel","0");
    		$(this).attr("rel","1");  		    		
    		popWindow(map,$(this).attr("pos"),"<span style='font-size:14px; font-weight:bolder; color:#268bd9;'>"+item.name+"</span><br />地址:"+item.address+"<br />电话:"+item.tel);    		
    	});
		if(i==0){
			$(itemDom).css("background","#f2f2f2");
			$(itemDom).attr("rel",0);
			var infowindow = new BMap.InfoWindow("<span style='font-size:14px; font-weight:bolder; color:#268bd9;'>"+item.name+"</span><br />地址:"+item.address+"<br />电话:"+item.tel);
			marker.addEventListener("click", function(){
				this.openInfoWindow(infowindow);
			});
		}
    	$("#zstore_info").append(itemDom);  
    }    
}


function popWindow(map,pos,content)
{
	var infowindow = new BMap.InfoWindow(content);
	var marker = marker_array[pos];
	map.addOverlay(marker);
	marker.openInfoWindow(infowindow);
	marker.addEventListener("click", function(){
		this.openInfoWindow(infowindow);
		$(".itemdom").css("background","#ffffff");
		$(".itemdom").attr("rel","0");
	});
}

function drawMarker(map,title,xpoint,ypoint)
{
	var latlng = new BMap.Point(xpoint,ypoint);
    var marker = new BMap.Marker(latlng);
	map.centerAndZoom(latlng, 16);
	map.enableScrollWheelZoom();
	map.addOverlay(marker);
    return marker;
};