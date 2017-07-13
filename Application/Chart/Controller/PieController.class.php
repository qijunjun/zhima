<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:08
 */
namespace Chart\Controller;

use Common\Controller\BaseController;
use Chart\API\ChartAPI;
use Think\Controller;

//use Chart\API\CompanyAPI;
class PieController extends BaseController
{

    public function index($statis = 'product', $startdate = 0, $enddate = 0)
    {
        $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
        if (($companyid !== '') && ($companyid !== null))
        {
            switch ($statis)
            {
                case "product":
                    //产品用码数量
                    self::buildStatisProduct($companyid, $startdate, $enddate);
                    break;
                case "qrcode":
                    //用码量图

                    //用码数量
                    self::buildStatisQrcode($companyid, $startdate, $enddate);
                    break;
                default:
                    //厂家用码数量
                    //buildStatisProduct($mysqli,$companyid, $startdate,$enddate );
                    break;
            }
        }
        else
        {
            json("002");

            return;
        }

    }

//        public function ip()
//        {
//            $model = D("base_scan_log");
//            $modelMysqlMap = array(
//                'ipaddr' => array('exp', ' is NULL')
//            );
//            $rows = $model->field("ip")->where($modelMysqlMap)->select();
//            //var_dump($rows);
//            //return;
//            foreach ($rows as $value)
//            {
//                $iparr = explode(",", $value["ip"]);
//                $arr[] = $iparr[0];
////                foreach($iparr as $ip){
////                    $arr[]=trim($ip);
////                }
//            }
//            //var_dump($arr);
//
//            set_time_limit(0);
//            foreach ($arr as $ip)
//            {
//                $api = A('Code/Api');
//                $address = $api::iplookup($ip);
//                var_dump($address);
//            }
//        }

    /**
     * 生成产品用码数量饼图数据
     *
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function buildStatisProduct($companyid, $startdate, $enddate)
    {
        $model = M();
        $sql = "select `zm_base_product`.`productname` AS `productname`,sum((`zm_corr_qrcode_product`.`qrcode_range_e` - `zm_corr_qrcode_product`.`qrcode_range_s` + 1)) AS `counts` from (`zm_corr_qrcode_product` left join `zm_base_product` on `zm_corr_qrcode_product`.`product_id` = `zm_base_product`.`productid`) where `zm_corr_qrcode_product`.`company_id` = " . $companyid . " " . ChartAPI::buildProductDateScop($startdate, $enddate) . " group by `zm_corr_qrcode_product`.`product_id` having (`counts` > 0) ORDER BY counts desc";
        $rows = $model->query($sql);
        foreach ($rows as $data)
        {
            $legenddata[] = $data['productname'];
            $seriesdata[] = array("value" => $data['counts'], "name" => $data['productname']);
        }

        $title = array("text" => "产品用码数量统计", "subtext" => ChartAPI::buildDateTitle($startdate, $enddate), "left" => "center");
        $tooltip = array("trigger" => "item", "formatter" => "{a} <br/>{b} : {c} ({d}%)");
        $toolbox = array("show" => true, "left" => "85%", "feature" => array("saveAsImage" => array("title" => "保存为图片")));
        $legend = array("orient" => "vertical", "left" => "left", "data" => $legenddata);
        $series[] = array("name" => "产品用码数量", "type" => "pie", "radius" => "50%", "z" => 5, "center" => array("60%", "66%"), "data" => $seriesdata);

        $option = array("title" => $title, "tooltip" => $tooltip, "toolbox" => $toolbox, "legend" => $legend, "series" => $series);
        echo json("001", $option);
    }

    /**
     * 总用码量饼图
     *
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function buildStatisQrcode($companyid, $startdate, $enddate)
    {
        $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
        $rows[] = array("value" => ChartAPI::getQrcodeUsed($companyid), "name" => "已使用");
        $rows[] = array("value" => ChartAPI::getQrcodeLeft($companyid), "name" => "未使用");

        foreach ($rows as $data)
        {
            $legenddata[] = $data['name'];
            $seriesdata[] = array("value" => $data['value'], "name" => $data['name']);
        }

        $title = array("text" => "用码数量统计", "subtext" => ChartAPI::buildDateTitle($startdate, $enddate), "left" => "center");
        $tooltip = array("trigger" => "item", "formatter" => "{a} <br/>{b} : {c} ({d}%)");
        $toolbox = array("show" => true, "left" => "85%", "feature" => array("saveAsImage" => array("title" => "保存为图片")));
        $legend = array("orient" => "vertical", "left" => "left", "data" => $legenddata);
        $series[] = array("name" => "产品用码数量", "type" => "pie", "radius" => "50%", "z" => 5, "center" => array("60%", "66%"), "data" => $seriesdata);

        $option = array("title" => $title, "tooltip" => $tooltip, "toolbox" => $toolbox, "legend" => $legend, "series" => $series);
        echo json("001", $option);

    }

}