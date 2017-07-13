<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/20
 * Time: 14:53
 */

namespace Government\Controller;

use Common\Controller\BaseController;
use Government\API\SuperviseAPI;
class SuperviseController extends BaseController
{
    //拿到登录政府的id
    private $_governmentid;
    public function _initialize(){
        parent::_initialize();
        $this->_governmentid = $_SESSION['user_info']['id'];
//        $this->_governmentid = 1;
    }
    /**
     * 获取企业名称2016820
     */
    public function getCompanyname(){
        $result = SuperviseAPI::getCompanyname($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的所有企业的基本信息2016820
     */
    public function showCompanyInfo(){
        $result = SuperviseAPI::showCompanyInfo($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的企业的资质信息2016820
     */
    public function showAptitude(){
        $result = SuperviseAPI::showAptitude($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的企业的产品信息2016820
     */
    public function showProduct(){
        $result = SuperviseAPI::showProduct($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的企业的产品的生产过程信息2016820
     */
    public function showProcess(){
        $result = SuperviseAPI::showProcess($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的企业的产品检测信息2016820
     */
    public function showCheckItem(){
        $result = SuperviseAPI::showCheckItem($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 政府监管的企业的产品的召回信息2016820
     */
    public function showRecall(){
        $result = SuperviseAPI::showRecall($this->_governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 获取指定公司的资质信息2016821
     */
    public function getComAptitude(){
        $companyid = I('companyid');
        $re = SuperviseAPI::checkComAndGov($this->_governmentid,$companyid);

        if($re == false){
            json("002",null,"对不起，您无权查看该企业信息！");
            return ;
        }
//        $result = R("Company/Aptitude/showAll",array($companyid),'API');
        $result = SuperviseAPI::getComAptitude($companyid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     *获取指定公司的产品信息2016821
     */
    public function getComProduct(){
        $companyid = I('companyid');
        $re = SuperviseAPI::checkComAndGov($this->_governmentid,$companyid);

        if($re == false){
            json("002",null,"对不起，您无权查看该企业信息！");
            return ;
        }
        $result = SuperviseAPI::getComProduct($companyid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 获取指定公司的生产过程信息2016821
     */
    public function getComProcess(){
        $companyid = I('companyid');
        $re = SuperviseAPI::checkComAndGov($this->_governmentid,$companyid);

        if($re == false){
            json("002",null,"对不起，您无权查看该企业信息！");
            return ;
        }
        $result = SuperviseAPI::getComProcess($companyid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 获取指定公司的检测记录2016821
     */
    public function getComCheckItem(){
        $companyid = I('companyid');
        $re = SuperviseAPI::checkComAndGov($this->_governmentid,$companyid);

        if($re == false){
            json("002",null,"对不起，您无权查看该企业信息！");
            return ;
        }
//        $result = R("Process/CheckItem/showList",array($companyid),'API');
        $result = SuperviseAPI::getComCheckItem($companyid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }

    /**
     * 获取指定公司的召回记录2016821
     */
    public function getComRecall(){
        $companyid = I('companyid');
        $re = SuperviseAPI::checkComAndGov($this->_governmentid,$companyid);

        if($re == false){
            json("002",null,"对不起，您无权查看该企业信息！");
            return ;
        }
//        $result = R("Recall/Recall/listRecall",array($companyid),'API');
        $result = SuperviseAPI::getComRecall($companyid);

        if($result){
            json("001",$result,"success");
        }else{
            json("002");
        }
    }
}