var path;
var imageList = new Array();

var cText = 0;
var cJson = 1;
var cInt = 2;

function createImg() {
	uexCamera.cbOpen = cbCamera;
	uexCamera.cbOpenInternal = cbCamera;
	uexCamera.open();
}

function cbCamera(opCode, dataType, data) {
	switch (dataType) {
    case cText:
      path = data;
      createImgTest();
      break;
    case cJson:
      alert("uex.cJson");
      break;
    case cInt:
      alert("uex.cInt");
      break;
    default:
      alert("error");
	}
}

function createImgTest() {
  var uploadID = localStorage.getItem("uploadID") - 0;
  if(uploadID == 0 || isNaN(uploadID)) {
    localStorage.setItem("uploadID", 1);
    uploadID = 1;
  } else {
    localStorage.setItem("uploadID", uploadID + 1);
  }
  uexUploaderMgr.cbCreateUploader = function(opId, dataType, data) {
    if (dataType == 2 && data == 0) {
      appcan.window.openToast('正在上传图片,请稍候...', 5000, 5);
      uexUploaderMgr.uploadFile(opId, path, 'fileInfo', '1', 1024);
    }
  };
  uexUploaderMgr.onStatus = function(opId, fileSize, percent, serverResponse, status) {
    switch (status) {
      case 0:
        //appcan.window.openToast(percent + "%", 500, 8);
        break;
      case 1:
        uexUploaderMgr.closeUploader(opId);
        appcan.window.openToast('上传成功！', 5000, 5);
        var messagePath = $.parseJSON(serverResponse).result.message;
        imageList[imageList.length] = {
          "imagePath" : messagePath,
          "imageTime" : Date.now(),
          "longitude" : longitude,
          "latitude" : latitude,
          "location" : userlocation
        };
        $("#createimg").before("<img src='" + messagePath + "' style='margin-right: 6px;' />");
        $(".h-photo-take").children("img").css("width", $(window).width() * 0.9 / 4 - 5 + "px");
        $(".h-photo-take").children("img").css("height", $(window).width() * 0.9 / 4 - 5 + "px");
        break;
      case 2:
        alert("上传图片失败!");
        appcan.window.openToast('正在重试,请稍候...', 5000, 5);
        uexUploaderMgr.closeUploader(opId);
        createImgTest(); //重传
        break;
      default:
        break;
    }
  };
  uexUploaderMgr.createUploader(uploadID, "http://" + window.location.hostname + '/Agriculture/Createnew/uploadTemp?type=event');
}
