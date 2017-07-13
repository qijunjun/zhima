/**
 * Created by GUOQH on 2016/7/4.
 */
$(document).ready(function () {
    var href = window.location.href;
    var code = href.substr(href.indexOf("/b/") + 3, 14);
    href = window.location.href;
    var md5Code = href.substr(href.indexOf("/c/") + 3, 16);

    var ajaxNumber = 0;

    var id, calling, longitude, latitude, position;

    var isBottom = true;

    methods.ajax({
        url: "/Code/api/check",
        callback: function (data) {
            isBottom = data.result.codetype == '1';

            methods.getId('code').innerHTML = code;
            if (!data.result.codestatus)
                methods.getId('message').innerHTML = "<h3 class='red'>" + (data.result.scan_tips_text || "您扫的码有异常,请联系厂家") + "</h3>";
            else {
                var time = new Date(data.result.first_time+"000"-0);
                methods.getId('time').innerHTML = time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate() + " " + (time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours()) + ":" + (time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes()) + ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
                methods.getId('nowTimes').innerHTML = data.result.scancount;
                methods.getId('report').setAttribute('href', '/App/report/index.html?c=' + md5Code + '&b=' + code + '&id=' + id + '&call=' + calling);
            }

            /**
             * 产品信息
             */
            methods.ajax({
                url: "/Trace/Info/productInfo",
                callback: function (data) {
                    ajaxReady();
                    if(data.result === null)
                        return ;
                    methods.getId('productArea').classList.remove('hidden');
                    console.log('产品信息');
                    id = data.result.id;
                    methods.getId('productName').innerHTML = data.result.name;
                    methods.getId('product').innerHTML = data.result.name + "-" + data.result.spec;
                    methods.getId('spec').innerHTML = data.result.spec;
                    methods.getId('price').innerHTML = data.result.price;
                    methods.getId('productIntroduct').innerHTML = data.result.info;
                    methods.getId('pImg').innerHTML = null;
                    for (var i = 0; i < 5; i ++) {
                        if (data.result['image' + i] && data.result['image' + i] != "0") {
                            methods.getId('pImg').innerHTML +=
                                '<div class="swiper-slide">' +
                                '<img id="image' + i + '" src="' + data.result['image' + i] + '" />' +
                                '</div>';
                        }
                    }
                    var mySwiper1 = new Swiper('.productinform .swiper-container',{
                        pagination : '.productinform .swiper-pagination',
                        loop: true,
                        autoplay : 3000,
                        speed: 750
                    });
                }
            });
            

            /**
             * 出入库
             */
            methods.ajax({
                url: "/Trace/Info/warehouseInfo",
                callback: function (data) {
                    ajaxReady();
                    if(data.result.length === 0)
                        return ;
                    methods.getId('checkIOArea').classList.remove('hidden');
                    console.log('出入库');
                    var list = "";
                    for (var i = 0; i < data.result.length; i ++) {
                        var time = new Date(data.result[i].time.toString().length != 13 ? (data.result[i].time + "000" - 0) : (data.result[i].time - 0));
                        if(data.result[i].type == 1){
                            list += "<li>" +
                                "<b class='checkin'>入库</b>" +
                                "<span>" + time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate() +"</span>" +
                                "<span>" + data.result[i].name + "</span>" +
                                "</li>";
                        }else{
                            list += "<li>" +
                                "<b  class='checkout'>发往</b>" +
                                "<span>" + time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate() +"</span>" +
                                "<span>" + data.result[i].name + "</span>" +
                                "</li>";
                        }
                }
                    methods.getId('checkIO').innerHTML = list;
                }
            });
        }
    });

    var ajaxReady = function () {
        if (++ajaxNumber != 2) {
            return ;
        }

        if (!isBottom) {
            return ;
        }
        getLocation();
    };

    var getLocation = function (){
        var options={
            enableHighAccuracy:true,
            maximumAge:1000
        };
        if(navigator.geolocation){
            //浏览器支持geolocation
            navigator.geolocation.getCurrentPosition(onSuccess,onError,options);
        }else{
            //浏览器不支持geolocation
            // alert('您的浏览器不支持地理位置定位');
            new Inform({title: '通知', content: '您的浏览器不支持地理位置定位'}).alert();
        }
    };

    var onSuccess = function (_position){
        //返回用户位置
        //经度
        longitude = _position.coords.longitude;
        //纬度
        latitude = _position.coords.latitude;

        //根据经纬度获取地理位置，不太准确，获取城市区域还是可以的
        var map = new BMap.Map("allmap");
        var point = new BMap.Point(longitude,latitude);
        var gc = new BMap.Geocoder();
        gc.getLocation(point, function(rs){
            var addComp = rs.addressComponents;
            position = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
            locationReady();
        });
    };

    var onError = function (error) {
        switch (error.code) {
            case 1:
                // alert("位置服务被拒绝");
                //new Inform({title: '通知', content: '位置服务被拒绝'}).alert();
                break;
            case 2:
                // alert("暂时获取不到位置信息");
                //new Inform({title: '通知', content: '暂时获取不到位置信息'}).alert();
                break;
            case 3:
                // alert("获取信息超时");
                //new Inform({title: '通知', content: '获取信息超时'}).alert();
                break;
            case 4:
                // alert("未知错误");
                //new Inform({title: '通知', content: '未知错误'}).alert();
                break;
        }
        locationReady();
    };

    var locationReady = function () {
        /**
         * 扫码记录
         */
        $.ajax({
            url: "/Code/api/scaninfo",
            method: 'post',
            dataType: "json",
            data: {
                longitude: longitude || null,
                latitude: latitude || null,
                location: position || null
            },
            success: function (data) {
                console.log('扫码记录');
            },
            error: function (err) {
                console.error(err);
            }
        });

        /**
         * 窜货
         */
        $.ajax({
            url: "/Fleeing/Fleeing/analyze",
            method: 'post',
            dataType: "json",
            data: {
                b: code,
                longitude: longitude || null,
                latitude: latitude || null,
                location: position || null
            },
            success: function (data) {
                console.log('窜货');
            },
            error: function (err) {
                console.error(err);
            }
        });
    };
});