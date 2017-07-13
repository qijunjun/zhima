/**
 * Created by Lenovo on 2016/6/18.
 */
$(document).ready(function(){
    var id =location.href.substr(location.href.lastIndexOf("=") + 1);
    $("#getlist").bootgrid({
        ajax: true,
        ajaxSettings: {
            dataType: "json"
        },
        url: "/Marketing/Index/listRecordByPromotion/promotionid/"+id,
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
            currency: {
                to: function (value)
                {
                    var str;
                    var integer;
                    str = value.toString();
                    integer = (str / 100).toPrecision(str.toString().length);
                    return integer;
                }
            }
        }
    });
    methods.ajax({
        url : '/Marketing/Index/actname',
        data:{promotionid:id},
        callback : function(data){
            methods.getId('promotionName').innerHTML += data.result;
        }
    });
    $("#promotionid").val(id);
});