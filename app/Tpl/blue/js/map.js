function draw_map(xpoint,ypoint)
{
	var map = new BMap.Map("map_canvas"); 
    var opts = {type: BMAP_NAVIGATION_CONTROL_ZOOM }  
    map.addControl(new BMap.NavigationControl());  
    // map.centerAndZoom(new BMap.Point(116.404, 39.915), 11);  
    // 创建地理编码服务实例  
    var point = new BMap.Point(xpoint,ypoint);
    
    // 将结果显示在地图上，并调整地图视野  
    map.centerAndZoom(point, 16);  
    map.addOverlay(new BMap.Marker(point));
}