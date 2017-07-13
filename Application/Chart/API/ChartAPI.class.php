<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 16/4/12
 * Time: 下午11:31
 */
namespace Chart\API;

class ChartAPI
{

    /**
     * 生成统计图形标题中的日期范围部分
     *
     * @param $startdate
     * @param $enddate
     *
     * @return string
     */
    public static function buildDateTitle($startdate, $enddate)
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
    public function buildProductDateScop($startdate, $enddate)
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
                $where .= "`zm_corr_qrcode_product`.create_time>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`zm_corr_qrcode_product`.create_time<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = " AND " . $where;
        }

        return $where;
    }

    /**
     * 用码数量的时间范围条件
     *
     * @param $startdate
     * @param $enddate
     *
     * @return string
     */
    public function buildQrcodeDateScop($startdate, $enddate)
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
                $where .= "`base_qrcode_used_log`.operate_time>" . strtotime(date('Y-m-d', $starttime));
            }
            if ($endtime > 0)
            {
                if (strlen($where) > 0)
                {
                    $where .= " AND ";
                }
                $where .= "`base_qrcode_used_log`.operate_time<" . (strtotime(date('Y-m-d', $endtime)) + 86400);
            }
        }
        if ($where != "")
        {
            $where = " AND " . $where;
        }

        return $where;
    }

    /**
     * 获取企业的剩余可用码数量
     *
     * @param $companyid
     *
     * @return null
     */
    public static function getQrcodeLeft($companyid)
    {
        $model = D('base_qrcode_created_log');
        $modelMap = array(
            'companyid' => $companyid,
        );
        $list = $model->field('qrcode_left')->where($modelMap)->order('operate_time desc')->limit(1)->select();

        if (!empty($list))
        {
            return $list[0]['qrcode_left'];
        }
        else
        {
            return null;
        }
    }

    /**
     * 获取企业的用码数量
     *
     * @param $companyid
     *
     * @return null
     */
    public static function getQrcodeUsed($companyid)
    {
        $model = D('base_qrcode_used_log');
        $modelMap = array(
            'companyid' => $companyid,
        );
        $list = $model->field('sum(qrcode_counts) as qrcode_used')->where($modelMap)->select();

        if (!empty($list))
        {
            return $list[0]['qrcode_used'];
        }
        else
        {
            return null;
        }
    }

}