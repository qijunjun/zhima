<?php
/*
	Module GroupAPI
	Interface functions to manage user group and its priviledge.
	Revision 00016
	By James "Carbon" leon Neo
*/
namespace Admin\API;

use Admin\Utility\RuleManager;
use Admin\Utility\MenuManager;
use Admin\Utility\GroupManager;

class GroupAPI
{
	protected static function checkInput()
	{
		return true;
	}

	public static function fetchRules()
	{
		$rules = RuleManager::fetchRules();
		$menus = MenuManager::fetchMenus();
		$rule_menu = F("rule_menu");
		$menu_rule = F("menu_rule");
		$flag = is_array($rules) && is_array($menus);
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		if ($flag)
		{
			$map = array(
				"rule" => $rule_menu,
				"menu" => $menu_rule
			);
			$result = array(
				"rules" => $rules,
				"menus" => $menus,
				"map"   => $map
			);
		}
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function fetchGroups()
	{
		$result = GroupManager::fetchGroups();
		$flag = $result !== false;
		$code = $flag ? "001" : "006";
		$msg = $flag ? "Success" : "";
		$result = !$flag ? false : array(
			"current" => 1,
			"total"   => count($result),
			"rows"    => $result
		);
		return array(
			"code"   => $code,
			"msg"    => $msg,
			"result" => $result
		);
	}

	public static function addGroup($title, $description, $rules)
	{
		$result = null;
		$flag = GroupAPI::checkInput();
		if ($flag)
		{
			$result = GroupManager::addGroup($title, $description, $rules);
			$code = $result === false ? "007" : "001";
			$msg = $result === false ? "数据库错误" : "Success";
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

	public static function editGroup($id, $title, $description, $status, $rules)
	{
		$result = null;
		$flag = GroupAPI::checkInput();
		if ($flag)
		{
			$result = GroupManager::editGroup($id, $title, $description, $status, $rules);
			$code = $result === false ? "007" : "001";
			$msg = $result === false ? "数据库错误" : "Success";
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
}
