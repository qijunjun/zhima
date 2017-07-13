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
class BarController extends BaseController
{

    public function index($statis = 'product', $startdate = 0, $enddate = 0)
    {
        $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
        if (($companyid !== '') && ($companyid !== null))
        {
            switch ($statis)
            {
                case "product":
                    //产品用码量趋势图
                    self::buildStatisProduct($companyid, $startdate, $enddate);
                    break;
                case "qrcode":
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

    /**
     * 生成产品用码数量趋势柱状图数据
     *
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function buildStatisProduct($companyid, $startdate, $enddate)
    {
        $model = M();
        $sql = "select date_format(from_unixtime(`zm_corr_qrcode_product`.`create_time`),'%Y-%m-%d') AS `create_date`,`zm_base_product`.`productname` AS `productname`,sum((`zm_corr_qrcode_product`.`qrcode_range_e` - `zm_corr_qrcode_product`.`qrcode_range_s` + 1)) AS `counts` from (`zm_corr_qrcode_product` left join `zm_base_product` on `zm_corr_qrcode_product`.`product_id` = `zm_base_product`.`productid`) where `zm_corr_qrcode_product`.`company_id` = " . $companyid . " " . ChartAPI::buildProductDateScop($startdate, $enddate) . " group by `create_date`,`zm_base_product`.`productid` having `counts` > 0";
        $rows = $model->query($sql);

        $legenddata = [];
        $categories = [];
        foreach ($rows as $data)
        {
            if (!in_array($data['productname'], $legenddata))
            {
                $legenddata[] = $data['productname'];
            }
            if (!in_array($data['create_date'], $categories))
            {
                $categories[] = $data['create_date'];
            }
            $datacollection[] = array("counts" => $data['counts'], "name" => $data['productname'], "date" => $data['create_date']);
        }

        $series = self::buildSeries($datacollection, $legenddata, $categories, true);

        $left = 5;
        $top = 10;
        $bottom = 15;
        $width = 80;

        $title = array("text" => "用码量统计" . ChartAPI::buildDateTitle($startdate, $enddate), "left" => "center", "top" => (100 - $bottom / 2) . "%");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $toolbox = array("show" => true, "left" => ($left + $width - 1) . "%", "feature" => array("saveAsImage" => array("title" => "保存为图片")));
        $legend = array("data" => $legenddata);
        $xAxis = array("type" => "category", "axisLabel" => array("margin" => 12), "data" => $categories);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "top" => $top . '%', "width" => $width . '%', "height" => (100 - $top - $bottom) . '%');
        $option = array("title" => $title, "tooltip" => $tooltip, "toolbox" => $toolbox, "legend" => $legend, "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }

    static function buildStatisQrcode($companyid, $startdate, $enddate)
    {
        $model = M();
        $sql = "select date_format(from_unixtime(`zm_base_qrcode_used_log`.`operate_time`),'%Y-%m-%d') AS `operate_date`,sum(`zm_base_qrcode_used_log`.`qrcode_counts`) AS `counts`,if((`zm_base_qrcode_used_log`.`flag` = 0),'箱码',if((`zm_base_qrcode_used_log`.`flag` = 1),'质量码','关联的箱码和质量码')) AS `title` from `zm_base_qrcode_used_log` where `zm_base_qrcode_used_log`.`companyid` = " . $companyid . " " . ChartAPI::buildProductDateScop($startdate, $enddate) . " group by `operate_date`,`zm_base_qrcode_used_log`.`flag` order by `operate_date`";

        $rows = $model->query($sql);
        $legenddata = [];
        $categories = [];
        foreach ($rows as $data)
        {
            if (!in_array($data['title'], $legenddata))
            {
                $legenddata[] = $data['title'];
            }
            if (!in_array($data['operate_date'], $categories))
            {
                $categories[] = $data['operate_date'];
            }
            $datacollection[] = array("counts" => $data['counts'], "name" => $data['title'], "date" => $data['operate_date']);
        }

        $series = self::buildSeries($datacollection, $legenddata, $categories, true);

        $left = 5;
        $top = 10;
        $bottom = 15;
        $width = 80;

        $title = array("text" => "生码量统计" . ChartAPI::buildDateTitle($startdate, $enddate), "left" => "center", "top" => (100 - $bottom / 2) . "%");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $toolbox = array("show" => true, "left" => ($left + $width - 1) . "%", "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar','stack','tiled'))));
        $legend = array("data" => $legenddata);
        $xAxis = array("type" => "category", "axisLabel" => array("margin" => 12), "data" => $categories);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "top" => $top . '%', "width" => $width . '%', "height" => (100 - $top - $bottom) . '%');
        $option = array("title" => $title, "tooltip" => $tooltip, "toolbox" => $toolbox, "legend" => $legend, "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }


    /**
     * 获取柱状图的数据
     *
     * @param $datacollection
     * @param $legenddata
     * @param $categories
     *
     * @return array
     */
    static function buildSeries($datacollection, $legenddata, $categories, $stack = false)
    {
        foreach ($legenddata as $name)
        {
            unset($seriesdata);
            foreach ($categories as $date)
            {
                $seriesdata[] = self::getDateCounts($datacollection, $name, $date);
            }
            if ($stack)
            {
                $series[] = array("name" => $name, "type" => "bar", "stack" => "数量", "data" => $seriesdata, 'barWidth' => '80%');
            }
            else
            {
                $series[] = array("name" => $name, "type" => "bar", "data" => $seriesdata,'barWidth' => '80%');
            }
        }

        return $series;
    }

    /**
     * 获取特定名称，特定时间的数据数量
     *
     * @param $rows
     * @param $name
     * @param $date
     *
     * @return int
     */
    static function getDateCounts($rows, $name, $date)
    {
        $taskcounts = 0;
        foreach ($rows as $row)
        {
            if ($row['date'] == $date && $row['name'] == $name)
            {
                return $row['counts'];
            }
        }

        return $taskcounts;
    }

}