/**
 * 谷歌地图api二次封装
 * @author jiweitao
 * @version 2013-03-23
 */

function googleMap(opts)
{
	//默认配置
	var defaultOpts = {
		'zoom': '6',
		'center': this.getLatLng(39.966596, 116.422119),//默认为北京
		'mapTypeId': google.maps.MapTypeId.ROADMAP,//地图类型为必填项
		'mapTypeControl': false,
		'streetViewControl': false
	};

	//处理自定义配置
	if (opts.lat && opts.lng) {
		opts.center = this.getLatLng(opts.lat, opts.lng);
	}
	mapDiv = document.getElementById(opts.div);
	delete opts.lat;
	delete opts.lng;
	delete opts.div;
	
	//合并配置
	var mapOpts = $.extend(defaultOpts, opts);
	
	//创建地图
	this.map = new google.maps.Map(mapDiv, mapOpts);
	
	//初始化
	this.markers = [];
	this.infoWindows = [];
}


////////////////////Base（基础）////////////////////
//获取位置对象
googleMap.prototype.getLatLng = function(lat, lng) {
	var latLng = new google.maps.LatLng(lat, lng);
	return latLng;
}

//设置地图中文
googleMap.prototype.setCenter = function(latLng) {
	this.map.setCenter(latLng);
}

//设置缩放级别
googleMap.prototype.setZoom = function(level) {
	this.map.setZoom(level);	
}

//根据标记，确定地图显示范围
googleMap.prototype.fitZoom = function() {
	var latLngs = [];
	var markersLength = this.markers.length;
	for(var i=0; i < markersLength; i++) {
		latLngs.push(this.markers[i].getPosition());
	}
	this.fitLatLngBounds(latLngs);
};

//根据一组经纬度，确定地图显示范围	
googleMap.prototype.fitLatLngBounds = function(latLngs) {
	var total = latLngs.length;
	var bounds = new google.maps.LatLngBounds();
	for(var i=0; i < total; i++) {
		bounds.extend(latLngs[i]);
	}
	this.map.fitBounds(bounds);
};

////////////////////Overlays(叠加层)////////////////////

//////////Marker（标记）//////////
//添加一个标记
googleMap.prototype.addMarker = function(opts) {
	//在时间处理程序中this会被覆盖，为了方便调用，给this定义一个别名self
	var self = this;
	
	//默认配置
	var defaultOpts = {
		draggable: false,
	}
	//自定义配置
	if (!opts.hasOwnProperty('lat') || !opts.hasOwnProperty('lng')) {
		throw '标记的经度、纬度不能为空';
		return false;
	}
	opts.position = this.getLatLng(opts.lat, opts.lng);
	delete opts.lat;
	delete opts.lng;
	
	//合并配置
	var markerOpts = $.extend(defaultOpts, opts);
	markerOpts.map = this.map;
	
	//创建标记
	var marker = new google.maps.Marker(markerOpts);
	//marker.setMap(this.map);

	//添加信息窗口
	if (opts.infoWindow) {
		opts.infoWindow.size = new google.maps.Size(500, 500);
		marker.infoWindow = new google.maps.InfoWindow(opts.infoWindow);
	}
	google.maps.event.addListener(marker, 'click', function() {
    	if (marker.infoWindow) {
			self.hideInfoWindows();
			marker.infoWindow.open(self.map, marker);
		}
	})
	this.markers.push(marker);
	return marker;
}

//添加多个标记
googleMap.prototype.addMarkers = function(optsList) {
	for(var i, opts; opts=optsList[i]; i++) {
		this.addMarker(opts)
	}
	return this.markerList;
}

//删除指定标记
googleMap.prototype.removeMarker = function() {
	
}

//删除所有标记
googleMap.prototype.removeMarkers = function(){
	for(var i=0;i < this.markers.length; i++){
		if(this.markers[i] === collection[i]) {
			this.markers[i].setMap(null); 
		}
	}
}

//删除所有标记
googleMap.prototype.clearMarkers = function(){
	for(var i=0;i < this.markers.length; i++){
		this.markers[i].setMap(null); 
	}
	this.markers.length = 0;
}



//隐藏所有的信息窗口
googleMap.prototype.hideInfoWindows = function() {
	for (var i=0, marker; marker=this.markers[i]; i++) {
		if (marker.infoWindow) {
			marker.infoWindow.close();
		}
	}
}

////////////////////infoWindow////////////////////

//创建infoWindow
googleMap.prototype.createInfoWindow = function(content, infoWindowOpts) {
	var infoWindowOption = {
		'content': content
	}	
    var infoWindow = new google.maps.InfoWindow(infoWindowOption);
	this.infoWindowList.push(infoWindow);
	
	if (infoWindowOpts.marker) {
		infoWindow.open(this.map, infoWindowOpts.marker);
	}
	return infoWindow;
}

/*
//创建折线
googleMap.prototyp.createPolyline = function() {
	
}

//创建圆形
googleMap.prototype.createCircle = function() {
	
}

//创建多边形
googleMap.prototype.createPolygon = function() {
	
}

//创建矩形
googleMap.prototype.createRectangle = function() {
	
}

//创建圆形
googleMap.prototype.createCircle = function() {
	
}
*/

////////////////////Services////////////////////
//地点名称=>经纬度,异步操作
googleMap.prototype.getLatLngByAddress = function(address, callback)
{
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode(
		{'address': address},
		function(results, status) {
			if(status == google.maps.GeocoderStatus.OK){
				var latLng = results[0].geometry.location;
				callback(latLng, results);
			}
		}
	);
}

//经纬度=>地点名称
googleMap.prototype.getAddressByLatLng = function(latLng) {
	//
}