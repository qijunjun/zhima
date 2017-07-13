/**
 * 获取页面的数据类
 * @returns {Data} 如果不是new生成的,则需要return一个new Data()
 * @constructor 指向自己
 */
var Data = function () {
    if (this instanceof Data) {
        this.constructor = this;
        this.input = $('input');
        this.data = {};
    } else {
        return new Data();
    }
};
Data.prototype = {
    /**
     * 获取input的数据
     * 如果有checkbox则会把checkbox保存在this.data.checkbox里,可以使用common获取,使用后需要用delete删除
     * @returns {*, boolean, object} boolean是false表示内容不完整
     */
    getInput: function () {
        var name = [], data = [];
        for (var i = 0; i < this.input.length; i ++) {
            var _input = $(this.input[i]);
            if (_input.attr("type") == "checkbox") {
                if (!_input.is(":checked")) continue ;
                if (!this.data.checkBox) this.data.checkBox = "";
                this.data.checkBox += _input.val() + ",";
            } else if (_input.attr("id") != "search") {
                name[i] = _input.attr("name");
                data[i] = _input.val().trim() || "";
                if ((_input.data('noCheck') || _input.data('nocheck')) != "noCheck" && data[i] == "") {
                    return false;
                }
                this.data[name[i]] = data[i];
            }
        }
        return this.data;
    },

    /**
     * @param_1 string 获取data的[arg[0]]
     * @param_1 object 将object的内容填充到this.data
     * @param_2 string value 将this.data[arg[0]]赋值为value
     * @returns {*}
     */
    common: function () {
        var i;
        if (arguments.length == 1 && typeof arguments[0] == "string") {
            return this.data[arguments[0]] || "undefined " + arguments[0] + " prop";
        } else if (arguments.length == 2 && typeof arguments[0] == "string") {
            this.data[arguments[0]] = arguments[1];
        } else if(arguments.length == 1 && typeof arguments[0] == "object") {
            for (i in arguments[0]) {
                this.data[i] = arguments[0][i];
            }
        }
        return this;
    }
};