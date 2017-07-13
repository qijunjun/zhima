<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/4/13
 * Time: 下午9:53
 */
namespace  Company\Controller;
use Common\Controller\BaseController;
use Company\API\CompanyAPI;
class AgentController extends BaseController{

    /*
    public function add(){
        $data = [
            'agent_name' => I('request.agent_name', "test"),
            'agent_phone' => I('request.agent_phone', null),
            'agent_address' => I('request.agent_address', null),
            'agent_legalperson' => I('request.agent_legalperson', null),
            'agent_products'=>I('request.agent_products', null),
            'province'=>I('request.province',null),
            'city'=>I('request.city',null),
            'district'=>I('request.district',null),
            'longitude'=>I('request.longitude',100.000001),
            'latitude'=>I('request.latitude',100.000001),
            'companyid' => $_SESSION["user_info"]["companyid"],
            'create_time' => time()
        ];
        if($data['agent_name'] == null ||
            $data['agent_name'] === '' ||
            $data['agent_phone'] == null ||
            $data['agent_phone'] === ''
        ) {
            json("002",null,'请检查字段输入是否合法');
            return;
        }
        //$companyId=$this->get("company_id");
        $response=CompanyAPI::addAgent($data);
        if($response){
            json("001",$response);
        }else{
            json('002');
            return;
        }

    }
    */
    /*
     * 添加经销商,多地域问题
     */
    public function add()
    {
        $agentname= I('request.agent_name', null);
        $agentphone=I('request.agent_phone', null);
        if($agentname == null  || $agentphone == null)
        {
            json("002",null,'请检查字段输入是否合法');
            return;
        }
        //经销商基本信息
        $data = [
            'agent_name' => I('request.agent_name', "test"),
            'agent_phone' => I('request.agent_phone', null),
            'agent_address' => I('request.agent_address', null),
            'agent_legalperson' => I('request.agent_legalperson', null),
            'agent_products'=>I('request.agent_products', null),
            'agent_area'=>"",
            'longitude'=>I('request.longitude',100.000001),
            'latitude'=>I('request.latitude',100.000001),
            'companyid' => $_SESSION["user_info"]["companyid"],
            'create_time' => time()
        ];
        $area=I('request.submitAddress',null); //获得经销商的代理区域
        $response=CompanyAPI::addAgentA($data,$area);
        if($response){
            json("001",$response);
        }else{
            json('002');
            return;
        }
    }
    public function edit($id){
        if($id===''||$id==null){
            json(002);
            return;
        }
        $response=CompanyAPI::editAgent($id);
        /*$response=array(
            'id'=>1,
            'agency_name'=>'天津三元副食品公司',
            'agency_phone'=>'23508813',
            'agency_address'=>'天津市南开区卫津路22号',
            'agency_legalperson'=>'三元',
            'province'=>1,
            'city'=>1,
            'district'=>1,
            'longitude'=>'33.000394',
            'latitude'=>'112.987654'
        );*/
        if($response){
            json("001",$response);
        }
        else{
            json("002");
            return;
        }
    }
    public function update(){
        $data = [
            'agent_name' => I('request.agent_name', "test"),
            'agent_phone' => I('request.agent_phone', null),
            'agent_address' => I('request.agent_address', null),
            'agent_legalperson' => I('request.agent_legalperson', null),
            'agent_products'=>I('request.agent_products', null),
            'agent_area'=>I('request.agent_area', null),
            'longitude'=>I('request.longitude',100.000001),
            'latitude'=>I('request.latitude',100.000001),
            'update_time' => time(),
            'id'=>I('request.id')
        ];
        if($data['agent_name'] == null ||
            $data['agent_name'] === '' ||
            $data['agent_phone'] == null ||
            $data['agent_phone'] === ''||
            $data['id'] == null ||
            $data['id'] === ''

        ) {
            json("002");
        }
        $response=CompanyAPI::updateAgent($data);
        //$response='ok';
        if($response){
            json("001",$response);
        }
        else{
            json("002");
            return;
        }
    }
    public function delete($id){
        if($id===''||$id==null){
            json("002");
        }
        $response=CompanyAPI::deleteAgent($id);
        //$response='ok';
        if($response){
            json("001",$response);
        }
        else{
            json("002");
            return;
        }
    }
    /*
    * 列出企业所有的经销商
    */
    public function listAgent(){

        $map['companyid'] = $_SESSION["user_info"]["companyid"]; //获取公司id
        $response=CompanyAPI::listAgent($map);
        if($response){
            json("001",$response);
        }else{
            json("001",array());
            return;
        }
    }
    /*
     * 根据经销商id获取信息
     */
    public function getAgentbyId($agentid)
    {
        if(!is_numeric($agentid))
        {
            return;
        }
        $map['id'] = $agentid; //经销商id
        $response=CompanyAPI::getAgentbyId($map);
        return $response;
    }
}