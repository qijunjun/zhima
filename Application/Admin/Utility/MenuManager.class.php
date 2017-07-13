<?php
/*
	Module MenuManager
	Functions to manage interface menus.
	Revision 00066
	By James "Carbon" leon Neo
*/
namespace Admin\Utility;

class MenuManager
{
	public static function fetchMenus($forced = false)
	{
		$menus = $forced ? null : F("menus");
		if (!is_array($menus))
		{
			$menu = M("BaseMenu");
			$data = $menu -> select();
			$rules = F("rule_map");
			if (!is_array($rules))
			{
				RuleManager::fetchRules();
				$rules = F("rule_map");
			}
			if ($data !== false && is_array($rules))
			{
				$menus = array();
				$tree = array();
				$menu_rmap = array();
				$menu_rule = array();
				foreach ($data as $item)
				{
					$rule_id = $item["rule_id"];
					if ($rule_id == null)
					{
						$id = $item["id"];
						$item["status"] = $item["status"] == 1;
						unset($item["id"]);
						unset($item["rule_id"]);
						unset($item["parent_id"]);
						$menus[$id] = $item;
						$tree[$id] = array();
					}
					else
					{
						$id = $item["id"];
						$rule_id = $item["rule_id"];
						$path = $rules[$rule_id];
						$item["path"] = $path;
						unset($item["id"]);
						unset($item["rule_id"]);
						$menus[$id] = $item;
						array_push($tree[$item["parent_id"]], $id);
						$menu_rmap[$id] = $path;
						$menu_rule[$id] = $rule_id;
					}
				}
				F("menus", $menus);
				F("menu_tree", $tree);
				F("menu_map", array_flip($menu_rmap));
				F("menu_rmap", $menu_rmap);
				F("menu_rule", $menu_rule);
				F("rule_menu", array_flip($menu_rule));
				self::fetchIcons($forced);
			}
			else
			{
				$result = false;
			}
		}
		$result = $menus;
		return $result;
	}

	public static function fetchIcons($forced = false)
	{
		$icons = $forced ? null : F("icons");
		if (!is_array($icons))
		{
			$icons = array();
			$files = glob(__UPLOAD__ . "menu_icons/*.svg");
			foreach ($files as $file)
			{
				$id = basename($file, ".svg");
				$icons[$id] = file_get_contents($file);
			}
			F("icons", $icons);
		}
		$result = $icons;
		return $result;
	}

	public static function addMenu($path, $title, $parentId, $icon)
	{
		$menu = M("BaseMenu");
		$menu -> startTrans();
		$data = array(
			"title"     => $title,
			"parent_id" => $parentId
		);
		if ($parentId)
		{
			$rule_rmap = F("rule_rmap");
			$rule_id = $rule_rmap[$path];
			if ($rule_id == null)
			{
				$result = RuleManager::addRule($path, $title, 0, "");
				if ($result !== false)
				{
					$data["rule_id"] = $result;
				}
			}
			else
			{
				$result = RuleManager::editRule($rule_id, $path, $title, 0, 1, null);
				if ($result !== false)
				{
					$data["rule_id"] = $rule_id;
				}
			}
		}
		if ($result !== false)
		{
			$result = $menu -> data($data) -> add();
		}
		if ($result === false)
		{
			$menu -> rollback();
		}
		else
		{
			$menu -> commit();
			$menus = F("menus");
			$tree = F("menu_tree");
			$icons = F("icons");
			if ($path == "")
			{
				$item = array(
					"title" => $title,
					"status" => 1
				);
				$tree[$result] = array();
				mkdir(__UPLOAD__ . "menu_icons/", 0755, true);
				file_put_contents(__UPLOAD__ . "menu_icons/$result.svg", $icon, LOCK_EX);
				$icons[$result] = $icon;
				F("icons", $icons);
			}
			else
			{
				$menu_rmap = F("menu_rmap");
				$menu_rule = F("menu_rule");
				$item = array(
					"title"     => $title,
					"status"    => 1,
					"parent_id" => $parentId,
					"path"      => $path
				);
				array_push($tree[$parentId], $result);
				$menu_rmap[$result] = $path;
				$menu_rule[$result] = $data["rule_id"];
				F("menu_map", array_flip($menu_rmap));
				F("menu_rmap", $menu_rmap);
				F("menu_rule", $menu_rule);
				F("rule_menu", array_flip($menu_rule));
			}
			$menus[$result] = $item;
			F("menus", $menus);
			F("menu_tree", $tree);
			$result = array(
				"id"        => $result,
				"path"      => $path,
				"title"     => $title,
				"status"    => 1,
				"parent_id" => $parentId
			);
			if ($path == "")
			{
				$result["icon"] = $icon;
			}
		}
		return $result;
	}

	public static function editMenu($id, $path, $title, $status, $icon)
	{
		$menu = M("BaseMenu");
		$menus = F("menus");
		$tree = F("menu_tree");
		$menu_rmap = F("menu_rmap");
		$rule_rmap = F("rule_rmap");
		$menu -> startTrans();
		$data = array(
			"title"  => $title,
			"status" => $status
		);
		$flag = $tree[$id] == null;
		$old_path = $flag ? $menu_rmap[$id] : "";
		$rule_id = $flag ? $rule_rmap[$old_path] : null;
		if ($rule_id != null)
		{
			$name = $old_path != $path ? $path : null;
			$result = RuleManager::editRule($rule_id, $name, $title, null, $status, null);
		}
		if ($result !== false)
		{
			$result = $menu -> where("id = " . $id) -> setField($data);
		}
		if ($result === false)
		{
			$menu -> rollback();
		}
		else
		{
			$menu -> commit();
			$menus = F("menus");
			$menus[$id]["title"] = $title;
			$menus[$id]["status"] = $status;
			if ($path != "")
			{
				$menus[$id]["path"] = $path;
				$menu_rmap[$id] = $path;
				F("menu_map", array_flip($menu_rmap));
				F("menu_rmap", $menu_rmap);
			}
			else if ($icon !== null)
			{
				$icons = F("icons");
				mkdir(__UPLOAD__ . "menu_icons/", 0755, true);
				file_put_contents(__UPLOAD__ . "menu_icons/$id.svg", $icon, LOCK_EX);
				$icons[$id] = $icon;
				F("icons", $icons);
			}
			F("menus", $menus);
			$result = array(
				"id"     => $id,
				"path"   => $path,
				"title"  => $title,
				"status" => $status
			);
			if ($path == "")
			{
				$result["icon"] = $icon;
			}
		}
		return $result;
	}

	public static function removeMenu($id)
	{
		$menu = M("BaseMenu");
		$menus = F("menus");
		$tree = F("menu_tree");
		$menu_rmap = F("menu_rmap");
		$rule_rmap = F("rule_rmap");
		$menu_rule = F("menu_rule");
		$menu -> startTrans();
		$flag = $tree[$id] == null;
		$data = array(
			"rule_id"   => $flag ? $menu_rule[$id] : null,
			"parent_id" => $flag ? $menus[$id]["parent_id"] : 0
		);
		if ($data["parent_id"] > 0)
		{
			$result = RuleManager::editRule($data["rule_id"], null, null, 1, 0, null);
		}
		else
		{
			$sub_menus = $tree[$id];
			foreach ($sub_menus as $item)
			{
				$rule_id = $menu_rule[$item];
				$result = RuleManager::editRule($rule_id, null, null, 1, 0, null);
				if ($result === false)
				{
					break;
				}
			}
			if ($result !== false)
			{
				$result = $menu -> where("parent_id = " . $id) -> delete();
			}
		}
		if ($result !== false)
		{
			$result = $menu -> where("id = " . $id) -> delete();
		}
		if ($result === false)
		{
			$menu -> rollback();
		}
		else
		{
			$menu -> commit();
			if ($flag)
			{
				$parent_id = $data["parent_id"];
				$sub_menus = $tree[$parent_id];
				$index = array_search($id, $sub_menus);
				if ($index !== false)
				{
					array_splice($sub_menus, $index, 1);
					$tree[$parent_id] = $sub_menus;
				}
				unset($menu_rmap[$id]);
			}
			else
			{
				unlink(__UPLOAD__ . "menu_icons/$id.svg");
				$icons = F("icons");
				unset($icons[$id]);
				foreach ($sub_menus as $item)
				{
					unset($menus[$item]);
					unset($menu_rmap[$item]);
					unset($menu_rule[$item]);
				}
				unset($tree[$id]);
				F("icons", $icons);
			}
			unset($menus[$id]);
			F("menus", $menus);
			F("menu_tree", $tree);
			F("menu_map", array_flip($menu_rmap));
			F("menu_rmap", $menu_rmap);
			F("menu_rule", $menu_rule);
			F("rule_menu", array_flip($menu_rule));
		}
		return $result;
	}
}
