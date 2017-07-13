<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/2
 * Time: 9:04
 */

namespace Logistics\API;

class LogisticsAPI
{
    /**添加物流信息
     * @param $data
     * @return bool
     */
    public static function add($data)
    {
        $model = M("BaseLogistics");
        $rules = self::checkPackCode();
        $auto = array(array('create_time', 'time', 1, 'function'),            // 对create_time字段在更新的时候写入当前时间戳
        );
        if (!$model->auto($auto)->validate($rules)->create($data)) {
            echo json("009", null, $model->getError());

            return false;
        }
        $id = $model->add();

        return $id;

    }

    /**更新物流信息
     * @param $id
     * @param $data
     * @return bool
     */
    public static function update($id,$data){
        $model = M("BaseLogistics");
        $company_id = $_SESSION['user_info']['companyid'];
        $map['id'] = $id;
        $map['company_id'] = $company_id;
        $rules = array(
            array('logistics','require','物流公司不能为空'),
            array('expresslist','require','发货单号不能为空'),
            array('logistics_time','require','发货时间不能为空'),
        );
        $auto = array(
            array('update_time','time',1,'function')
        );
        if(!$model->validate($rules)->auto($auto)->where($map)->create($data)){
            json("009",null,$model->getError());
            return false;
        }else{
            $result = $model->save();
            return $result;
        }

    }

    /**查找物流信息
     * @param $id
     * @return bool
     */
    public static function find($id){
        $model = M("BaseLogistics");
        $company_id = $_SESSION['user_info']['companyid'];
        $map['id'] = $id;
        $map['company_id'] = $company_id;
        $model->where($map)->find();
        //读取当前数据对象
        $result = $model->data();
        if($result){
            return $result;
        }else{
            return false;
        }
    }
    /**删除物流信息
     * @param $id
     * @return mixed
     */
    public static function del($id){
        $model = M("BaseLogistics");
        $company_id = $_SESSION['user_info']['companyid'];
        $map['id'] = $id;
        $map['company_id'] = $company_id;
        return $model->where($map)->delete();
    }

    /**显示信息
     * @return bool
     */
    public static function listLogistics(){
        $model = M("BaseLogistics");
        $company_id = $_SESSION['user_info']['companyid'];
        $map['company_id'] = $company_id;
        $fields = "id,qrcode_pack_s,qrcode_pack_e,logistics,expresslist,logistics_time";
        $result = $model->field($fields)->where($map)->select();
        if($result){
            return $result;
        }else{
            return false;
        }
    }
    /**验证规则
     * @return array
     */
    static function checkPackCode()
    {
        $company_id = $_SESSION['user_info']['companyid'];
        $rules = array(
            array('company_id', 'require', '企业ID不能为空'),
            array('qrcode_pack_s', '/' . $company_id . '0\d{9}/', '箱码起始码不符合规范'),
            array('qrcode_pack_e', '/' . $company_id . '0\d{9}/', '箱码结束码不符合规范'),
            array('logistics', 'require', '物流公司不能为空'),
            array('expresslist', 'require', '发货单不能为空'),
            array('logistics', 'require', '发货时间不能为空')
        );
        return $rules;
    }

    /**验证箱码是否重复
     * @param $start 起始码号
     * @param int $end 结束码号
     * @return mixed
     */
    static function checkPackCodeRepeate($start, $end = 0)
    {
        if (empty($end)) {
            $end = $start;
        }
        $model = M("BaseLogistics");
        $company_id = $_SESSION['user_info']['companyid'];
        $sql = 'SELECT `id` FROM `zm_base_logistics` WHERE ((`qrcode_pack_s` <= ' . $start . ' AND `qrcode_pack_e` >= ' . $start . ') OR (`qrcode_pack_s` <= ' . $end . ' AND `qrcode_pack_e` >= ' . $end . ') OR (`qrcode_pack_s` >= ' . $start . ' AND `qrcode_pack_s` <= ' . $end . ') OR (`qrcode_pack_e` >= ' . $start . ' AND `qrcode_pack_e` <= ' . $end . ')) AND `company_id` ='.$company_id;
        $rows = $model->query($sql);
        return $rows;
    }
}