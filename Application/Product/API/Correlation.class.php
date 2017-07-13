<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/29
 * Time: 12:49
 */


namespace Product\API;

/**
 * 产品与质量码关联
 * Class Correlation
 * @package Product\API
 */
class Correlation {
    /**
     * 通过质量码查找产品
     *
     * @param float $b 质量码
     *
     * @return bool|int 产品ID
     */
    public static function getProduct($b) {
        $model = M('corr_qrcode_product');
        $result = $model->where([
            'company_id' => session('user_info')['companyid'],
            'qrcode_range_s' => ['elt', $b],
            'qrcode_range_e' => ['egt', $b],
            'status' => 1
        ])->select();
        if($result === false || count($result) == 0) {
            return false;
        } else {
            return intval($result[0]['id']);
        }
    }
    /*
     * 判断码段是否已经关联到产品,已经关联返回true
     */
    public static function existCorrelation($qStart,$qEnd){
        $result=false;
        $companyid=session('user_info')['companyid'];
        $model = M('corr_qrcode_product');
        $qrcodeCorr=$model->where([
            'company_id' => session('user_info')['companyid']
        ])->field('qrcode_range_s,qrcode_range_e')->select(); //step1:得到当前公司所有关联码段
        for($i=0;$i<count($qrcodeCorr);$i++){
            $start=$qrcodeCorr[$i]['qrcode_range_s'];
            $end=$qrcodeCorr[$i]['qrcode_range_e'];
            if(($qStart>$end)||($qEnd<$start))
            {
                continue;
            }
            else
            {
                $result=true;
                break;
            }
        }
        return $result;
    }

    /*
     * 查找码段关联的产品
     * 成功返回产品id,否则返回false
     */
    public static function getProductbyRange($qStart,$qEnd)
    {
        $company_id=session('user_info')['companyid'];
        $model = M();
        $sql = 'select product_id from zm_corr_qrcode_product where(qrcode_range_s <='.$qStart.'  and  qrcode_range_e >='.$qEnd.' ) and company_id='.$company_id;
        $result = $model->query($sql);
        if($result)
        {
            return $result[0]['product_id'];
        }
        else
        {
            return false;
        }

    }
}
