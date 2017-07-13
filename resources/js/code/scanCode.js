/**
 * Created by apple on 16/5/13.
 */
$(document).ready(function () {
    var scanCode = $("#scanCode");
    scanCode.bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/code/api/scan",
        identifier: "id",
        responseHandler: function (response) {
            var rows;
            var count;
            var i;
            count = response.result.length;
            for (i = 0; i < count; i++)
            {
                if (!isNaN(response.result[i].scan_count))
                {
                    response.result[i].scan_count = parseInt(response.result[i].scan_count);
                }
            }
            rows = {
                total: response.result.length,
                current: 1,
                rows: response.result
            };
            response.result = rows;
            return response;
        },
        formatters: {
            productImage: function (column, row) {
                return "<img src='" + row.productimage + "' />";
            }
            // cout: function (column, row) {
            //  if (row.flag == 2 && row.qrcodetypeid != 0) {
            //     return ;
            //  }
            //  return "<p class='cout' data-id='" + row.id + "'>导出</p>";
            // }
        }
    });
    //  .on("loaded.rs.jquery.bootgrid", function () {
    //  var list;
    //  list = $(this).bootgrid("getCurrentIdentifiers");
    //  var eventBind = new EventBind();
    //  eventBind.deleteData.click(function () {
    //     var id = list[this.parentNode.parentNode.rowIndex - 1];
    //     $(this).parent().parent().attr("id", id);
    //     customPop("删除提示", "tip", "", id);
    //  });
    // });
    methods.addEvent(methods.getId('export'),'click',function(){
        window.location.href="/Exportexcel/Exportexcel/index/name/scaninfo";
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
            $("#switchCode").css('display','table');
            $("#scanCode-footer").css('display','none');
            $("#scanCode").css('display','none');
            $("#switchCode").bootgrid({
                ajax: true,
                ajaxSettings: {
                    dataType: "json"
                },
                url: "/Code/Api/Searchscan",
                post:{
                    startdate:startDate,
                    enddate:endDate
                },
                identifier: "id",
                responseHandler: function (response) {
                    var rows;
                    var count;
                    var i;
                    count = response.result.length;
                    for (i = 0; i < count; i++)
                    {
                        if (!isNaN(response.result[i].scan_count))
                        {
                            response.result[i].scan_count = parseInt(response.result[i].scan_count);
                        }
                    }
                    rows = {
                        total: response.result.length,
                        current: 1,
                        rows: response.result
                    };
                    response.result = rows;
                    return response;
                },
                formatters: {
                    productImage: function (column, row) {
                        return "<img src='" + row.productimage + "' />";
                    }
                }
            })
        }
    });
});