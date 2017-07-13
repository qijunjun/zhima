<?php
/**
 * Created by PhpStorm.
 * User: Yu
 * Date: 2016/8/24
 * Time: 17:52
 */

namespace Statistic\Controller;


use Common\Controller\BaseController;
use Statistic\API\CompanyAPI;
use Statistic\API\StatisticAPI;

class StatisticController extends BaseController
{

    public function updateData()
    {
        $this->getProductCounts();
        $this->getQrcodeBoughtCounts();
        $this->getQrcodeUsedCounts();
        $this->getQrcodeJoinedCounts();
        $this->getQrcodeCheckinCounts();
        $this->getQrcodeCheckoutCounts();
        $this->getQrcodeScanedCounts();
        $this->getQrcodeTipoffCounts();
    }

    public function getProductCounts()
    {
        $result = StatisticAPI::getProductCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;
                $data = array("company_id" => $item["companyid"], "product_count" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }

    public function getQrcodeBoughtCounts()
    {
        $result = StatisticAPI::getQrcodeBoughtCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;

                $data = array("company_id" => $item["companyid"], "qrcode_bought_count" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }

    public function getQrcodeUsedCounts()
    {
        $result = StatisticAPI::getQrcodeUsedCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;

                $data = array("company_id" => $item["companyid"], "qrcode_used_counts" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }

    public function getQrcodeJoinedCounts()
    {
        $result = StatisticAPI::getQrcodeJoinedCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;

                $data = array("company_id" => $item["companyid"], "qrcode_joined_counts" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }

    public function getQrcodeCheckinCounts()
    {
        $result = StatisticAPI::getCompanyList();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item)) continue;
                $counts = CompanyAPI::getQrcodeCheckinCounts($item);
                if (empty($counts)) continue;

                $data = array("company_id" => $item, "qrcode_checkin_counts" => $counts);
                CompanyAPI::saveData($data);
                $list[] = array("companyid" => $item, "counts" => $counts);
            }
        }
        json("001", $list);
    }

    public function getQrcodeCheckoutCounts()
    {
        $result = StatisticAPI::getCompanyList();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item)) continue;
                $counts = CompanyAPI::getQrcodeCheckoutCounts($item);
                if (empty($counts)) continue;

                $data = array("company_id" => $item, "qrcode_checkout_counts" => $counts);
                CompanyAPI::saveData($data);
                $list[] = array("companyid" => $item, "counts" => $counts);
            }
        }
        json("001", $list);
    }

    public function getQrcodeScanedCounts()
    {
        $result = StatisticAPI::getQrcodeScanedCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;

                $data = array("company_id" => $item["companyid"], "qrcode_scaned_counts" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }

    public function getQrcodeTipoffCounts()
    {
        $result = StatisticAPI::getQrcodeTipoffCounts();
        if (!empty($result))
        {
            foreach ($result as $item)
            {
                if (empty($item["companyid"]) || empty($item["counts"])) continue;

                $data = array("company_id" => $item["companyid"], "qrcode_tipoff_counts" => $item["counts"]);
                CompanyAPI::saveData($data);
            }
        }
        json("001", $result);
    }
}