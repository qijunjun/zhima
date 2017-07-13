<?php
namespace Produce\Utility;

class ProfileManager
{
	public static function fetchProfile()
	{
		$companyId = $_SESSION["user_info"]["companyid"];
		$product = M("BaseProduct");
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"company_id" => $companyId
		);
		$products = $product -> where("companyid = " . $companyId) -> getField("productid id, productname name, guige spec", true);
		$profiles = $profile -> where($cond) -> select();
		$result = array();
		foreach ($profiles as $item)
		{
			$row = $products[$item["product_id"]];
			$id = $item["_id"];
			unset($item["_id"]);
			unset($item["company_id"]);
			unset($item["product_id"]);
			$row["list"] = $item;
			$row["_id"] = $id;
			unset($row["id"]);
			array_push($result, $row);
		}
		return $result;
	}

	public static function addProfile($productId, $list)
	{
		$companyId = $_SESSION["user_info"]["companyid"];
		$profile = MM("BaseProductprofile","zm_");
		$data = array(
			"company_id" => $companyId,
			"product_id" => $productId
		);
		$data["name"] = $list["name"];
		unset($list["name"]);
		foreach (array_keys($list) as $key)
		{
			$data[$key] = $list[$key];
		}
		$result = $profile -> data($data) -> add();
		return $result;
	}

	public static function editProfile($id, $list)
	{
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"_id" => $id
		);
		$data = array();
		foreach (array_keys($list) as $key)
		{
			$data[$key] = $list[$key];
		}
		$result = $profile -> where($cond) -> setField($data);
		return $result;
	}

	public static function removeProfile($id)
	{
		$profile = MM("BaseProductprofile","zm_");
		$cond = array(
			"_id" => $id
		);
		$result = $profile -> where($cond) -> delete();
		return $result;
	}
}
