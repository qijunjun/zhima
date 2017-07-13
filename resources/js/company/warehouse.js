/**
 * Created by Lenovo on 2016/5/2.
 */
$(document).ready(function(){
    $("#warehouse").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Company/Warehouse/listWarehouse",
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
            operation: function (column, row)
            {
                return "<a href='/Application/Company/View/Warehouse/edit.html?id=" + row.id + "'><p class='operation' data-list='Company/View/Warehouse' data-operation='edit'>修改</p></a>";
            },
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
            new Delete({title : '删除提示',content: '是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Company/Warehouse/delete');
        });
    });
});