/**
 * Created by GUOQH on 2016/5/23.
 */
$(document).ready(function () {
    // $.ajax({
    //     url: "/Trace/Info/productInfo",
    //     method: "post",
    //     dataType: "json",
    //     success: function (data) {
    //         console.log(data);
    //         if(data.code !== "001") {
    //             throw new Error(data.code, data.message);
    //         }
    //         data = data.result;
    //         id = data.id;
    //     },
    //     error: function (err) {
    //         console.error(err);
    //     }
    // });
    var page="all";
    // var productid = window.location.search.substring(4);
    var qrcode = window.location.href.split('?')[1].substring(7);
    $.ajax({
        url:"/Process/Index/getProcessbyCode/page/"+page+"/qrcode/"+qrcode,
        method: "post",
        dataType: "json",
        data:{
            page: page,
            qrcode :qrcode
        },
        success: function(data){
            if(data){
                typeof data == "string" && (data = eval("(" + data + ")"));
                console.log(data);
                if (data.code === "001") {
                    var section = $("#Process");
                    var div = [];
                    var td = [];
                    
                    data=data.result;
                    for (var i = 0; i < data.length; i ++) {
                        div[i] = $("<div class='process'></div>");
                        td[0] = $("<div></div>");
                        td[1] = $("<div></div>");
                        td[2] = $("<div></div>");
                        td[3] = $("<div></div>");
                        td[4] = $("<div></div>");
                        td[5] = $("<div></div>");
                        td[1].attr("class","process_operation").html(data[i].function_name);
                        td[2].append($("<img/>").attr("src", data[i].operatorimage));
                        if(data[i].function_name !="包装入库"){
                            td[3].append("<span></span>").html("操作时间:&nbsp;&nbsp;"+data[i].event_time+"<br/>"+"操作地点:&nbsp;&nbsp;"+data[i].userlocation+"<br/>"+"文字描述:&nbsp;&nbsp;"+data[i].event_details);
                        }else{
                            td[3].append("<span></span>").html("操作时间:&nbsp;&nbsp;见包装盒<br/>"+"操作地点:&nbsp;&nbsp;"+data[i].userlocation+"<br/>"+"文字描述:&nbsp;&nbsp;"+data[i].event_details);
                        }
                        var str = "";
                        for(var k = 0; k < data[i].image_path.length; k++) {
                            str += "<img class='magnifyImg' src='" + data[i].image_path[k] + "' />";
                        }
                        td[4].append(str);
                        td[5].append($("<img/>").attr("src", "/resources/images/app/up.png"));
                        section.append(div[i].append(td[0]).append(td[1]).append(td[2]).append(td[3]).append(td[4]).append(td[5]));
                    }
                } else {
                    console.error(data.code, data.msg);
                }
            }
            else{
                console.error(data.code, data.msg);
            }
        },
        error: function (err) {
            console.error(err);
        }
    });
    $(function() {
        var bigImg = $(".bigImg");
        var popmask = $(".popmask");
        var popCnt = $(".popCnt");
        $("#Process").on("click",".magnifyImg",function(){
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
    })
});