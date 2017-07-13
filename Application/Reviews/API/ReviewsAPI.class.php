<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/7/28
 * Time: 17:46
 */

namespace Reviews\API;

use Reviews\Utility\ReviewsUtility;

class ReviewsAPI
{
    public static function reviewsInfo()
    {
        $code = $_SESSION["code_info"]["b"];

//        $company_id = $_SESSION["code_info"]["company_id"];

        return ReviewsUtility::reviewsInfo($code);
    }

    public static function updateReviews($data)
    {
        $code = $_SESSION["code_info"]["b"];
        $company_id = $_SESSION["code_info"]["company_id"];

        return ReviewsUtility::updateReviews($data, $company_id, $code);


    }

    public static function clearReviews($id)
    {
        $companyid = $_SESSION["user_info"]["companyid"];//获取企业id

        $data["quality_goods"] = null;
        $data["reviews"] = null;
        $data["reviews_time"] = null;
        $data["reviews_ipaddr"] = null;
        $data["reviews_address"] = null;

        return ReviewsUtility::clearReviews($data, $companyid, $id);

    }
}