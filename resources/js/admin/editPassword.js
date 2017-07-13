/**
 * Created by GUOQH on 2016/6/16.
 */
$(document).ready(function(){
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        var oldPassword = methods.getId('oldPassword').value;
        var newPassword = methods.getId('newPassword').value;
        var confirmPassword = methods.getId('confirmPassword').value;
        if(newPassword === confirmPassword){
            methods.ajax({
                url : "/Home/API/repassword",
                data : {
                    oldPassword:oldPassword,
                    newPassword:newPassword
                },
                callback : function(){
                    new Inform({title : '通知',content : '修改成功!'}).alert(function(){
                        window.location.href = "/Application/Welcome/View/Index/welcome.html";
                    })
                }
            })
        }else{
            new Inform({title : '通知',content : '两次新密码输入的不一致!'}).alert();
        }

    });
});