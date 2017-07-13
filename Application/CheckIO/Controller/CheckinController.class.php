<?php
namespace  CheckIO\Controller;
use CheckIO\API\CheckioAPI;
use Common\API\MatchtimeAPI;
use Common\Controller\BaseController;
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/23
 * Time: 下午4:43
 */
class CheckinController extends BaseController
{

    /*
    * @author       David
    * @param
    * @return       列出公司所有入库信息
    */
    public function listAll()
    {
       $companyid=$_SESSION["user_info"]["companyid"];
       $condition=array(
            "companyid" => $companyid
        );
        $response=CheckioAPI::listCheckin($condition);
        if($response){
            json("001",$response);
        }else{
            json("001",array());
            return;
        }
    }
    /*
     * 插入箱码码段到mongo数据库
     * $start:开始箱码
     * $end:结束箱码
     * $warehouseid:仓库编号
     */
    public function add($start,$end,$warehouseid){

        if ($end < $start)
        {
            json("002",null,'请检查输入的码段');
            return;
        }
        //$companyId=$this->get("company_id");
        $companyid=$_SESSION["user_info"]["companyid"];
        $userId=3;
        $response=CheckioAPI::addinRecord($start,$end,$warehouseid,$companyid,$userId);
        //$response=$start+'#'+$end+'#'+$warehouseid;
        if($response){
            json("001",$response);
        }
        else{
            json("002",null,'添加失败');
            return;
        }
    }
    /*
     * 接收app传来的数组
     * 添加入库记录到mongo数据库
     * $records:一组入库记录,已经从app上包装好
     */
    public function appAdd($records){
        if($records==null){
            json("002",null,'添加失败');
            return;
        }
        $response=CheckioAPI::addinRecordAPP($records);
        if($response){
            json("001",$response);
        }
        else{
            json("002",null,'添加失败');
        }
    }
    /*
     * 删除入库的箱码
     */
    public function delete($id=null)
    {
        if ($id === '' || $id == null) {
            json("002",null,'删除失败');
        }
        $response=CheckIOAPI::deleteinRecord($id);
        if($response){
            //$response='delete:'+$id+' ok';
            json("001",$response);
        }else{
            json("002",null,'删除失败');
        }

    }
    public function getWhList(){
        //$companyid=$this -> get("company_id");
        $companyid=$_SESSION["user_info"]["companyid"];
        $response=CheckioAPI::getWhList($companyid);
       /* $response=array(
            0=>array(
                'id'=>1,
                'warehouse_name'=>'仓库1'
            ),
            1=>array(
                'id'=>2,
                'warehouse_name'=>'仓库2'
            ),
            2=>array(
                'id'=>3,
                'warehouse_name'=>'仓库3'
            )
        );*/
        if($response){
            json('001',$response);
        }
        else
        {
            json('001',array());
        }
    }
    public function getAGList(){
        //$companyid=$this -> get("company_id");
        $companyid=$_SESSION["user_info"]["companyid"];
        $response=CheckioAPI::getAGList($companyid);

       /* $response=array(
            0=>array(
                'id'=>1,
                'agency_name'=>'经销商1'
            ),
            1=>array(
                'id'=>2,
                'agency_name'=>'经销商2'
            ),
            2=>array(
                'id'=>3,
                'agency_name'=>'经销商3'
            )
        );*/
        if($response){
            json('001',$response);
        }
        else
        {
            json('001',array());
        }
    }

    /**
     * 入库按时间查询2016822
     */
    public function SearchCheckin(){
        $companyid=$_SESSION["user_info"]["companyid"];
        $starttime = I('startdate',null);
        $endtime = I('enddate',null);
        $re = MatchtimeAPI::checkDate($starttime,$endtime);

        if($re === false){
            return;
        }
        if($starttime && $endtime){
            $condition = array(
                "companyid" => $companyid,
                'create_time' => array('between',array($re[1][0],$re[1][1]))
            );
        }elseif($starttime && empty($endtime)){
            $condition = array(
                "companyid" => $companyid,
                'create_time' => array('egt',$re[1])
            );
        }elseif(empty($starttime) && $endtime){
            $condition = array(
                "companyid" => $companyid,
                'create_time' => array('elt',$re[1])
            );
        }elseif($starttime == null && $endtime == null){
            $condition = array(
                "companyid" => $companyid
            );
        }

        $response=CheckioAPI::listCheckin($condition);

        if($response){
            json("001",$response);
        }else{
            json("001",array());
            return;
        }
    }
}