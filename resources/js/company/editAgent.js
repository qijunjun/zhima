$(document).ready(function(){
    var id=window.location.href.split("?")[1].substring(3);
    var list = $("#list");
    methods.ajax({
        url:'/Company/Agent/edit',
        data: {id: id},
        callback: function (data){
            setData(data.result);
            data = data.result.agent_area;
            var area = new Area(JSON.parse(localStorage.getItem("area")));
            var str = "";
            for(i=0;i<data.length;i++){
                str += "<div class='grid' id='grid"+i+"'><div><label>省</label><select class='province' id='province"+i+"'><option value='0'>请选择省</option></select></div><div><label>市</label><select class='city' id='city"+i+"'><option value='0'>请选择市</option></select></div><div><label>区/县</label><select class='district' id='district"+i+"'><option value='0'>请选择区/县</option></select></div><button class='delete' id='delete"+i+"'>" + methods.getSvg() +"</button></div>";
            }
            $("#list").append(str);
            var grids = $(".list >.grid");
            var len = grids.length;
            for(var m =0;m<len;m++){
                var setProvince,setCity,setDistrict;
                setProvince = $("#province"+m);
                setCity = $("#city"+m);
                setDistrict = $("#district"+m);
                setProvince.html(area.getProvinceOption());
                area.setValue(setProvince, data[m].province);
                setCity.html(area.getCityOption(setProvince.val()));
                area.setValue(setCity, data[m].city);
                setDistrict.html(area.getDistrictOption(setProvince.val(), setCity.val()));
                area.setValue(setDistrict, data[m].district);
            }
            list.delegate('.province', 'change', function () {
                var city = $(this).parent().next().children('select');
                var district = city.parent().next().children('select');
                district.html('<option value="-1" disabled="disabled" selected="selected">请选择</option>').val('-1');
                city.html(area.getCityOption($(this).val()));
            });

            list.delegate('.city', 'change', function () {
                var district = $(this).parent().next().children('select');
                district.html(area.getDistrictOption($(this).parent().prev().children('select').val(), $(this).val()));
            });
        }
    });
    $("#add").click(function(){
        var grids = $(".list >.grid");
        var addlen = grids.length+1;
        addstr = "<div class='grid' id='grid"+addlen+"'><div><label>省</label><select class='province' id='province"+addlen+"'><option value='0'>请选择省</option></select></div><div><label>市</label><select class='city addcity' id='city"+addlen+"'><option value='0'>请选择市</option></select></div><div><label>区/县</label><select class='district' id='district"+addlen+"'><option value='0'>请选择区/县</option></select></div><button class='delete' id='delete"+addlen+"'>" + methods.getSvg() +"</button></div>";
        $("#list").append(addstr);
        var Province,City,District;
        Province = $("#province"+addlen);
        City = $("#city"+addlen);
        District = $("#district"+addlen);
        var area = new Area(JSON.parse(localStorage.getItem("area")));
        Province.html(area.getProvinceOption());
        Province.change(function () {
            City.html(area.getCityOption(Province.val()));
            District.empty();
            District.val("请选择");
        });
        City.change(function () {
            District.html(area.getDistrictOption(Province.val(), City.val()));
        });
        var arr =["grid","province","city","district","delete"];
        for(var j=0;j<5;j++){
            $("."+arr[j]).attr("id",function(index){
                return arr[j]+index;
            });
        }
    });
    $("#list").on("click",".delete",function(){
        var self = $(this);
        var grid = self.closest(".grid");
        grid.index()!=2 && grid.remove();
        var grids = $(".list >.grid");
        addlen = grids.length-1;
        var arr =["grid","province","city","district","delete"];
        for(var j=0;j<5;j++){
            $("."+arr[j]).prop("id",function(index){
                return arr[j]+index;
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
        var submitlen = grids.length;
        for(var i=0;i<submitlen;i++){
            agent_area[i] = {};
            agent_area[i].province = $("#province"+i).val();
            agent_area[i].city = $("#city"+i).val();
            agent_area[i].district = $("#district"+i).val();
        }
        data.common({id: id,'agent_area':agent_area});
        if(submitlen>5){
            new Inform({title: '通知', content: '代理区域不能超过5个<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
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