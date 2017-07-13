/**
 * Created by apple on 16/5/5.
 */
$(document).ready(function () {
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var code = $("#code").val();
        var codefirst = code.substring(4,5);
        if(codefirst == "1"){
            $.ajax({
                url: "/Trace/Info/prepareInfo/",
                method:"post",
                dataType: "json",
                data: {code: code},
                success: function(data){
                    if(data.code === "001") {
                        //查询的为质量码，则把关联质量码信息清空
                        var xcode = $(".xcode");
                        xcode.empty();
                        $(".zcode").empty();
                        //抓取箱码信息
                        $.ajax({
                            url :"/Trace/Info/findCodeInfo",
                            method:"post",
                            dataType: "json",
                            success: function(data){
                                //每次提交查询新箱码时，把原来的数据清空
                                var zcode = $(".zcode");
                                zcode.empty();
                                if(data.result.length != 0){
                                    var str = "<div class='title xzcode'><h3>关联箱码信息</h3></div>"+"<div class='xmcode'>"+"<div>"+
                                        "<label for='x_code'>质量码所在的箱码</label>"+
                                        "<input type='text' name='x_code' id='x_code' value='"+data.result.qrcode_pack+"' readonly='readonly'/>"+
                                        "</div>"+"</div>";
                                    methods.getId('xcode').innerHTML = str;
                                }else{
                                    var str = "<div class='title xzcode'><h3>关联箱码信息</h3></div>"+"<div class='xmcode'>"+"<div>"+
                                        "<label for='x_code'>质量码所在的箱码</label>"+
                                        "<input type='text' name='x_code' id='x_code' value='该质量码未关联箱码' readonly='readonly'/>"+
                                        "</div>"+"</div>";
                                    methods.getId('xcode').innerHTML = str;
                                }
                            },
                            error:function(err){
                                console.error(err);
                            }
                        });
                        zData();
                        // 抓取质量码查询物流信息
                        $.ajax({
                            url : "/Trace/Info/logisticsInfo" ,
                            method: "post",
                            dataType: "json",
                            success: function (data) {
                                var logistics = $("#logistics");
                                logistics.empty();
                                if(data.result != null){
                                    var str = "<div style='width: 330px;'>"+
                                        "<label for='logistics' >物流公司</label>"+
                                        "<input type='text' name='logistics' id='logistics' style='width: 200px;' value='"+data.result.logistics+"' readonly='readonly'/>"+
                                        "</div>"+"<div>"+
                                        "<label for='expresslist'>物流单号</label>"+
                                        "<input type='text' name='expresslist' id='expresslist' value='"+data.result.expresslist+"' readonly='readonly'/>"+
                                        "</div>";
                                    document.getElementById('logistics').innerHTML = str;
                                }
                            },
                            error:function(err){
                                customPop("错误", "tip", "", "", undefined, "网络错误!");
                                console.error(err);
                            }
                        });
                    }else{
                        $(".xcode").empty();
                        $(".zcode").empty();
                        $("#product").empty();
                        $(".produceInfo").empty();
                        $(".warehouse").empty();
                        $("#agent").empty();
                        $("#logistics").empty();
                        customPop("错误", "tip", "", "", undefined, data.message);
                    }
                },
                error: function (err) {
                    customPop("错误", "tip", "", "", undefined, "网络错误!");
                    console.error(err);
                }
            });
        }else{
            $.ajax({
                url: "/Trace/Info/prepareInfo/",
                method:"post",
                dataType: "json",
                data: {
                    code: code
                },
                success: function(data){
                    if(data.code === "001") {
                        console.log(data);
                        var xzcode = $(".xzcode");
                        xzcode.empty();
                        $(".xmcode").empty();
                        //抓取质量码信息
                        $.ajax({
                            url :"/Trace/Info/searchCodeInfo",
                            method:"post",
                            dataType: "json",
                            success: function(data){
                                //每次提交查询新质量码时，把原来的数据清空
                                var xmcode = $(".xmcode");
                                xmcode.empty();
                                if(data.result != null){
                                    var str = "<div class='title xcode'><h3>关联质量码信息</h3></div>"+"<div class='zcode'>"+"<div>"+
                                        "<label for='z_code'>此箱包含的质量码</label>"+
                                        "<input type='text' name='z_code' id='z_code' value='"+data.result.qrcode_range_s+"—"+data.result.qrcode_range_e+"' readonly='readonly'/>"+
                                        "</div>"+"</div>";
                                    methods.getId('xcode').innerHTML = str;
                                }
                                else{
                                    var str1 = "<div class='title xcode'><h3>关联质量码信息</h3></div>"+"<div class='zcode'>"+"<div>"+
                                        "<label for='z_code'>此箱包含的质量码</label>"+
                                        "<input type='text' name='z_code' id='z_code' value='该箱码未关联质量码' readonly='readonly'/>"+
                                        "</div>"+"</div>";
                                    document.getElementById('xcode').innerHTML = str1;
                                }

                            },
                            error:function(err){
                                console.error(err);
                            }
                        });
                        zData();
                        // 抓取箱码查询物流信息
                        $.ajax({
                            url : "/Trace/Info/logisticspackInfo" ,
                            method: "post",
                            dataType: "json",
                            success: function (data) {
                                var logistics = $("#logistics");
                                logistics.empty();
                                if(data.result != null){
                                    var str = "<div style='width: 330px;'>"+
                                        "<label for='logistics' >物流公司</label>"+
                                        "<input type='text' name='logistics' id='logistics' style='width: 200px;' value='"+data.result.logistics+"' readonly='readonly'/>"+
                                        "</div>"+"<div>"+
                                        "<label for='expresslist'>物流单号</label>"+
                                        "<input type='text' name='expresslist' id='expresslist' value='"+data.result.expresslist+"' readonly='readonly'/>"+
                                        "</div>";
                                    document.getElementById('logistics').innerHTML = str;
                                }
                            },
                            error:function(err){
                                customPop("错误", "tip", "", "", undefined, "网络错误!");
                                console.error(err);
                            }
                        });
                    }else{
                        $(".zcode").empty();
                        $("#product").empty();
                        $(".produceInfo").empty();
                        $(".warehouse").empty();
                        $("#agent").empty();
                        $("#logistics").empty();
                        customPop("错误", "tip", "", "", undefined,data.message);
                        console.error(data.code,data.msg);
                    }
                },
                error: function (err) {
                    customPop("错误", "tip", "", "", undefined, "网络错误!");
                    console.error(err);
                }
            });
        }
    });

    var zData = function () {
        //抓取产品信息
        $.ajax({
            url : "/Trace/Info/productInfo",
            method: "post",
            dataType: "json",
            success: function (data) {
                $("#product").empty();
                if(data.result != null){
                    var str = "<div>"+
                        "<label for='name'>产品名称</label>"+
                        "<input type='text' name='name' id='name' value='"+data.result.name+"' readonly='readonly'/>"+
                        "</div>"+"<div>"+
                        "<label for='spec'>产品规格</label>"+
                        "<input type='text' name='spec' id='spec' value='"+data.result.spec+"' readonly='readonly'/>"+
                        "</div>"+"<div>"+
                        "<label for='price'>产品价格</label>"+
                        "<input type='text' name='price' id='price' value='"+data.result.price+"' readonly='readonly'/>"+
                        "</div>";
                    document.getElementById('product').innerHTML = str;
                }
            },
            error:function(err){
                customPop("错误", "tip", "", "", undefined, "网络错误!");
                console.error(err);
            }
        });
        //抓取生产信息
        var produceInfoList = {};
        $.ajax({
            url: "/admin/profile/fetch",
            method: "post",
            dataType: "json",
            async: false,
            success: function (data) {
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
        $.ajax({
            url: "/Trace/Info/profileInfo",
            method:"post",
            dataType:"json",
            success: function(data){
                if (data.code !== "001") {
                    throw new Error(data.code, data.message);
                }
                var produceInfo = $(".produceInfo");
                produceInfo.empty();
                var str = "<div style='width: 330px;'>"+
                    "<label for='name' style='width: 100px;'>生产信息名称</label>"+
                    "<input type='text' name='name' id='name' style='width: 190px;' value='"+data.result.name+"' readonly='readonly'/>"+
                    "</div>";
                for (var i in data.result){
                    for (var j in produceInfoList) {
                        if (i == j) {
                            str += "<div>" +
                                "<label for='" + i + "'>" + produceInfoList[j] + "</label>" +
                                "<input type='text' name='" + i + "' id='" + i + "' data-name='" + produceInfoList[j] + "' value='" + data.result[i] + "' readonly='readonly' />" +
                                "</div>"
                        }
                    }
                }
               document.getElementById('produceInfo').innerHTML= str;
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
                if (data.code !== "001") {
                    throw new Error(data.code, data.message);
                }
                var warehouse = $(".warehouse");
                warehouse.empty();
                var ul = $("<ul></ul>");
                var li, datetime, type,str,hours, minutes, seconds;
                data = data.result;
                for (var i = 0; i < data.length; i ++) {
                    if (data[i].time != null) {
                        datetime = new Date(typeof data[i].time == "number" ? data[i].time.toString().length == 13 ? (data[i].time - 0) : (data[i].time + "000" - 0) : (data[i].time.length == 13 ? (data[i].time - 0) : (data[i].time + "000" - 0)));
                        str = datetime.getFullYear() + "年" + (datetime.getMonth() + 1) + "月" + datetime.getDate() + "日";
                        str += " ";
                        hours = datetime.getHours();
                        minutes = datetime.getMinutes();
                        seconds = datetime.getSeconds();
                        str += (hours < 10 ? "0" : "") + hours + ":";
                        str += (minutes < 10 ? "0" : "") + minutes + ":";
                        str += (seconds < 10 ? "0" : "") + seconds;
                    } else {
                        str = "时间为空";
                    }
                    data[i].type == 1 ? type = "入库" : type = "出库";
                    li = $("<li></li>");
                    li.html(str + "-" + type + "-" + data[i].name);
                    ul.append(li);
                }
                warehouse.append(ul);
            },
            error:function(err){
                customPop("错误", "tip", "", "", undefined, "网络错误!");
                console.error(err);
            }
        });
        // 抓取经销商信息
        var qcode = $("#code").val();
        $.ajax({
            url : "/CheckIO/Checkout/getAgentbyQCode/qcode/"+qcode ,
            method: "post",
            dataType: "json",
            success: function (data) {
                var agent = $("#agent");
                agent.empty();
                if(data.result.length != 0){
                    var str = "<div style='width: 330px;'>"+
                        "<label for='agent_name' >经销商名称</label>"+
                        "<input type='text' name='agent_name' id='agent_name' style='width: 200px;' value='"+data.result.agent_name+"' readonly='readonly'/>"+
                        "</div>"+"<div>"+
                        "<label for='agent_legalperson'>经销商负责人</label>"+
                        "<input type='text' name='agent_legalperson' id='agent_legalperson' value='"+data.result.agent_legalperson+"' readonly='readonly'/>"+
                        "</div>"+"<div>"+
                        "<label for='agent_phone'>经销商电话</label>"+
                        "<input type='text' name='agent_phone' id='agent_phone' value='"+data.result.agent_phone+"' readonly='readonly'/>"+
                        "</div>";
                    document.getElementById('agent').innerHTML = str;
                }
            },
            error:function(err){
                customPop("错误", "tip", "", "", undefined, "网络错误!");
                console.error(err);
            }
        });
    };
});