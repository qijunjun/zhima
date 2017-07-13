<?php
/**
 * Created by PhpStorm.
 * User: Liming
 * Date: 2016/4/29
 * Time: 12:19
 */


namespace Product\Controller;

use Common\Controller\BaseController;
use Product\API\Correlation;

/**
 * 产品与质量码关联
 * Class CorrelationController
 * @package Product\Controller
 */
class CorrelationController extends BaseController {
    /**
     * 添加码段关联
     *
     * @param int   $productId 产品ID
     * @param float $bStart    起始码段
     * @param float $bEnd      终止码段
     */
    public function add($productId, $bStart, $bEnd) {
        if($productId==null)
        {
            json("002",null,'请选择产品');
            return;
        }
        $productId = intval($productId);
        $bStart = floatval($bStart);
        $bEnd = floatval($bEnd);
        $productId = intval($productId);
        //TODO: 输入检查
        if($bEnd<$bStart){
            json('002',null,'请检查输入的码段');
            return;
        }
        $companyid=session('user_info')['companyid'];
        if(!checkQCode($companyid,$bStart,$bEnd)){
            json('002',null,'请检查输入的码段');
            return;
        }
        if(Correlation::existCorrelation($bStart,$bEnd)){
            json('002',null,'码段已被关联!');
            return;
        }
        if(!is_exist_b($bStart)||!is_exist_b($bEnd))
        {
            json('002',null,'码还没生成,逗我呢?');
            return;
        }
        $model = M('corr_qrcode_product');
        if(!$model->create([
            'company_id'     => $companyid,
            'product_id'     => $productId,
            'qrcode_range_s' => $bStart,
            'qrcode_range_e' => $bEnd,
            'create_time'    => time(),
            'status'         => 1
        ])
        ) {
            json("009");
        }
        $result = $model->add();
        if($result === false) {
            json("007");
        } else {
            json("001", $result);
        }
    }

    /**
     * 删除码段关联
     *
     * @param int $id 关联记录ID
     */
    public function remove($id) {
        $model = M('corr_qrcode_product');
        $result = $model->where(['id' => $id, 'company_id' => session('user_info')['companyid']])->delete();
        if($result === false) {
            json("009");
        } else {
            json("001", $result);
        }
    }

    /**
     * 获取关联记录
     */
    public function get() {
        $model = M('corr_qrcode_product');
        $result = $model
            ->join([
                'LEFT JOIN __BASE_PRODUCT__ ON __CORR_QRCODE_PRODUCT__.product_id=__BASE_PRODUCT__.productid'
            ])
            ->field('zm_corr_qrcode_product.*, zm_base_product.productname,zm_base_product.productimage,zm_base_product.guige')
            ->where(['company_id' => session('user_info')['companyid']])
            ->select();
        if($result === false) {
            json("007");
        } else {
            json("001", $result);
        }
    }
    
    
}
