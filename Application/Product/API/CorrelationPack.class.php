<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/5/6
 * Time: 13:14
 */


namespace Product\API;

class CorrelationPack {
    /**检验箱码是否合法201689
     * @param $qrcode
     * @return int
     */
    public static function checkCompany($qrcode){
        $companyid = $_SESSION['user_info']['companyid'];
        $result = preg_match('/^'.$companyid.'0\d{9}$/',$qrcode);
        return $result;
    }
    /**检验输入的箱码是否已有关联201689
     * @param $qrcode_pack
     */
    public static function checkRepeat($qrcode_pack){
        $model = M("CorrQrcodePack");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            "company_id" => $companyid,
            "qrcode_pack" => $qrcode_pack
        ];
        $result = $model->where($map)->find();

        return $result;


    }

    /**检验输入的质量码是否已有关联201689
     * @param $start
     * @param $end
     * @return array|void
     */
    public static function checkRepeatQrcode($start ,$end ){
        if(empty($end)){
            $end = $start;
        }
        if($start > $end){
            json("002",null,"起始质量码不能大于结束质量码");
        }
        $model = M();
        $companyid = $_SESSION['user_info']['companyid'];
        $sql = 'select id from zm_corr_qrcode_pack where((qrcode_range_s <='.$start.'  and  qrcode_range_e >='.$start.' ) or (qrcode_range_s <='.$end.' and qrcode_range_e >='.$end.' ) or (qrcode_range_s >='.$start.' and qrcode_range_s <='.$end.' ) or (qrcode_range_e >='.$start.' and qrcode_range_e <='.$end.' )) and company_id='.$companyid;

        $result = $model->query($sql);

        return $result;

    }
    /**检查箱码是否已生成2016811
     * @param $qrcode
     * @return mixed
     */
    public static function checkQrcodeUsed($qrcode){
        $model = M("BaseQrcodeUsedLog");
        $companyid = $_SESSION['user_info']['companyid'];
        $map = [
            'companyid' => $companyid,
            'qrcode_s' => array('elt',$qrcode),
            'qrcode_e' => array('egt',$qrcode)
        ];
        $result = $model->where($map)->select();

        return $result;
    }

    /**检查质量码是否已生成2016811
     * @param $start
     * @param $end
     * @return mixed
     */
    public static function checkQrcodeUseds($start,$end){
        $model = M("BaseQrcodeUsedLog");
        $companyid = $_SESSION['user_info']['companyid'];

        if(empty($end)){
            $end = $start;
        }
        if($start > $end){
            json("002",null,"起始质量码不能大于结束质量码");
        }

        $sql = "select id from zm_base_qrcode_used_log where((qrcode_s <=".$start." and qrcode_e>=".$start.") and (qrcode_s<=".$end." and qrcode_e>=".$end.")) and companyid=".$companyid;
        $result = $model->query($sql);

        return $result;
    }

}
