## 这是二维码管理模块，杨斌负责开发

[md](http://blog.csdn.net/skykingf/article/details/45536231)  

[thinphp函数](http://www.thinkphp.cn/info/309.html)  

[thinphpDocument](http://document.thinkphp.cn/manual_3_2/preface.html) 
 
[json在线解析](http://www.bejson.com/)

[json视图解析](http://www.bejson.com/jsonviewernew/)

[前台首页](http://zhima.zmade.cn/Application/Common/Tpl/index.html)
 
* 

---

* ApiModel Mongo Package

1.initialization `ex: $model = new \Code\Model\ApiModel(string collection); collection is mongo table.`
                        `$model->add().....`

2.use Like Thinkphp Operation

//phpMongo 接口说明
//初始化一个mongo的collection，如果不存在则新建一个collection
$model = new \Code\Model\ApiModel('testcollection');
//字段条件，与thinkphp一致
$modeMap = array(
    '_id' => 1,
);
//数据
$data['test'] = 'test';
//添加单个数据，与thinkphp一致
$result = $model->add($data);
//按照字段条件查询，与thinkphp一致
$result = $model->where($modeMap)->select();
//按照条件保存更新，与thinkphp一致
$result = $model->where($modeMap)->save($data);
//按照条件删除，与thinkphp一致
$result = $model->where($modeMap)->delete();

......

---

* //ini_set('mongo.native_long', 1);

---

* 获取生码日志

1.Post address : http://zhima.serzc.com/Code/api/info

2.Return Json Value

`
"code": "001",
    "message": "Success",
    "result": {
        "list": [
            {
                "id": "82",
                "companyid": "1001",
                "operatorid": "2",
                "qrcodetypeid": "0",
                "operate_time": "1461674670",
                "qrcode_s": "10010000000001",
                "qrcode_e": "10010000000005",
                "flag": "0",
                "qrcode_counts": "5",
                "qrcode_left": "45",
                "remark": null
            }
        ],
        "coderemainder": "20"
    }
`

* 箱码操作

1.Post address : http://zhima.serzc.com/Code/api/add/codenum/10/codetype/0

2.Return Json Value

`
"code": "001",
    "message": "Success",
    "result": {
        "info": "Congratulations,successful completion!",
        "qrcodetype": "0",
        "num": 10,
        "gtime": 0,
        "mtime": 0.035,
        "mysql": "69",
        "mongo": {
            "connectionId": 6905,
            "n": 0,
            "syncMillis": 0,
            "writtenTo": null,
            "err": null,
            "ok": 1
        }
    }
`

* 质量码操作

1.Post address : http://zhima.serzc.com/Code/api/add/codenum/10/codetype/1

2.Return Json Value

`
"code": "001",
    "message": "Success",
    "result": {
        "info": "Congratulations,successful completion!",
        "qrcodetype": "1",
        "num": 10,
        "gtime": 0,
        "mtime": 0.035,
        "mysql": "70",
        "mongo": {
            "connectionId": 6906,
            "n": 0,
            "syncMillis": 0,
            "writtenTo": null,
            "err": null,
            "ok": 1
        }
    }
`

* 箱码-质量码操作

1.Post address : http://zhima.serzc.com/Code/api/add/codenum/10/codetype/2/codenumtwo/2

2.Return Json Value

`
 "code": "001",
    "message": "Success",
    "result": {
        "xcode": {
            "info": "Congratulations,successful completion!",
            "qrcodetype": 0,
            "num": 10,
            "gtime": 0,
            "mtime": 0.035,
            "mysql": "80",
            "mongo": {
                "connectionId": 6931,
                "n": 0,
                "syncMillis": 0,
                "writtenTo": null,
                "err": null,
                "ok": 1
            }
        },
        "zcode": {
            "info": "Congratulations,successful completion!",
            "qrcodetype": 1,
            "num": 20,
            "gtime": 0,
            "mtime": 0.035,
            "mysql": "81",
            "mongo": {
                "connectionId": 6931,
                "n": 0,
                "syncMillis": 0,
                "writtenTo": null,
                "err": null,
                "ok": 1
            }
        },
        "coderelation": "261"
    }
`

* 导出操作

1.Post address : http://zhima.serzc.com/Code/api/exp/id/x

2.Return Json Value

* 企业增码操作

1.Post address : http://zhima.zmade.cn/code/api/log/operation/add/companyid/1001/userid/2/num/10

2.Return Json Value

3.需要post的参数有：operation,companyid,userid,num

* 企业增码记录查看操作

1.Post address : http://zhima.zmade.cn/code/api/log/operation/list/companyid/1001/userid/2

2.Return Json Value

3.需要的参数：operation,companyid或者userid

* 导出操作

1.Post address : http://zhima.zmade.cn/Code/api/exp/id/57/num/80

2.Return Json Value

* 扫码日志记录

1.Post address : http://zhima.zmade.cn/Code/api/scanlog/c/D2BA1B22205A4D2D/b/10010000000001

2.Return Json Value

* 扫码信息记录

1.Post address : http://zhima.zmade.cn/code/api/scaninfo/c/D2BA1B22205A4D2D/b/10010000000001

2.Return Json Value

* 假码获取

1.Post address : http://zhima.zmade.cn/Code/api/fake

2.Return Json Value

* 真伪验证

1.Post address : http://zhima.zmade.cn/code/api/check/c/D2BA1B22205A4D2D/b/10010000000001

2.Return Json Value

* 防伪配置

1.Post address : http://zhima.zmade.cn/code/api/updateSetting/max_scan_count/6/scan_tips_text/123/fake_tips_text/1234
                 http://zhima.zmade.cn/code/api/scanSetting

2.Return Json Value

