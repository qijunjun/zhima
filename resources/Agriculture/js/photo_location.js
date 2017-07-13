var longitude = "", latitude = "", userlocation = "";
var locationOpening = false;

function getLocation() {
	uexLocation.onChange = function (inLat, inLog) {
		longitude = inLog;
		latitude = inLat;

		uexLocation.cbGetAddress = function (opId, dataType, data) {
			userlocation = data;
		};
		uexLocation.getAddress(inLat, inLog);

		uexLocation.closeLocation();
		locationOpening = false;
	}
	/*
	 * 此功能手机必需处于联网状态
	 * 定位处于开启状态时不能再调用开启定位功能，否则可能导致崩溃。
	 * */
	//Showbo.Msg.alert('get ing...');
	if (locationOpening == false) {
		uexLocation.openLocation();
		locationOpening = true;
	}
};
