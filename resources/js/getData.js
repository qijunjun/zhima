/*
 * Created by apple on 16/4/21.
 * Edited by Carbon on 2016/04/25.
 */
var Data = function () {
    if (this instanceof Data) {
        this.input = $("input");
        this.data = {};
    } else {
        return new Data();
    }
};
Data.prototype = {
    getInput: function (ischeck) {
        if (ischeck != false && !ischeck) ischeck = true;
        var name = [], data = [];
        for (var i = 0; i < this.input.length; i ++) {
            if ($(this.input[i]).attr("type") == "checkbox") {
                continue ;
            }
            if ($(this.input[i]).attr("id") == "search") {
                continue ;
            }
            name[i] = $(this.input[i]).attr("name");
            data[i] = $(this.input[i]).val().trim();
            if (data[i] == "" && ischeck) {
                this.showError($(this.input[i]).data("name") + "不可为空");
                return false;
            }
            this.data[name[i]] = data[i];
        }
        return this.data;
    },
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
    },
    showError: function (content) {
        // alert(content);
        customPop("错误", "tip", "", "", undefined, content);
    }
};

var gotErrorMsg = function() {
    $(".popMsg").remove();
};

var inheritObject = function (o) {
    function F() {}
    F.prototype = o;
    return new F();
};
var inheritPrototype = function (superClass, subClass) {
    var p = inheritObject(superClass.prototype);
    p.constructor = subClass;
    subClass.prototype = p;
};