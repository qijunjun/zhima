<?php

/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/2
 * Time: 9:02
 */
namespace Logistics\Controller;

use Common\Controller\BaseController;
use Think\Controller;
use Logistics\API\LogisticsAPI;

class LogisticsController extends BaseController
{
    /**
     * 添加物流信息
     */
    public function add(){
        //接收数据
        $company_id = $_SESSION['user_info']['companyid'];
        $qrcodestart = I('qrcode_pack_s',null,'htmlspecialchars');
        $qrcodeend = I('qrcode_pack_e',null,'htmlspecialchars');
        $logistics = I('logistics',null,'htmlspecialchars');
        $expresslist = I('expresslist',null,'htmlspecialchars');
        $logistics_time = I('logistics_time',null,'htmlspecialchars');

        if ($qrcodestart > $qrcodeend)
        {
            echo json("002", null, "起始码不可大于结束码");

            return;
        }
        if(preg_match('/^'.$company_id.'[0]\d{9}$/',$qrcodestart) == false){
            echo json("002",null,"输入的码段有问题");
            return;
        }
        if(preg_match('/^'.$company_id.'[0]\d{9}$/',$qrcodeend) == false){
            echo json("002",null,"输入的码段有问题");
            return;
        }
        $idcollection = LogisticsAPI::checkPackCodeRepeate($qrcodestart, $qrcodeend);
        if (sizeof($idcollection) > 0)
        {
            echo json("009", $idcollection, "新增的码段与现有的码段重复");

            return;
        }

        $data = array(
            "company_id" => $company_id,
            "qrcode_pack_s" => $qrcodestart,
            "qrcode_pack_e" => $qrcodeend,
            "logistics" => $logistics,
            "expresslist" => $expresslist,
            "logistics_time" => $logistics_time
        );
        $response = LogisticsAPI::add($data);
        if ($response != false)
        {
            echo json("001", $response);
        }
    }

    /**
     * 更新物流信息
     */
    public function update(){
        $id = I('id',0,'htmlspecialchars');
        $data = array(
            "logistics" => I('logistics',null,'htmlspecialchars'),
            "expresslist" => I('expresslist',null,'htmlspecialchars'),
            "logistics_time" => I('logistics_time',null,'htmlspecialchars')
        );
        $response = LogisticsAPI::update($id,$data);
        if($response != false){
            echo json("001",$response);
        }
    }

    /**
     * 删除物流信息
     */
    public function del(){
        $id = I('id',0,'htmlspecialchars');
        $response = LogisticsAPI::del($id);
        if($response){
            echo json("001",$response);
        }elseif($response == 0){
            echo json("009",null,'没有删除任何数据');
        }else{
            echo json("002");
        }
    }

    /**
     * 查找
     */
    public function find(){
        $id = I('id',0,'htmlspecialchars');
        $result = LogisticsAPI::find($id);
        if($result){
            echo json("001",$result);
        }else{
            echo json("002");
        }
    }

    /**
     * 显示数据
     */
    public function index(){
        $result = LogisticsAPI::listLogistics();
        if($result){
            echo json("001",$result);
        }
    }
}