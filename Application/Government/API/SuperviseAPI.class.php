<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/20
 * Time: 14:39
 */

namespace Government\API;


class SuperviseAPI
{

    /**获取企业名称2016820
     * @return mixed
     */
    public static function getCompanyname($governmentid){
        $model = M("corr_government_company");

        $fileds = 'zm_base_company.id AS companyid,zm_base_company.name AS companyname';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fileds)->join('zm_base_company ON zm_corr_government_company.company_id = zm_base_company.id')->where($map)->select();

        return $res;
    }

    /**政府监管的所有企业的基本信息2016820
     * @return mixed
     */
    public static function showCompanyInfo($governmentid){
        $model = M("corr_government_company");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.name AS companyname,zm_base_company.introduction,zm_base_company.contact,zm_base_company.legalperson,zm_base_company.address,zm_base_company.phone,zm_base_company.introimage1,zm_base_company.introimage2,zm_base_company.introimage3,zm_base_company.introimage4,zm_base_government.`name` AS governmentname,zm_base_government.id AS governmentid';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fields)
            ->join('zm_base_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->join('zm_base_government ON zm_corr_government_company.government_id = zm_base_government.id')
            ->where($map)->select();

        return $res;
    }

    /**政府监管的企业的资质信息2016820
     * @return mixed
     */
    public static function showAptitude($governmentid){
        $model = M("BaseCompany");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_aptitude.aptitudeid,zm_base_aptitude.aptitudecode,zm_base_aptitude.aptitudename,zm_base_aptitude.authorizer,zm_base_aptitude.create_time,zm_base_aptitude.validity_time,zm_base_aptitude.`range`,zm_base_aptitude.aptitudeimage1,zm_base_aptitude.aptitudeimage2,zm_base_aptitude.aptitudeimage3,zm_base_aptitude.aptitudeimage4';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fields)
            ->join('zm_base_aptitude ON zm_base_aptitude.companyid = zm_base_company.id')
            ->join('zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->where($map)->select();

        return $res;
    }

    /**政府监管的企业的产品信息2016820
     * @return mixed
     */
    public static function showProduct($governmentid){
        $model = M("corr_government_company");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.price,zm_base_product.guige,zm_base_product.productimage';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fields)
            ->join('zm_base_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->join('zm_base_product ON zm_corr_government_company.company_id = zm_base_product.companyid')
            ->where($map)->select();

        return $res;
    }

    /**政府监管的企业的产品的生产过程信息2016820
     * @return mixed
     */
    public static function showProcess($governmentid){
        $model = M("base_product_event_data");

        $fields = '	zm_base_company.id AS companyid,zm_base_company.name AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.guige,zm_base_product.productimage,zm_base_product_event_data.id AS eventid,zm_base_product_event_data.function_name,zm_base_product_event_data.operatorimage,zm_base_product_event_data.event_time,zm_base_product_event_data.userlocation,zm_base_product_event_data.event_details';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $re = $model->field($fields)
            ->join('zm_base_company ON zm_base_product_event_data.companyid = zm_base_company.id')
            ->join('zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->join('zm_base_product ON zm_base_product_event_data.productid = zm_base_product.productid')
            ->where($map)->select();

        for($i = 0;$i<count($re);$i++){
            $eventid = $re[$i]['eventid'];
            $Model = M("base_product_event_image");
            $map = [
                'eventid' => $eventid
            ];
            $image = $Model->where($map)->getfield('image_path',true);
            $re[$i]['image_path'] = $image;
        }
        return $re;
    }

    /**政府监管的企业的产品检测信息2016820
     * @return mixed
     */
    public static function showCheckItem($governmentid){
        $model = M("BaseCompany");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.guige,zm_base_inspection_report.id AS inspectionid,zm_base_inspection_report.inspectionname,zm_base_inspection_report.institution,zm_base_inspection_report.inspectionitem,zm_base_inspection_report.create_time,zm_base_inspection_report.inspectioncontent,zm_base_inspection_report.attachment1,zm_base_inspection_report.attachment2,zm_base_inspection_report.attachment3,zm_base_inspection_report.attachment4,zm_base_inspection_report.attachment5';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fields)
            ->join('zm_base_inspection_report ON zm_base_inspection_report.companyid = zm_base_company.id')
            ->join('zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->join('zm_base_product ON zm_base_product.companyid = zm_base_company.id AND zm_base_inspection_report.productid = zm_base_product.productid')
            ->where($map)->select();

        return $res;
    }

    /**政府监管的企业的产品的召回信息2016820
     * @return mixed
     */
    public static function showRecall($governmentid){
        $model = M("BaseCompany");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.guige,zm_base_recall.id AS recallid,zm_base_recall.qrcode_range_s,zm_base_recall.qrcode_range_e,zm_base_recall.create_time,zm_base_recall.reason';
        $map = [
            'zm_corr_government_company.government_id' => $governmentid
        ];
        $res = $model->field($fields)
            ->join('zm_base_product ON zm_base_product.companyid = zm_base_company.id')
            ->join('zm_base_recall ON zm_base_recall.company_id = zm_base_company.id AND zm_base_product.productid = zm_base_recall.product_id')
            ->join('zm_corr_government_company ON zm_corr_government_company.company_id = zm_base_company.id')
            ->where($map)->select();

        return $res;
    }

    /**获取指定公司的资质信息2016821
     * @param $companyid
     * @return mixed
     */
    public static function getComAptitude($companyid){
        $model = M("BaseCompany");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_aptitude.aptitudeid,zm_base_aptitude.aptitudecode,zm_base_aptitude.aptitudename,zm_base_aptitude.authorizer,zm_base_aptitude.create_time,zm_base_aptitude.validity_time,zm_base_aptitude.`range`,zm_base_aptitude.aptitudeimage1,zm_base_aptitude.aptitudeimage2,zm_base_aptitude.aptitudeimage3,zm_base_aptitude.aptitudeimage4';
        $map = [
            'zm_base_aptitude.companyid' => $companyid
        ];
        $res = $model->field($fields)
            ->join('zm_base_aptitude ON zm_base_aptitude.companyid = zm_base_company.id')
            ->where($map)->select();

        return $res;
    }

    /**获取指定公司的产品信息2016821
     * @param $companyid
     * @return mixed
     */
    public static function getComProduct($companyid){
        $model = M("base_company");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.price,zm_base_product.guige,zm_base_product.productimage';
        $map = [
            'zm_base_product.companyid' => $companyid
        ];
        $res = $model->field($fields)
            ->join('zm_base_product ON zm_base_product.companyid = zm_base_company.id')
            ->where($map)->select();

        return $res;
    }

    /**获取指定公司的生产过程信息2016821
     * @param $companyid
     * @return mixed
     */
    public static function getComProcess($companyid){
        $model = M("base_product_event_data");

        $fields = '	zm_base_company.id AS companyid,zm_base_company.name AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.guige,zm_base_product.productimage,zm_base_product_event_data.id AS eventid,zm_base_product_event_data.function_name,zm_base_product_event_data.operatorimage,zm_base_product_event_data.event_time,zm_base_product_event_data.userlocation,zm_base_product_event_data.event_details';
        $map = [
            'zm_base_product_event_data.companyid' => $companyid
        ];
        $re = $model->field($fields)
            ->join('zm_base_company ON zm_base_product_event_data.companyid = zm_base_company.id')
            ->join('zm_base_product ON zm_base_product_event_data.productid = zm_base_product.productid')
            ->where($map)->select();

        for($i = 0;$i<count($re);$i++){
            $eventid = $re[$i]['eventid'];
            $Model = M("base_product_event_image");
            $map = [
                'eventid' => $eventid
            ];
            $image = $Model->where($map)->getfield('image_path',true);
            $re[$i]['image_path'] = $image;
        }
        return $re;
    }

    /**获取指定公司的检测信息2016821
     * @param $companyid
     * @return mixed
     */
    public static function getComCheckItem($companyid){
        $model = M("BaseInspectionReport");
        $fields = 'zm_base_product.productname,zm_base_product.guige,zm_base_inspection_report.id,zm_base_inspection_report.companyid,zm_base_inspection_report.productid,inspectionname,zm_base_inspection_report.create_time,inspectionitem,institution,attachment1,attachment2,attachment3,attachment4,attachment5';
        $map = [
            'zm_base_inspection_report.companyid' => $companyid
        ];
        $res = $model->field($fields)
            ->join('zm_base_company ON zm_base_inspection_report.companyid = zm_base_company.id')
            ->join('zm_base_product ON zm_base_inspection_report.productid = zm_base_product.productid')
            ->where($map)->select();

            return $res;
    }

    /**获取指定公司的召回信息2016821
     * @param $companyid
     * @return mixed
     */
    public static function getComRecall($companyid){
        $model = M("BaseCompany");

        $fields = 'zm_base_company.id AS companyid,zm_base_company.`name` AS companyname,zm_base_product.productid,zm_base_product.productname,zm_base_product.guige,zm_base_recall.id AS recallid,zm_base_recall.qrcode_range_s,zm_base_recall.qrcode_range_e,zm_base_recall.create_time,zm_base_recall.reason';
        $map = [
            'zm_base_product.companyid' => $companyid
        ];
        $res = $model->field($fields)
            ->join('zm_base_product ON zm_base_product.companyid = zm_base_company.id')
            ->join('zm_base_recall ON zm_base_recall.product_id = zm_base_product.productid')
            ->where($map)->select();

        return $res;
    }

    /**检测政府与企业是否有关联2016820
     * @return mixed
     */
    public static function checkComAndGov($governmentid,$companyid){
        $model = M("CorrGovernmentCompany");

        $map = [
            'government_id' => $governmentid,
            'company_id' => $companyid
        ];
        $re = $model->where($map)->find();
        if($re){
            return true;
        }else{
            return false;
        }
    }
}