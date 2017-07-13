<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/9/5
 * Time: 10:17
 */

namespace Company\Controller;

use Common\Controller\BaseController;
use Company\API\ChartAPI;
use Statistic\API\GovernmentAPI;
use Statistic\API\CompanyAPI;

class ChartdataController extends BaseController
{
    private $_companyid;
    public function _initialize(){
        parent::_initialize();
        $this->_companyid = session('user_info.companyid');
//        $this->_companyid = 1019;
    }
    //出入库统计柱图201698
    public function check(){
        $companyid = $this->_companyid;
        $result = GovernmentAPI::check($companyid);

        $checkindata = [];
        $checkoutdata =[];
        $xAxisdata = [];
        foreach($result as $data) {
            $checkindata[] = $data['checkin'];
            $checkoutdata[] = $data['checkout'];
            $xAxisdata[] = $data['time'];
        }
        $title = array('text' => '出入库数量统计','left' => 'center');
        $tooltip = array('trigger' => 'axis',"axisPointer" => array("type" => "shadow"));
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar','stack','tiled'))));
        $legend = array('data' => ['出库量','入库量'],'top' => '30px','left' => '80px');
        $grid = array('top' => '90px');
        $xAxis = array('type' => 'category','data' =>$xAxisdata);
        $yAxis = array('type' => 'value');
        $series = array(array('name' => '出库量','type' => 'bar', 'barWidth' => '30%','data' => $checkoutdata),array('name' => '入库量','type' => 'bar','data' => $checkindata));

        $option = array('title' => $title, 'tooltip' => $tooltip, 'toolbox' => $toolbox, 'legend' => $legend, 'grid' => $grid, 'xAxis' => $xAxis, 'yAxis' => $yAxis, 'series' => $series);
        echo json('001',$option);
    }

    //出入库地区统计201698
    public function checkoutArea(){
        $companyid = $this->_companyid;
        $result = GovernmentAPI::checkoutArea($companyid);
        $info = ChartAPI::getCompanyInfo($companyid);

        foreach($result as $value){
            $new['center_longitude'] = $value['center_longitude'];
            $new['center_latitude'] = $value['center_latitude'];
            $new['agent_name'] = $value['agent_name'];
            $new['agent_id'] = $value['agent_id'];
            $new['count'] = $value['count'];
            $new['company_name'] = $info['name'];
            $new['company_longitude'] = $info['longitude'];
            $new['company_latitude'] = $info['latitude'];
            $res[] = $new;
        }

        if($res){
            json("001",$res,'success');
        }else{
            json("001");
        }
    }

    /**
     * 购码，生码，已关联总统计三环图201698
     */
    public function qrcode(){
        $companyid = $this->_companyid;
        $bought = CompanyAPI::getQrcodeBoughtCounts($companyid);
        $used = CompanyAPI::getQrcodeUsedCounts($companyid);
        $joined = CompanyAPI::getQrcodeJoinedCounts($companyid);

        if($bought == null){
            $bought = 0;
        }
        if($used == null){
            $used = 0;
        }
        if($joined == null){
            $joined = 0;
        }

        $result = [
            'bougth_count' => $bought,
            'used_count' => $used,
            'joined_count' => $joined
        ];

        $fillNum = $result['bougth_count'];
        $fillNumb = 0;
        if(($fillNum - $result['used_count']) > 0){
            $fillNumu = $fillNum - $result['used_count'];
        }else{
            $fillNumu = 0;
        }
        if(($fillNum - $result['joined_count']) > 0){
            $fillNumj = $fillNum - $result['joined_count'];
        }else{
            $fillNumj = 0;
        }

        $itemStyle = array('tooltip' => array('show' => false),'normal' => array('color' => 'rgba(0,0,0,0)', 'label' => array('show' => false), 'labelLine' => array('show' => false)), 'emphasis' => array('color' => 'rgba(0,0,0,0)'));
        $title = array('text' => '企业用码统计','left' => 'center');
        $tooltip = array('trigger' => 'item', 'formatter' => '{a} <br/>{b} : {c} ({d}%)');
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片")));
        $legend = array('orient' => 'vertical', 'x' => 'left', 'top' => '40px', 'data' => array('购码量','生码量','已关联码量'));
        $series = array(array('name' => '', 'type' => 'pie', 'center' => array('50%','55%'), 'radius' => array('55%','70%'), 'avoidLabelOverlap' => false, 'label' => array('normal' => array('show' => false, 'position' => 'center'),'emphasis' => array('show' => true, 'textStyle' => array('fontSize' => '18', 'fontWeight' => 'bold'))),'labelLine' => array('normal' => array('show' => false)),'data' => array(array('value' => $result['bougth_count'], 'name' => '购码量'),array('value' => $fillNumb, 'name' => '', 'itemStyle' => $itemStyle))),array('name' => '', 'type' => 'pie', 'center' => array('50%','55%'), 'radius' => array('40%','55%'), 'avoidLabelOverlap' => false, 'label' => array('normal' => array('show' => false, 'position' => 'center'),'emphasis' => array('show' => true, 'textStyle' => array('fontSize' => '18', 'fontWeight' => 'bold'))),'labelLine' => array('normal' => array('show' => false)),'data' => array(array('value' => $result['used_count'], 'name' => '生码量'),array('value' => $fillNumu, 'name' => '', 'itemStyle' => $itemStyle))),array('name' => '', 'type' => 'pie', 'center' => array('50%','55%'), 'radius' => array('25%','40%'), 'avoidLabelOverlap' => false, 'label' => array('normal' => array('show' => false, 'position' => 'center'),'emphasis' => array('show' => true, 'textStyle' => array('fontSize' => '18', 'fontWeight' => 'bold'))),'labelLine' => array('normal' => array('show' => false)),'data' => array(array('value' => $result['joined_count'], 'name' => '已关联码量'),array('value' => $fillNumj, 'name' => '', 'itemStyle' => $itemStyle))));
        $option = array('title' => $title, 'tooltip' => $tooltip, 'toolbox' => $toolbox, 'legend' => $legend, 'series' => $series);
        echo json('001',$option);
    }


    /**
     * 生码量按天统计柱图和折线图201698
     */
    public function usedQrcode(){
        $companyid = $this->_companyid;
        $used = ChartAPI::usedCounts($companyid);

        $seriesdata = [];
        $xAxisdata = [];
        foreach($used as $data){
            $seriesdata[] = $data['qrcode_used_counts'];
            $xAxisdata[] = $data['used_time'];
        }
        $left = 5;
        $right = 4;
        $bottom = 3;
        $color = ['#3398DB'];
        $title = array("text" => "生码量统计" , "left" => "center");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $legend = array('orient' => 'vertical','left' => '80px', 'top' => '10px', 'data' => array('生码数量'));
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar'))));
        $xAxis = array("type" => "category", "axisTick" => array("alignWithLabel" => true), "data" => $xAxisdata);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "right" => $right . '%', "bottom" => $bottom . '%', "containLabel" => true);
        $series = array('name' => '生码数量', 'type' => 'bar', 'barWidth' => '60%', 'data' => $seriesdata);
        $option = array('color' => $color, "title" => $title, 'legend' => $legend, "tooltip" => $tooltip, "toolbox" => $toolbox,  "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }

    /**
     * 用码量按天统计柱图和折线图
     */
    public function joinedQrcode(){
        $companyid = $this->_companyid;
        $productid = I('productid');
        $used = ChartAPI::projoinedCounts($companyid,$productid);

        $seriesdata = [];
        $xAxisdata = [];
        foreach($used as $data){
            $seriesdata[] = $data['qrcode_joined_counts'];
            $xAxisdata[] = $data['joined_time'];
        }
        $left = 5;
        $right = 4;
        $bottom = 3;
        $color = ['#3398DB'];
        $title = array("text" => "用码量统计图" , "left" => "center");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $legend = array('orient' => 'vertical','left' => '80px', 'top' => '10px', 'data' => array('用码数量'));
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar'))));
        $xAxis = array("type" => "category", "axisTick" => array("alignWithLabel" => true), "data" => $xAxisdata);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "right" => $right . '%', "bottom" => $bottom . '%', "containLabel" => true);
        $series = array('name' => '用码数量', 'type' => 'bar', 'barWidth' => '60%', 'data' => $seriesdata);
        $option = array('color' => $color, "title" => $title, 'legend' => $legend, "tooltip" => $tooltip, "toolbox" => $toolbox,  "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }

    /**
     * 举报数据按月统计柱图折线图201698
     */
    public function tipoffCountsBar(){
        $productid = I('productid');
        $companyid = $this->_companyid;
        $startdate = I('startdate',0);
        $enddate = I('enddate',0);
        $result =  ChartAPI::tipoffCountsBar($companyid,$productid,$startdate,$enddate);

        $seriesdata = [];
        $xAxisdata = [];
        foreach($result as $data) {
            $seriesdata[] = $data['counts'];
            switch($data['tipofftime']){
                case '01':
                    $data['tipofftime'] = '一月份';
                    break;
                case '02':
                    $data['tipofftime'] = '二月份';
                    break;
                case '03':
                    $data['tipofftime'] = '三月份';
                    break;
                case '04':
                    $data['tipofftime'] = '四月份';
                    break;
                case '05':
                    $data['tipofftime'] = '五月份';
                    break;
                case '06':
                    $data['tipofftime'] = '六月份';
                    break;
                case '07':
                    $data['tipofftime'] = '七月份';
                    break;
                case '08':
                    $data['tipofftime'] = '八月份';
                    break;
                case '09':
                    $data['tipofftime'] = '九月份';
                    break;
                case '10':
                    $data['tipofftime'] = '十月份';
                    break;
                case '11':
                    $data['tipofftime'] = '十一月份';
                    break;
                case '12':
                    $data['tipofftime'] = '十二月份';
                    break;
            }
            $xAxisdata[] = $data['tipofftime'];
        }

        $left = 5;
        $right = 4;
        $bottom = 3;
        $color = ['#3398DB'];
        $title = array("text" => "产品举报统计" , "left" => "center");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $legend = array('orient' => 'vertical','left' => '80px', 'top' => '10px', 'data' => array('举报次数'));
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar'))));
        $xAxis = array("type" => "category", "axisTick" => array("alignWithLabel" => true), "data" => $xAxisdata);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "right" => $right . '%', "bottom" => $bottom . '%', "containLabel" => true);
        $series = array('name' => '举报次数', 'type' => 'bar', 'barWidth' => '60%', 'data' => $seriesdata);
        $option = array('color' => $color, "title" => $title, 'legend' => $legend, "tooltip" => $tooltip, "toolbox" => $toolbox,  "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }

    /**
     * 产品扫码统计饼图201696
     */
    public function scanedCountsPie(){
        $productid = I('productid');
        $companyid = $this->_companyid;
        $startdate = I('startdate',0);
        $enddate = I('enddate',0);
        $result =  ChartAPI::scanedCountsPie($companyid,$productid,$startdate,$enddate);

        foreach($result as $data){
            $legenddata[] = $data['address'];
            $seriesdata[] = array('value'=>$data['counts'],'name'=>$data['address']);
        }
        $title = array('text' => '产品扫码地域分布图','left' => 'center');
        $tooltip = array('trigger' => 'item','formatter' => "{a} <br/>{b} : {c} ({d}%)");
        $toolbox = array("show" => true, "left" => "85%", "feature" => array("saveAsImage" => array("title" => "保存为图片")));
        $legend = array('orient' => 'vertical','left' => 'left','data' => $legenddata);
        $series = array('name' => '产品扫码数量', 'type' => 'pie', 'radius' => '50%', 'z' => 5,'center' => array('50%','50%'), 'data' => $seriesdata, 'itemStyle' => array('emphasis',array('shadowBlur' => 10,'shadowOffsetX' => 0,'shadowColor' => 'rgba(0, 0, 0, 0.5)')));
        $option = array('title' => $title, 'tooltip' => $tooltip, 'toolbox' => $toolbox, 'legend' => $legend, 'series' => $series);
        echo json('001',$option);
    }

    /**
     * 产品扫码按月统计柱图折线图201698
     */
    public function scanedCountsBar(){
        $productid = I('productid');
        $companyid = $this->_companyid;
        $startdate = I('startdate',0);
        $enddate = I('enddate',0);
        $result = ChartAPI::scanedCountsBar($companyid,$productid,$startdate,$enddate);

        $seriesdata = [];
        $xAxisdata = [];
        foreach($result as $data) {
            $seriesdata[] = $data['counts'];
            switch($data['scantime']){
                case '01':
                    $data['scantime'] = '一月份';
                    break;
                case '02':
                    $data['scantime'] = '二月份';
                    break;
                case '03':
                    $data['scantime'] = '三月份';
                    break;
                case '04':
                    $data['scantime'] = '四月份';
                    break;
                case '05':
                    $data['scantime'] = '五月份';
                    break;
                case '06':
                    $data['scantime'] = '六月份';
                    break;
                case '07':
                    $data['scantime'] = '七月份';
                    break;
                case '08':
                    $data['scantime'] = '八月份';
                    break;
                case '09':
                    $data['scantime'] = '九月份';
                    break;
                case '10':
                    $data['scantime'] = '十月份';
                    break;
                case '11':
                    $data['scantime'] = '十一月份';
                    break;
                case '12':
                    $data['scantime'] = '十二月份';
                    break;
            }
            $xAxisdata[] = $data['scantime'];
        }

        $left = 5;
        $right = 4;
        $bottom = 3;
        $color = ['#3398DB'];
        $title = array("text" => "产品扫码统计" , "left" => "center");
        $tooltip = array("trigger" => "axis", "axisPointer" => array("type" => "shadow"));
        $legend = array('orient' => 'vertical','left' => '80px', 'top' => '10px', 'data' => array('扫码次数'));
        $toolbox = array("show" => true, "left" => '85%', "feature" => array("saveAsImage" => array("title" => "保存为图片"),'magicType' => array('type' => array('line','bar'))));
        $xAxis = array("type" => "category", "axisTick" => array("alignWithLabel" => true), "data" => $xAxisdata);
        $yAxis = array("type" => "value");
        $grid = array("left" => $left . "%", "right" => $right . '%', "bottom" => $bottom . '%', "containLabel" => true);
        $series = array('name' => '扫码次数', 'type' => 'bar', 'barWidth' => '60%', 'data' => $seriesdata);
        $option = array('color' => $color, "title" => $title, 'legend' =>$legend, "tooltip" => $tooltip, "toolbox" => $toolbox,  "xAxis" => $xAxis, "yAxis" => $yAxis, "series" => $series, "grid" => $grid);
        echo json("001", $option);
    }

    /*
     * 按照时间列出扫码的前n个地域
     * 柱状图
     */
    public function scanedCountsTop()
    {
        $productid = I('productid');
        $companyid = $this->_companyid;
        $startdate = I('startdate',0);
        $enddate = I('enddate',0);
        $result = ChartAPI::scanedCountsTop($companyid,$productid,$startdate,$enddate);

        $legenddata = [];
        $categories = [];
        foreach ($result as $key => $value)
        {
            if (!in_array($key, $categories))
            {
                $categories[] = $key;
            }
            foreach ($value as $item)
            {
                if (!in_array($item['address'], $legenddata))
                {
                    $legenddata[] = $item['address'];
                }
                $datacollection[] = array("counts" => $item['counts'], "name" => $item['address'], "date" => $key);
            }
        }
        $series = self::buildSeries($datacollection, $legenddata, $categories, true);
        $left = 5;
        $top = 10;
        $bottom = 15;
        $width = 80;

        $title = array("text" => "扫码统计" , "left" => "center", "top" => (100 - $bottom / 2) . "%");
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
                $series[] = array("name" => $name, "type" => "bar", "stack" => "数量", "data" => $seriesdata);
            }
            else
            {
                $series[] = array("name" => $name, "type" => "bar", "data" => $seriesdata);
            }
        }

        return $series;
    }

    /**
     * 获取特定名称，特定时间的数据数量
     * @param $rows
     * @param $name
     * @param $date
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