<?php
/**
 * Created by PhpStorm.
 * User: yumin
 * Date: 2016/7/18
 * Time: 15:53
 */

namespace Exportexcel\Controller;

use Common\Controller\BaseController;
use Exportexcel\API\ExportexcelAPI;
use CheckIO\API\CheckioAPI;
use Think\Controller;

/**
 * 产品管理，出入库管理，假货数据，举报数据，窜货数据，扫码数据，红包领取记录
 *
 * Class ExportexcelController
 *
 * @package Exportexcel\Controller
 */
class ExportexcelController extends BaseController
{
    public function index($name, $startdate = null, $enddate = null)
    {
        $companyid = $_SESSION["user_info"]["companyid"];//获取企业id
        if (($companyid !== '') && ($companyid !== null)) {
            switch ($name) {
                case "scaninfo":
                    self::exportScanInfo($name, "扫码数据", $companyid, $startdate, $enddate);
                    break;
                case "productinfo":
                    self::exportProductInfo($name, "产品数据", $companyid, $startdate, $enddate);
                    break;
                case "fakeinfo":
                    self::exportFakeInfo($name, "假货数据", $companyid, $startdate, $enddate);
                    break;
                case "scanreport":
                    self::exportScanReport($name, "举报数据", $companyid, $startdate, $enddate);
                    break;
                case "fleeinginfo":
                    self::exportFleeingInfo($name, "窜货数据", $companyid, $startdate, $enddate);
                    break;
                case "hongbaoinfo":
                    self::exportHongbaoInfo($name, "红包数据", $companyid, $startdate, $enddate);
                    break;
                case "checkininfo":
                    self::exportCheckinInfo($name, "入库数据", $companyid, $startdate, $enddate);
                    break;
                case "checkoutinfo":
                    self::exportCheckoutInfo($name, "出库数据", $companyid, $startdate, $enddate);
                    break;
                default:

                    break;
            }
        } else {
            json("002");

            return;
        }


    }

    /**
     * 扫码记录导出为excel
     * 产品名称    产品规格    质量码    首次扫码时间    首次扫码地点    最近扫码时间    最近扫码地点    扫码次数
     *
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportScanInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $model = M();
        $sql = "select `zm_base_product`.`productname`,`zm_base_product`.`guige`,`zm_base_scaninfo`.`b`,`zm_base_scaninfo`.`first_time`,`zm_base_scaninfo`.`first_address`,`zm_base_scaninfo`.`recent_time`,`zm_base_scaninfo`.`recent_address`,`zm_base_scaninfo`.`scan_count` from (`zm_base_scaninfo` left join `zm_base_product` on((`zm_base_scaninfo`.`productid` = `zm_base_product`.`productid`))) where `zm_base_product`.`companyid` = " . $companyid . " " . ExportexcelAPI::buildScanInfoScop($startdate, $enddate);
        $rows = $model->query($sql);
        //输出excel
        import("Org.Util.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("质码")
            ->setLastModifiedBy("质码")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");

        //行号
        $r = 1;
        //列号
        $c = 1;
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品规格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "质量码")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "首次扫码时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "首次扫码地点")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "最近扫码时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "最近扫码地点")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码次数");

        for ($c = 1; $c < 9; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }


        foreach ($rows as $row) {
            $r++;
            $c = 1;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["productname"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["guige"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $row["b"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $row["first_time"]))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["first_address"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $row["recent_time"]))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["recent_address"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["scan_count"]);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 产品名称    规格    价格(元)    录入时间    网店链接
     *
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate '
     */
    static function exportProductInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $model = M('base_product');
        $sql = "select productname,guige,price,create_time,wdadr from `zm_base_product` where `companyid` = " . $companyid . " " . ExportexcelAPI::bulidProductInfoScop($startdate, $enddate);
        $rows = $model->query($sql);
        //输出excel
        //1.引入PHPExcel类
        import("Org.Util.PHPExcel");
        //2.实例化一个PHPExcel类
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("产品数据")
            ->setLastModifiedBy("产品数据")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");
        //行号
        $r = 1;
        //列号
        $c = 1;

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "规格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "价格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "录入时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "网店链接");

        for ($c = 1; $c < 6; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        for ($i = 0; $i < sizeof($rows); $i++) {
            $c = 1;
            $r++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['productname'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['guige'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['price'] . "元")
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $rows[$i]['create_time']))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['wdadr']);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }


    /**
     * 扫码IP    时间    地点    质量码
     *
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportFakeInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $model = M('base_scaninfo');
        $sql = 'select `b`,`recent_time`,`recent_ipaddr`,`recent_address` from zm_base_scaninfo where ( companyid=' . $companyid . ' and istf = 1 )' . " " . ExportexcelAPI::bulidFakeInfoScop($startdate, $enddate);
        $rows = $model->query($sql);
        //输出excel
        //1.引入PHPExcel类
        import("Org.Util.PHPExcel");
        //2.实例化一个PHPExcel类
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("假货数据")
            ->setLastModifiedBy("假货数据")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");
        //行号
        $r = 1;
        //列号
        $c = 1;

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "IP区域")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码地点")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "质量码");

        for ($c = 1; $c < 5; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        for ($i = 0; $i < sizeof($rows); $i++) {
            $c = 1;
            $r++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['recent_ipaddr'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $rows[$i]['recent_time']))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['recent_address'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $rows[$i]['b']);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 扫码IP    时间    联系方式    举报内容    地点    质量码
     *
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportScanReport($name, $title, $companyid, $startdate, $enddate)
    {
        $model = M('base_scanreport');
        $sql = "select ip,ipaddr,create_time,tel,content,address,b from zm_base_scanreport where b LIKE '$companyid%'"." " . ExportexcelAPI::bulidScanReportInfoScop($startdate, $enddate);
        $rows = $model->query($sql);
        //输出excel
        //1.引入PHPExcel类
        import("Org.Util.PHPExcel");
        //2.实例化一个PHPExcel类
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("举报数据")
            ->setLastModifiedBy("举报数据")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");
        //行号
        $r = 1;
        //列号
        $c = 1;

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "IP区域")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "联系方式")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "举报内容")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "地点")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "质量码");

        for ($c = 1; $c < 7; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        for ($i = 0; $i < sizeof($rows); $i++) {
            $c = 1;
            $r++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['ipaddr'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $rows[$i]['create_time']))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['tel'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['content'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['address'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $rows[$i]['b']);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

    /**
     * 名称	规格	质量码	经销商	扫码位置	IP区域	扫码时间	窜货类型
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportFleeingInfo($name, $title, $companyid, $startdate, $enddate)
    {

        $model = M('base_beyond_areas');

        if(!is_null($startdate))
            $starttime=strtotime($startdate);
        if(!is_null($enddate))
            $endtime=strtotime($enddate);
        //判断starttime和endtime大小，倒置需交换
        if ($starttime > 0 || $endtime > 0) {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }
            if ($starttime > 0)
                $condition["create_time"] = array('EGT', $starttime);
            if ($endtime > 0)
                $condition["create_time"] = array('ELT', $endtime);
        }
        $condition['company_id'] = $companyid;
        $result = $model->where($condition)->select();

        if ($result === false) {
            return;
        }



        $rows = array();
        $model = M('base_agent');
        $modelFlee = M('dict_beyond_areas');
        $fleeAddress = $modelFlee->select();
        foreach ($result as $key => $item) {
            $productinfo = CheckioAPI::productInfo($item['b']);
            //获取箱码信息
            $p = getPcodeByXcode($item['b']);
            //根据箱码得到出库信息
            $data = CheckioAPI::listCheckoutbyPack(['p' => floatval($p)]);
            $data = $data[count($data) - 1];
            //获得经销商信息
            $agent = $model->where(['id' => $data['destinationid']])->find();

            if ($productinfo != null) {
                $record = array(
                    'id' => $item['id'],
                    'b' => $item["b"],
                    'ip' => $item['ip'],
                    "product_name" => $productinfo['name'],
                    'spec' => $productinfo['spec'],
                    'product_image' => $productinfo['image'],
                    'ipaddr' => $item['ipaddr'],
                    'create_time' => $item['create_time'],
                    'lon' => $item['lon'],
                    'lat' => $item['lat'],
                    'address' => $item['address'],
                    'beyondareas_id' => $fleeAddress[$item['beyondareas_id']]['beyond_areas_type'],
                    'agent' => $agent['agent_name']
                );
                array_push($rows, $record);
            }
        }
        unset($record);
//        var_dump($rows);
        //输出excel
        //1.引入PHPExcel类
        import("Org.Util.PHPExcel");
        //2.实例化一个PHPExcel类
        $objPHPExcel = new \PHPExcel();
        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("窜货数据")
            ->setLastModifiedBy("窜货数据")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");
        //行号
        $r = 1;
        //列号
        $c = 1;
        //名称	规格	质量码	经销商	扫码位置	IP区域	扫码时间	窜货类型
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "规格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "质量码")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "经销商")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码位置")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "IP区域")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "扫码时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "窜货类型");

        for ($c = 1; $c < 9; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        for ($i = 0; $i < sizeof($rows); $i++) {
            $c = 1;
            $r++;
            $objPHPExcel->getActiveSheet()
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['product_name'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['spec'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $rows[$i]['b'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['agent'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['address'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['ipaddr'])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $rows[$i]['create_time']))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $rows[$i]['beyondareas_id']);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }


    /**
     * 质量码  手机号 用户名 性别  红包创建时间  红包发放时间  发放状态    红包金额(分) 活动名称
     *
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */

    static function exportHongbaoInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $promotionid = I('promotionid',null);
        if($promotionid){
            $where = "AND" . "`zm_base_hongbao`.`promotionid` = " . $promotionid;
        }else{
            $where = "";
        }
        $model = M();
        $sql = "select `zm_base_hongbao`.`b` AS `b`,`zm_base_hongbao`.`mobile` AS `mobile`,`zm_base_hongbao`.`customer` AS `customer`,`zm_base_hongbao`.`sex` AS `sex`,`zm_base_hongbao`.`create_time` AS `create_time`,`zm_base_hongbao`.`send_time` AS `send_time`,`zm_dict_hongbao_status`.`hongbao_status` AS `hongbao_status`,`zm_base_hongbao`.`total_amount` AS `total_amount`,`zm_base_hongbao`.`act_name` AS `act_name` from (`zm_base_hongbao` left join `zm_dict_hongbao_status` on((`zm_base_hongbao`.`status` = `zm_dict_hongbao_status`.`id`))) where `zm_base_hongbao`.`companyid` = " . $companyid . " ". $where . " " . ExportexcelAPI::buildHongbaoInfoScop($startdate, $enddate);
        $rows = $model->query($sql);

        //输出excel
        import("Org.Util.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("质码")
            ->setLastModifiedBy("质码")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");


        //行号
        $r = 1;
        //列号
        $c = 1;
        //设置宽width
        // Set column widths
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "质量码")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "手机号")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "用户名")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "性别")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "红包创建时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "红包发放时间")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "发放状态")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "红包金额")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "活动名称");

        for ($c = 1; $c < 10; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        foreach ($rows as $row) {
            $r++;
            $c = 1;
            if ($row["sex"] == 1) {
                $sex = "男";
            } else {
                $sex = "女";
            }

            if (empty($row["send_time"])) {
                $sendtime = "";
            } else {
                $sendtime = date("Y-m-d H:i:s", $row["send_time"]);
            }

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $row["b"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["mobile"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["customer"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $sex)
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $row["create_time"]))
                ->setCellValue(self::getColumnCharacter($c++) . $r, $sendtime)
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["hongbao_status"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["total_amount"] / 100)
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["act_name"]);
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

    /**
     * 产品名称    产品规格    箱码    仓库名称    入库时间
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportCheckinInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $condition = array(
            "companyid" => $companyid
        );

        if(!is_null($startdate))
            $starttime=strtotime($startdate);
        if(!is_null($enddate))
            $endtime=strtotime($enddate);
        //判断starttime和endtime大小，倒置需交换
        if ($starttime > 0 || $endtime > 0) {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }
            if ($starttime > 0)
                $condition["create_time"] = array('EGT', $starttime);
            if ($endtime > 0)
                $condition["create_time"] = array('ELT', $endtime);
        }

        $rows = CheckioAPI::listCheckin($condition);

        //var_dump($rows);
        //输出excel
        import("Org.Util.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("质码")
            ->setLastModifiedBy("质码")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");


        //行号
        $r = 1;
        //列号
        $c = 1;
        //设置宽width
        // Set column widths
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品规格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "箱码")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "仓库名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "入库时间");

        for ($c = 1; $c < 6; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        foreach ($rows as $row) {
            $r++;
            $c = 1;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["product_name"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["spec"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $row["p"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["destination"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $row["create_time"]));
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }

    /**
     *        产品名称    产品规格    箱码    出库去向    出库时间
     * @param $name
     * @param $title
     * @param $companyid
     * @param $startdate
     * @param $enddate
     */
    static function exportCheckoutInfo($name, $title, $companyid, $startdate, $enddate)
    {
        $condition = array(
            "companyid" => $companyid
        );
        if(!is_null($startdate))
            $starttime=strtotime($startdate);
        if(!is_null($enddate))
            $endtime=strtotime($enddate);
        //判断starttime和endtime大小，倒置需交换
        if ($starttime > 0 || $endtime > 0) {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }
            if ($starttime > 0)
                $condition["create_time"] = array('EGT', $starttime);
            if ($endtime > 0)
                $condition["create_time"] = array('ELT', $endtime);
        }

        $rows = CheckioAPI::listCheckout($condition);

        //var_dump($rows);
        //输出excel
        import("Org.Util.PHPExcel");
        $objPHPExcel = new \PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
        $objPHPExcel->getProperties()->setCreator("质码")
            ->setLastModifiedBy("质码")
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription($title)
            ->setKeywords("excel")
            ->setCategory("result file");

        //行号
        $r = 1;
        //列号
        $c = 1;
        //设置宽width
        // Set column widths
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品名称")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "产品规格")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "箱码")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "出库去向")
            ->setCellValue(self::getColumnCharacter($c++) . $r, "出库时间");

        for ($c = 1; $c < 6; $c++) {
            $objPHPExcel->getActiveSheet()->getStyle(self::getColumnCharacter($c) . $r)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getColumnDimension(self::getColumnCharacter($c))->setAutoSize(true);   //内容自适应
        }

        foreach ($rows as $row) {
            $r++;
            $c = 1;

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["product_name"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["spec"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, " " . $row["p"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, $row["destination"])
                ->setCellValue(self::getColumnCharacter($c++) . $r, date("Y-m-d H:i:s", $row["time"]));
        }

        try {
            $objPHPExcel->getActiveSheet()->setTitle($title);
        } catch (Exception $e) {
        }
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '.xls"');
        header('Cache-Control: max-age=0');
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    function getColumnCharacter($c)
    {
        if ($c > 0 && $c < 27) {
            return chr($c + 64);
        } elseif ($c > 26 && $c < 256) {
            $c1 = intval(floor($c / 26));
            $c2 = $c % 26;
            if ($c2 == 0) {
                $c1--;
                $c2 = 26;
            }

            return (chr($c1 + 64) . chr($c2 + 64));
        } else {
            throw_exception("Over Max Columns");
        }
    }
}