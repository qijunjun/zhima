<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:31
 */
namespace Exportexcel\API;

class ExportexcelAPI
{

    /**
     * 生成统计图形标题中的日期范围部分
     *
     * @param $startdate
     * @param $enddate
     *
     * @return string
     */
    public function buildDateTitle($startdate, $enddate)
    {
        $title = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0 && $endtime > 0)
            {
                $title = "(" . date('Y-m-d', $starttime) . "到" . date('Y-m-d', $endtime) . ")";
            }
            elseif ($starttime > 0)
            {
                $title = "(" . date('Y-m-d', $starttime) . "之后)";
            }
            elseif ($endtime > 0)
            {
                $title = "(" . date('Y-m-d', $endtime) . "之前)";
            }
        }
//        if (strlen($title) == 0)
//        {
//            $title = "(2016-01-01" . "到" . date('Y-m-d', time()) . ")";
//        }

        return $title;
    }

    /**
     * 生成产品用码数量的时间范围条件
     *
     * @param $startdate
     * @param $enddate
     *
     * @return string
     */
    public function buildScanInfoScop($startdate , $enddate  )
    {
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.`first_time`>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.`first_time`<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = " AND " . $where;
        }

        return $where;
    }

    /**红包时间范围条件
     * @param $startdate
     * @param $enddate
     * @return string
     */
    public function buildHongbaoInfoScop($startdate, $enddate)
    {
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_hongbao`.`create_time`>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_hongbao`.`create_time`<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = " AND " . $where;
        }

        return $where;
    }

    /**产品数据时间范围条件2016810
     * @param $startdate
     * @param $enddate
     * @return string
     */
    public function bulidProductInfoScop($startdate,$enddate){
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_product`.`create_time`>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_product`.`create_time`<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = "AND" .$where;
        }
        return $where;
    }

    /**假货数据时间范围条件2016810
     * @param $startdate
     * @param $enddate
     * @return string
     */
    public function bulidFakeInfoScop($startdate,$enddate){
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.`recent_time`>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scaninfo`.`recent_time`<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = "AND" .$where;
        }
        return $where;
    }

    /**举报数据时间范围条件2016810
     * @param $startdate
     * @param $enddate
     * @return string
     */
    public function bulidScanReportInfoScop($startdate,$enddate){
        $where = "";
        if (strlen($startdate) > 0)
        {
            $starttime = strtotime($startdate);
        }
        if (strlen($enddate) > 0)
        {
            $endtime = strtotime($enddate);
        }

        if ($starttime > 0 || $endtime > 0)
        {
            if (($starttime > 0 && $endtime > 0) && $starttime > $endtime)//如果起始时间大于结束时间，互换
            {
                $tmptime = $starttime;
                $starttime = $endtime;
                $endtime = $tmptime;
            }

            if ($starttime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scanreport`.`create_time`>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_base_scanreport`.`create_time`<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = "AND" .$where;
        }
        return $where;
    }


}