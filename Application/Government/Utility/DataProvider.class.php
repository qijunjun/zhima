<?php
namespace Government\Utility;

use Common\Utility\RegionManager;

class DataProvider
{
	public static function regionInfo()
	{
		$result = $_SESSION["user_info"]["region"];
		if (!is_array($result))
		{
			$region = M("DictRegion");
			$user_info = $_SESSION["user_info"];
			$type = intval($user_info["type"]);
			$cond = array();
			$fields = "region_name name, region_code code, center_longitude longitude, center_latitude latitude";
			switch ($type)
			{
				case 1:
					$cond["region_id"] = intval($user_info["province"]);
					break;
				case 2:
					$cond["region_id"] = intval($user_info["city"]);
					break;
				case 3:
					$cond["region_id"] = intval($user_info["district"]);
					break;
			}
			$result = $region -> where($cond) -> field($fields) -> find();
			$_SESSION["user_info"]["region"] = $result;
		}
		return $result;
	}

	public static function fetchCompanies($page, $size, $conditions, $sort)
	{
		$company = M("BaseCompany");
		$correlation = M("CorrGovernmentCompany");
		$order = array(
			"id" => "asc"
		);
		$fields = "id, name, address, logo, contact, introduction, phone, longitude, latitude";
		$user_info = $_SESSION["user_info"];
		$cond = array(
			"government_id" => $user_info["id"]
		);
		$subQuery = $correlation -> where($cond) -> field("company_id") -> buildSql();
		$criteria = array(
			"id" => array("exp", "IN " . $subQuery)
		);
		if ($sort !== null)
		{
			$order = array_merge($sort, $order);
		}
		$result = retrieve_data_advanced($company, $fields, $criteria, $page, $size, $order);
		return $result;
	}

	public static function companyInfo($companyId)
	{
		$company = M("BaseCompany");
		$cond = array(
			"id" => $companyId
		);
		$result = $company -> where($cond) -> find();
		return $result;
	}

	public static function fetchProducts($companyId, $page, $size, $conditions, $sort)
	{
		$product = M("BaseProduct");
		$fields = "productid, productname, productinfo, price, guige, productimage, create_time";
		$criteria = array(
			"companyid" => $companyId
		);
		$order = array(
			"productid" => "asc"
		);
		$result = retrieve_data_advanced($product, $fields, $criteria, $page, $size, $order);
		return $result;
	}

	public static function fetchCompaniesByGov()
	{
		$correlation = M("CorrGovernmentCompany");
		$user_info = $_SESSION["user_info"];
		$cond = array(
			"government_id" => $user_info["id"]
		);
		$result = $correlation -> where($cond) -> field("company_id") -> select();
		return $result;
	}
}
