<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/7/28
 * Time: 17:53
 */

namespace Reviews\Utility;


class ReviewsUtility
{
    /**
     * 显示某个码的评价信息
     * @param $code
     *
     * @return mixed
     */
    public static function reviewsInfo($code)
    {
        $scan_info = M("BaseScaninfo");
        $fields = "id,b,quality_goods,reviews,reviews_time,reviews_ipaddr,reviews_address";
        $map = array(
            "b" => $code
        );
        $result = $scan_info->field($fields)->where($map)->find();

        return $result;
    }

    /**
     * 显示某类产品的评价信息
     * @param $productid
     *
     * @return mixed
     */
    public static function productReviews($productid)
    {
        $model = M();
        $sql = 'select id,b,quality_goods,reviews,reviews_time,reviews_ipaddr,reviews_address from zm_base_scaninfo where productid='.$productid.' and reviews_time IS NOT NULL order by reviews_time desc';
        $result = $model->query($sql);

        return $result;
    }

    public static function allReviews($companyid)
    {
        $scan_info = M("BaseScaninfo");
        $fields = "id,b,productid,quality_goods,reviews,reviews_time,reviews_ipaddr,reviews_address";
        $map = array(
            "companyid" => $companyid
        );
        $result = $scan_info->field($fields)->where($map)->select();

        return $result;
    }

    public static function updateReviews($data, $companyid, $b)
    {
        $model = M();
        $sql = "UPDATE `zm_base_scaninfo` SET `quality_goods`=" . $data["quality_goods"] . ",`reviews`='" . $data["reviews"] . "',`reviews_ipaddr`=`recent_ipaddr`,`reviews_address`=`recent_address`,`reviews_time`=" . time() . " WHERE `companyid` = " . $companyid . " AND `b` = " . $b . " AND  `reviews_time` IS NULL ";
        $result = $model->execute($sql);

        return $result;
    }

    public static function clearReviews($data, $companyid, $id)
    {

        $model = M('BaseScaninfo');
        $map['companyid'] = $companyid;
        $map['id'] = $id;

        if (!$model->where($map)->create($data)) {
            json("009", null, $model->getError());

            return false;
        }

        $result = $model->save();

        return $result;
    }

    /**测试使用array_merge得到结果，显示某类产品的评价信息201687
     * @param $productid
     * @return array
     */
    public static function ces($productid){
        $product = M("BaseProduct");
        $scan = M("BaseScaninfo");
        $map = array(
            "productid" => $productid
        );
        $fields = "id,b,quality_goods,reviews,reviews_time,reviews_ipaddr,reviews_address";
        $scans = $scan->where($map)->where('reviews_time IS NOT NULL')->field($fields)->order('reviews_time desc')->select();

        $where = array(
            "productid" => $productid
        );
        $field = "productname,guige";
        $products = $product->where($where)->field($field)->find();

        foreach($scans as $scaninfo){
            $item = array_merge($scaninfo,$products);
            $result[] = $item;
        }
        return $result;
    }

    /**获得红包产品活动名称201687
     * @param $productid
     * @return mixed
     */
    public static function productact($productid){
        $product = M("BaseProduct");
        $where = array(
            "productid" => $productid
        );
        $field = "productname,guige";
        $products = $product->where($where)->field($field)->find();

        return $products;
    }

}