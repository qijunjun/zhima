<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:31
 */
namespace Company\API;

class CompanyAPI{

    public static function addCompany($data) {
        $model = M('base_company');
        if(!$model->create($data)) {
            return false;
        }
       $id = $model->add();
        //$id=1;
       return $id;
    }
    public static function updateCompany($data) {
        $model = M('base_company');
        $rules = array(
            array('phone','/^[\d-]*$/',-1,0),
            array('email','email',-2,0),

        );
        if(!$model->validate($rules)->create($data)) {
            return false;
        }
        $result = $model->save();
        return $result;
    }
    public static function editCompany($id) {
        $model = M('base_company');
        $data=$model->find($id);

        if($data) {
            return $data;
        }else{
            return false;
        }
    }
    //公司仓库操作--start
    public static function addWarehouse($data){
        $model = M('base_warehouse');
        if(!$model->create($data)) {
            return false;
        }
        else{
            $id = $model->add();
            return $id;
        }

    }
    public static function editWarehouse($id){
        $model = M('base_warehouse');
        $data=$model->find($id);
        /*$data=array(
            'name'=>'仓库1',
            'address'=>'天津市西青区华鼎仓库',
            'contact'=>'黎明',
            'phone'=>'13752328117',
            'longitude'=>'34.098765',
            'latitude'=>'102.087654'
        );*/

        if($data) {
            return $data;
        }else{
            return false;
        }
    }
    public static function updateWarehouse($data){
        $model = M('base_warehouse');
        if(!$model->create($data)) {
            return false;
        }
        $result = $model->save();

        //$result=1;
        return $result;
    }
    public static function deleteWarehouse($id){
        $map=array(
            'id'=>$id
        );
        $model = M('base_warehouse');
        $result=$model->where($map)->delete();
        return $result;
    }

    public static function listWarehouse($filter = [], $start = 0, $limit = 100) {
        $model = M('base_warehouse');
        $data= $model->where($filter)->limit($start, $limit)->select();
        return $data;
    }
    //公司仓库操作--end


    //公司经销商操作--start
    public static function addAgent($data){
        $model = M('base_agent');
        $rules = array(
            //array('agent_phone','/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/',-6,0),
            array('agent_phone','/^[\d-]*$/',-1,0),

        );
        if(!$model->validate($rules)->create($data)) {
           return false;
        }
        $id = $model->add();
        //$id=1;
        return $id;
    }
    public static function addAgentA($data,$area){
        $model = M('base_agent');
        $model1=M('base_agent_area');
        $areas="";
        for($i=0;$i<count($area);$i++){
            $data1=[
                'companyid'=>$_SESSION["user_info"]["companyid"],
                'province' =>$area[$i]['province'],
                'city' => $area[$i]['city'],
                'district' => $area[$i]['district'],
                'create_time'=>time()
            ];
            $areaid=$model1->add($data1);
            $areas=$areas.$areaid.',';
        }
        if($areas=="")
        {
            return false;
        }
        $rules = array(
            //array('agent_phone','/^(0|86|17951)?(13[0-9]|15[012356789]|18[0-9]|14[57])[0-9]{8}$/',-6,0),
            array('agent_phone','/^[\d-]*$/',-1,0),

        );
        $data['agent_area']=$areas;
        if(!$model->validate($rules)->create($data)) {
            return false;
        }
        $id = $model->add();
        //$id=1;
        return $id;
    }
    public static function editAgent($id){
        $model = M('base_agent');
        $model1=M('base_agent_area');
        $data=$model->find($id);
        if($data) {
            $areasid=$data['agent_area'];
            $areasarr= explode(',',$areasid);
            $areas=[];
            for ($i=0;$i<count($areasarr);$i++)
            {
                if($areasarr[$i]!=""){
                    $cond=array(
                        'agent_id'=>$areasarr[$i]
                    );
                    $areas[$i]=$model1->where($cond)->find();;
                }
            }
            $data['agent_area']=$areas;
            return $data;
        }else{
            return false;
        }
    }
    public static function updateAgent($data){
        $model = M('base_agent');
        $model1=M('base_agent_area');
        $agent_area=$data['agent_area'];
        $areas="";
        foreach ($agent_area as $value){
            $areaid=self::getAreaid($value);
            if($areaid<0) //已存在同样的区域
            {
                $data1=[
                    'companyid'=>$_SESSION["user_info"]["companyid"],
                    'province' =>$value['province'],
                    'city' => $value['city'],
                    'district' => $value['district'],
                    'create_time'=>time()
                ];
                $areaid=$model1->add($data1);

            }
            $areas=$areas.$areaid.',';
        }
        $data['agent_area']=$areas;
        if(!$model->create($data)) {
            return false;
        }
        $result = $model->save();
        return $result;
    }
    private function getAreaid($arr)
    {
        $area=M('base_agent_area');
        $companyid=$_SESSION["user_info"]["companyid"];
        $map['companyid'] = $companyid;
        $map['province'] = $arr['province'];
        $map['city'] = $arr['city'];
        $map['district'] = $arr['district'];
        $agentid=-1;
        $result=$area->where($map)->select();
        if($result){
            $agentid=$result[0]['agent_id'];
        }
        return $agentid;
    }
    public static function deleteAgent($id){
        $map=array(
            'id'=>$id
        );
        $model = M('base_agent');
        $result=$model->where($map)->delete();
        return $result;
    }

    public static function listAgent($filter = [], $start = 0, $limit = 100) {
        $model = M('base_agent');
        return $model->where($filter)->limit($start, $limit)->select();
    }
    //公司经销商操作--end

    public static function lists($filter = [], $start = 0, $limit = 100) {
        $model = M('base_company');
        return $model->where($filter)->limit($start, $limit)->select();
    }
    public static function checkInput($operation, $companyId, $page = null, $size = null, $conditions = null, $sort =null, $id = null, $name = null, $description = null)
    {
        if (!is_numeric($companyId) || $companyId <= 0)
        {
            return false;
        }
        switch ($operation)
        {
            case "fetch":

                break;
            case "add":
          
            case "edit":
                if (!is_numeric($id) || $id <= 0)
                {
                    return false;
                }
        }
        return true;
    }
    /*
     * 根据经销商id获取其详细信息
     * 经销商的id,名称,法人,电话
     */
    public static function getAgentbyId($cond)
    {
        $model = M('base_agent');
        $fields="id,agent_name,agent_legalperson,agent_phone";
        $result=$model->where($cond)->field($fields)->find();
        return $result;
    }

    /*
     * 根据b码查询c码
     * 用于企业根据质量码生成二维码
     * $bcode:当前企业使用的二维码
     */
    public static function getCCode($bcode)
    {
        $xcode=MM("zcode", null);
        $cond=array(
            'b'=>floatval($bcode)
        );
        $res=$xcode->field("c")->where($cond)->find();
        return $res;
    }
}