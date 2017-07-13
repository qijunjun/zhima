<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/7/28
 * Time: 17:48
 */

namespace Reviews\Controller;

use Think\Controller;
use Reviews\API\ReviewsAPI;

class ReviewsController extends Controller
{
    public function reviewsInfo()
    {
        $result = ReviewsAPI::reviewsInfo();
        json("001", $result);
    }

    public function updateReviews()
    {

        $qualitygoods = I('qualitygoods', 0, 'htmlspecialchars');
        $reviews = I('reviews', '', 'htmlspecialchars');

        if (empty($qualitygoods)) {
            json("009", null, "评价分数不能为空");
            return;
        }
        if (empty($reviews)) {
            json("009", null, "评价内容不能为空");
            return;
        }

        $data["quality_goods"] = $qualitygoods;
        $data["reviews"] = $reviews;

        $result = ReviewsAPI::updateReviews($data);

        if (!empty($result)) {
            json("001", $result);
        } else {
            json("002", null, "该商品已经评价过了");
        }
    }

}