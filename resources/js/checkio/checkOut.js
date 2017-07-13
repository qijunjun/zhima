/**
 * Created by Lenovo on 2016/4/30.
 */

$(document).ready(function(){
    $("#checkOut").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/CheckIO/Checkout/listall",
        identifier: "_id",
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
        },
        formatters: {
            delete: function (column, row)
            {
                return "<div class=\"delete\" data-id='" + row._id + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $('.delete').click(function(){
            var id = $(this).data("id");
            $(this).parent().parent().attr("id",id);
            new Delete({title : '通知',content : '是否确定删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/CheckIO/Checkout/delete');
        });
    });
    methods.addEvent(methods.getId('export'),'click',function(){
        window.location.href="/Exportexcel/Exportexcel/index/name/checkoutinfo";
    });
    methods.addEvent(methods.getId('searchResult'),'click',function(){
        var startDate = $("#startDate").val();
        var endDate = $("#endDate").val();
        var start = parseInt(new Date(startDate).getTime() / 1000);
        var end = parseInt(new Date(endDate).getTime() / 1000);
        if(start > end){
            new Inform({title:'通知',content:'起始时间不能大于终止时间！'}).alert();
            return ;
        }else{
            $("#switchcheckOut").css('display','table');
            $("#checkOut-footer").css('display','none');
            $("#checkOut").css('display','none');
            $("#switchcheckOut").bootgrid({
                ajax: true,
                ajaxSettings: {
                    dataType: "json"
                },
                url: "/CheckIO/Checkout/SearchCheckout",
                post:{
                    startdate:startDate,
                    enddate:endDate
                },
                identifier: "_id",
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
                },
                formatters: {
                    delete: function (column, row)
                    {
                        return "<div class=\"delete\" data-id='" + row._id + "'>" + methods.getSvg() + "</div>"
                    }
                }
            }).on("loaded.rs.jquery.bootgrid", function()
            {
                $('.delete').click(function(){
                    var id = $(this).data("id");
                    $(this).parent().parent().attr("id",id);
                    new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/CheckIO/Checkin/delete');
                });
            });
        }
    });
});