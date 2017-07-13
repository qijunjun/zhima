<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/13
 * Time: 9:14
 */

namespace Admin\Controller;


use Common\Controller\BaseController;
use Admin\API\GovernmentAPI;

class GovernmentController extends BaseController
{
    /**
     * 列出政府信息2016813
     */
    public function showGovernmentInfo(){
        $result = GovernmentAPI::showGovernmentInfo();

        if($result){
            json("001",$result,"查询政府信息成功");
        }else{
            json("002");
        }
    }

    /**
     * 列出企业信息2016813
     */
    public function showCompanyInfo(){
        $result = GovernmentAPI::showCompanyInfo();

        if($result){
            json("001",$result,"查询企业信息成功");
        }else{
            json("002");
        }
    }

    /**
     * 添加政府与企业关联信息2016813
     */
    public function addCorrGovernment_Company(){
        $governmentid = I("government_id");
        $companyid    = I("company_id");

        $result = GovernmentAPI::addCorrGovernment_Company($governmentid,$companyid);

        if($result){
            json("001",$result,"添加政府与企业关联成功");
        }else{
            json("002",null,"添加关联信息失败");
        }
    }

    /**
     * 政府关联企业名称201683
     */
    public function showCorrCompany(){
        $governmentid = I("government_id");

        $result = GovernmentAPI::showCorrCompany($governmentid);

        if($result){
            json("001",$result,"成功显示关联企业名称");
        }else{
            json("002",null,"显示关联企业名称失败");
        }
    }

    /**
     * 关联政府名称2016813
     */
    public function showCorrGovernment(){
        $governmentid = I("government_id");
        $result = GovernmentAPI::showCorrGovernment($governmentid);

        if($result){

            json("001",$result,"成功显示关联政府");
        }else{
            json("002",null,"显示关联政府失败");
        }
    }

    /**
     * 列出合并关联的企业和政府信息2016813
     */
    public function combineGovernmentCompany(){
        $governmentid = I("government_id",1);
        $government = GovernmentAPI::showCorrGovernment($governmentid);
        $company = GovernmentAPI::showCorrCompany($governmentid);

        for($i=0;$i<count($company);$i++){
            $result[] =  array_merge($company[$i],$government);
        }

        if($result){
            json("001",$result,"success");
        }else{
            json("002",null,"显示失败");
        }
    }

    /**
     * 删除关联列表中的数据2016813
     */
    public function del(){
        $id = I('id');
        $result = GovernmentAPI::del($id);

        if($result){
            json("001",$result,"success");
        }elseif($result == 0){
            json("009",null,"没有删除数据");
        }else{
            json("002",null,"删除失败");
        }
    }

    /**
     * 显示列表数据2016813
     * 
     */
    public function listAllCorrInfo(){
        $result = GovernmentAPI::listAllCorrInfo();

        if($result){
            json("001",$result,"success");
        }else{
            json("002",null,"显示失败");
        }
    }
}