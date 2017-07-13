/**
 * Created by apple on 16/4/21.
 */
$(document).ready(function () {
    $("#submit").click(function () {
        var data = new Register();
        if (!data.getInput() || !data.checkPassword()) {
            return null;
        }
        // $.ajax({
        //     url: "",
        //     method: "post",
        //     datatype: "json",
        //     data: data.getInput(),
        //     success: function (data) {
        //         console.log(data);
        //     },
        //     error: function (err) {
        //         console.error(err);
        //     }
        // });
    });
});

var Register = function () {
    Data.call(this);
};
inheritPrototype(Data, Register);
Register.prototype.checkPassword = function () {
    if (this.getInput().password != this.getInput().rePassword) {
        alert("两次密码不一致!");
    }
    return false;
};