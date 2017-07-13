var page = 1;
var loading = false;
$(document).ready(function () {
	$("#mission").mousedown(function () {
		// newEventTest();
		newEvent();
	});
    getUserInformation();
  // showEvent([]);
});

/** 获取用户信息 */
/*function getUserInformation() {
  $.ajax({
    url: "/API/public/validation",
    method: "get",
    dataType: "json",
    success: function(data) {
      typeof data == "string" && (data = eval("(" + data + ")"));
      if(data.code === "001" && data.result.companyid && data.result.companyid-0 != 0) {
        localStorage.setItem("companyid", data.result.companyid); //记住公司ID
        localStorage.setItem("companyName", data.result.name); //记住公司名称
        document.getElementById('companyLogo').innerHTML = "<img src='/resources/Agriculture/images/head.png' alt='头像' title='头像' />";
				document.getElementById('companyName').innerHTML = "<strong>" + data.result.name + "</strong>";
        loadDatas();
      } else if(confirm("当前没有登录或登录的用户没有权限！")) {
        window.location.href = loginURL;
      } else {
        window.history.back();
      }
    },
    error: function(err) {
      console.error(err);
      appcan.window.openToast("加载失败！", 2000, 5);
      window.history.back();
    }
  });
}*/

function getUserInformation() {
    document.getElementById('companyLogo').innerHTML = "<img src='"+localStorage.getItem("companyLogo")+"' alt='头像' title='头像' />";
    document.getElementById('companyName').innerHTML = "<strong>" + localStorage.getItem("companyName") + "</strong>";
    loadDatas();
}
/** 加载事件列表 */
function loadDatas() {
  if(loading) {
    return;
  }
  $.ajax({
    url: "/API/Company/fetchdata",
    method: "post",
    data: {
      page: page
    },
    dataType: "json",
    beforeSend: function() {
      loading = true;
    },
    success: function(data) {
      typeof data == "string" && (data = eval("(" + data + ")"));
      if(data.code == "001") {
        if(data.result.length == 0) {
          appcan.window.openToast("没有更多的数据啦！", 2000, 5);
        } else {
          showEvent(data.result);
          page++;
        }
      } else {
        appcan.window.openToast("请登录有效的用户！", 2000, 5);
        window.location = loginURL;
      }
    },
    error: function(err) {
      console.error(err);
      appcan.window.openToast("事件加载失败，请稍后重试！", 2000, 5);
    },
    complete: function() {
      loading = false;
    }
  });
}

var cText = 0;
var cJson = 1;
var cInt = 2;

window.uexOnload = function () {

	uexCamera.cbOpen = cbCamera;
	uexCamera.cbOpenInternal = cbCamera;
	uexWindow.cbActionSheet = cbActionSheet;

	var plat = uexWidgetOne.getPlatform();
	// alert(plat);
	if (plat == 1) {
		uexWindow.onKeyPressed = onKeyPressed;
		uexWindow.setReportKey(0, 1);
		uexWindow.setReportKey(1, 1);
	}
  getUserInformation();
}

function onKeyPressed(keyCode) {
	// alert(keyCode);
	if (keyCode == 0) {
//  window.location.href = "/";
	window.history.back(-1);
		//uexWidgetOne.exit();
	} else if (keyCode == 1) {
		//createActionSheet();
	}
}

function onClickItem(dateType) {
	switch (parseInt(dateType)) {
	case 0:
		//uexWidgetOne.exit();
		break;
	}}

function createActionSheet() {
	uexActionSheet.onClickItem = onClickItem;
	var x = 0;
	var y = 0;
	//没有用
	var width = 0;
	//如果传0，默认是屏幕宽度
	var height = 0;
	//没用的高度
	//var JsonData = '{"actionSheet_style":{"frameBgColor":"#ffffff","frameBroundColor":"#ff0000","frameBgImg":"","btnSelectBgImg":"res://btn-act.png","btnUnSelectBgImg":"res://btn.png","cancelBtnSelectBgImg":"res://cancel-act.png","cancelBtnUnSelectBgImg":"res://cancel.png","textSize":"17","textColor":"#ffffff","actionSheetList":[{"name":"新浪微博"},{"name":"腾讯微博"},{"name":"分享"}]}}';
	var JsonData = '{"actionSheet_style":{"frameBgColor":"#ffffff","frameBroundColor":"#00000000","frameBgImg":"","btnSelectBgImg":"res://btn-act.png","btnUnSelectBgImg":"res://btn.png","cancelBtnSelectBgImg":"res://cancel-act.png","cancelBtnUnSelectBgImg":"res://cancel.png","textSize":"17","textNColor":"#ffffff","textHColor":"#ffffff", "cancelTextNColor":"#ffffff","cancelTextHColor":"#ffffff", "actionSheetList":[{"name":"退出"}]}}';
	uexActionSheet.open(x, y, width, height, JsonData);
}

//弹出actionSheet
function actionsheet() {
	uexWindow.actionSheet("织码农业数据采集", "取消", ["退出"]);

}

function cbActionSheet(opId, dataType, data) {
	switch (parseInt(data)) {
	case 0:
		//uexWidgetOne.exit();
		break;
	}
}
/*
appcan.ready(function(){
alert('ok');
appcan.initBounce();
uexWidgetOne.onMemoryWarning = function (isWarning, level) {
alert("warning");
// alert("isWarning"+isWarning+"level"+level);
};

uexCamera.cbOpen = cbCamera;
uexCamera.cbOpenInternal = cbCamera;
uexWidgetOne.cbError = function (opCode, errorCode, errorInfo) {
alert("errorCode:" + errorCode + "\nerrorInfo:" + errorInfo);
};
})
*/
