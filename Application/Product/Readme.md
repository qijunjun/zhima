## 这是产品模块，杨斌负责开发  

[md](http://blog.csdn.net/skykingf/article/details/45536231)  

[thinphp函数](http://www.thinkphp.cn/info/309.html)  

[thinphpDocument](http://document.thinkphp.cn/manual_3_2/preface.html) 
 
[json在线解析](http://www.bejson.com/)  

---  

* 产品基本信息获取方式
1. Post地址：http://zhima.serzc.com/Product/api/info/id/1,这里的数字代表产品id，若为空，则为请求所有产品列表

* 产品新增
1. Post地址：http://zhima.serzc.com/Product/api/add/productname/xxx/productinfo/xxx/......,这里的xxx为传递值，xxx前为字段名

* 产品修改信息页
1. Post地址：http://zhima.serzc.com/Product/api/edit/id/1

* 产品更新
1. Post地址：http://zhima.serzc.com/Product/api/update/id/4/productname/test1/productinfo/test1/companyid/1

* 产品删除
1. Post地址：http://zhima.serzc.com/Product/api/del/id/4/


* 遗留问题
1. 图片js上传更新问题