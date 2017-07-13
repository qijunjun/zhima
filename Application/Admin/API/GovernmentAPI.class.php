<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/13
 * Time: 9:13
 */

namespace Admin\API;


class GovernmentAPI
{
    /**列出政府信息2016813
     * @return mixed
     */
    public static function showGovernmentInfo(){
        $model = M("base_user");
        $map = [
            'zm_base_user.type' => 4
        ];
        $res = $model->join('zm_base_government ON zm_base_user.id = zm_base_government.user_id')->where($map)->select();

        for($i=0;$i<count($res);$i++){
            unset($res[$i]['password']);
        }
        return $res;
    }

    /**列出企业信息2016813
     * @return mixed
     */
    public static function showCompanyInfo(){
        $model = M("BaseCompany");
        $fields = 'name,id';
        $res = $model->field($fields)->select();
        return $res;
    }

    /**检测关联是否重复2016815
     * @param $governmentid
     * @param $companyid
     * @return mixed
     */
    public static function checkRepeatCorr($governmentid,$companyid){
        $model = M("CorrGovernmentCompany");
        $map = [
            'government_id' => $governmentid,
            'company_id'    => $companyid
        ];
        $res = $model->where($map)->select();

        return $res;
    }

    /*
     * 添加政府与企业关联信息2016813
     */
    public static function addCorrGovernment_Company($governmentid,$companyid){
        $re = self::checkRepeatCorr($governmentid,$companyid);
        if($re){
            return false;
        }

        $model = M("CorrGovernmentCompany");
        $data = [
            'government_id' => $governmentid,
            'company_id'    => $companyid
        ];
        if(!$model->create($data)){
            json("009",null,$model->getError());

            return false;
        }else{
            $res = $model->add();
        }

        return $res;
    }

    /**政府关联企业名称2016813
     * @param $governmentid
     * @return mixed
     */
    public static function showCorrCompany($governmentid){
        $model = M("BaseCompany");
        $map = [
            'zm_corr_government_company.government_id' =>$governmentid
        ];
        $fields = 'name as companyName,legalperson as companyLegal';
        $res =  $model->field($fields)->join('zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id','left')->where($map)->select();

        return $res;
    }

    /**关联政府名称2016813
     * @param $governmentid
     * @return mixed
     */
    public static function showCorrGovernment($governmentid){
        $model = M("BaseGovernment");
        $map = [
            'zm_base_government.id' => $governmentid
        ];
        $field = 'name,id';
        $res = $model->field($field)->where($map)->find();

        return $res;
    }

    /**删除关联列表中的数据2016813
     * @param $id
     * @return mixed
     */
    public static function del($id){
        $model = M("CorrGovernmentCompany");
        $map = [
            'id' => $id
        ];
        $res = $model->where($map)->delete();

        return $res;
    }

    /**显示列表数据2016813
     * @return mixed
     */
    public static function listAllCorrInfo(){
        $model = M();
        $sql = "SELECT zm_base_company.`name` AS companyName,zm_base_company.legalperson AS companyLegal,zm_base_government.id AS gov_id,zm_base_government.`name`,zm_corr_government_company.id FROM zm_base_company INNER JOIN zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id INNER JOIN zm_base_government ON zm_corr_government_company.government_id = zm_base_government.id";
        $res = $model->query($sql);

        return $res;
    }
}