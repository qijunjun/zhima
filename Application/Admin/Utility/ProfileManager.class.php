<?php
namespace Admin\Utility;

class ProfileManager
{
	public static function fetchProfiles($forced = false)
	{
		$profiles = $forced ? null : F("profiles");
		if (!is_array($profiles))
		{
			$profile = M("DictProductprofileItems");
			$profiles = $profile -> getField("id, profile", true);
			if (is_array($profiles))
			{
				F("profiles", $profiles);
			}
		}
		$result = $profiles;
		return $result;
	}

	public static function addProfile($name)
	{
		$profile = M("DictProductprofileItems");
		$data = array(
			"profile" => $name
		);
		$result = $profile -> data($data) -> add();
		if ($result !== false)
		{
			$profiles = F("profiles");
			$profiles[$result] = $name;
			F("profiles", $profiles);
		}
		return $result;
	}

	public static function editProfile($id, $name)
	{
		$profile = M("DictProductprofileItems");
		$cond = array(
			"id" => $id
		);
		$data = array(
			"profile" => $name
		);
		$result = $profile -> where($cond) ->setField($data);
		if ($result !== false)
		{
			$profiles = F("profiles");
			$profiles[$result] = $name;
			F("profiles", $profiles);
		}
		return $result;
	}

	public static function removeProfile($id)
	{
		$profile = M("DictProductprofileItems");
		$cond = array(
			"id" => $id
		);
		$result = $profile -> where($cond) -> delete();
		if ($result !== false)
		{
			$profiles = F("profiles");
			unset($profiles[$id]);
			F("profiles", $profiles);
		}
		return $result;
	}
}
