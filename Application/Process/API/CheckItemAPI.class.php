<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/17
 * Time: 14:09
 */

namespace Process\API;


class CheckItemAPI
{
    /**新增检测记录2016818
     * @param $data
     * @return bool
     */
    public static function addCheckItem($data){
        $model = M("BaseInspectionReport");
        if(!$model->create($data)){
            json("009",$model->getError());
            return false;
        }
        $id = $model->add();
        return $id;
    }

    /**新增检测图片2016818
     * @param $data1
     * @return bool
     */
    public static function addCheckImage($productid,$data1){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        if(!$model->create($data1)){
            json("009",$model->getError());
            return false;
        }
        $map = [
            'companyid' => $companyid,
            'productid' => $productid
        ];
        $re[] = $model->where($map)->save();

        return $re;
    }

    /**显示信息列表2016818
     * @param $companyid
     * @return bool
     */
    public static function showList($companyid){
        $model = M("BaseInspectionReport");
        $fields = 'zm_base_product.productname,zm_base_product.guige,zm_base_inspection_report.id,zm_base_inspection_report.companyid,zm_base_inspection_report.productid,inspectionname,zm_base_inspection_report.create_time,inspectionitem,institution,attachment1,attachment2,attachment3,attachment4,attachment5';
        $map = [
            'zm_base_inspection_report.companyid' => $companyid
        ];
        $res = $model->field($fields)->where($map)->join('zm_base_product ON zm_base_product.productid = zm_base_inspection_report.productid')->select();

        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**删除2016818
     * @param $id
     * @return mixed
     */
    public static function del($id){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'id' => $id,
            'companyid' => $companyid
        ];
        $re = $model->where($map)->delete();
        return $re;
    }

    /**编辑2016818
     * @param $id
     * @return mixed
     */
    public static function edit($id){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'id' => $id,
            'companyid' => $companyid
        ];
        $re = $model->where($map)->find();
        return $re;
    }

    /**更新检测信息2016818
     * @param $id
     * @param $data
     * @return mixed
     */
    public static function update($id,$data){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'id' => $id,
            'companyid' => $companyid,
//            'productid' => $productid
        ];
        $re = $model->where($map)->save($data);
        return $re;
    }

    /**更新检测信息图片
     * @param $id
     * @param $productid
     * @param $name
     * @param $data1
     * @return array|bool
     */
    public static function updateCheckImage($id,$productid,$name,$data1){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        if(!$model->create($data1)){
            json("009",$model->getError());
            return false;
        }
        $map = [
            'companyid' => $companyid,
            'productid' => $productid,
            'id' => $id,
            'inspectionname' => $name
        ];
        $re[] = $model->where($map)->save();
        return $re;
    }

    /**检查检测名称是否重复2016818
     * @param $name
     * @return mixed
     */
    public static function checkRepeat($name){
        $model = M("BaseInspectionReport");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid,
            'inspectionname' => $name
        ];
        $re = $model->where($map)->find();
        if($re){
            return $re;
        }else{
            return false;
        }
    }

    /**检查产品与公司是否匹配2016818
     * @param $productid
     * @return bool
     */
    public static function checkProduct($productid){
        $model = M("BaseProduct");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid,
            'productid' => $productid
        ];
        $re = $model->where($map)->select();
        if($re){
            return $re;
        }else{
            return false;
        }
    }

    /*
     * 检查是否重复关联检查信息
     */
    public static function checkItemRepeat($start, $end = 0)
    {
        if (empty($end))
        {
            $end = $start;
        }
        $model=M();
        $companyid = $_SESSION["user_info"]["companyid"];
        $sql = 'SELECT `id` FROM `zm_corr_qrcode_inspection_report` WHERE ((`qrcode_range_s` <= ' . $start . ' AND `qrcode_range_e` >= ' . $start . ') OR (`qrcode_range_s` <= ' . $end . ' AND `qrcode_range_e` >= ' . $end . ') OR (`qrcode_range_s` >= ' . $start . ' AND `qrcode_range_s` <= ' . $end . ') OR (`qrcode_range_e` >= ' . $start . ' AND `qrcode_range_e` <= ' . $end . ')) AND `company_id` = ' . $companyid;
        $rows = $model->query($sql);
        
        return $rows;
    }

    public static function addCorrCheck($data)
    {
        $model=M("corr_qrcode_inspection_report");
        if (!$model->create($data))
        {
            json("009", null, $model->getError());
            return false;
        }
        $id = $model->add();
        return $id;
    }

    public static function listCheckItems($companyid){
        if(!is_numeric($companyid))
        {
            return false;
        }
        $model = M("base_inspection_report");
        $cond=array(
            'companyid'=>$companyid
        );
        $fields="id,inspectionname,productid";
        $res = $model->field($fields)->where($cond)->select();
        return $res;
    }

    public static function listCorrAll($companyid)
    {
        if(!is_numeric($companyid))
        {
            return false;
        }
        $model=M();
        $sql='
         SELECT zm_corr_qrcode_inspection_report.company_id, 
            zm_corr_qrcode_inspection_report.inspection_id, 
            zm_corr_qrcode_inspection_report.id, 
            zm_base_product.productname, 
            zm_corr_qrcode_inspection_report.qrcode_range_s, 
            zm_corr_qrcode_inspection_report.qrcode_range_e, 
            zm_base_inspection_report.inspectionname, 
            zm_corr_qrcode_inspection_report.create_time, 
            zm_base_product.guige
            FROM zm_corr_qrcode_inspection_report 
            INNER JOIN zm_base_inspection_report ON zm_corr_qrcode_inspection_report.inspection_id = zm_base_inspection_report.id
	        INNER JOIN zm_base_product ON zm_base_inspection_report.productid = zm_base_product.productid
            WHERE zm_corr_qrcode_inspection_report.company_id='.$companyid;
        $result=$model->query($sql);
        return $result;
    }

    public static function delCorrCheck($corrid)
    {
        if(!is_numeric($corrid))
        {
            return false;
        }
        $model=M('corr_qrcode_inspection_report');
        $cond=array(
            'id'=>$corrid
        );
        $result=$model->where($cond)->delete();
        return $result;
    }
}