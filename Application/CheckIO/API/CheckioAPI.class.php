<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/23
 * Time: 下午4:45
 */
namespace CheckIO\API;
class CheckioAPI
{

    /*
     * 列出入库记录列表
     * $filter:查询条件,公司id
     */
    public static function listCheckin($filter = [], $start = 0, $limit = 100)
    {
        $model=MM('base_scanin',"zm_");
        $warehouse = M("BaseWarehouse");
        $data= $model->where($filter)->select();
        unset($filter["create_time"]);
        $warehouses = $warehouse -> where($filter) -> getField("id, warehouse_name name", true); //仓库列表
        $result = array();
        foreach ($data as $key=>$item){
            $productinfo=self::getProductInfoByPack($item['p']);
            $destination=$item['warehouseid'];
            $record = array(
                '_id'=>$item['_id'],
                "product_name" => $productinfo['name'],
                'spec'=>$productinfo['spec'],
                'p'=>$item['p'],
                'destination'=>$warehouses[$destination],
                //'destination'=>$warehouses[4],
                'create_time'=>$item['create_time'],
                'userid'=>$item['userid']
            );
            array_push($result, $record);
        }

        return $result;
    }
    /*
     * 调用杨斌的mongo处理模块
     */
    public static function addinRecord($start,$end,$warehouseid,$companyid,$userid){
        $model=MM('base_scanin',"zm_");
        import("Code.Lib.Mongo.MongoInt64");
        $final_result = array();
        //检查箱码输入是否有效
        if(!checkXCode($companyid,$start,$end)){
            json('002','请检查输入的码段');
            return;
        }
        for ($x=$start; $x<=$end; $x++) {
            $p=new \MongoInt64($x);
            //压进数组
            $single_record = array(
                'warehouseid'=>$warehouseid,
                'p'=>$p,
                'companyid'=>$companyid,
                'userid'=>$userid,
                'create_time'=>time()
            );
            array_push($final_result, $single_record);

        }
        $response=$model->addAll($final_result);
        return $response;
    }
    /*
     * 接收app入库操作
     */
    public static function addinRecordAPP($records){
        $model=MM('base_scanin',"zm_");
        import("Code.Lib.Mongo.MongoInt64");
        foreach ($records as &$item){
            $item['p']=new \MongoInt64($item['p']);
            $item['userid']=3;
        }
        unset($item);
        $response=$model->addAll($records);
        return $response;
    }

    public static function deleteinRecord($id)
    {
        $model=MM('base_scanin',"zm_");
        $map=array(
            '_id'=>$id
        );
        $response=$model->where($map)->delete();
        return $response;
    }
    /*
     * 列出出库记录列表
     * $filter:查询条件,公司id
     */
    public static function listCheckout($filter = [], $start = 0, $limit = 100) {
        $warehouse = M("BaseWarehouse");
        $agent = M("BaseAgent");
        $model=MM('base_scanout',"zm_");
        $data=$model->where($filter)->select();
        unset($filter["create_time"]);
        $warehouses = $warehouse -> where($filter) -> getField("id, warehouse_name name", true); //仓库列表
        $agents = $agent -> where($filter) -> getField("id, agent_name name", true); //经销商列表
        $result = array();
        foreach ($data as $key=>$item){
            $productinfo=self::getProductInfoByPack($item['p']);
            $destination=$item['destinationid'];
            if($productinfo!=null){
                $record = array(
                    '_id'=>$item['_id'],
                    'outtype'=>$item["outtype"],
                    "product_name" => $productinfo['name'],
                    'spec'=>$productinfo['spec'],
                    'p'=>$item['p'],
                    'destination'=>$item["outtype"] == "0" ? $warehouses[$destination] : $agents[$destination],
                    //'destination'=>$warehouses[6],
                    'time'=>$item['create_time'],
                    'userid'=>$item['userid']
                );
                array_push($result, $record);
            }else{
                $record = array(
                    '_id'=>$item['_id'],
                    'outtype'=>$item["outtype"],
                    "product_name" => '无',
                    'spec'=>'无',
                    'p'=>$item['p'],
                    'destination'=>$item["outtype"] == "0" ? $warehouses[$destination] : $agents[$destination],
                    //'destination'=>$warehouses[6],
                    'time'=>$item['create_time'],
                    'userid'=>$item['userid']
                );
                array_push($result, $record);
            }

        }
        return $result;
    }
    /*
     * 按条件返回出库信息
     */
    public static function listCheckoutbyPack($filter = [])
    {
        $model=MM('base_scanout',"zm_");
        $data=$model->where($filter)->order("create_time desc")->select();
        return $data;
    }
    public static function addoutRecord($start,$end,$warehouseid,$companyid,$userid){
        $model=MM('base_scanout',"zm_");
        import("Code.Lib.Mongo.MongoInt64");
        //检查箱码输入是否有效
        if(!checkXCode($companyid,$start,$end)){
            json('002','请检查输入的码段');
            return;
        }
        $final_result = array();
        $outtype=substr($warehouseid,0,1);
        if($outtype=='w'){
            $outtype=0;  //出到仓库
        }else{
            $outtype=1;  //出到经销商
        }
        for ($x=$start; $x<=$end; $x++) {
            $p=new \MongoInt64($x);
            //压进数组
            $single_record = array(
                'outtype'=>$outtype,
                'destinationid'=>substr($warehouseid, 1),
                'p'=>$p,
                'companyid'=>$companyid,
                'userid'=>$userid,
                'create_time'=>time()
            );
            array_push($final_result, $single_record);
        }
        unset($single_record);
        $response=$model->addAll($final_result);
        return $response;
    }
    /*
     * 添加出库信息
     */
    public static function addoutRecordAPP($records){
        $model=MM('base_scanout',"zm_");
        $final_result = array();
        import("Code.Lib.Mongo.MongoInt64");
        foreach ($records as $item){
            $outtype=substr($item['warehouseid'],0,1);
            if($outtype=='w'){
                $outtype=0;
            }else{
                $outtype=1;
            }
            $p=new \MongoInt64($item['p']);
            //压进数组
            $single_record = array(
                'outtype'=>$outtype,
                'destinationid'=>substr($item['warehouseid'], 1),
                'p'=>$p,
                'companyid'=>$item['companyid'],
                'userid'=>3,
                'create_time'=>$item['create_time']
            );
            array_push($final_result, $single_record);
        }
        unset($single_record);
        $response=$model->addAll($final_result);
        return $response;
    }

    public static function deleteoutRecord($id){
        $model=MM('base_scanout',"zm_");
        $map=array(
            '_id'=>$id
        );
        $response=$model->where($map)->delete();
        return $response;
    }
    /**
     * 根据公司id得到公司的仓库列表
     * @param string $companyid 公司id
     * @return 公司所有的仓库,失败返回false
     */
    public static function getWhList($companyid=1){

        if($companyid===''||$companyid==null){
            return false;
        }
        $condition=array(
            "companyid" => $companyid
        );
        $wlist=M('base_warehouse');
        $list=$wlist->where($condition)->field('id,warehouse_name')->select();
        return $list;
    }
    /**
     * 根据公司id得到公司的经销商列表
     * @param string $companyid 公司id
     * @return 公司所有的经销商,失败返回false
     */
    public static function getAGList($companyid=1){
        if($companyid===''||$companyid==null){
            return false;
        }
        $condition=array(
            "companyid" => $companyid
        );
        $alist=M('base_agent');
        $list=$alist->where($condition)->field('id,agent_name')->select();

        return $list;
    }
    /*
     * 通过瓶码得到产品信息
     * $code:瓶码
     */
    public static function productInfo($code)
    {
        $qrcode_info = M("CorrQrcodeProduct");
        $product = M("BaseProduct");
        // code between start and end
        $cond = array(
            "qrcode_range_s" => array('elt', $code),
            "qrcode_range_e" => array('egt', $code)
        );
        $product_id = $qrcode_info -> where($cond) -> getField("product_id");
        $fields = "productname name, guige spec, price, productinfo info,productimage image";
        $cond = array(
            "productid" => $product_id
        );
        $result = $product -> field($fields) -> where($cond) -> find();
        return $result;
    }
    /*
     * 通过箱码得到产品信息
     * $code:箱码
     */
    public static function getProductInfoByPack($code){
        $qrcode_pack = M("CorrQrcodePack");
        $cond=array(
            "qrcode_pack" =>array('eq',$code)
        );
        $zcode = $qrcode_pack -> where($cond) -> getField("qrcode_range_s");
        $data=self::productInfo($zcode);
        return $data;
    }
    
}