<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/17
 * Time: 13:59
 */

namespace Process\Controller;

use Process\API\CheckItemAPI;
use Product\API\Correlation;
use Common\Controller\BaseController;
class CheckItemController extends BaseController
{
    /**
     * 新增检测记录2016818
     */
    public function addCheckItem(){
        $companyid = $_SESSION['user_info']['companyid'];
        $data = [
            'inspectionname' => I('checkName'),//检验名称
            'inspectionitem' => I('checkItem'),//检测项
            'create_time' => I('checkTime'),//检验时间
            'productid' => I('productid'),//产品id
            'companyid' => $companyid,
            'institution' => I('checkMechanism')
        ];
        $name = $data['inspectionname'];
        $re = CheckItemAPI::checkRepeat($name);
        if($re){
            json("002",null,"检测记录已存在");
            return;
        }
        $productid = $data['productid'];
        $r = CheckItemAPI::checkProduct($productid);
        if(!$r){
            json("002",null,"此产品不属于本公司");
            return;
        }
        $result = CheckItemAPI::addCheckItem($data);
        if($result){
            $image[0] = I('check_image',null);
            $image[1] = I('check_image1',null);
            $image[2] = I('check_image2',null);
            $image[3] = I('check_image3',null);
            $image[4] = I('check_image4',null);
            for($i=0;$i<count($image);$i++){
                $data1 = [
                    'attachment'.($i+1) => $image[$i],
                ];
                $res = CheckItemAPI::addCheckImage($productid,$data1);
            }
            if($res){
                json("001",$result,"添加成功");
            }else{
                json("002",null,"检测报告图片上传失败");
            }
        }else{
            json("002",null,"添加失败");
            return;
        }
    }

    /**
     * 更新检测信息2016818
     */
    public function update(){
        $id = I('id');
        if(!is_numeric($id)){
            json('002',null,'参数错误');
            return;
        }
        $data = [
            'inspectionname' => I('checkName'),//检验名称
            'inspectionitem' => I('checkItem'),//检测项
            'create_time' => I('checkTime'),//检验时间
            'productid' => I('productid'),
            'institution' => I('checkMechanism')
        ];
        $name = $data['inspectionname'];
//        $productid = I('productid',72);
        $productid = $data['productid'];
        $r = CheckItemAPI::checkProduct($productid);
        if(!$r){
            json("002",null,"此产品不属于本公司");
            return;
        }
        $result = CheckItemAPI::update($id,$data);

        $image[0] = I('check_image',null);
        $image[1] = I('check_image1', null);
        $image[2] = I('check_image2', null);
        $image[3] = I('check_image3', null);
        $image[4] = I('check_image4', null);
        for ($i = 0; $i < count($image); $i++) {
            $data1 = [
                'attachment' . ($i + 1) => $image[$i],
            ];
            $res = CheckItemAPI::updateCheckImage($id,$productid,$name,$data1);
        }

        if($result == 0 && $res == 0){
            json("002",null,"修改失败");
        }elseif($result > 0 || $res >0){
            json("001",null,"修改成功");
        }
    }

    /**
     * 显示信息列表2016818
     */
    public function showList(){
        $companyid = $_SESSION['user_info']['companyid'];
        $result = CheckItemAPI::showList($companyid);
        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 删除2016818
     */
    public function del(){
        $id = I('id');
        $result = CheckItemAPI::del($id);
        if($result>0){
            json("001",null,"删除成功");
        }elseif($result == 0){
            json("009",null,"没有删除数据");
        }else{
            json("002",null,"删除失败");
        }
    }

    /**
     * 编辑2016818
     */
    public function edit(){
        $id = I('id');
        $result = CheckItemAPI::edit($id);
        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }
    
    /*
     * 关联码段与检测记录
     */
    public function addCorrCheckItem()
    {
        $bStart=I('bStart');
        $bEnd=I('bEnd');
        $inspection_id=I('inspection_id');
        $companyid = $_SESSION['user_info']['companyid'];
        $productid=I('productid');
        if($bEnd<$bStart)
        {
            json('002',null,'起始码不能大于结束码');
            return;
        }
        if(!checkQCode($companyid,$bStart,$bEnd))
        {
            json('002',null,'请检查输入的码段是否正确');
            return;
        }
        $pid=Correlation::getProductbyRange($bStart,$bEnd);
        if($pid!=$productid)
        {
            json('002',null,'输入的不是当前产品关联的码段');
            return;
        }
        $idcollection = CheckItemAPI::checkItemRepeat($bStart, $bEnd);
        if (sizeof($idcollection) > 0)
        {
            json("009", $idcollection, "码段已关联检测信息");
            return;
        }
        $data = [
            'company_id' => $companyid,
            'inspection_id' => $inspection_id,
            'qrcode_range_s' => $bStart,
            'qrcode_range_e' => $bEnd,
            'create_time' => time()
        ];
        $res=CheckItemAPI::addCorrCheck($data);
        if($res)
        {
            json('001',null,'添加成功');
        }
        else
        {
            json('002',null,'添加失败');
        }
    }

    /*
     * 返回检测记录列表
     * 根据公司id
     */
    public function listCheckItems()
    {
        $companyid = $_SESSION['user_info']['companyid'];
        $response=CheckItemAPI::listCheckItems($companyid);
        if($response){
            json("001",$response);
        }else{
            json("001",null);
            return;
        }
    }

    /*
     * 返回已经关联的检测记录列表
     */
    public function listCorrAll()
    {
        $companyid = $_SESSION['user_info']['companyid'];
        $response=CheckItemAPI::listCorrAll($companyid);
        if($response){
            json("001",$response);
        }else{
            json("001",null);
            return;
        }
    }

    /*
     * 删除码段和检测记录的关联关系
     * 根据关联id
     */
    public function  delCorrCheck()
    {
        $corrid = I('id');
        $response=CheckItemAPI::delCorrCheck($corrid);
        if($response){
            json("001",$response,'删除成功');
        }else{
            json("002",null,'删除失败');
            return;
        }
    }
}