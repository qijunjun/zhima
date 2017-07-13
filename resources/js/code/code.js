/**
 * Created by apple on 16/4/28.
 * Edited by James "Carbon" leon Neo on 2016/07/27.
 */

var codeNumber = 0;
var object = {};

$(document).ready(function () {
	//缓存起来所有会使用到的class或id
	$.ajax({
		url: "/Code/api/info",
		method: "post",
		dataType: "json",
		data: {},
		success: function (data) {
			console.log(data);
			if (data.code === "001") {
				codeNumber = data.result.coderemainder;
			} else {
				customPop("错误", "tip", "", "", undefined, data.message);
				console.error(data.code, data.message);
			}
		},
		error: function (err) {
			customPop("错误", "tip", "", "", undefined, "网络错误");
			console.error(err);
		}
	});

	var codeData = $("#codeData");
	codeData.bootgrid({
		ajax: true,
		ajaxSettings: {
			dataType: "json"
		},
		url: "/Code/api/info",
		identifier: "id",
		responseHandler: function (response) {
			var rows;
			rows = {
				total: response.result.listxz.length + response.result.listrelation.length,
				current: 1,
				rows: [].concat(response.result.listxz, response.result.listrelation)
			};
			response.result = rows;
			return response;
		},
		converters: {
			type: {
				to: function (value) {
					switch (value) {
						case "0":
							return "箱码";
						case "1":
							return "质量码";
						case "2":
							return "箱码-质量码";
					}
				}
			}
		},
		formatters: {
			cout: function (column, row) {
				if (row.flag == 2 && row.qrcodetypeid != 0) {
					return ;
				}
				return "<p class='cout' data-id='" + row.id + "'>导出</p>";
			}
		}
	}).on("loaded.rs.jquery.bootgrid", function () {
		var eventBind = new EventBind();
		eventBind.cout.click(function () {
			var id = $(this).data("id");
			$(this).parent().parent().attr("id", id);
			customPop("导出", "tip", "", id);
		});
	});
});

var coutCode = function (id) {
	var num = $("#coutNumber").val();
	console.log(num, id);
	$.ajax({
		url: "/Code/api/exp/id/" + id + "/num/" + num,
		method: "post",
		dataType: "json",
		data: {
			id: id,
			num: num
		},
		success: function (data) {
			console.log(data);
			if (data.code !== "001") {
				customPop("错误", "tip", "", "", undefined, data.message);
				throw new Error(data.code, data.message);
			}
			window.open(data.result.link);
			var popMsg = $(".popMsg");
			popMsg.remove();
		},
		error: function (err) {
			customPop("错误", "tip", "", "", undefined, "网络错误");
			console.error(err);
		}
	});
};

var wayOfCode = function () {
	var code, label, input, p, div, select,result,xCode,zCode,xzCode;
	select = $("#wayCode");
	code = $("#code");
	xCode = $("#xCode");
	zCode= $("#zCode");
	xzCode =$("#xzCode");
	result= $("#result");
	if (xCode != "undefined"){
		xCode.remove();
	}
	if (zCode !="undefined"){
		zCode.remove();
	}
	if(xzCode !="undefined"){

		xzCode.remove();
	}
	switch (select.val()) {
		case "箱码":
			div = $("<div></div>").attr("id", "xCode");
			label =$("<label></label>").attr("for", "codeNumber").html("箱码数量:");
			input = $("<input>").attr("type", "text").attr("name","xcodeNumber").attr("id","xcodeNumber").attr("placeholder", "剩余码数量:" + codeNumber).attr("onchange","xCodeSubmit();");
			div.append(label).append(input);
			code.after(div);
			break;
		case "质量码":
			div = $("<div></div>").attr("id","zCode");
			label = $("<label></label>").attr("for","codeNumber").html("质量码数量：");
			input = $("<input>").attr("type","text").attr("name","zcodeNumber").attr("id","zcodeNumber").attr("placeholder","剩余码数量:"+codeNumber).attr("onchange","zCodeSubmit();");
			div.append(label).append(input);
			code.after(div);
			break;
		case "箱码-质量码":
			div =$("<div></div>").attr("id","zCode");
			label= $("<label></label>").attr("for","codeNumber").html("质量码数量");
			input = $("<input>").attr("type","text").attr("name","zcodeNumber").attr("id","zcodeNumber").attr("placeholder","剩余码数量："+codeNumber).attr("readonly", "readonly").attr("onchange","zCodeSubmit();");
			div.append(label).append(input);
			code.after(div);
			div = $("<div></div>").attr("id","xzCode");
			p = $("<p></p>").html("1箱装<input type=\"text\" name=\"zNum\" id=\"zNum\" onchange=\"displayZCode(); xzCodeSubmit();\"  />个质量码");
			div.append(p);
			code.after(div);
			div =$("<div></div>").attr("id","xCode");
			label = $("<label></label>").attr("for", "codeNumber").html("箱码数量:");
			input = $("<input>").attr("type", "text").attr("name","xcodeNumber").attr("id","xcodeNumber").attr("placeholder", "剩余码数量:" + codeNumber).attr("onchange", "displayZCode();xCodeSubmit();");
			div.append(label).append(input);
			code.after(div);
			break;
		default:
	}
	result.off("click");
	result.click(function(){
		var _codeNumber = $("#xcodeNumber").val() || $("#zcodeNumber").val();
		if (Number(_codeNumber) > Number(codeNumber)) {
			customPop("错误", "tip", "", "", undefined, "没有那么多码!请联系管理员");
			return ;
		}
		var resultBtn = this;
		console.log(object);
		if (!Object.keys(object).length)
			return;
		$.ajax({
			url: object.url,
			method:"post",
			dataType:"jsonp",
			data: object.data,
			beforeSend: function () {
				//1.让提交按钮失效，以实现防止按钮重复点击
				$(resultBtn).attr('disabled', 'disabled');
				//2.给用户提供友好状态提示
				$(resultBtn).text('玩命生成中...');
			},
			complete: function () {
				//3.让生成按钮重新有效
				$(resultBtn).removeAttr('disabled');
			},
			success:function(data){
				console.log(data);
				if(data.code === "001"){
					window.location.reload();
				}else{
					customPop("错误", "tip", "", "", undefined, data.message);
					console.error(data.code,data.message);
				}
			},
			error:function(err){
				customPop("错误", "tip", "", "", undefined, "网络错误");
				console.error(err);
			}
		});
	});
};

var displayZCode = function () {
	var xCodeNumber = $("#xcodeNumber").val() || 0;
	var zNum = $("#zNum").val() || 0;
	$("#zcodeNumber").val(xCodeNumber * zNum);
};

var xCodeSubmit = function () {
	var input=$("#xcodeNumber");
	var num = input.val();
	if(num < 0 || num == 0){
		alert("箱码数量必须大于0");
		object = {};
		return ;
	}
	if(num > 10000){
		alert("请输入小于1万的码量");
		object = {};
		return ;
	}
	object = {
		url: "/Code/api/add/codenum/" + input.val() + "/codetype/" + 0,
		data: {
			codenum: input.val(),
			codetype: 0
		}
	};
};

var zCodeSubmit = function() {
	var input=$("#zcodeNumber");
	var num = input.val();
	if(num < 0 || num == 0){
		alert("质量码数量必须大于0");
		object = {};
		return ;
	}
	if(num > 10000){
		alert("请输入小于1万的码量");
		object = {};
		return ;
	}
	object = {
		url: "/Code/api/add/codenum/" + input.val() + "/codetype/" + 1,
		data: {
			codenum: input.val(),
			codetype: 1
		}
	};
};

var xzCodeSubmit = function() {
	var input=$("#xcodeNumber");
	var inputzNum=$("#zNum");
	var num = $("#zcodeNumber").val();
	if(input.val() < 0 || input.val() == 0){
		alert("箱码数量必须大于0");
		object = {};
		return ;
	}
	if(inputzNum.val() < 0 || inputzNum.val() == 0){
		alert("装码数必须大于0");
		object = {};
		return ;
	}
	if(num > 10000){
		alert("请输入小于1万的码量");
		object = {};
		return ;
	}
	object = {
		url: "/Code/api/add/codenum/" + input.val() + "/codetype/" + 2+"/codenumtwo/"+inputzNum.val(),
		data: {
			codenum: input.val(),
			codetype: 2,
			codenumtwo:inputzNum.val()
		}
	};
};

var gotErrorMsg = function () {
	$(".popMsg").remove();
};
