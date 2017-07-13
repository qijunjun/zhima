<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/23
 * Time: 13:47
 */

namespace Trace\Controller;

use Common\Controller\BaseController;
use Trace\API\TraceAPI;
use Trace\API\TrackAPI;
class TrackController extends BaseController
{
    private $companyid;
    private $_governmentid;

    public function _initialize()
    {
        $this->companyid = $_SESSION['user_info']['companyid'];
        $this->_governmentid = $_SESSION['user_info']['id'];
//        $this->companyid = 1019;
//        $this->_governmentid = 1;
    }

    /**
     * 企业的检测信息2016824
     */
    public function checkItem()
    {
        $productid = I('productid');
        $result = TrackAPI::checkItem($productid, $this->companyid);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("001");
        }
    }

    /**
     * 企业的仓库信息2016824
     */
    public function warehouse()
    {
        $result = TrackAPI::warehouse($this->companyid);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }

    /**
     * 根据产品查质量码段2016823
     */
    public function SearchQrcodeProduct()
    {
        $productid = I('productid');
        $result = TrackAPI::SearchQrcodeProduct($productid, $this->companyid);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }

    /**
     * 根据生产信息查质量码段2016823
     */
    public function SearchQrcodeProduce()
    {
        $id = I('id');
        $result = TrackAPI::SearchQrcodeProduce($id, $this->companyid);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }

    /**
     * 根据检测名称查质量码段2016823
     */
    public function SearchQrcodeCheckItem()
    {
        $id = I('id');
//        $tmptime = I('create_time');
//        $time = strtotime($tmptime);
        $result = TrackAPI::SearchQrcodeCheckItem($id, $this->companyid);

        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }

    /**
     * 根据入库查质量码段2016824
     */
    public function SearchCheckin()
    {
     //   ini_set("mongo.long_as_object", 1);
        $companyid = $this->companyid;
        $model = M("CorrQrcodePack");
        $fields = 'id,qrcode_range_s,qrcode_range_e,create_time';
        $waerhouseid = I('warehouseid');
        $res = TrackAPI::SearchCheckin($waerhouseid, $companyid);

        foreach ($res as $key => $val) {
        //    if (is_object($val)) $val = $val->value;
            $map = [
                'qrcode_pack' => $val,
                'company_id' => $companyid
            ];

            $re = $model->field($fields)->where($map)->select();
            if (!empty($re)) {
                $result[] = $re;
            }
        }
     //   ini_set("mongo.long_as_object", 0);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }

    /**
     * 根据出库查质量码段2016824
     */
    public function SearchCheckout()
    {
      //  ini_set("mongo.long_as_object", 1);
        $companyid = $this->companyid;
        $model = M("CorrQrcodePack");
        $fields = 'id,qrcode_range_s,qrcode_range_e,create_time';
        $destinationid = I('destinationid');
        $res = TrackAPI::SearchCheckout($destinationid, $companyid);

        foreach ($res as $key => $val) {
         //   if (is_object($val)) $val = $val->value;
            $map = [
                'qrcode_pack' => $val,
                'company_id' => $companyid
            ];

            $re = $model->field($fields)->where($map)->select();
            if (!empty($re)) {
                $result[] = $re;
            }
        }
       // ini_set("mongo.long_as_object", 0);
        if ($result) {
            json("001", $result, "success");
        } else {
            json("002");
        }
    }
    /**
     * 组合查询2016824
     * @return bool
     */
    public function SearchAll()
    {
        $profileid = I('profileid');
        $productid = I('productid');
        $inspectionid = I('checknameid');
        if (empty($profileid) && empty($productid) && empty($inspectionid)) {
            return false;
        }
        if (!empty($profileid)) {
            $code1 = TrackAPI::SearchQrcodeProduce($profileid, $this->companyid);
            if($code1 !== false){
                sort($code1);
            }else{
                json("002",null,"该生产信息没有关联记录");
                return;
            }
        }
        if (!empty($productid)) {
            $code2 = TrackAPI::SearchQrcodeProduct($productid, $this->companyid);
            if($code2 !== false){
                sort($code2);
            }else{
                json("002",null,"该产品没有关联记录");
                return;
            }
        }
        if (!empty($inspectionid)) {
            $code3 = TrackAPI::SearchQrcodeCheckItem($inspectionid, $this->companyid);
            if($code3 !== false){
                sort($code3);
            }else{
                json("002",null,"该检测信息没有关联记录");
                return;
            }
        }
        if (!empty($code2) && empty($profileid) && empty($inspectionid)) {
            json("001", $code2, "success");
        }elseif (!empty($code1) && !empty($code2) && empty($code3)) {
            $result = TrackAPI::inter($code1, $code2);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"该产品没有关联记录");
            }
        }elseif (empty($code1) && !empty($code2) && !empty($code3)) {
            $result = TrackAPI::inter($code2, $code3);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"该检测信息没有关联记录");
            }
        }elseif (!empty($code1) && !empty($code2) && !empty($code3)) {
            $result = TrackAPI::intersect($code1, $code2, $code3);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"该检测信息没有关联记录");
            }
        }
    }

    /**
     * 查找政府管辖的企业201692
     */
    public function searchCorr(){
        $governmentid = $this->_governmentid;
        $result = TrackAPI::searchCorr($governmentid);

        if($result){
            json("001",$result,"success");
        }else{
            json("001");
        }
    }

    /**
     * 查找企业的所有产品201692
     */
    public function searchComProduct(){
        $companyid = I('companyid');
        $re = TrackAPI::searchComProduct($companyid);
        if(empty($re)){
            $res['productid'] = 0;
            $res['productname'] = '该公司没有产品记录';
        }
        if(!empty($re)){
            $res = $re;
        }
        if($res){
            json("001",$res,"success");
        }else{
            json("001");
        }
    }

    /**
     * 查找企业对应产品的检测信息201692
     */
    public function searchComCheckItem(){
        $companyid = I('companyid');
        $productid = I('productid');
        $result = TrackAPI::checkItem($productid,$companyid);
        if($result){
            json("001",$result,"success");
        }else{
            json("001");
        }
    }

    /**
     * 查找企业对应产品的生产信息201692
     */
    public function searchComProfile(){
        $companyid = I('companyid');
        $productid = I('productid');
        $result = TrackAPI::searchComProfile($productid,$companyid);
        if($result){
            json("001",$result,"success");
        }else{
            json("001");
        }
    }

    /**
     * 根据企业查质量码段201692
     */
    public function searchQrcodeCom(){
        $companyid  = I('companyid');
        $result = TrackAPI::searchQrcodeCom($companyid);
        if($result){
            json("001",$result,'success');
        }else{
            json("002",null,"该企业没有关联记录");
        }
    }

    /**
     * 政府追踪查询201692
     * @return bool
     */
    public function govSearchAll(){
        $profileid = I('profileid');
        $productid = I('productid');
        $inspectionid = I('checknameid');
        $companyid = I('companyid');
        if (empty($profileid) && empty($productid) && empty($inspectionid) && empty($companyid)) {
            return false;
        }
        if (!empty($companyid)) {
            $code4 = TrackAPI::SearchQrcodeCom($companyid);
            if($code4 !== false){
                sort($code4);
            }else{
                json("002",null,"该企业没有关联记录");
                return;
            }
        }
        if (!empty($profileid)) {
            $code1 = TrackAPI::SearchQrcodeProduce($profileid, $companyid);
            if($code1 !== false){
                sort($code1);
            }else{
                json("002",null,"该生产信息没有关联记录");
                return;
            }
        }
        if (!empty($productid)) {
            $code2 = TrackAPI::SearchQrcodeProduct($productid, $companyid);
            if($code2 !== false){
                sort($code2);
            }else{
                json("002",null,"该产品没有关联记录");
                return;
            }
        }
        if (!empty($inspectionid)) {
            $code3 = TrackAPI::SearchQrcodeCheckItem($inspectionid, $companyid);
            if($code3 !== false){
                sort($code3);
            }else{
                json("002",null,"该检测记录没有关联记录");
                return;
            }
        }

        if (!empty($code4) && empty($profileid) && empty($inspectionid) && empty($productid)) {
            json("001", $code4, "success");
        }elseif (!empty($code1) && !empty($code2) && empty($code3) && !empty($code4)) {
            $result = TrackAPI::intersect($code1, $code2,$code4);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"生产信息没有关联记录");
            }
        }elseif (empty($code1) && !empty($code2) && !empty($code3) && !empty($code4)) {
            $result = TrackAPI::intersect($code2, $code3,$code4);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"检测信息没有关联记录");
            }
        }elseif (!empty($code1) && !empty($code2) && !empty($code3) && !empty($code4)) {
            $result = TrackAPI::interfour($code1, $code2, $code3,$code4);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"检测信息没有关联记录");
            }
        }elseif(empty($code1) && !empty($code2) && empty($code3) && !empty($code4)){
            $result = TrackAPI::inter($code2,$code4);
            if ($result) {
                json("001", $result, "success");
            } else {
                json("002",null,"该产品没有关联记录");
            }
        }

    }


}



