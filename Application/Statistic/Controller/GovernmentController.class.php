<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/8/24
 * Time: 17:54
 */

namespace Statistic\Controller;


use Common\Controller\BaseController;
use Statistic\API\GovernmentAPI;

class GovernmentController extends BaseController
{
    /**
     * 政府管辖企业的产品数量2016827
     */
    public function productCounts(){
        $result = GovernmentAPI::productCounts();
        json("001",$result);
    }

    /**
     * 政府管辖企业的用码统计（购码/已生成/关联）2016827
     */
    public function qrcodeCounts(){
        $result = GovernmentAPI::qrcodeCounts();
        json("001",$result);
    }
    /**
     * 政府管辖企业的扫描数量2016827
     */
    public function qrcodeScanedCounts(){
        $result = GovernmentAPI::qrcodeScanedCounts();
        json("001",$result);
    }

    /**
     * 政府管辖企业的举报数量2016827
     */
    public function qrcodeTipoffCounts(){
        $result = GovernmentAPI::qrcodeTipoffCounts();
        json("001",$result);
    }

    /**
     * 政府管辖企业出入库统计2016827
     */
    public function qrcodeCheck(){
        $result = GovernmentAPI::qrcodeCheck();
        json("001",$result);
    }

    /**
     * 企业出入库每天统计2016827
     */
    public function check(){
        $companyid = I('id');
        $result = GovernmentAPI::check($companyid);
        if($result){
            json("001",$result,'success');
        }else{
            json("001",null,'没有出入库记录');
        }
    }

    /**
     * 企业的统计信息2016826
     */
    public function companyInfo(){
        $companyid = I('id');
        $result = GovernmentAPI::companyInfo($companyid);
        if($result){
            json("001",$result,'success');
        }else{
            json("001");
        }
    }

    /**
     * 出库的经纬度信息2016826
     */
    public function checkoutArea(){
        $companyid = I('id');
        $result = GovernmentAPI::checkoutArea($companyid);

        if($result){
            json("001",$result,'success');
        }else{
            json("001");
        }
    }
}