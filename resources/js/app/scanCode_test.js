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
            if (!data.result.codestatus) {
                methods.getId('message').innerHTML = "<h3 class='red'>" + (data.result.scan_tips_text || "您扫的码有异常,请联系厂家") + "</h3>";
                $('#redPocket').remove();
            }
            else {
                var time = new Date(data.result.first_time+"000"-0);
                methods.getId('time').innerHTML = time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate() + " " + (time.getHours() < 10 ? ('0' + time.getHours()) : time.getHours()) + ":" + (time.getMinutes() < 10 ? ('0' + time.getMinutes()) : time.getMinutes()) + ":" + (time.getSeconds() < 10 ? ('0' + time.getSeconds()) : time.getSeconds());
                methods.getId('nowTimes').innerHTML = data.result.scancount;
            }

            /**
             * 产品信息
             */
            methods.ajax({
                url: "/Trace/Info/productInfo",
                callback: function (data) {
                    ajaxReady();
                    if(data.result === null) {
                        methods.getId('message').innerHTML = "<h3 class='red'>此码尚未启用或激活</h3>";
                        $('#redPocket').remove();
                        return ;
                    }
                    methods.getId('productArea').classList.remove('hidden');
                    console.log('产品信息');
                    id = data.result.id;
                    methods.getId('productName').innerHTML = data.result.name;
                    methods.getId('product').innerHTML = data.result.name + "-" + data.result.spec;
                    // methods.getId('productImage').src = data.result.image;
                    methods.getId('spec').innerHTML = data.result.spec;
                    methods.getId('price').innerHTML = data.result.price;
                    var address = data.result.netshop.substr(0,4);
                    if(address == "http"){
                        var netshop = data.result.netshop;
                    }else{
                        netshop = "http://"+data.result.netshop;
                    }
                    methods.getId('netShop').setAttribute('href', netshop);
                    methods.getId('netShop-btn').setAttribute('href', netshop);
                    methods.getId('productIntroduct').innerHTML = data.result.info;
                    methods.getId('pImg').innerHTML = null;
                    for (var i = 0; i < 5; i ++) {
                        if (data.result['image' + i] && data.result['image' + i] != "0") {
                            methods.getId('pImg').innerHTML +=
                                '<div class="swiper-slide">' +
                                '<img id="image' + i + '" class="magnifyImg" src="' + data.result['image' + i] + '" />' +
                                '</div>';
                        }
                        var img = "image"+i;
                    }
                    var mySwiper1 = new Swiper('.productinform .swiper-container',{
                        pagination : '.productinform .swiper-pagination',
                        loop: true,
                        autoplay : 3000,
                        speed: 750
                    });
                    $(function() {
                        var bigImg = $(".bigImg");
                        var popmask = $(".popmask");
                        var popCnt = $(".popCnt");
                        $('.magnifyImg').click(function(){
                            var self = $(this);
                            var src = self.attr("src");
                            bigImg.attr("src", src);
                            popmask.show(0);
                            popCnt.fadeIn(200);
                            $("body").addClass('hasPop');

                            bigImg.click(function() {
                                $("body").removeClass('hasPop');
                                popCnt.hide(0);
                                popmask.hide(0);
                            })
                        })
                    });
                }
            });
            /**
             * 生产信息
             */
            methods.ajax({
                url: "/Trace/Info/profileInfo",
                callback: function (data) {
                    ajaxReady();
                    if(data.result === null)
                        return ;
                    methods.getId('produceArea').classList.remove('hidden');
                    console.log('生产信息');
                    var _data = data;
                    methods.ajax({
                        url: "/admin/profile/fetch",
                        callback: function (data) {
                            var list = "";
                            for (var i in data.result) {
                                for (var j in _data.result) {
                                    if (i == j) {
                                        list += "<tr>" +
                                                    "<td>" + data.result[i] + "</td>" +
                                                    "<td>" + _data.result[j] + "</td>" +
                                                "</tr>";
                                    }
                                }
                            }
                            methods.getId('produceInfo').innerHTML = list;
                        }
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
                                "<b  class='checkout'>出库</b>" +
                                "<span>" + time.getFullYear() + "-" + (time.getMonth() + 1) + "-" + time.getDate() +"</span>" +
                                "<span>" + data.result[i].name + "</span>" +
                                "</li>";
                        }
                    }
                    methods.getId('checkIO').innerHTML = list;
                }
            });

            /**
             * 公司信息
             */
            methods.ajax({
                url: "/Trace/Info/companyInfo",
                callback: function (data) {
                    ajaxReady();
                    if(data.result === null)
                        return ;
                    methods.getId('companyArea').classList.remove('hidden');
                    console.log('公司信息');
                    calling = data.result.phone;
                    methods.getId('company').innerHTML = data.result.name;
                    methods.getId('companyLogo').src = data.result.logo;
                    methods.getId('cImg').innerHTML = null;
                    for (var i = 0; i < 5; i ++) {
                        if (data.result['intro_image' + i]) {
                            methods.getId('cImg').innerHTML +=
                                '<div class="swiper-slide">' +
                                    '<img id="companyImage' + i + '" src="' + data.result['intro_image' + i] + '" />' +
                                '</div>';
                        }
                    }

                    // methods.getId('companyImage').src = data.result.intro_image;
                    // methods.getId('companyImage1').src = data.result.intro_image1;
                    // methods.getId('companyImage2').src = data.result.intro_image2;
                    // methods.getId('companyImage3').src = data.result.intro_image3;
                    // methods.getId('companyImage4').src = data.result.intro_image4;
                    methods.getId('companyAddress').innerHTML = data.result.address;
                    methods.getId('companyIntroduct').innerHTML = data.result.introduction;
                    methods.getId('companyContact').innerHTML = data.result.phone;
                    // methods.getId('companyPhone').innerHTML = "<a href='tel:" + data.result.phone + "'><img src='/resources/images/app/contact.png' alt='联系厂家图片'></a>";
                    methods.getId('companyPhone').innerHTML = "<a href='tel:" + data.result.phone + "'><span class='blue-btn'>联系厂家</span></a>";
                    methods.getId('companyPhone-btn').setAttribute('href',"tel:" + data.result.phone);
                    var mySwiper = new Swiper('.companyinform .swiper-container',{
                        pagination : '.companyinform .swiper-pagination',
                        loop: true,
                        autoplay : 3000,
                        speed: 750
                    });
                }
            })
        }
    });

    var ajaxReady = function () {
        if (++ajaxNumber != 4) {
            return ;
        }
       methods.getId('process').setAttribute('href', '/App/ScanCode/searchProcess.html?qrcode=' + code);
        if (methods.getId('redPocket'))
            methods.addEvent(methods.getId('redPocket'), 'click', function () {
                window.location.href = 'http://hongbao.zmade.cn?qrcode=' + md5Code + code;
            });
        //相应评论的地址
        methods.ajax({
            url: "/Trace/Info/productInfo",
            callback: function (data) {
                ajaxReady();
                if(data.result === null) {
                    $('#comment').removeAttr("href");
                    $('#comment-btn').removeAttr("href");
                    $('#comment').css("background-color","rgba(183, 183, 183, 0.6)");
                    $('#comment-btn').css("background-color","rgba(183, 183, 183, 0.6)");
                    $('#report').removeAttr("href");
                    $('#report-btn').removeAttr("href");
                    $('#report-btn').css("background-color","rgba(183, 183, 183, 0.6)");
                    return ;
                }else{
                    methods.getId('comment').setAttribute('href', '/App/ScanCode/comment.html?id=' + id);
                    methods.getId('comment-btn').setAttribute('href', '/App/ScanCode/comment.html?id=' + id);
                    methods.getId('report').setAttribute('href', '/App/report/index.html?c=' + md5Code + '&b=' + code + '&id=' + id + '&call=' + calling);
                    methods.getId('report-btn').setAttribute('href', '/App/report/index.html?c=' + md5Code + '&b=' + code + '&id=' + id + '&call=' + calling);
                }
            }
        });
        // methods.getId('comment-btn').on('click',function(e) {
        //     localStorage.removeItem('lastPage');
        //     localStorage.setItem('lastPage',document.location.href);
        //     window.location.href = data.result.commentAdress;
        // });

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