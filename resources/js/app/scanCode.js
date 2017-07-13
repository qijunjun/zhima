/**
 * Created by apple on 16/5/6.
 */
var href = window.location.href;
var code = href.substr(href.indexOf("/b/") + 3, 14);
href = window.location.href;
var md5Code = href.substr(href.indexOf("/c/") + 3, 16);
var readyNumber = 0;
var id, calling, longitude, latitude, position;
$(document).ready(function () {
    $.ajax({
        url: "/code/api/check/c/" + md5Code + "/b/" + code,
        method: "post",
        dataType: "json",
        data: {
            b: code,
            c: md5Code
        },
        success: function (data) {
            $.ajax({
                url: "/code/api/scaninfo/c/" + md5Code + "/b/" + code,
                method: "post",
                dataType: "json",
                async: false,
                data: {
                    b: code,
                    c: md5Code
                },
                success: function (data) {},
                error: function (err) {
                    console.error(err);
                }
            });
            var datetime;
            var str;
            var hours, minutes, seconds;
            if (data.code !== "001") {
                $("#message").html("<p>您所查询的二维码有异常！请与厂家联系</p>");
                $("#redPocket").css("display", "none");
                throw new Error(data.code, data.message);
            }
            data = data.result;
            if(data.first_time==null){
                var myDate = new Date();
                datetime = new Date( myDate.getTime());
                str = datetime.getFullYear() + "年" + (datetime.getMonth() + 1) + "月" + datetime.getDate() + "日";
                str += " ";
                hours = datetime.getHours();
                minutes = datetime.getMinutes();
                seconds = datetime.getSeconds();
                str += (hours < 10 ? "0" : "") + hours + ":";
                str += (minutes < 10 ? "0" : "") + minutes + ":";
                str += (seconds < 10 ? "0" : "") + seconds;
            }
            else{
                datetime = new Date(data.first_time + '000'- 0);
                str = datetime.getFullYear() + "年" + (datetime.getMonth() + 1) + "月" + datetime.getDate() + "日";
                str += " ";
                hours = datetime.getHours();
                minutes = datetime.getMinutes();
                seconds = datetime.getSeconds();
                str += (hours < 10 ? "0" : "") + hours + ":";
                str += (minutes < 10 ? "0" : "") + minutes + ":";
                str += (seconds < 10 ? "0" : "") + seconds;
            }
            $("#time").html(str);
            $("#nowTimes").html(Number(data.scancount)+1);
        },
        error: function (err) {
            console.error(err);
        }
    });
    $.ajax({
        url: "/Trace/Info/prepareInfo/code/" + code,
        method: "post",
        dataType: "json",
        data: {
            code: code
        },
        success: function (data) {
            ajaxData();
        },
        error: function (err) {
            console.error(err);
        }
    });
    //数据填充
    $("#code").html(code);
});

var ajaxData = function () {
    //抓取产品信息
    $.ajax({
        url: "/Trace/Info/productInfo",
        method: "post",
        dataType: "json",
        success: function (data) {
            console.log(data);
            if(data.code !== "001") {
                throw new Error(data.code, data.message);
            }
            data = data.result;
            $("#productName").html(data.name);
            $("#productInfo").html(data.info);
            $("#productImage").attr("src",data.image);
            $("#product").html(data.name + "-" + data.spec);
            $("#spec").html(data.spec);
            $("#price").html("<b>零售价格</b>：" + data.price + "RMB");
            $("#productIntroduct").html(data.info);
            id = data.id;
            readyNumber ++;
            aLink();
            $("#netshop").click(function(){
                window.location.href= (data.netshop == 0 ? "javascript:void(0);" : data.netshop);
            });
            $("#redPocket").click(function(){
                 window.location.href="http://hongbao.zmade.cn?qrcode="+md5Code+code;
            });
            $("#process").click(function(){
                window.location.href="/App/ScanCode/searchProcess.html?id="+id;
            });
        },
        error: function (err) {
            console.error(err);
        }
    });
    //抓取生产信息记录
    var produceInfoList = {};
    $.ajax({
        url: "/admin/profile/fetch",
        method: "post",
        dataType: "json",
        async: false,
        success: function (data) {
            console.log(data);
            if (data.code !== "001") {
                customPop("错误", "tip", "", "", undefined, data.msg);
                throw new Error(data.code, data.msg);
            }
            produceInfoList = data.result;
        },
        error: function (err) {
            customPop("错误", "tip", "", "", undefined, "网络错误!");
            console.error(err);
        }
    });
    var id = getParameterByName("id");
    $.ajax({
       url: "/Trace/Info/profileInfo",
        method:"post",
        dataType:"json",
        data: {
            id: id
        },
        success: function(data){
            console.log(data);
            if(data.code !== "001"){
                throw new Error(data.code,data.message);
            }
            console.log(data.result);
            var str ="";
            // var str = "<tr>" +
            //     "<td>生产信息名称</td>" +
            //     "<td>"+data.result.name+"</td>" +
            //     "</tr>";
            for (var i in data.result) {
                for (var j in produceInfoList) {
                    if (i == j) {
                        str += "<tr>" +"<td>" + produceInfoList[j] + "</td>" +"<td>" + data.result[i] + "</td>"+"</tr>";
                    }
                }
            }
            $("#produceInfo").html(str);
        },
        error:function(err){
            customPop("错误", "tip", "", "", undefined, "网络错误!");
            console.error(err);
        }
    });
    //抓取出入库记录
    $.ajax({
        url: "/Trace/Info/warehouseInfo",
        method: "post",
        dataType: "json",
        success: function (data) {
            console.log(data);
            if (data.code !== "001") {
                throw new Error(data.code, data.message);
            }
            var ul, li, b, span,datetime,str,hours, minutes, seconds;
            data = data.result;
            ul = $("<ul></ul>");
            span = [];
            for (var i = 0; i < data.length; i ++) {
                datetime = new Date(typeof data[i].time == "string" && data[i].time.length == 13? data[i].time-0 : data[i].time + "000"-0);
                str = datetime.getFullYear() + "年" + (datetime.getMonth() + 1) + "月" + datetime.getDate() + "日";
                str += " ";
                hours = datetime.getHours();
                minutes = datetime.getMinutes();
                seconds = datetime.getSeconds();
                str += (hours < 10 ? "0" : "") + hours + ":";
                str += (minutes < 10 ? "0" : "") + minutes + ":";
                str += (seconds < 10 ? "0" : "") + seconds;
                li = $("<li></li>");
                b = $("<b></b>");
                data[i].type == 1 ? b.html("入库") : b.html("出库");
                span[0] = $("<span></span>").html(str);
                span[1] = $("<span></span>").html(data[i].name);
                span[2] = $("<span></span>").html("&nbsp;/");
                li.append(b).append(span[0]).append(span[2]).append(span[1]);
                ul.append(li);
            }
            $("#checkIO").append(ul);
        },
        error: function (err) {
            console.error(err);
        }
    });
    //抓取企业信息
    $.ajax({
        url:"/Trace/Info/companyInfo",
        method:"post",
        dataType:"json",
        success: function (data) {
            console.log(data);
            if(data.code !== "001") {
                throw new Error(data.code, data.message);
            }
            data = data.result;
            var a=$("<a></a>").attr("href","tel:"+data.phone);
            var img=$('<img src="/resources/images/app/contact.png" alt="联系厂家图片">');
            $("#company").html(data.name);
            $("#companyLogo").attr("src",data.logo);
            $("#companyImage").attr("src",data.intro_image);
            $("#companyAddress").html(data.address);
            $("#companyIntroduct").html(data.introduction);
            $("#companyContact").html(data.contact);
            a.append(img);
            $("#companyPhone").append(a);
            calling = data.phone;
            readyNumber ++;
            aLink();
        },
        error: function (err) {
            console.error(err);
        }
    });
};
function getParameterByName (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}
var aLink = function() {
    if (readyNumber == 2){
        $("#report").attr("href", "/App/report/index.html?c=" + md5Code + "&b=" + code + "&id=" + id + "&call=" + calling + "");

        (function getLocation(){
            var options={
                enableHighAccuracy:true,
                maximumAge:1000
            };
            if(navigator.geolocation){
                //浏览器支持geolocation
                navigator.geolocation.getCurrentPosition(onSuccess,onError,options);
            }else{
                //浏览器不支持geolocation
                alert('您的浏览器不支持地理位置定位');
            }
        })();
        //成功时
        function onSuccess(position){
            //返回用户位置
            //经度
            longitude = position.coords.longitude;
            //纬度
            latitude = position.coords.latitude;

            //根据经纬度获取地理位置，不太准确，获取城市区域还是可以的
            var map = new BMap.Map("allmap");
            var point = new BMap.Point(longitude,latitude);
            var gc = new BMap.Geocoder();
            gc.getLocation(point, function(rs){
                var addComp = rs.addressComponents;
                position = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + ", " + addComp.streetNumber;
                $.ajax({
                    url: "/Fleeing/Fleeing/analyze",
                    method: "post",
                    dataType: "json",
                    data: {
                        b: code,
                        longitude: longitude,
                        latitude: latitude,
                        location: position
                    },
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (err) {
                        // alert("错误");
                    }
                });
            });
        }
        //失败时
        function onError(error){
            switch(error.code){
                case 1:
                    alert("位置服务被拒绝");
                    break;
                case 2:
                    alert("暂时获取不到位置信息");
                    break;
                case 3:
                    alert("获取信息超时");
                    break;
                case 4:
                    alert("未知错误");
                    break;
            }
            $.ajax({
                url: "/Fleeing/Fleeing/analyze",
                method: "post",
                dataType: "json",
                data: {
                    b: code
                },
                success: function (data) {
                    console.log(data);
                },
                error: function (err) {
                    // alert("错误");
                }
            });
        }
    }
};