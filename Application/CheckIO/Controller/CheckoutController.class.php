<?php
namespace  CheckIO\Controller;
use CheckIO\API\CheckioAPI;
use Common\Controller\BaseController;
use Common\API\MatchtimeAPI;
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/23
 * Time: 下午4:44
 */
class CheckoutController extends BaseController
{
    public function blackList($b) {
        $model = M('base_qrcode_spam');
        $count = $model->where(['b' => $b, 'status' => 1])->count();
        if($count > 0){
            $result=true;
        }else{
            $result=false;
        }
        return $result;
    }
    public function test()
    {
        //var_dump($_SESSION['companyid']);
        //$warehouse = M("BaseWarehouse");
        /*$filter=array(
            "companyid" => $_SESSION["user_info"]["companyid"]
        );*/
        //$warehouses = $warehouse -> where($filter) -> getField("id, warehouse_name name", true); //仓库列表
        //$data=CheckioAPI::listCheckoutbyPack('10190000000001');
        //$data1=CheckioAPI::productInfo('10191000000003');
        //$data1=getPcodeByXcode('10191000000003');
        //$data = $data[count($data) - 1];
        //echo $data;
        //$modelFlee=M('dict_beyond_areas');
        //$fleeAddress=$modelFlee->where()->select();
        //dump($fleeAddress);
        //$data=$fleeAddress[1]['beyond_areas_type'];

        //dump($data);
        //dump($data1);
        //echo $warehouses["6"];
    }
    /*
     * 根据质量码获取经销商信息(最后一次出库的经销商)
     */
    public function getAgentbyQCode($qcode)
    {
        $pattern = '/\d{14}$/';
        $res= preg_match($pattern,$qcode );
        if(!$res)
        {
            json('002',null,'请检查输入码的格式');
            return;
        }
        $agent = A('Company/Agent');   //创建公司模块下的经销商控制器
        $codeType = substr($qcode,4,1);
        if($codeType=='1')
        {
            $xcode=getPcodeByXcode($qcode); //获得质量码对应的箱码
        }
        else
        {
            $xcode=$qcode;
        }
        $cond=array(
            'p'=>floatval($xcode)
        );
        $agents=CheckioAPI::listCheckoutbyPack($cond);
        if(count($agents)<=0)
        {
            json("001",array(),"没有经销商信息");
        }else
        {
            $agentid=$agents[0]['destinationid'];
            $result=$agent->getAgentbyId($agentid);     //根据经销商id得到经销商的详细信息
            if($result){
                json('001',$result);
            }
            else
            {
                json("001",array(),"没有经销商信息");
            }
        }
    }
    /*
     * 根据质量码获取经销商id,给小飞接口
     */
    public function getAgentbyQCodeData($qcode)
    {
        $agent = A('Company/Agent');   //创建公司模块下的经销商控制器
        $xcode = getPcodeByXcode($qcode); //获得质量码对应的箱码
        $cond = array(
            'p' => floatval($xcode)
        );
        $agents = CheckioAPI::listCheckoutbyPack($cond);
        if (count($agents) <= 0) {
            return null;
        } else {
            $agentid = $agents[0]['destinationid'];
            $result = $agent->getAgentbyId($agentid);     //根据经销商id得到经销商的详细信息
            if ($result) {
                return $result;
            } else {
                return null;
            }
        }
    }
        /*
        * @author       David
        * @param
        * @return       列出公司所有入库信息
        */
    
    public function listAll()
    {
        //$companyid=$this -> get("company_id");
       $companyid=$_SESSION["user_info"]["companyid"];
       $condition=array(
            "companyid" => $companyid
        );
        $response=CheckioAPI::listCheckout($condition);
       if($response){
            json("001",$response);
        }else{
            json("001",array());
           return;
        }
    }

    /*
     * 出库
     * 插入箱码码段到mongo数据库
     * $start:开始箱码
     * $end:结束箱码
     * $warehouseid:仓库编号
     */
    public function add($start,$end,$warehouseid){

        if ($end < $start)
        {
            json('002',null,'请检查输入的码段');
            return;
        }
        //$companyId=$this->get("company_id");
        $companyid=$_SESSION["user_info"]["companyid"];
        $userId=3;
        $response=CheckioAPI::addoutRecord($start,$end,$warehouseid,$companyid,$userId);
        //$response=$start+'-'+$end+'-'+$warehouseid;
        if($response){
            json("001",$response);
        }
        else{
            json('002',null,'请检查输入的码段');
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
            json('002',null,'上传失败');
            return;
        }
        $response=CheckioAPI::addoutRecordAPP($records);
        if($response){
            json("001",$response);
        }
        else{
            json('002',null,'上传失败');
            return;
        }
    }
    /*
     * 删除入库的箱码
     */
    public function delete($id=null)
    {
        if ($id === '' || $id == null) {
            json("002",null,'删除失败');
            return;
        }
        $response=CheckioAPI::deleteoutRecord($id);
        if($response){
            json("001",$response);
        }else{
            json("002",null,'删除失败');
            return;
        }

    }
    public function getWhList(){
        $companyid=$_SESSION["user_info"]["companyid"];
        $response=CheckIOAPI::getWhList($companyid);
        if($response){
            json('001',$response);
        }
        else
        {
            json('001',array());
            return;
        }
    }
    public function getAGList(){
        //$companyid=$this -> get("company_id");
        $companyid=$_SESSION["user_info"]["companyid"];
        $response=CheckIOAPI::getAGList($companyid);
        if($response){
            json('001',$response);
        }
        else
        {
            json('001',array());
            return;
        }
    }

    /**
     * 出库按时间查询2016822
     */
    public function SearchCheckout(){
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

        $response=CheckioAPI::listCheckout($condition);

        if($response){
            json("001",$response);
        }else{
            json("001",array());
            return;
        }
    }
}