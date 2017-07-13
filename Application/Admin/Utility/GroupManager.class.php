<?php
/*
	Module GroupManager
	Utility functions to manage user group and its priviledge.
	Revision 00016
	By James "Carbon" leon Neo
*/
namespace Admin\Utility;

class GroupManager
{
	public static function fetchGroups($forced = false)
	{
		$groups = $forced ? null : F("groups");
		if (!is_array($groups))
		{
			$group = M("BaseGroup");
			$data = $group -> select();
			if ($data !== false)
			{
				$groups = array();
				foreach ($data as $item)
				{
					$id = $item["id"];
					$item["status"] = $item["status"] == 1;
					unset($item["id"]);
					$groups[$id] = $item;
				}
				F("groups", $groups);
			}
			else
			{
				return false;
			}
		}
		$result = $groups;
		return $result;
	}

	public static function addGroup($title, $description, $rules)
	{
		$group = M("BaseGroup");
		$data = array(
			"title"       => $title,
			"description" => $description,
			"rules"       => $rules
		);
		$result = $group -> data($data) -> add();
		if ($result !== false)
		{
			$groups = F("groups");
			$item = $data;
			$item["status"] = true;
			$groups[$result] = $item;
			F("groups", $groups);
			$result = array(
				"id"          => $result,
				"title"       => $title,
				"description" => $description,
				"status"      => true,
				"rules"       => $rules
			);
		}
		return $result;
	}

	public static function editGroup($id, $title, $description, $status, $rules)
	{
		$group = M("BaseGroup");
		$data = array(
			"title"       => $title,
			"description" => $description,
			"status"      => $status,
			"rules"       => $rules
		);
		$result = $group -> where("id=" . $id) -> setField($data);
		if ($result !== false)
		{
			$groups = F("groups");
			$data["status"] = $data["status"] == 1;
			$groups[$id] = $data;
			F("groups", $groups);
			$result = $data;
		}
		return $result;
	}
}
