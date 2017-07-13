/**
 * Created by GUOQH on 2016/5/27.
 */
$(document).ready(function(){
    $("#marketing").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Marketing/Index/listPromotion",
        identifier: "promotionid",
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
            record: function (column, row)
            {
                return "<a href='/Application/Marketing/View/Index/getList.html?id=" + row.promotionid + "'><p class='operation' data-list='Marketing/View/Index' data-operation='getList'>查看</p>";
            },
            operation: function (column, row)
            {
                return "<a href='/Application/Marketing/View/Index/wxhongbao.html?id=" + row.promotionid + "'><p class='operation' data-list='Marketing/View/Index' data-operation='wxhongbao'>修改</p>";
            },
            delete: function (column, row)
            {
                return "<div class=\"delete\" data-id='" + row.promotionid + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $('.delete').click(function(){
            var id = $(this).data("id");
            $(this).parent().parent().attr("id",id);
            new Delete({title:'通知',content:'是否确定删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Marketing/Index/delete');
        });
    });
    methods.ajax({
        url : '/Marketing/Index/getAccount',
        callback : function(data){
            methods.getId('blance').innerHTML += Math.floor(data.result/100) + "元";
        }
    })
});