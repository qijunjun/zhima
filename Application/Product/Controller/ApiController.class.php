<?php
/**
 * Created by Yangbin.
 * User: Yangbin
 * Date: 2016/4/13
 * Time: 11:27
 */
namespace Product\Controller;
use Product\Model\ApiModel;
use Think\Controller;
class ApiController extends Controller{

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    public function info()
    {
        $productId = I('id','0','htmlspecialchars');

        $model = new ApiModel();

        $model->show($productId);
    }

    public function add()
    {
        $model = new ApiModel();

        $model->insert();
    }

    public function edit()
    {
        $productId = I('id','0','htmlspecialchars');

        $model = new ApiModel();

        $model->edit($productId);
    }

    public function update()
    {
        $productId = I('id','0','htmlspecialchars');

        $model = new ApiModel();

        $model->update($productId);
    }

    public function del()
    {
        $productId = I('id','0','htmlspecialchars');

        $model = new ApiModel();

        $model->delete($productId);
    }

    public function fieldsList()
    {
        $model = new ApiModel();

        $companyId = $model->getCompanyId();

        $model->getFieldsList($companyId);


    }

    /**
     *按时间查询产品信息2016822
     */
    public function SearchInfo(){
        $startdate = I('startdate',null);
        $enddate = I('enddate',null);

        $model = new ApiModel();
        $model->timeSearchInfo($startdate,$enddate);
    }

}