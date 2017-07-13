<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/6/26
 * Time: 上午1:04
 */
namespace Process\Controller;

use Common\Controller\BaseController;
use Process\API\ProcessAPI;

class ConfigController extends BaseController
{
    /*
   * 添加生产过程环节
   */
    public function addFunction(){
        $data = [
            'companyid' => $_SESSION["user_info"]["companyid"],
            'function_name'=>I('request.function_name', null),
            'function_image' => I('request.function_image', null),
            'synch_time' =>  date('Y-m-d H:i:s',time())
        ];
        if(($data['function_name']==null)||($data['function_image']==null)){
            json('002',null,'请检查输入是否正确');
            return;
        }

        $result=ProcessAPI::addFunction($data);
        if($result){
            json('001',$result);

        }else{
            json('002',$result,'添加生产过程环节失败!');
            return;
        }
    }

    /*
     * $id对应base_product_function_operates中的functionid
     */
    public function delFunction($id){
        
        $map=array(
            'functionid'=>$id
        );
        $result=ProcessAPI::delFunction($map);
        if($result){
            json('001',$result);

        }else{
            json('002','','删除环节失败');  //没有生产环节返回空数组
            return;
        }
    }
    /*
     * 编辑生产环节操作
     */
    public function editFunction($id){
        $map=array(
            'functionid'=>$id
        );
        $result=ProcessAPI::editFunction($map);
        if($result){
            json('001',$result);

        }else{
            json('002','','无法获取相关信息');  //没有生产环节返回空数组
            return;
        }
    }
    public function updateFunction(){
        $data = [
            'functionid'=>I('request.functionid'),
            'companyid' => $_SESSION["user_info"]["companyid"],
            'function_name'=>I('request.function_name', null),
            'function_image' => I('request.function_image', null),
            'synch_time' =>  date('Y-m-d H:i:s',time())
        ];
        if(!is_numeric($data['functionid']))
        {
            json('002',null,'无法获取正确的参数');
            return;
        }
        if(($data['function_name']==null)||($data['function_image']==null)){
            json('002',null,'请检查输入是否正确');
            return;
        }

        $result=ProcessAPI::updateFunction($data);
        if($result){
            json('001',$result);

        }else{
            json('002',$result,'添加生产过程环节失败!');
            return;
        }
    }
    public function listFunction()
    {
        $companyid= $_SESSION["user_info"]["companyid"];
        $map=array(
            'companyid'=>$companyid
        );
        $result=ProcessAPI::listFunction($map);
        if($result){
            json('001',$result);

        }else{
            json('001',array());  //没有生产环节返回空数组
            return;
        }
    }
    public function addCorrFunction()
    {
        $productid=I('request.productid',null);
        $funtions=I('request.functions',null);
        if(($productid==null)||($funtions==null))
        {
            json('002','','请检查输入');
            return;
        }
        //$functionid=implode(",",$funtions);
        //$companyid=$_SESSION["user_info"]["companyid"];
        $funtions=explode(",",$funtions);
        $result=ProcessAPI::addCorrFunction($productid,$funtions);
        if($result){
            json('001',$result);

        }else{
            json('002','','添加环节失败');  //没有生产环节返回空数组
            return;
        }
    }
    /**
     * 显示产品和绑定的生产环节
     */
    public function showCorrFunction(){
        $result = ProcessAPI::showCorrFunction();
        if($result){
            json("001",$result);
        }
    }
}
