/**
 * Created by apple on 16/5/14.
 */
var Area = function (area) {
    if (this instanceof Area) {
        this.area = area;
    } else {
        return new Area(area);
    }
};

Area.prototype = {
    setValue: function (domTarget, val) {
        domTarget ? domTarget.val(val) : null;
    },
    getProvince: function () {
        return this.area;
    },
    getCity: function (province) {
        for (var i in this.area) {
            if (this.area[i].id == province)
                break;
        }
        return {number: i, cities: this.area[i].cities};
    },
    getDistrict: function (province, city) {
        for (var i in this.area[this.getCity(province).number].cities) {
            if (this.area[this.getCity(province).number].cities[i].id == city) {
                break;
            }
        }
        return this.area[this.getCity(province).number].cities[i].districts;
    },
    getProvinceOption: function () {
        var province = this.getProvince();
        var provinceOption = "<option selected='selected' disabled='disabled'>请选择省</option>";
        for (var i = 0; i < province.length; i ++) {
            provinceOption += "<option value='" + province[i].id + "'>" + province[i].name + "</option>"
        }
        return provinceOption;
    },
    getCityOption: function (province) {
        if (province == null || province == "") {
            return ;
        }
        var city = this.getCity(province).cities;
        var cityOption = "<option selected='selected' disabled='disabled'>请选择市</option>";
        for (var i = 0; i < city.length; i ++) {
            cityOption += "<option value='" + city[i].id + "'>" + city[i].name + "</option>";
        }
        return cityOption;
    },
    getDistrictOption: function (province, city) {
        if (province == null || province == "" || city == null || city == "") {
            return ;
        }
        var district = this.getDistrict(province, city) || [];
        var districtOption = "<option selected='selected' disabled='disabled'>请选择区/县</option>";
        for (var i = 0; i < district.length; i ++) {
            districtOption += "<option value='" + district[i].id + "'>" + district[i].name + "</option>";
        }
        return districtOption;
    }
};