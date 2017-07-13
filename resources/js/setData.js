/**
 * Created by apple on 16/5/3.
 */
var setData = function (data) {
    for(var i in data) {
        var _object = $("#" + i);
        if (_object == "undefined") {
            return;
        }
        _object.val(data[i]);
    }
};