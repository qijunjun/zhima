/**
 * Created by Lenovo on 2016/5/28.
 */
$(document).ready(function(){
    $("#user").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/admin/user/fetch",
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
                    return value != 0 ? "已认证" : "待认证";
                }
            }
        },
        formatters: {
            authenticate: function (column, row)
            {
                if (row.status != 1)
                {
                    return "<a><p class='operation authenticate'>认证</p></a>";
                }
                else
                {
                    return "";
                }
            },
            operation: function (column, row)
            {
                return "<a href='/Application/Admin/View/Edit/editUser.html?id=" + row.id + "'><p class='operation'>修改</p></a>";
            },
            repassword : function(column,row){
               // return '<a onclick="confirm(\'确定要重置'+row.name+'的密码？\')" data-id="'+row.id+'"><p class="operation comfirm">重置</p></a>';
                return '<a><p class="operation comfirm">重置</p></a>';
            },
            addCode: function (column, row)
            {
                return "<a href='/Application/Admin/View/Add/addCode.html?id=" + row.id + "'><p class='operation addCode'>增码</p></a>";
            },
            recharge: function (column, row)
            {
                return "<a href='/Application/Admin/View/Add/recharge.html?id=" + row.id + "'><p class='operation recharge'>充值</p></a>";
            }

        }
    }).on("loaded.rs.jquery.bootgrid", function()
    {
        var list;
        list = $(this).bootgrid("getCurrentIdentifiers");
        $(".authenticate").click(function (e)
        {
            var id = list[this.parentNode.parentNode.parentNode.rowIndex - 1];
            var row = $("#user").bootgrid("getRowByIdentifier", [id]);
            $.ajax({
                url: "/Admin/User/Authenticate",
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
                        $("#user").bootgrid("update", [id], [row]);
                    }
                }
            })
        });
        var comfirm;
        comfirm = $(this).bootgrid("getCurrentIdentifiers");
        $(".comfirm").click(function (e)
        {
            var id = comfirm[this.parentNode.parentNode.parentNode.rowIndex - 1];
            var row = $("#user").bootgrid("getRowByIdentifier", [id]);
            var Password = prompt("重置"+row.name+"密码为","");
            if(Password !=null && Password !=""){
                $.ajax({
                    url: "/Admin/User/repassword",
                    method: "post",
                    data: {
                        companyid: id,
                        newPassword:Password
                    },
                    dataType: "json",
                    success: function (data)
                    {
                        if (data.code === "001")
                        {
                            new Inform({title:"通知",content:"重置成功"}).alert();
                        }
                        if(data.code === "002")
                        {
                            new Inform({title:"通知",content:"密码过于简单！<br><span class='red'>请输入不同字符的密码</span>"}).alert();
                        }
                        if(data.code === "007")
                        {
                            new Inform({title:"通知",content:"重置密码与原密码一致！"}).alert();
                        }
                    },
                    error: function (err)
                    {
                        new Inform({title:"错误",content:"网络错误！"}).alert();
                        console.error(err);
                    }
                })
            }
        });
    });
});