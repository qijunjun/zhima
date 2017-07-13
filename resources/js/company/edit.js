/**
 * Created by 123 on 2016/7/12.
 */
$(document).ready(function () {
    var id = getParameterByName("id");
    var grid= "grid0";
    var index = 0;
    var procince_id ="procince1";
    var city_id = "city1";
    var district_id = "district1";
    var procince = "省";
    var procince1 = "请选择省";
    var city = "市";
    var city1 = "请选择市";
    var district = "区/县";
    var district1 = "请选择区/县";
    var del_id= "delete1";
    var domProvince, domCity, domDistrict,domProvince1, domCity1, domDistrict1,domProvince2, domCity2, domDistrict2;
    methods.ajax({
        url: "/Company/Agent/edit",
        data: {id: id},
        callback: function (data) {
            setData(data.result);
            data = data.result.agent_area;
            var n= data.length;
            var area = new Area(JSON.parse(localStorage.getItem("area")));
            domProvince = $("#procince0");
            domCity = $("#city0");
            domDistrict = $("#district0");
            domProvince.html(area.getProvinceOption());
            domProvince.change(function () {
                domCity.html(area.getCityOption(domProvince.val()));
                domDistrict.empty();
                domDistrict.val("");
            });
            domCity.change(function () {
                domDistrict.html(area.getDistrictOption(domProvince.val(), domCity.val()));
            });
            domProvince.html(area.getProvinceOption());
            area.setValue(domProvince, data[0].province);
            domCity.html(area.getCityOption(domProvince.val()));
            area.setValue(domCity, data[0].city);
            domDistrict.html(area.getDistrictOption(domProvince.val(), domCity.val()));
            area.setValue(domDistrict, data[0].district);
            for(var i=1;i<data.length;i++){
                var div =  $('<div>',{
                    id : grid,
                    "class" : "grid",
                    html:"<div>"+"<label>"+procince+"</label>"+"<select class='procince' name="+procince_id+" id='"+procince_id+"'>"+"<option value='0'>"+procince1+"</option>"+"</select>"+"</div>"+"<div>"+"<label for="+city_id+">"+city+"</label>"+"<select name="+city_id+" id="+city_id+" class='city'>"+"<option value='0'>"+city1+"</option>"+"</select>"+"</div>"+"<div>"+"<label for="+district_id+">"+district+"</label>"+"<select name="+district_id+" id="+district_id+" class='district'>"+"<option value='0'>"+district1+"</option>"+"</select>"+"</div>"+"<button class='delete' id="+del_id+" >" + methods.getSvg() + "</button>"
                });
                $("#list").append(div);
                var arr =["grid","procince","city","district","delete"];
                for(var j=0;j<5;j++){
                    $("."+arr[j]).attr("id",function(i){
                        return arr[j]+i;
                    });
                }
                domProvince1 = $("#procince"+i);
                domCity1 = $("#city"+i);
                domDistrict1 = $("#district"+i);
                domProvince1.html(area.getProvinceOption());
                domProvince1.change(function () {
                    domCity1.html(area.getCityOption(domProvince1.val()));
                    domDistrict1.empty();
                    domDistrict1.val("");
                });
                domCity1.change(function () {
                    domDistrict1.html(area.getDistrictOption(domProvince1.val(), domCity1.val()));
                });
                domProvince1.html(area.getProvinceOption());
                area.setValue(domProvince1, data[i].province);
                domCity1.html(area.getCityOption(domProvince1.val()));
                area.setValue(domCity1, data[i].city);
                domDistrict1.html(area.getDistrictOption(domProvince1.val(), domCity1.val()));
                area.setValue(domDistrict1, data[i].district);
            }
            $("#add").click(function(){
                var div =  $('<div>',{
                    id : grid,
                    "class" : "grid",
                    html:"<div>"+"<label>"+procince+"</label>"+"<select class='procince' name="+procince_id+" id='"+procince_id+"'>"+"<option value='0'>"+procince1+"</option>"+"</select>"+"</div>"+"<div>"+"<label for="+city_id+">"+city+"</label>"+"<select name="+city_id+" id="+city_id+" class='city'>"+"<option value='0'>"+city1+"</option>"+"</select>"+"</div>"+"<div>"+"<label for="+district_id+">"+district+"</label>"+"<select name="+district_id+" id="+district_id+" class='district'>"+"<option value='0'>"+district1+"</option>"+"</select>"+"</div>"+"<button class='delete' id="+del_id+" >" + methods.getSvg() + "</button>"
                });
                $("#list").append(div);
                index++;
                var arr =["grid","procince","city","district","delete"];
                for(var j=0;j<5;j++){
                    $("."+arr[j]).attr("id",function(index){
                        return arr[j]+index;
                    });
                }
                domProvince2 = $("#procince"+n);
                domCity2 = $("#city"+n);
                domDistrict2 = $("#district"+n);
                var area = new Area(JSON.parse(localStorage.getItem("area")));
                domProvince2.html(area.getProvinceOption());
                domProvince2.change(function () {
                    domCity2.html(area.getCityOption(domProvince2.val()));
                    domDistrict2.empty();
                    domDistrict2.val("");
                });
                domCity2.change(function () {
                    domDistrict2.html(area.getDistrictOption(domProvince2.val(), domCity2.val()));
                });
                n++;
            });
            $("#list").on("click",".delete",function(){
                var self = $(this);
                var grid = self.closest(".grid");
                grid.index()!=2 && grid.remove();
                var arr =["grid","procince","city","district","delete"];
                for(var j=0;j<5;j++){
                    $("."+arr[j]).prop("id",function(index){
                        return arr[j]+index;
                    });
                }
                n--;
            });
        }
    });
    methods.addEvent(methods.getId('submit'), 'click', function () {
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        var agent_area = [];
        var grids = $(".list >.grid");
        var len = grids.length;
        for(var i=0;i<len;i++){
            agent_area[i] = {};
            agent_area[i].province = $("#procince"+i).val();
            agent_area[i].city = $("#city"+i).val();
            agent_area[i].district = $("#district"+i).val();
        }
        data.common({id: id,'agent_area':agent_area});
        console.log(data.data);
        methods.ajax({
            url: '/Company/Agent/update',
            data: data.data,
            callback: function () {
                new Inform({title: '通知', content: '修改成功!'}).alert(function () {
                    window.location.href = "/Application/Company/View/Agent/agent.html";
                });
            }
        });
    });
});

var getParameterByName = function (name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
};
