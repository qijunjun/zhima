<?php
namespace Common\Utility;

class RegionManager
{
	public static function fetchRegions()
	{
		$regions = F("regions");
		if (!is_array($regions))
		{
			$region = M("DictRegion");
			$fields = "region_id id, region_name name, region_code code, center_longitude longitude, center_latitude latitude, parent_id";
			$cond = array(
			);
			$cond["region_type"] = 1;
			$provinces = $region -> where($cond) -> getField($fields);
			$cond["region_type"] = 2;
			$cities = $region -> where($cond) -> getField($fields);
			$cond["region_type"] = 3;
			$districts = $region -> where($cond) -> getField($fields);
			if ($provinces !== false && $cities !== false && $districts !== false)
			{
				$regions = array();
				foreach ($districts as $item)
				{
					$parent_id = $item["parent_id"];
					unset($item["parent_id"]);
					if (!$cities[$parent_id]["districts"])
					{
						$cities[$parent_id]["districts"] = array();
					}
					array_push($cities[$parent_id]["districts"], $item);
				}
				foreach ($cities as $item)
				{
					$parent_id = $item["parent_id"];
					unset($item["parent_id"]);
					if (!$provinces[$parent_id]["cities"])
					{
						$provinces[$parent_id]["cities"] = array();
					}
					array_push($provinces[$parent_id]["cities"], $item);
				}
				foreach ($provinces as $item)
				{
					unset($item["parent_id"]);
					array_push($regions, $item);
				}
				F("regions", $regions);
			}
			else
			{
				$regions = false;
			}
		}
		$result = $regions;
		return $result;
	}

	public static function fetchProvinces()
	{
		$region = M("DictRegion");
		$fields = "region_id id, region_name name, region_code code, center_longitude longitude, center_latitude latitude";
		$cond = array(
			"region_type" => 1
		);
		$result = $region -> field($fields) -> where($cond) -> select();
		return $result;
	}

	public static function fetchCities($provinceId)
	{
		$region = M("DictRegion");
		$fields = "region_id id, region_name name, region_code code, center_longitude longitude, center_latitude latitude";
		$cond = array(
			"region_type" => 2,
			"parent_id"   => $provinceId
		);
		$result = $region -> field($fields) -> where($cond) -> select();
		return $result;
	}

	public static function fetchDistricts($cityId)
	{
		$region = M("DictRegion");
		$fields = "region_id id, region_name name, region_code code, center_longitude longitude, center_latitude latitude";
		$cond = array(
			"region_type" => 3,
			"parent_id"   => $cityId
		);
		$result = $region -> field($fields) -> where($cond) -> select();
		return $result;
	}
}
