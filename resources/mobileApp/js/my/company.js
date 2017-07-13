$(document).ready(function() {
	$.ajax({
		url: "http://nongye.zmade.cn/CompanyCenter/Company/appPushCompanyList",
		method: "post",
		dataType: "json",
		data: {},
		success: function(data) {
			typeof data == "string" && (data = eval("(" + data + ")"));
			if (data.code == "001") {
		        showData(data.result[0]);
		    } else {
		        console.error (data.code, data.msg);
		        showError(data);
		    }
		},
		error: function(err) {
			console.error(err);
		}
	});
});

function showData(data) {
	$("#companyName").val(data.name);
}

function showError(data) {
	console.error(data.msg, data.code);
}
