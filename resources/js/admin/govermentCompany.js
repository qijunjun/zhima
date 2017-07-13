/**
 * Created by 123 on 2016/8/13.
 */

$(document).ready(function () {
    var companyName =document.getElementById('companyName');
    var select = document.getElementById('government_name');
    select.innerHTML = null;
    methods.ajax({
        url: "/Admin/Government/showGovernmentInfo",
        callback : function(data){
            data = data.result;
            var str = "<option value='0' disabled='disabled' selected='selected'>--请选择政府名称--</option>";
            for(var i = 0;i < data.length;i++){
                str +="<option value='"+data[i].id+"'>"+data[i].name + "</option>";
            }
            select.innerHTML += str;
        }
    });
    // methods.addEvent(select,'change',function(){
        companyName.innerHTML = null;
        methods.ajax({
            url: "/Admin/Government/showCompanyInfo",
            callback : function(data){
                var str1="<option value='0' disabled='disabled' selected='selected'>--请选择企业名称--</option>";
                for (var i = 0; i < data.result.length; i ++) {
                    str1 += "<option value='" + data.result[i].id + "'>" + data.result[i].name + "</option>";
                }
                companyName.innerHTML += str1;
            }
        });
    // });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var govermentselect = document.getElementById('government_name');
        if(govermentselect.value === "0" || companyName.value === "0"){
            new Inform({title:'通知',content:'政府或企业名称不能为空<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        methods.ajax({
            url: "/Admin/Government/addCorrGovernment_Company",
            data: {
                government_id:methods.getId("government_name").value,
                company_id: methods.getId("companyName").value
            },
            callback : function(data){
                new Inform({title:'通知',content:'关联成功'}).alert(function(){
                    window.location.reload();
                })
            }
        })
    });
    $("#governmentCompany").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Admin/Government/listAllCorrInfo",
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
            new Delete({title:'删除提示',content:'是否确认删除?<br /><span class="red">一旦删除将不可恢复</span>'}).alert(id,'/Admin/Government/del');
        })
    });
});