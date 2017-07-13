<?php
namespace Admin\Utility;

class RuleManager
{
	public static function fetchRules($forced = false)
	{
		$rules = $forced ? null : F("rules");
		if (!is_array($rules))
		{
			$rule = M("BaseRule");
			$data = $rule -> select();
			$rules = array();
			$rule_map = array();
			foreach ($data as $item)
			{
				$id = $item["id"];
				$name = $item["name"];
				$item["status"] = $item["status"] == 1;
				unset($item["id"]);
				$rules[$id] = $item;
				$rule_map[$id] = $item["name"];
			}
			F("rules", $rules);
			F("rule_map", $rule_map);
			F("rule_rmap", array_flip($rule_map));
		}
		$result = $rules;
		return $result;
	}

	public static function addRule($name, $title, $type, $condition)
	{
		$rule = M("BaseRule");
		$data = array(
			"name"      => $name,
			"title"     => $title,
			"type"      => $type,
			"condition" => $condition,
		);
		$result = $rule -> data($data) -> add();
		if ($result !== false)
		{
			$rules = F("rules");
			$rule_map = F("rule_map");
			$item = $data;
			$item["status"] = 1;
			$rules[$result] = $item;
			$rule_map[$result] = $name;
			F("rules", $rules);
			F("rule_map", $rule_map);
			F("rule_rmap", array_flip($rule_map));
		}
		return $result;
	}

	public static function editRule($id, $name = null, $title = null, $type = null, $status = null, $condition = null)
	{
		$rule = M("BaseRule");
		$data = array();
		if ($name !== null)
		{
			$data["name"] = $name;
		}
		if ($title !== null)
		{
			$data["title"] = $title;
		}
		if ($type !== null)
		{
			$data["type"] = $type;
		}
		if ($status !== null)
		{
			$data["status"] = $status;
		}
		if ($condition !== null)
		{
			$data["condition"] = $condition;
		}
		if (!count($data))
		{
			$result = false;
		}
		else
		{
			$result = $rule -> where("id=" . $id) -> setField($data);
		}
		if ($result !== false)
		{
			$rules = F("rules");
			if ($name !== null)
			{
				$rules[$id]["name"] = $name;
				$rule_map = F("rule_map");
				$rule_map[$id] = $name;
				F("rule_map", $rule_map);
				F("rule_rmap", array_flip($rule_map));
			}
			if ($title !== null)
			{
				$rules[$id]["title"] = $title;
			}
			if ($type !== null)
			{
				$rules[$id]["type"] = $type;
			}
			if ($status !== null)
			{
				$rules[$id]["status"] = $status;
			}
			if ($condition !== null)
			{
				$rules[$id]["condition"] = $condition;
			}
			F("rules", $rules);
		}
		return $result;
	}
}
