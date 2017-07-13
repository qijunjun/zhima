/**
 * Created by GUOQH on 2016/5/28.
 */
var id =  location.href.substr(location.href.lastIndexOf("=") + 1);
var qrcode_all,qrcode_left;
$(document).ready(function(){
    $.ajax({
        url: "/code/api/log",
        method: "post",
        dataType: "json",
        data: {
            operation : "list",
            companyid : id
        },
        success: function (data) {
            if (data.code === "001") {
                console.log(data);
                qrcode_all = data.result[0].qrcode_all;
                qrcode_left = data.result[0].qrcode_left;
            } else {
                console.error(data.code, data.message);
            }
        },
        error: function (err) {
            console.error(err);
        }
    });

    $("#addCode").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/code/api/log",
        identifier: "id",
        post: {
            operation : "list",
            companyid:id
        },
        responseHandler: function (response)
        {
            var rows;
            rows = {
                total: response.result.length,
                current: 1,
                rows: response.result
            };
            response.result = rows;
            return response;
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var addCodeNumber = methods.getId('qrcode_counts').value;
        var addNumber = Math.abs(addCodeNumber);
        if(addCodeNumber<0 && addNumber>qrcode_left){
            new Inform({title:'通知',content:'减码量超出剩余码量!<br><span class="red">请检查</span>'}).alert();
            return ;
        }else if(addCodeNumber == 0){
            new Inform({title:'通知',content:'加码量不能为0'}).alert();
            return ;
        }else{
            methods.ajax({
                url: "/code/api/createLog",
                data: {companyid : id, userid :1, num : addCodeNumber},
                callback : function(data){
                    new Inform({title:'通知',content:'加码成功'}).alert(function(){
                        window.location.reload();
                    });
                }
            })
        }
    })
});