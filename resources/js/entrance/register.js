/*
	Inherited from code by apple on 16/4/21.
	Created by James "Carbon" leon Neo on 2016/04/25.
 */
$(document).ready(function ()
{
	function checkInput()
	{
        var inputData = new Data().getInput();
        if (!inputData)
		{
            return;
        }
		if(inputData.username.length <4 ||inputData.username.length> 16){
			//长度不能小于4，大于16
			new Inform({title: '通知', content: '用户名长度为4-16个字符!<br><span class="red">请检查</span>'}).alert();
			return;
		}
		if (inputData.password != inputData.reconfirm)
		{
			// alert("两次输入密码不一致！");
			new Inform({title: '通知', content: '两次密码不一致!<span class="red">请检查</span>'}).alert();
			return;
		}
		//if (!(/^\d{11}$/.test(inputData.phone)))
		if(!(/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/.test(inputData.phone)))
		{
			// alert("请输入正确的手机号码！");
			new Inform({title: '通知', content: '请输入正确的手机号!'}).alert();
			return;
		}
		register(inputData)
	}
	function register(inputData)
	{
		$.ajax({
			method: "post",
			data: inputData,
			dataType: "json",
			success: function (data)
			{
				console.log(data);
				if(data.code == "001") {
					// customPop("提示", "tip");
					new Inform({title: '通知', content: '注册成功'}).alert(function () {
						window.location.href = "/login.php";
					});
				} else {
					// customPop("错误", "tip", "", "", undefined, data.msg);
					new Inform({title: '通知', content: data.msg}).alert();
					throw new Error(data.code + "," + data.msg);
				}
			},
			error: function (err)
			{
				new Inform({title: '通知', content: '网络错误!'}).alert();
				console.error(err);
			}
		});
    }
    $("#submit").click(checkInput);
});
