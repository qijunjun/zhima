<?php
namespace Common\API;

use Common\Utility\RegionManager;

class RegionAPI
{
	public static function fetchRegions()
	{
		$result = RegionManager::fetchRegions();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function fetchProvinces()
	{
		$result = RegionManager::fetchProvinces();
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function fetchCities($provinceId)
	{
		$result = RegionManager::fetchCities($provinceId);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function fetchDistricts($cityId)
	{
		$result = RegionManager::fetchDistricts($cityId);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
