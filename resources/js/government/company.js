$(document).ready(function() {
	var mapOptions;
	var map;
	var marker;
	var markers;
	var markerMap;
	var cluster;
	var infoWindow;
	var article;
	var articleTop;
	var companyGrid;
	$('#map').css('height', $(window).height() + 'px');

	mapOptions = {
		level: 9
	};
	map = new AMap.Map("map", mapOptions);
	map.plugin(["AMap.ToolBar"], function ()
	{
		var tool = new AMap.ToolBar();
		map.addControl(tool);
	});
	markers = [];
	markerMap = {};
	infoWindow = new AMap.InfoWindow({
		offset: new AMap.Pixel(0, -30)
	});
	$.ajax({
		url: "/government/API/region",
		dataType: "json",
		success: function (data)
		{
			console.log(data);
			if (data.code !== "001")
			{
				return;
			}
			map.setCenter([data.result.longitude, data.result.latitude]);
			AMap.service(["AMap.DistrictSearch"], function()
			{
				var opts;
				var bounds;
				var polygons, polygon;
				opts = {
					subdistrict: 1,   //返回下一级行政区
					extensions: 'all'  //返回行政区边界坐标组等具体信息
				};
				//实例化DistrictSearch
				district = new AMap.DistrictSearch(opts);
				//行政区查询
				district.search(data.result.code, function(status, result)
				{
					bounds = result.districtList[0].boundaries;
					polygons = [];
					if (bounds)
					{
						for (var i = 0, l = bounds.length; i < l; i++)
						{
							// 生成行政区划polygon
							polygon = new AMap.Polygon({
								map: map,
								strokeWeight: 4,
								path: bounds[i],
								fillOpacity: 0.4,
								fillColor: '#CCF3FF',
								//strokeColor: '#CC66CC'
								strokeColor: '#7A378B'
							});
							polygons.push(polygon);
						}
					}
				});
			});
		}
	});
	$.ajax({
		url: "/government/company/fetch",
		method: "get",
		dataType: "json",
		data: {
			page: 1,
			size: 10000
		},
		success: function(data){
			var list;
			var count;
			var item;
			var companies = $('#companies-side');
			var listElems = '';
			if (data.code !== "001")
			{
				return;
			}
			list = data.result.rows;
			count = list.length;
			if(count == 0) {
				listElems = '<li>暂无企业信息！</li>';
			}
			for (var i = 0; i < count; i++)
			{
				item = list[i];
				listElems +=
					'<li data-id=' + item.id + '>'
					+'<span class="side-name">' + whiteSpace(item.name,18) + '</span>'
					//+	'<span class="side-detail"><a href="/government/company/detail/companyId/' + item.id + '""\">>></a></span>'
					+	'<span class="side-detail"><a href="/company/portal/portal/companyId/' + item.id + '""\">>></a></span>'
					+'</li>';
				marker = new AMap.Marker({
					position: [item.longitude, item.latitude],
					map: map
				});
				marker.data = item;
				marker.on("click", showInfo);
				markers.push(marker);
				markerMap[item.id] = markers.length - 1;
			}
			companies.append(listElems)
				.children('li').on("click", function (e) {
					marker = markers[markerMap[$(this).attr('data-id')]];
					marker.emit("click", {
						target: marker
					});
				});
			map.plugin(["AMap.MarkerClusterer"], function ()
			{
				cluster = new AMap.MarkerClusterer(map, markers);
			});
			$('.side-box').removeClass('hidden');
		},
		error: function (err) {
			console.log(err);
		}
	});

	function showInfo(e)
	{
		var position,
			infoContent = '',
			name,
			detail,
			img,
			address,
			contact,
			introduction,
			phone;
		position = e.target.getPosition();
		name = '<span id="info-name">' + e.target.data.name + '</span>';
		detail = '<span id="info-detail"><a href="/company/portal/portal/companyId/' + e.target.data.id + '">>>详情</a></span>';
		img = (e.target.data.logo === null?'': e.target.data.logo === undefined?'':e.target.data.logo === ''?'':'<a href="/company/portal/portal/companyId/' + e.target.data.id +'"><img src="'+ e.target.data.logo +'" alt="' + e.target.data.name + '" /></a>');
		address = e.target.data.address === null?'': e.target.data.address === undefined?'':'<div id="info-address"><span>地址：</span><span>'+ e.target.data.address + '</span></div>';
		contact = '<div id="info-contact"><span>负责人：</span><span>' + e.target.data.contact + '</span></div>';
		//屏蔽空介绍
		//introduction = e.target.data.introduction === null?'': e.target.data.introduction === undefined?'':e.target.data.introduction === ''?'':'<div id="introduction"><span>企业信息：</span><span>' + e.target.data.introduction + '</span></div>';
		//不屏蔽空介绍
		introduction = e.target.data.introduction === null?'': e.target.data.introduction === undefined?'':'<div id="introduction"><span>企业信息：</span><span class="hide-lines">' + e.target.data.introduction + '</span></div>';
		phone = '<div id="phone"><span>联系电话：</span><span>' + e.target.data.phone +'</span></div>';

		map.setCenter(position);
		console.log(e.target.data);
		infoContent +=
			'<div class="info-box">'
			+	'<div class="info-title">'
			+		name
			+		detail
			+	'</div>'
			+ 	img
			+	address
			+ 	contact
			+	introduction
			+	phone
			+'</div>'
		;
		infoWindow.setContent(infoContent);
		//弹出消息内容
		//infoWindow.setContent(e.target.data.name);
		infoWindow.open(map, position);
		article.animate(articleTop, 100);
	}
	article = $(document.body);
	articleTop = {
		scrollTop: 0
	};
	function whiteSpace(value,num) {
		return value == null ? "" : value.length > num ? value.substr(0, num) + "..." : value;
	}
	$('#side-anchor').on('click',function () {
		var box = $('.side-box'),
			list = $('.side-list');
		var right = box.css('right');
		var width = (right.indexOf('-'))*list.width();
		if(width === 0) $(this).text('>');
		else if(width < 0) $(this).text('<');
		box.animate({
			right: width
		})
	});
	$('#introduction').children('.hide-lines').dotdotdot({
		height: 128
	});
	//$('.side-box').css('display','none');
});
