/**
 * Created by user on 2016/6/16.
 */
$(document).ready(function(){
    alarmValue.onkeydown = function (e)
    {
        var keyCode = e.keyCode;
        if (keyCode == 69)
        {
            return false;
        }
    };
    methods.ajax({
        url : "/code/api/scanSetting",
        callback : function(data){
            data = data.result;
            methods.getId('alarmValue').value = data.max_scan_count;
            methods.getId('alarmWord').value = data.scan_tips_text;
            methods.getId('fakerContent').value = data.fake_tips_text;
        }
    });
    methods.addEvent(document.getElementById('submit'),'click',function(){
        var data = new Data();
        if (!data.getInput()) {
            new Inform({title: '通知', content: '数据项不完整<br /><span class="red">请检查</span>'}).alert();
            return ;
        }
        var alarmValue = methods.getId('alarmValue').value;
        var alarmWord = methods.getId('alarmWord').value;
        var fakerContent = methods.getId('fakerContent').value;
        methods.ajax({
            url : "/code/api/updateSetting",
            data : {
                max_scan_count : alarmValue,
                scan_tips_text : alarmWord,
                fake_tips_text : fakerContent
            },
            callback : function(){
                new Inform({title : '通知',content : '修改成功!'}).alert(function(){
                    window.location.href = "/Application/Faker/View/Index/fakerSetting.html";
                })
            }
        })
    });
});