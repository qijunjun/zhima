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
		login(inputData)
	}
	function login(inputData)
	{
		$.ajax({
			method: "post",
			data: inputData,
			dataType: "json",
			success: function (data)
			{
				console.log(data);
				if(data.code == "001") {
					window.location.href = "/";
				} else {
					// new Inform({title: '通知', content: data.message || data.msg || "错误"}).alert();
					customPop("错误", "tip", "", "", undefined, "账号密码错误!");
				}
			},
			error: function (err)
			{
				customPop("错误", "tip", "", "", undefined, "账号密码错误!");
				console.error(err);
			}
		});
    }
	$("#submit").click(function(){
		var bool = $('.popMsg').length;
		if(bool){
			gotErrorMsg();
		}else{
			checkInput();
		}
	});

	$("#password").bind("keypress", function (e) {
		if (e.keyCode == 13) {
			var bool = $('.popMsg').length;
			if(bool){
				gotErrorMsg();
			}else{
				checkInput();
			}
		}
	});
});
