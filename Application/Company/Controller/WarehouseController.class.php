<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 16/4/13
 * Time: 下午9:54
 */
namespace  Company\Controller;
use Common\Controller\BaseController;
use Company\API\CompanyAPI;
class WarehouseController extends BaseController{

    protected function _initialize(){

    }
    /*
     * 添加仓库
     */
    public function add(){
        $data = [
            'warehouse_name' => I('request.warehouse_name', "test"),
            'warehouse_phone' => I('request.warehouse_phone', null),
            'warehouse_address' => I('request.warehouse_address', null),
            'warehouse_manager' => I('request.warehouse_manager', null),
            'longitude'=>I('request.longitude',100.000001),
            'latitude'=>I('request.latitude',100.000001),
            'companyid' => $_SESSION["user_info"]["companyid"],
            'create_time' => time()
        ];
        $response=CompanyAPI::addWarehouse($data);
        if($response){
            json("001",$response);
        }
        else{
            json('002');
        }
    }
    public function edit($id){
        if($id===''||$id==null){
            json(002);
        }
        $response=CompanyAPI::editWarehouse($id);
        /*$response=array(
            'id'=>1,
            'warehouse_name'=>'仓库1',
            'warehouse_phone'=>'23508813',
            'warehouse_address'=>'天津市南开区卫津路22号',
            'warehouse_manager'=>'徐慧',
            'longitude'=>44.667987,
            'latitude'=>112.008987,
        );*/
        if($response){
            json("001",$response);
        }
        else{
            json("002");
        }
    }
    public function update(){
        $data = [
            'warehouse_name' => I('request.warehouse_name', "test"),
            'warehouse_phone' => I('request.warehouse_phone', null),
            'warehouse_address' => I('request.warehouse_address', null),
            'warehouse_manager' => I('request.warehouse_manager', null),
            'longitude'=>I('request.longitude',100.000001),
            'latitude'=>I('request.latitude',100.000001),
            'update_time' => time(),
            'id'=>I('request.id')
        ];
        if($data['warehouse_name'] == null ||
            $data['warehouse_name'] === '' ||
            $data['warehouse_phone'] == null ||
            $data['warehouse_phone'] === ''||
            $data['id'] == null ||
            $data['id'] === ''

        ) {
            json("002");
        }
        $response=CompanyAPI::updateWarehouse($data);
        //$response='ok';
        if($response){
            json("001",$response);
        }
        else{
            json("002");
        }
    }
    public function delete($id){
        if($id===''||$id==null){
            json("002");
        }
        $response=CompanyAPI::deleteWarehouse($id);
        //$response=$id;
        if($response){
            json("001",$response);
        }
        else{
            json("002");
        }
    }
    /*
     * 列出企业所有的仓库
     */
    public function listWarehouse(){
        $map['companyid'] = $_SESSION["user_info"]["companyid"]; //获取公司id
        $response=CompanyAPI::listWarehouse($map);
        if($response){
            json("001",$response);
        }else{
            json("001",array());
        }
    }
}