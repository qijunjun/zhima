<?php
namespace Government\API;

use Government\Utility\DataProvider;

class CompanyAPI
{
	public static function fetchCompanies($page, $size, $conditions, $sort)
	{
		$result = DataProvider::fetchCompanies($page, $size, $conditions, $sort);
		$flag = $result !== false;
		$code = $flag ? "001" : "007";
		$msg = $flag ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function companyInfo($companyId)
	{
		$result = DataProvider::companyInfo($companyId);
		$flag = $result !== false;
		$code = $flag ? "001" : "007";
		$msg = $flag ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
	public static function fetchProducts($companyId, $page, $size, $conditions, $sort)
	{
		$result = DataProvider::fetchProducts($companyId, $page, $size, $conditions, $sort);
		$flag = $result !== false;
		$code = $flag ? "001" : "007";
		$msg = $flag ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	/*
	 * 判断公司是否为当前政府用户的管辖范围
	 * $companyid:被检查的公司id
	 * 属于政府管辖返回true,否则返回false
	 * by david
	 */
	public static function inCompass($companyid)
	{
		$arr = DataProvider::fetchCompaniesByGov();
		$res = array_filter($arr, function($t) use ($companyid) { return $t['company_id'] == $companyid; });
		if($res)
		{
			return true;
		}
		else{
			return false;
		}
	}
}
