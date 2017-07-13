/**
 * Created by 123 on 2016/12/26.
 */
$(document).ready(function(){
    $("#Inspection").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/ByInspection/ByInspection/getRecord",
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
        converters: {
            status: {
                to: function (value)
                {
                    return value != 0 ? "已处理" : "待处理";
                }
            }
        },
        formatters: {
            authenticate: function (column, row)
            {
                if (row.status != 1)
                {
                    return "<a><p class='operation authenticate'>处理</p></a>";
                }
                else
                {
                    return "";
                }
            },
            delete: function (column, row)
            {
                return "<div class=\"delete\" data-id='" + row.id + "'>" + methods.getSvg() + "</div>"
            }
        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        var list;
        list = $(this).bootgrid("getCurrentIdentifiers");
        $(".authenticate").click(function (e)
        {
            var id = list[this.parentNode.parentNode.parentNode.rowIndex - 1];
            var row = $("#Inspection").bootgrid("getRowByIdentifier", [id]);
            $.ajax({
                url: "/ByInspection/ByInspection/dealRecord",
                method: "post",
                data: {
                    id: id
                },
                dataType: "json",
                success: function (data)
                {
                    if (data.code === "001")
                    {
                        row.status = 1;
                        $("#Inspection").bootgrid("update", [id], [row]);
                    }
                }
            })
        });
        $('.delete').click(function(){
            var id = $(this).data("id");
            $(this).parent().parent().attr("id",id);
            new Delete({title:'通知',content:'是否确定删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/ByInspection/ByInspection/delRecord');
        });
    });
});