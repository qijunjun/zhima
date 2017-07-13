<?php
/**
 * Created by PhpStorm.
 * User: dichun
 * Date: 2016/8/22
 * Time: 11:42
 */

namespace Common\API;


class MatchtimeAPI
{
    /**
     * 时间条件2016822
     * @param $startdate
     * @param $enddate
     * @return array|void
     */
    public static function checkDate($startdate=null,$enddate=null){
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate) + 86400;
        }

        if ($starttime > 0 && $endtime > 0)
        {
            if($starttime>$endtime)//如果初始日期比开始日期大
            {
                json("002",null,"请输入正确的起止日期");
                return false;
            }
            return $map = array("between", array($starttime, $endtime));
        }
        elseif (!empty($starttime) && is_numeric($starttime))
        {
            return $map = array("EGT", $starttime);
        }
        elseif (!empty($endtime) && is_numeric($endtime))
        {
            return $map = array("ELT", $endtime);
        }
    }
}