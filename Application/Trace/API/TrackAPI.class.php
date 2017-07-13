<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/23
 * Time: 13:47
 */

namespace Trace\API;


class TrackAPI
{
    /**
     * 企业的检测信息2016824
     * @param $companyid
     * @return mixed
     */
    public static function checkItem($productid,$companyid){
        $model = M("BaseInspectionReport");
        $fields = 'id,companyid,productid,inspectionname,create_time';
        $map = [
            'productid' => $productid,
            'companyid' => $companyid
        ];
        $res = $model->field($fields)->where($map)->select();
        return $res;
    }

    /**
     * 企业的仓库信息2016824
     * @param $companyid
     * @return mixed
     */
    public static function warehouse($companyid){
        $model = M("BaseWarehouse");
        $fields = 'id AS warehouseid,warehouse_name,companyid,create_time';
        $map = [
            'companyid' => $companyid
        ];
        $res = $model->field($fields)->where($map)->select();
        return $res;
    }

    /**
     * 根据产品查质量码段2016823
     * @param $productid
     * @param $companyid
     * @return mixed
     */
    public static function SearchQrcodeProduct($productid,$companyid){
        $model = M("CorrQrcodeProduct");
        $fields = 'qrcode_range_s,qrcode_range_e';
        $map = [
            'product_id' => $productid,
            'company_id' => $companyid
        ];
        $res = $model->field($fields)->where($map)->select();
        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 根据生产信息查质量码段2016823
     * @param $productprofileid
     * @param $companyid
     * @return mixed
     */
    public static function SearchQrcodeProduce($productprofileid,$companyid){
        $model = M("CorrQrcodeProductprofile");
        $fields = 'qrcode_range_s,qrcode_range_e';
        $map = [
            'productprofile_id' => $productprofileid,
            'company_id' => $companyid
        ];
        $res = $model->field($fields)->where($map)->select();

        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 根据检测名称查质量码段2016823
     * @param $id
     * @param $companyid
     * @return mixed
     */
    public static function SearchQrcodeCheckItem($id,$companyid){
        $model = M("CorrQrcodeInspection_report");
        $fields = 'qrcode_range_s,qrcode_range_e';
        $map = [
            'inspection_id' => $id,
            'company_id' => $companyid
        ];
        $res = $model->field($fields)->where($map)->select();
        if($res){
            return $res;
        }else{
            return false;
        }
    }

    /**
     * 根据入库查质量码段2016824
     * @param $waerhouseid
     * @param $companyid
     * @return mixed
     */
    public static function SearchCheckin($waerhouseid,$companyid){
        // ini_set("mongo.long_as_object", 1);
        $model = MM("base_scanin","zm_");
        $map = [
            'companyid' => strval($companyid),
            'warehouseid' => strval($waerhouseid)
        ];
        $res = $model->where($map)->getField('p',true);
        //  ini_set("mongo.long_as_object", 0);
        return $res;
    }

    /**
     * 根据出库查质量码段2016824
     * @param $destinationid
     * @param $companyid
     * @return mixed
     */
    public static function SearchCheckout($destinationid,$companyid){
        //  ini_set("mongo.long_as_object", 1);
        $model = MM("base_scanout","zm_");
        $map = [
            'companyid' => strval($companyid),
            'destinationid' => strval($destinationid)
        ];
        $res = $model->where($map)->getField('p',true);
        //    ini_set("mongo.long_as_object", 0);
        return $res;
    }

    /**
     * 3比较结果取交集2016826
     * @param $arr1
     * @param $arr2
     * @param $arr3
     * @return array
     */
    public static function intersect($arr1,$arr2,$arr3){
        sort($arr1);
        sort($arr2);
        sort($arr3);
        for($i=0;$i<count($arr1);$i++) {
            for ($j = 0; $j < count($arr2); $j++) {
                $re = overlapinterval($arr1[$i]['qrcode_range_s'],$arr1[$i]['qrcode_range_e'],$arr2[$j]['qrcode_range_s'],$arr2[$j]['qrcode_range_e']);
                if($re != false){
                    $result[]=$re ;
                }
            }
        }

        for($k=0;$k<count($result);$k++){
            for($v=0;$v<count($arr3);$v++){
                $res = overlapinterval($result[$k]['qrcode_range_s'],$result[$k]['qrcode_range_e'],$arr3[$v]['qrcode_range_s'],$arr3[$v]['qrcode_range_e']);
                if($res != false){
                    $results[]=$res ;
                }
            }
        }
        return $results;
    }

    /**
     * 2比较结果取交集2016826
     * @param $arr1
     * @param $arr2
     * @return array
     */
    public static function inter($arr1,$arr2){
        sort($arr1);
        sort($arr2);
        for($i=0;$i<count($arr1);$i++) {
            for ($j = 0; $j < count($arr2); $j++) {
                $re = overlapinterval($arr1[$i]['qrcode_range_s'],$arr1[$i]['qrcode_range_e'],$arr2[$j]['qrcode_range_s'],$arr2[$j]['qrcode_range_e']);
                if($re != false){
                    $result[]=$re ;
                }
            }
        }
        return $result;
    }

    /**
     * 4比较结果取交集201692
     * @param $arr1
     * @param $arr2
     * @param $arr3
     * @param $arr4
     * @return array
     */
    public static function interfour($arr1, $arr2, $arr3,$arr4){
        sort($arr1);
        sort($arr2);
        sort($arr3);
        sort($arr4);
        for($i=0;$i<count($arr1);$i++) {
            for ($j = 0; $j < count($arr2); $j++) {
                $re = overlapinterval($arr1[$i]['qrcode_range_s'],$arr1[$i]['qrcode_range_e'],$arr2[$j]['qrcode_range_s'],$arr2[$j]['qrcode_range_e']);
                if($re != false){
                    $result[]=$re ;
                }
            }
        }

        for($k=0;$k<count($result);$k++){
            for($v=0;$v<count($arr3);$v++){
                $res = overlapinterval($result[$k]['qrcode_range_s'],$result[$k]['qrcode_range_e'],$arr3[$v]['qrcode_range_s'],$arr3[$v]['qrcode_range_e']);
                if($res != false){
                    $results[]=$res ;
                }
            }
        }

        for($x=0;$x<count($results);$x++){
            for($y=0;$y<count($arr4);$y++){
                $res = overlapinterval($results[$x]['qrcode_range_s'],$results[$x]['qrcode_range_e'],$arr4[$y]['qrcode_range_s'],$arr4[$y]['qrcode_range_e']);
                if($res != false){
                    $last[]=$res ;
                }
            }
        }
        return $last;
    }

    /**
     * 查找政府管辖的企业201692
     * @return mixed
     */
    public static function searchCorr($governmentid){
        $model = M("CorrGovernmentCompany");
        $map = [
            'government_id' => $governmentid
        ];
        $fields = 'company_id as companyid,zm_base_company.name as companyname';
        $res = $model->where($map)->field($fields)->join('zm_base_company ON zm_base_company.id = zm_corr_government_company.company_id')->select();
        return $res;
    }

    /**
     * 查找企业的所有产品201692
     * @param $companyid
     * @return mixed
     */
    public static function searchComProduct($companyid){
        $model = M("BaseProduct");
        $map = [
            'companyid' => $companyid
        ];
        $fields = 'productid,productname,companyid,guige';
        $res = $model->where($map)->field($fields)->select();
        return $res;
    }

    /**
     * 查找企业产品对应的生产信息201692
     * @param $companyid
     * @param $productid
     * @return mixed
     */
    public static function searchComProfile($productid,$companyid){
//        ini_set('mongo.long_as_object',1);
        $profile = MM("BaseProductprofile","zm_");
        $cond = array(
            "company_id" => strval($companyid),
            'product_id' => strval($productid)
        );
        $fields = '_id,name,company_id,product_id';
        $profiles = $profile -> where($cond) ->field($fields)  -> select();
//        ini_set('mongo.long_as_object',0);

        return $profiles;
    }

    /**
     * 根据企业查质量码段201692
     * @param $companyid
     * @return bool
     */
    public static function searchQrcodeCom($companyid){
        $model = M("BaseProduct");
        $map = [
            'companyid' => $companyid
        ];
        $fields = 'qrcode_range_s,qrcode_range_e';
        $res = $model->field($fields)->where($map)->join('zm_corr_qrcode_product ON zm_corr_qrcode_product.product_id = zm_base_product.productid AND zm_corr_qrcode_product.company_id = zm_base_product.companyid')->select();
        if($res){
            return $res;
        }else{
            return false;
        }
    }
}