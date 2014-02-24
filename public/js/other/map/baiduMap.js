////////////////////google map api 二次封装，需要jquery支持////////////////////
function baiduMap (opts)
{
	this.canvas = opts.canvas;
	//默认为北京
	this.center = opts.center || {lat: 39.966596, lng : 116.422119};
	this.zoom = opts.zoom || 6;
	//初始化地图
	this.map = new google.maps.Map(
		document.getElementById(this.canvas),
		{
			center : this.getLatLng(this.center),
			zoom : this.zoom,
			mapTypeId : google.maps.MapTypeId.ROADMAP,
			mapTypeControl : false,
			streetViewControl : false
		}
	);
	this.markerList = [];
	this.infoWindowList = [];
}


////////////////////Map////////////////////
baiduMap.prototype.setCenter = function(latLng) {
	this.map.setCenter(latLng);
}

baiduMap.prototype.setZoom = function(level) {
	this.map.setZoom(level);	
}

////////////////////Base////////////////////
//支持{lat, lng}和google.maps.LatLng两种格式
baiduMap.prototype.getLatLng = function(latLng) {
	if (latLng instanceof google.maps.LatLng) {
		//
	} else {
		latLng = new google.maps.LatLng(latLng.lat, latLng.lng)
	}
	return latLng;
}


////////////////////Ovrlays////////////////////
//创建marker
baiduMap.prototype.createMarker = function(latLng, opts) {
	var defaultOpts = {
		map: this.map,
		draggable: false,
	}
	opts = jQuery.extend(defaultOpts, opts);
	opts.position = this.getLatLng(latLng);
	var marker = new google.maps.Marker(opts);
	this.markerList.push(marker);
	return marker;
}

//创建infoWindow
baiduMap.prototype.createInfoWindow = function(content, infoWindowOpts) {
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
baiduMap.prototyp.createPolyline = function() {
	
}

//创建圆形
baiduMap.prototype.createCircle = function() {
	
}

//创建多边形
baiduMap.prototype.createPolygon = function() {
	
}

//创建矩形
baiduMap.prototype.createRectangle = function() {
	
}

//创建圆形
baiduMap.prototype.createCircle = function() {
	
}
*/

////////////////////Services////////////////////
//地点名称=>经纬度,异步操作
baiduMap.prototype.getLatLngByAddress = function(address, callback)
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
baiduMap.prototype.getAddressByLatLng = function(latLng) {
	//
}