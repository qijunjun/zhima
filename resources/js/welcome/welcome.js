/*
 * Created by apple on 16/5/13.
 * Edited by James "Carbon" leon Neo on 2016/08/08.
 */
$(document).ready(function () {
	var select = document.getElementById('data_name');
	var map1 =document.getElementById('map');
	select.innerHTML = null;
	$.ajax({
		url: "/home/utility/regions",
		method: "post",
		dataType: "json",
		success: function (data) {
			if (data.code !== "001") {
				throw new Error(data.code, data.message);
			}
			data = data.result;
			data = JSON.stringify(data);
			localStorage.setItem("area", data);
		},
		error: function (err) {
			console.error(err);
		}
	});
	//抓取企业信息
	$.ajax({
		url:"/Company/Basic/index",
		method:"post",
		dataType:"json",
		success: function (data) {
			console.log(data);
			if(data.code !== "001") {
				throw new Error(data.code, data.message);
			}
			data = data.result;
			$("#company").html(data.name);
			$("#companyLogo").attr("src",data.logo);
		},
		error: function (err) {
			console.error(err);
		}
	});
	//抓取产品数量
	$.ajax({
		url: "/Product/api/info",
		method: "post",
		dataType: "json",
		success: function (data) {
			console.log(data);
			if(data.code !== "001") {
				throw new Error(data.code, data.message);
			}
			data = data.result;
			$("#productNum").html(data.length);
		},
		error: function (err) {
			console.error(err);
		}
	});
	//抓取入库数量
	$.ajax({
		url : "/CheckIO/Checkin/listall",
		method: "post",
		dataType: "json",
		success: function(data){
			console.log(data);
			if(data.code !== "001"){
				throw new Error(data.code,data.message);
			}
			data = data.result;
			$("#checkinNum").html(data.length);
		},
		error: function(err){
			console.error(err);
		}
	});
	//抓取出库数量
	$.ajax({
		url : "/CheckIO/Checkout/listall",
		method: "post",
		dataType: "json",
		success: function(data){
			console.log(data);
			if(data.code !=="001"){
				throw new Error(data.code,data.message);
			}
			data = data.result;
			$("#checkoutNum").html(data.length);
		},
		error: function(err){
			console.error(err);
		}
	});
	//抓取码数剩余量
	$.ajax({
		url: "/Code/api/info",
		method: "post",
		dataType: "json",
		data: {},
		success: function (data) {
			console.log(data);
			if (data.code === "001") {
				codeNumber = data.result.coderemainder;
				var total;
				var total1 = 0 ;
				var total2= 0 ;
				for(var i = 0;i<data.result.listxz.length; i++){
					total1 += Number(data.result.listxz[i].qrcode_counts);
				}
				for(var j=0;j<data.result.listrelation.length; j++){
					total2 += Number(data.result.listrelation[j].qrcode_counts);
				}
				total = total1 + total2;
				$("#codeNum").html(total);
				$("#codeNumber").html(codeNumber);
			} else {
				console.error(data.code, data.message);
			}
		},
		error: function (err) {
			console.error(err);
		}
	});
	// Map
	var mapOptions;
	var map;
	var cluster;
	var infoWindow;
	var contentTemplate;
	var partialContentTemplate;
	var productMap;
	var productMarkers;
	var totalMarkers;
	mapOptions = {
		resizeEnable: true
	};
	map = new AMap.Map("map", mapOptions);
	map.setZoom(4);
	map.plugin(["AMap.ToolBar"], function ()
	{
		var tool = new AMap.ToolBar();
		map.addControl(tool);
	});
	map.plugin(["AMap.MarkerClusterer"], function ()
	{
		cluster = new AMap.MarkerClusterer(map, [], {maxZoom: 17});
	});
	infoWindow = new AMap.InfoWindow({
		offset: new AMap.Pixel(0, -30),
		closeWhenClickMap: true
	});
	contentTemplate = '<div class="info_window"><div class="title"><span>$productName</span></div><div class="body"><img style="height: 80px; float: left;" src="$productImage" /><div style="float: left;"><div>质量码：$code</div><div>扫码时间：$time</div><div>扫码结果：$flag</div><div>经销商：$agent</div><div>电话：$phone</div></div></div></div>';
	partialContentTemplate = '<div class="info_window"><div class="title"><span>$productName</span></div><div class="body"><img style="height: 80px; float: left;" src="$productImage" /><div style="float: left;"><div>质量码：$code</div><div>扫码时间：$time</div><div>扫码结果：$flag</div></div></div></div>';
	productMap = {};
	productMarkers = [];
	totalMarkers = [];
	$.ajax({
		url: "/welcome/info/scan",
		dataType: "json",
		success: function (data)
		{
			var list;
			if (data.code === "001")
			{
				list = data.result;
				renderMarkers(list);
			}
		}
	});
	//产品下拉列表
	$.ajax({
		url: "/Product/api/fieldsList",
		method: "post",
		dataType: "json",
		success: function (data)
		{
			var product;
			var str;
			var index;
			var i;
			if (data.code === "001")
			{
				data = data.result;
				str = "<option value='0' disabled='disabled' selected='selected'>--请选择产品--</option>";
				for (i = 0; i < data.length; i++)
				{
					product = data[i].productname + " - " + data[i].guige;
					str += "<option value='" + data[i].productid + "'>" + product + "</option>";
					index = productMap[product];
					if (!index)
					{
						productMarkers.push([]);
						index = productMarkers.length - 1;
						productMap[product] = index;
					}
				}
				select.innerHTML += str;
			}
			else
			{
				console.error(data.code, data.message);
			}
		},
		error: function (err)
		{
			console.error(err);
		}
	});
	$("#search").click(function(){
		map.clearMap();
		var startdate = $("#startdate").val();
		var enddate = $("#enddate").val();
		var start = parseInt(new Date(startdate).getTime() / 1000);
		var end = parseInt(new Date(enddate).getTime() / 1000);
		console.log('start:' + start + ',startdate:' + startdate);
		console.log('end:' + end + ',enddate' + enddate);
		if(start > end){
			customPop("错误", "tip", "", "", undefined, "起始时间不能大于终止时间！");
			return;
		}
		$.ajax({
			url: "/welcome/info/scan",
			method:"post",
			data:{
				productid:$("#data_name").val(),
				startdate:startdate,
				enddate:enddate
			},
			dataType: "json",
			success: function (data)
			{
				var list;
				if (data.code === "001")
				{
					list = data.result;
					renderMarkers(list);
				}
			}
		});
	});
	function switchProduct()
	{
		var select;
		var product;
		var index;
		var startdate = $("#startdate").val();
		var enddate = $("#enddate").val();
		var start = parseInt(new Date(startdate).getTime() / 1000);
		var end = parseInt(new Date(enddate).getTime() / 1000);
		
		select = document.getElementById("data_name");
		product = select.options[select.selectedIndex].text;
		index = productMap[product];
		infoWindow.close();
		cluster.clearMarkers();
		//cluster.addMarkers(productMarkers[index]);
		
		if(start<=end && start != null && start != '' && start != undefined && end != null && end != '' && end  != undefined) {
			$.ajax({
			url: "/welcome/info/scan",
			methods:"post",
			data:{
				productid:$("#data_name").val(),
				startdate:startdate,
				enddate:enddate
			},
			dataType: "json",
			success: function (data)
			{
				var list;
				if (data.code === "001")
				{
					list = data.result;
					renderMarkers(list);
				}
			}
		});
		} else {
			cluster.addMarkers(productMarkers[index]);
		}
	}
	function renderMarkers(list)
	{
		var markers;
		var count;
		var item;
		var marker;
		var product;
		var index;
		var i;
		markers = [];
		count = list.length;
		for (i = 0; i < count; i++)
		{
			item = list[i];
			product = item.name + " - " + item.spec;
			marker = new AMap.Marker({
				position: [item.longitude, item.latitude],
				map: map
			});
			marker.data = item;
			marker.on("click", showInfo);
			totalMarkers.push(marker);
			index = productMap[product];
			if (!index)
			{
				productMarkers.push([]);
				index = productMarkers.length - 1;
				productMap[product] = index;
			}
			productMarkers[index].push(marker);
			markers.push(marker);
		}
		cluster.clearMarkers();
		cluster.addMarkers(markers);
	}
	function showInfo(e)
	{
		var position;
		var productName;
		var datetime;
		var date, time;
		var flag;
		var content;
		var agent;
		var phone;
		position = e.target.getPosition();
		map.setCenter(position);
		productName = e.target.data.name;
		agent = e.target.data.agentname;
		phone = e.target.data.agentphone;
		if (e.target.data.spec)
		{
			productName += " - " + e.target.data.spec;
		}
		datetime = new Date(parseInt(e.target.data.time + "000"));
		date = datetime.getFullYear() + "年" + (datetime.getMonth() + 1) + "月" + datetime.getDate() + "日";
		time = "";
		time += (datetime.getHours() < 10 ? "0" : "") + datetime.getHours() + ":";
		time += (datetime.getMinutes() < 10 ? "0" : "") + datetime.getMinutes() + ":";
		time += (datetime.getSeconds() < 10 ? "0" : "") + datetime.getSeconds();
		flag = e.target.data.flag == 1 ? "此码有异常，请联系厂家" : "真码";
		if (agent)
		{
			content = contentTemplate;
			content = content.replace("$agent", agent);
			content = content.replace("$phone", phone);
		}
		else
		{
			content = partialContentTemplate;
		}
		content = content.replace("$productName", productName);
		content = content.replace("$code", e.target.data.code);
		content = content.replace("$time", date + " " + time);
		content = content.replace("$flag", flag);
		content = content.replace("$productImage", e.target.data.image);
		infoWindow.setContent(content);
		infoWindow.open(map, position);
	}
	function resize()
	{
		var map;
		var height;
		map = document.getElementById("map");
		height = $(window).height() - $(map.previousElementSibling).outerHeight();
		map.style.height = height + "px";
	}
	$(window).on("resize", resize);
	// $("select").change(switchProduct);
	resize();
	$("#cancel").click(function(){
		window.location.reload();
	});
});
