/**
 * Created by apple on 16/5/6.
 */
$(document).ready(function () {
    $("#connectManage").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Product/CorrelationPack/getList",
        identifier: "id",
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
                return "<div class=\"delete\" data-id='" + row.id + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        $('.delete').click(function(){
            var id = $(this).data("id");
            $(this).parent().parent().attr("id",id);
            new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,"/Product/CorrelationPack/remove");
        });
    });
});