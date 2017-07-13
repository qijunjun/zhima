<?php
namespace Marketing\API;
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/25
 * Time: 上午12:01
 */
class MarketingAPI{

    public  static function addConfig($data)
    {
        $model=M('base_promotion');

        //检查箱码输入是否有效
        if(count($data)<1){
            json('002',null,'没有数据');
            return;
        }
        $response=$model->add($data);
        if($response){
            return $response;
        }else{
            return "插入失败";
        }

    }
    public static function addCorrData($data){
        $model=M('corr_qrcode_promotion');

        //检查箱码输入是否有效
        if(count($data)<1){
            json('002',null,'没有数据');
            return;
        }
        $response=$model->add($data);
        if($response){
            return $response;
        }else{
            return "插入失败";
        }
    }
    /*
     * 列出厂家的活动
     */
    public static function listPromotion($companyid)
    {
        $model=M();
        $sql="SELECT promotion.*, qrcode_range_s, qrcode_range_e,id
					FROM zm_corr_qrcode_promotion,
					(
						SELECT *
						FROM zm_base_promotion
						WHERE zm_base_promotion.companyid = ". $companyid ."
					) AS promotion
					WHERE zm_corr_qrcode_promotion.promotionid = promotion.promotionid
                    ORDER BY promotionid DESC
            ";
        $data = $model -> query($sql);
        return $data;

    }
    public static function listPromotionbyid($id){
        $model=M();
        $sql="SELECT promotion.*, corr.qrcode_range_s, corr.qrcode_range_e, corr.id
					FROM zm_base_promotion as promotion,
					(
						SELECT *
						FROM zm_corr_qrcode_promotion
						WHERE zm_corr_qrcode_promotion.promotionid = " . $id . "
					) AS corr
					WHERE promotion.promotionid = " . $id . "
                    ORDER BY promotionid DESC
            ";
        $data = $model -> query($sql);
        return $data;
    }
    public static function listRecordByPromotion($filter)
    {
        $model=M('base_hongbao');
        $data= $model->field('id,b,mobile,customer,longitude,latitude,total_amount,create_time,status')->where($filter)->select();
        $sendStatus=array("空红包", "已发放待领取", "等待发送", "发放中", "发放失败", "已领取", "已退款", "不足1元等待发送");
        foreach ($data as &$value)
        {
            $value['status']=$sendStatus[$value['status']];
        }
        return $data;
    }
    public static function updateConfig($data1,$data2){
        $model1=M('base_promotion');
        $model2=M('corr_qrcode_promotion');
        $model1->startTrans(); //开启事务
        $result1=$model1->save($data1);
        $result2=$model2->save($data2);
        if(($result1!==false)&&($result2!==false)){
            $model1->commit();
            return true;
        }else{
            $model1->rollback();
            return false;
        }
    }
    /*
     * 根据id删除活动
     */
    public static function deletePromotionbyid($id){
        try
        {
            $model=M('base_promotion');
            $cond=array(
                'promotionid'=>$id
            );
            $result=$model->where($cond)->delete();
        }
        catch (Exception $er){
            $result="该活动已被使用,无法删除!";
        }
        return $result;

    }

    /**获取红包活动名称
     * @param $filter
     * @return mixed
     */
    public static function actname($filter){
        $model = M("BasePromotion");
        $data = $model->where($filter)->getField('promotion_name');
        return $data;
    }
}