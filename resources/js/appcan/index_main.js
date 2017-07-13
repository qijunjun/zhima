/**
 * Created by yumingfei on 2016/01/20.
 */
var isMenu = false;
//针对不同的app，需修改以下两项
var app_title = "好收成";//在app中显示出来的应用程序名称
var web_url = "http://nongye.zmade.cn/app_download/";//远程路径

function onClickItem(dateType) {
	switch (parseInt(dateType)) {
		case 0:
			uexWidgetOne.exit();
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
	uexWindow.actionSheet("好收成", "取消", ["退出"]);
}

function cbActionSheet(opId, dataType, data) {
	switch (parseInt(data)) {
		case 0:
			uexWidgetOne.exit();
			break;
	}
}

//不经确认退出app
function exitApp() {
	uexWidgetOne.exit();
};


//获取平台版本回调函数，确定是客户端是那个平台的客户端 （step:2）
function cbGetPlatform(opId, dataType, data) {
	Log('uexWidgetOne.cbGetPlatform ');
	//获取系统版本信息回调函数
	platform = data;
	Log('platform= ' + platform);
	if (data == 0) {
		// 是iphone
		uexWidget.checkUpdate();// 直接调用检查更新，检查更新地址在config.xml里面有配置
	} else if (data == 1) {
		// 是android
		flag_sdcard = 0;
		uexFileMgr.isFileExistByPath('/sdcard/');//先判断是否存在sd卡，再调用checkUpdate来进行更新
	} else {
		// 是其他平台
	}
};


function Log(s) {
	uexLog.sendLog(s);
}

function onKeyPressed(keyCode) {
	// alert(keyCode);
	if (keyCode == 0 && index == 1) {
		uexWidgetOne.exit();
	} else if (keyCode == 1) {
		//alert("menu key");
		createActionSheet();
	}
};

var flag_sdcard = 1;
var updateurl = '';//下载新apk文件地址
var filepath2 = "/sdcard/";//保存到sd卡
var fileName = '';//新版本文件名
var platform = '';//平台版本

//安卓版 ，显示下载进度 （step:7）
function onStatus(opId, fileSize, percent, status) {
	if (status == 0) {
		// 下载中...
		Log('download percent ' + percent + '%');
		uexWindow.toast('1', '5', '正在下载' + app_title + '新版，请稍后。进度：' + percent + '%', '');
	} else if (status == 1) {// 下载完成.
		uexWindow.closeToast();
		uexDownloaderMgr.closeDownloader('14');//关闭下载对象
		uexWidget.installApp(filepath2 + fileName);// 安装下载apk文件
	} else {
		uexWindow.toast('1', '5', '下载出错，请关闭' + app_title + '再次运行.', '');
	}
};

//安卓版 ，创建下载对象回调函数（step:6）
function cbCreateDownloader(opId, dataType, data) {
	Log('uexDownloaderMgr.cbCreateDownloader data=' + data);
	if (data == 0) {
		//updateurl是通过调用cbCheckUpdate回调后，放入全局变量的
		uexDownloaderMgr.download('14', updateurl, filepath2 + fileName, '0');//开始下载apk文件
	} else if (data == 1) {

	} else {

	}
};

//提示更新模态框按钮事件回调函数，判断用户选择更新还是取消 （step:5）
function cbConfirm(opId, dataType, data) {
	Log('uexWindow.cbConfirm ');
	//调用对话框提示函数
	if (data == 0) {
		//用户点击稍后按钮，不进行更新
	} else {
		//用户点击确定按钮，进行更新
		if (platform == 0) {
			//苹果版更新，通过浏览器加载appstore路径
			uexWidget.loadApp(updateurl, '', '');
			//uexWidget.loadApp("", "", updateurl);//旧方法 已经不可以使用了。
		} else if (platform == 1) {
			//安卓版更新，通过创建下载对象进行下载
			uexDownloaderMgr.createDownloader("14");
		} else {

		}
	}
};

//调用检查更新回调函数，请求成功后，弹出模态框让用户选择是否现在更新（step:4）
function cbCheckUpdate(opCode, dataType, jsonData) {
	Log('jsonData=' + jsonData);
	var obj = eval('(' + jsonData + ')');
	if (obj.result == 0) {
//                    alert("更新地址是：" + obj.url + "<br>文件名：" + obj.name + ".apk<br>文件大小：" + obj.size + "<br>版本号：" + obj.version);
		updateurl = obj.url;
		fileName = obj.name + ".apk";
		getVersionContent();
	} else if (obj.result == 1) {
		//苹果
		//alert("更新地址是：" + obj.url + "<br>文件名：" + obj.name + "<br>文件大小：" + obj.size + "<br>版本号：" + obj.version);
		// tips = "当前版本是最新的";
		// alert(tips);
	} else if (obj.result == 2) {
		// tips = "未知错误";
		// alert(tips);
	} else if (obj.result == 3) {
		// tips = "参数错误";
		// alert(tips);
	}
};

//检查是否已经存在sd卡的回调函数（step:3）
function cbIsFileExistByPath(opCode, dataType, data) {
	Log('uexFileMgr.cbIsFileExistByPath flag_sdcard=' + flag_sdcard + ' , data=' + data);
	if (flag_sdcard == 0) {
		if (data == 0) {
			Log('sdcard不存在，根据具体情况处理');
		} else {
			//执行检查更新
			uexWidget.checkUpdate();//根据config.xml里面配置的检查更新地址发起http请求
		}
		flag_sdcard = 1;
	}
};

function getVersionContent() {
	var url = web_url + "version.php";
	uexXmlHttpMgr.onData = getvSuccess;
	uexXmlHttpMgr.open(777, "get", url, "");
	uexXmlHttpMgr.send(777);
}

function getvSuccess(opid, status, result) {
	if (status == -1) {
		uexWindow.toast("0", "5", "连接不上网络^_^哦", "3000");
	}
	if (status == 1) {
		uexXmlHttpMgr.close(777);
		uexWindow.closeToast();
		if (result == "[]") {
			uexWindow.toast("0", "5", "无", "2000");
		}
		else {
			var con = eval('(' + result + ')');
			var value = "稍后;更新";
			var mycars = value.split(";");
			uexWindow.confirm(con.vtitle, con.vcontent, mycars);//弹出提示框，是否确定更新
		}
	}
}

appcan.ready(function() {
	uexWindow.cbActionSheet = cbActionSheet;
	uexWidgetOne.cbGetPlatform = cbGetPlatform;
	uexDownloaderMgr.onStatus = onStatus;
	uexDownloaderMgr.cbCreateDownloader = cbCreateDownloader;
	uexWindow.cbConfirm = cbConfirm;
	uexWidget.cbCheckUpdate = cbCheckUpdate;
	uexFileMgr.cbIsFileExistByPath = cbIsFileExistByPath;

	var plat = uexWidgetOne.getPlatform();

	if (plat == 1) {
		uexWindow.onKeyPressed = onKeyPressed;
		uexWindow.setReportKey(1, 1);
		uexWindow.setReportKey(0, 1);
	}

	var exit = document.getElementById("exit");
	exit.onclick = function () {
		exitApp();
	}
})
