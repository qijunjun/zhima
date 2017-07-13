<?php
/**
 * Created by PhpStorm.
 * User: yumin
 * Date: 2016/7/29
 * Time: 15:43
 */

namespace Reviews\Controller;

use Common\Controller\BaseController;
use Reviews\API\ReviewsAPI;
use Reviews\Utility\ReviewsUtility;
use \Think\Controller;

class ReviewsManagerController extends BaseController
{
    public function clearReviews()
    {
        $id = I('id', 0, 'htmlspecialchars');
        if (empty($id)) {
            json("009", null, "评价id不可为空");
            return;
        }
        $result = ReviewsAPI::clearReviews($id);
        json("001", $result);
    }

    public function reviewsInfo($code = null)
    {
        if (empty($code)) {
            $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
            $result = ReviewsUtility::allReviews($companyid);
            json("001", $result);
        } else {
            $result = ReviewsUtility::reviewsInfo($code);
            json("001", $result);
        }
    }

    public function productReviews($productid)
    {
        if (empty($productid))
            $productid = I('productid', 0, 'htmlspecialchars');

        if (empty($productid)) {
            json("009");
            return;
        }

        $result = ReviewsUtility::productReviews($productid);
        json("001", $result);
    }

    /**测试使用array_merge得到结果，显示某类产品的评价信息201687
     * @param $productid
     */
    public function ces($productid){
        if (empty($productid))
            $productid = I('productid', 0, 'htmlspecialchars');

        if (empty($productid)) {
            json("009");
            return;
        }
        $result = ReviewsUtility::ces($productid);
        json("001", $result);
    }

    /**获取红包活动产品名称201687
     * @param int $productid
     */
    public function productact($productid){
        if (empty($productid))
            $productid = I('productid', 0, 'htmlspecialchars');

        if (empty($productid)) {
            json("009");
            return;
        }
        $res = ReviewsUtility::productact($productid);
        $result = implode(",",$res);
        json("001", $result);
    }
}