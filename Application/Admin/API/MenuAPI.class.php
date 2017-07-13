<?php
namespace Admin\API;

use Admin\Utility\MenuManager;

class MenuAPI
{
	public static function checkInput()
	{
		return true;
	}

	public static function fetchData()
	{
		$nodes = ReflectionAPI::getNodes();
		$menus = MenuManager::fetchMenus();
		$rules = F("rule_rmap");
		$icons = F("icons");
		$result = null;
		$flag = is_array($menus) && is_array($rules);
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		if ($flag)
		{
			$result = array(
				"nodes" => $nodes,
				"menus" => $menus,
				"rules" => $rules,
				"icons" => $icons
			);
		}
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function addMenu($path, $title, $parentId, $icon)
	{
		$flag = MenuAPI::checkInput();
		if ($flag)
		{
			$result = MenuManager::addMenu($path, $title, $parentId, $icon);
			$flag = $result !== false;
			$code = $flag ? "001" : "007";
			$msg = $flag ? "Success" : "Database error";
		}
		else
		{
			$code = "002";
			$msg = "Invalid arguments";
		}
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function editMenu($id, $path, $title, $status, $icon)
	{
		$flag = MenuAPI::checkInput();
		if ($flag)
		{
			$result = MenuManager::editMenu($id, $path, $title, $status, $icon);
			$flag = $result !== false;
			$code = $flag ? "001" : "007";
			$msg = $flag ? "Success" : "Database error";
		}
		else
		{
			$code = "002";
			$msg = "Invalid arguments";
		}
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function removeMenu($id)
	{
		$result = MenuManager::removeMenu($id);
		$code = $result !== false ? "001" : "007";
		$msg = $result !== false ? "Success" : "Database error";
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}
}
