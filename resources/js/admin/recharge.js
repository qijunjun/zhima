/**
 * Created by GUOQH on 2016/5/28.
 */
var id =  location.href.substr(location.href.lastIndexOf("=") + 1);
$(document).ready(function(){
    $("#recharge").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/admin/account/fetch",
        identifier: "id",
        post: {
            companyId: id
        },
        converters: {
            currency: {
                to: function (value)
                {
                    var str;
                    var integer;
                    var decimal;
                    str = value.toString();
                    decimal = str.substr(-2);
                    if (decimal.length < 2)
                    {
                        decimal = "0" + decimal;
                    }
                    integer = str.length < 3 ? "0" : str.substr(0, str.length - 2);
                    return integer + "." + decimal;
                }
            }
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if(!data.getInput()){
            new Inform({title:'通知',content:'数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        methods.ajax({
            url : "/admin/account/charge",
            data:{id : id,fee: parseInt(100*$("#charge").val())},
            callback: function(data){
                new Inform({title:'通知',content:'充值成功'}).alert(function(){
                    window.location.reload();
                });
            }
        })
    })
});