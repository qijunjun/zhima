/**
 * Created by 123 on 2016/7/12.
 */
$(document).ready(function () {
    var index = 0;
    var i=1;
    var id= "grid0";
    var province_id ="province1";
    var city_id = "city1";
    var district_id = "district1";
    var del_id= "delete1";
    $("#add").click(function(){
        var div =  $('<div>',{
            id : id,
            "class" : "grid",
            html:"<div>"+"<label>省</label>"+"<select class='province' name="+province_id+" id='"+province_id+"'>"+"<option value='0'>请选择省</option>"+"</select>"+"</div>"+"<div>"+"<label for="+city_id+">市</label>"+"<select name="+city_id+" id="+city_id+" class='city'>"+"<option value='0'>请选择市</option>"+"</select>"+"</div>"+"<div>"+"<label for="+district_id+">县</label>"+"<select name="+district_id+" id="+district_id+" class='district'>"+"<option value='0'>请选择区/县</option>"+"</select>"+"</div>"+"<button class='delete' id="+del_id+" >" + methods.getSvg() + "</button>"
        });
        $("#list").append(div);
        index++;
        var arr =["grid","province","city","district","delete"];
        for(var j=0;j<5;j++){
            $("."+arr[j]).attr("id",function(index){
                return arr[j]+index;
            });
        }
        var domProvince1, domCity1, domDistrict1;
        domProvince1 = $("#province"+i);
        domCity1 = $("#city"+i);
        domDistrict1 = $("#district"+i);
        var area = new Area(JSON.parse(localStorage.getItem("area")));
        domProvince1.html(area.getProvinceOption());
        domProvince1.change(function () {
            domCity1.html(area.getCityOption(domProvince1.val()));
            domDistrict1.empty();
            domDistrict1.val("");
        });
        domCity1.change(function () {
            domDistrict1.html(area.getDistrictOption(domProvince1.val(), domCity1.val()));
        });
        i++;
    });

    $("#list").on("click",".delete",function(){
        var self = $(this);
        var grid = self.closest(".grid");
        grid.index()!=2 && grid.remove();
        var arr =["grid","province","city","district","delete"];
        for(var j=0;j<5;j++){
            $("."+arr[j]).prop("id",function(index){
                return arr[j]+index;
            });
        }
        i--;
    });
    var domProvince, domCity, domDistrict;
    domProvince = $("#province0");
    domCity = $("#city0");
    domDistrict = $("#district0");
    var area = new Area(JSON.parse(localStorage.getItem("area")));
    domProvince.html(area.getProvinceOption());
    domProvince.change(function () {
        domCity.html(area.getCityOption(domProvince.val()));
        domDistrict.empty();
        domDistrict.val("");
    });
    domCity.change(function () {
        domDistrict.html(area.getDistrictOption(domProvince.val(), domCity.val()));
    });
    methods.addEvent(document.getElementById('submit'), 'click', function () {
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        var submitAddress = [];
        var grids = $(".list >.grid");
        var len = grids.length;
        for(var i=0;i<len;i++){
            submitAddress[i] = {};
            submitAddress[i].province = $("#province"+i).val();
            submitAddress[i].city = $("#city"+i).val();
            submitAddress[i].district = $("#district"+i).val();

        }
        data.common('submitAddress',submitAddress);
        console.log(data.data);
        if(len>5){
            new Inform({title: '通知', content: '代理区域不能超过5个<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        methods.ajax({
            url: "/Company/Agent/add",
            data: data.data,
            callback: function () {
                new Inform({title: '通知', content: '已添加经销商成功!'}).alert(function () {
                    window.location.href = "/Application/Company/View/Agent/agent.html";
                });
            }
        });
    });
});