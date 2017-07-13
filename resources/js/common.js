/*
 * Created by apple on 16/3/12.
 * Edited by James "Carbon" leon Neo on 2016/04/16.
 */
$(document).ready(function ()
{
	// Event-handlers for functional button in page header.
	localStorage.removeItem('#zcode-content');
	localStorage.removeItem('#text-content');
	$('.code-btn').removeClass('greyBg');
	$("header p.help").click(function ()
	{
		window.open("/help");
	});
	$("header p.logout").click(function ()
	{
		$.ajax({
			url: "/logout.php",
			method: "post",
			success: function ()
			{
				new Inform({title: "通知", content: "成功退出"}).alert(function (){
					window.location.href = "/login.php";
				});
			},
			error: function (err)
			{
				console.error(err);
			}
		});
	});
	$("header p.setting").click(function ()
	{
		article.src = "/admin/edit/editPassword";
	});
	$(".govinform").click(function(){
		article.src = "/Application/Government/View/ZQT/showMessage.html";
	});
	$(".inform").click(function(){
		article.src = "/Application/Company/View/ZQGT/ZQGT.html";
	});
	$.ajax({
		url: "/Government/Message/listComInfo",
		data:{},
		method: "post",
		success: function (data)
		{
			data = data.result;
			var str=0;
			if(data instanceof Array){
				for(var i=0;i<data.length;i++){
					if(data[i].hits == 0){
						str++;
					}
				}
				if(str !=0){
					var inform =$("<a class='a'>"+str+"</a>");
				}
				$(".inform").append(inform);
				$(".a").click(function(){
					article.src = "/Application/Company/View/ZQGT/ZQGT.html";
				})
			}
		},
		error: function (err)
		{
			console.error(err);
		}
	});
	$.ajax({
		url: "/Government/Message/listLeaveMessage",
		data:{},
		method: "post",
		success: function (data)
		{
			data = data.result;
			var str=0;
			if(data instanceof Array){
				for(var i=0;i<data.length;i++){
					if(data[i].hits == 0){
						str++;
					}
				}
				if(str !=0){
					var govinform =$("<a class='a'>"+str+"</a>");
				}
				$(".govinform").append(govinform);
				$(".a").click(function(){
					article.src = "/Application/Government/View/ZQT/showMessage.html";
				})
			}
		},
		error: function (err)
		{
			console.error(err);
		}
	});
	// Cascading action for navigation and aside part.
	var nav, menus, aside, serves, article;
	var navSections, asideSections;
	var entries;
	nav = $("nav");
	menus = nav.children(".menus");
	aside = $("aside");
	serves = $("#serves");
	article = document.getElementById("article");
	navSections = nav.children().children();
	asideSections = aside.children("section");
	entries = $("#entries");
	navSections.click(function ()
	{
		nav.addClass("black-gray");
		serves.addClass("black-gray");
		navSections.removeClass("active gray");
		$(this).addClass("active gray");
		if (this.previousElementSibling)
		{
			aside.addClass("asideAll asideWidth");
			entries.html($(this).find("div").html());
		}
		else
		{
			aside.removeClass("asideAll asideWidth");
			if (article.contentWindow.location.href.indexOf("welcome") < 0)
			{
				document.getElementById("article").src = "/Welcome/Index/welcome";
			}
		}
	});
	asideSections.click(function ()
	{
		asideSections.removeClass("active");
		$(this).addClass("active");
	});

	// Collapse aside bar when the mouse cursor moves out of it.
	aside.on("mouseout", function (e)
	{
		if (e.pageX > 80 + aside.width() || e.pageY < 80 || e.pageY > $(window).height() - 80)
		{
			nav.removeClass("black-gray");
			serves.removeClass("black-gray");
			aside.removeClass("asideAll asideWidth");
			$("nav .active").removeClass("gray");
		}
	});

	// Some measures to deal with the incomplete display of navigation part.
	var offsetHeight;
	var sliders;
	var originalPosition, objectivePosition;
	sliders = nav.children(".slider");
	originalPosition = {
		top: 0
	};
	objectivePosition = {};
	function resize()
	{
		offsetHeight = menus.outerHeight() - nav.outerHeight() + serves.outerHeight();
		objectivePosition["top"] = -offsetHeight;
		if (offsetHeight <= 0)
		{
			menus.css("top", 0);
			nav.off("wheel");
			sliders.off("mouseover mouseout");
		}
		else
		{
			nav.on("wheel", wheel);
			sliders.on("mouseover", slide);
			sliders.on("mouseout", stop);
		}
	}
	function slide()
	{
		var e;
		var target;
		var top;
		var duration;
		e = arguments[0];
		target = $(e.target);
		top = parseInt(menus.css("top"));

			if (target.hasClass("slideDown") && top > -offsetHeight)
		{
			duration = 750 * (1 - top / -offsetHeight);
			menus.animate(objectivePosition, duration);
		}
		if (target.hasClass("slideUp") && top < 0)
		{
			duration = 750 * top / -offsetHeight;
			menus.animate(originalPosition, duration);
		}
	}
	function stop()
	{
		menus.stop();
	}
	$(window).on("resize", resize);
	resize();

	// Bind mouse wheel event on the navigation bar.
	function wheel()
	{
		var e;
		var top;
		var step;
		var flag;
		e = arguments[0].originalEvent;
		e.preventDefault();
		top = parseInt(menus.css("top"));
		flag = e.deltaY > 0;
		if (flag)
		{
			step = offsetHeight + top;
			step = step >= 10 ? -10 : -step;
		}
		else
		{
			step = top > -10 ? -top : 10;
		}
		top += step;
		menus.css("top", top);
	}

	// Redirect iframe content according to hash.
	var frame;
	frame = document.getElementById("article");
	function hashChange()
	{
		var hash;
		hash = location.hash;
		if (hash != "")
		{
			frame.src = "/" + location.hash.substr(1);
		}
		location.hash = "";
	}
	$(window).on("hashchange", hashChange);
	hashChange();

	//下拉生码工具
	var timerFlag;
	$('.produce').on('mouseenter',function(e){
		$('.produce').addClass('greenBg');
		$('#code-toolbar').addClass('greenBg');
		if(timerFlag) window.clearTimeout(timerFlag);
		if(!$('#code-tool').is(':animated')) {
			timerFlag = setTimeout(function(e) {
				$('#code-tool').slideDown();
			},5);
		}
	});
	$('.produce').on('mouseleave',function(e){
		if(!$('#code-tool').is(':animated')){
			timerFlag = setTimeout(function(e){
				$('.produce').removeClass('greenBg');
				$('#code-toolbar').removeClass('greenBg');
				$('#code-tool').slideUp();
			},100);
		}
	});
	$('#code-tool').on('mouseenter',function(e){
		$('.produce').addClass('greenBg');
		$('#code-toolbar').addClass('greenBg');
		if(timerFlag) window.clearTimeout(timerFlag);
	});
	$('#code-tool').on('mouseleave',function(e){
		if(!$('#code-tool').is(':animated')){
			timerFlag = setTimeout(function(e){
				$('.produce').removeClass('greenBg');
				$('#code-toolbar').removeClass('greenBg');
				$('#code-tool').slideUp();
			},100);
		}
	});
	//二维码生成
	$('#code-text').addClass('tool-select');
	$('#code-box>img').css('display','none');
	//$('#toolbar>a>img').css('display','none')
	//					.attr('alt','保存');
	$('#code-make').unbind().on('click', function() {
		makeQRbyID('#text-content','Qrcode/Qrcode/qrcode');
	});
	$('#code-toolbar').children('span').on('mouseenter',function (e) {
		$('#code-toolbar').children('span').removeClass('tool-select');
		$(this).addClass('tool-select');
	})
	$('#code-text').on('mouseenter',function (e) {
		var img = $('#code-box').children('img'),
		//saveTool = $('#toolbar').children('a').children('img'),
			content = '#text-content',
			qrSrc = localStorage.getItem(content)
		makeBtn = $('#code-make');
		$('#code-zcode-left').css('display','none');
		$('#code-text-left').css('display','block');
		if(img.attr('alt')!='文本二维码') {
			img.attr('alt','文本二维码');
			if(qrSrc === '' || qrSrc === undefined || qrSrc === null) {
				img.attr('src','');
				//saveTool.css('display','none');
				img.css('display','none');
				makeBtn.text('生    成');
			} else {
				img.attr('src',qrSrc);
				//saveTool.css('display','inline-block');
				img.css('display','inline-block');
				makeBtn.text('重新生成');
			}
		}
		$('#code-make').removeClass('greyBg').unbind().on('click', function() {
			makeQRbyID('#text-content','Qrcode/Qrcode/qrcode');
		});
	});
	$('#code-zcode').on('mouseenter',function (e) {
		var img = $('#code-box').children('img'),
		//saveTool = $('#toolbar').children('a').children('img'),
			content = '#zcode-content',
			length = delSpace($(content).val()).length,
			qrSrc = localStorage.getItem(content),
			makeBtn = $('#code-make');
		$('#code-text-left').css('display','none');
		$('#code-zcode-left').css('display','block');
		if(img.attr('alt')!='质量码二维码') {
			img.attr('alt','质量码二维码');
			if(qrSrc === '' || qrSrc === undefined || qrSrc === null) {
				img.attr('src','')
				//saveTool.css('display','none');
				img.css('display','none');
				makeBtn.text('生    成');

			} else {
				img.attr('src',qrSrc);
				//saveTool.css('display','inline-block');
				img.css('display','inline-block');
				makeBtn.text('重新生成');
			}
		}
		$('#code-make').unbind().on('click', function() {
			var context = delSpace($(content).val());
			if(context.length === 14)
				makeQRbyID('#zcode-content','Qrcode/Qrcode/bqrcode', true);
		});
		if(length < 14) {
			$('#code-make').addClass('greyBg');
			$('#zcode-content').removeClass('warning-red');
			$('#code-zcode-left').children('.tip').text('请输入14位质量码').removeClass('tip-red').removeClass('tip-green');
		}
	});
	//限制输入只能为数字
	$('#zcode-content').on('keypress',function (e)  {
		var keyCode = event.keyCode;
		//numOfSpace = parseInt(this.value.length * 0.2),
		//length = delSpace(this.value).length,
		//lengthLimit = 14;
		if (keyCode >= 48 && keyCode <= 57)
		{
			//if(parseInt((length)*0.25)>numOfSpace){
			//    this.value = this.value + " ";
			//}
			event.returnValue = true;
		} else {
			event.returnValue = false;
		}
	})
		.on('focus',function (e) {
			$(this).removeClass('warning-red');
			if(delSpace(this.value).length != 14){
				$('#code-zcode-left').children('.tip').text('请输入14位质量码').removeClass('tip-red').removeClass('tip-green');
				$("#code-make").addClass('greyBg');
			}})
		.on('blur',function (e) {
			var code = delSpace(this.value);
			if (code.length < 14) {
				$(this).addClass('warning-red');
				$('#code-zcode-left').children('.tip').text('*质量码不足14位').removeClass('tip-green').addClass('tip-red');
				$("#code-make").addClass('greyBg');
			}})
		.on('on paste', function (e) {
			localStorage.setItem('lastLength','0');
		});
	if (!((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.indexOf('Opera') < 0))) {
		$('#zcode-content').on('on input', function(e) {
			var str,numOfSpace,lastLength,length,lengthLimit;
			if (/[^\d\s]/g.test(this.value)) $(this).val(this.value.replace(/[^\d\s]/g, ''));
			str = this.value;
			lastLength = localStorage.getItem('lastLength');
			lastLength = lastLength === null?0:parseInt(lastLength);
			numOfSpace = getCount(str,' ');
			length = delSpace(str).length;
			lengthLimit = 14;
			if(length === undefined || length === null || length == 0) {
				length = 0;
			}
			if(length > lengthLimit) this.value = str.slice(0,14+numOfSpace);
			if(Math.floor((length-1)/4) > numOfSpace && length>lastLength){
				localStorage.setItem('curPurse',this.selectionStart);
				$(this).val(addSpace(str,4));
				console.log('replace');
			}
			localStorage.setItem('lastLength',delSpace(str).length);
			if (length != 14) {
				$('#code-zcode-left').children('.tip').text('请输入14位质量码').removeClass('tip-red').removeClass('tip-green');
				$("#code-make").addClass('greyBg');
			}
			else if (length === 14) {
				$(this).removeClass('warning-red');
				//$(this).addClass('warning-green');
				$('#code-zcode-left').children('.tip').text('*质量码位数正确').addClass('tip-green');
				$("#code-make").removeClass('greyBg');
			}
		});
	} else {
		$('#zcode-content').on('onpropertychange', function(e) {
			var str,numOfSpace,length,lengthLimit;
			if (/[^\d\s]/g.test(this.value)) $(this).val(this.value.replace(/[^\d\s]/g, ''));
			str = this.value;
			numOfSpace = getCount(str,' ');
			lastLength = localStorage.getItem('lastLength');
			lastLength = lastLength === null?0:parseInt(lastLength);
			length = delSpace(str).length;
			lengthLimit = 14;
			if(length === undefined || length === null || length == 0) {
				length = 0;
			}
			if(length > lengthLimit) this.value = str.slice(0,14+numOfSpace);
			if(Math.floor((length-1)/4) > numOfSpace&&length>lastLength){
				$(this).val(addSpace(str,4));
				console.log('replace');
			}
			localStorage.setItem('lastLength',delSpace(str).length);
			if (length != 14) {
				$('#code-zcode-left').children('.tip').text('请输入14位质量码').removeClass('tip-red').removeClass('tip-green');
				$("#code-make").addClass('greyBg');
			}
			if (length === 14) {
				$(this).removeClass('warning-red');
				//$(this).addClass('warning-green');
				$('#code-zcode-left').children('.tip').text('*质量码位数正确').addClass('tip-green');
				$("#code-make").removeClass('greyBg');
			}
		})}
});

function addSpace(str,count) {
	var comps = [],
		nstr = delSpace(str),
		temp = '';
	comps[0] = '';
	if(nstr!='') {
		for(var i=0,j=0;i<nstr.length;i++) {
			comps[j] += nstr[i];
			console.log(nstr[i]);
			if(i===(j+1)*count-1) {
				j++;
				comps[j] = '';
			}
		}
		for(var i=0;i<comps.length-1;i++) {
			temp += comps[i] + ' ';
		}
		temp += comps[comps.length-1];
	}
	return temp;
}

function getCount(str1,str2)
{
	var r=new RegExp(str2,"gi");
	var str = str1.match(r);
	return str == null? 0:str.length;
}

function makeQR(JSON, direction) {
	console.log(JSON);
	var JSO = JSON;
	$.ajax({
		url: direction,
		dataType: "json",
		method: "post",
		data: {
			qr_data: JSON.qr_data
		},
		success: function(e) {
			var newpage,
				img = $('#code-box').children('img'),
			//saveTool = $('#toolbar').children('a').children('img'),
				makeBtn = $('#code-make'),
				qrSrc = e.result;

			console.log(e);
			if(e.code === '001') {
				if(qrSrc != '' && qrSrc != undefined && qrSrc != null) {
					img.css('display','inline-block');
					//saveTool.css('display','inline-block');
					$('#code-box>img').attr('src',qrSrc);
					//$('#toolbar>a>img').on('click',function() {
					//	newpage = window.open(window.location.href + qrSrc.slice(1));
					//	newpage.document.execCommand('SaveAS');
					//});
					$('#code-make').text('重新生成');
					localStorage.removeItem(JSO.mark);
					localStorage.setItem(JSO.mark, qrSrc);
				}

			}
			if(e.code === '002') {
				var JSON = {title:'警告',content:'质量码不正确！'};
				console.log('质量码不正确！');

				localStorage.removeItem(JSON.mark);
				img.attr('src','');
				//saveTool.css('display','none');
				img.css('display','none');
				makeBtn.text('生    成');
				new Inform(JSON).alert();
			}
		},
		error: function (err) {
			console.error(err);
		}
	});
}

function makeQRbyID(id, direction) {
	makeQRbyID(id, direction, false);
}

function makeQRbyID(id , direction, isDel) {
	var qr_data = $(id).val(),
		qrSrc;
	var JSON ={};
	if(isDel) qr_data = delSpace(qr_data);
	JSON.mark = id;
	JSON.qr_data = qr_data;
	makeQR(JSON, direction);
}

function delSpace(value) {
	var components = value.split(" "),
		comNum = components.length,
		context = "";
	for(var i = 0; i < comNum; i++) {
		context += components[i];
	}
	return context;
}