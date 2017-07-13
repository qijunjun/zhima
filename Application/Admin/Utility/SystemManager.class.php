<?php
/*
	Module SystemManager
	Utility functions to manage system configurations.
	Revision 00009
	By James "Carbon" leon Neo
*/
namespace Admin\Utility;

class SystemManager
{
	public static function clearTemp()
	{
		$result = true;
		$folders = glob(CACHE_PATH . "*", GLOB_ONLYDIR);
		foreach ($folders as $folder)
		{
			$files = glob($folder . "/*.php");
			foreach ($files as $file)
			{
				if (!unlink($file))
				{
					return false;
				}
			}
			if (rmdir($folder))
			{
				return false;
			}
		}
		return $result;
	}

	public static function updateCache()
	{
		$files = glob(DATA_PATH . "*.php");
		foreach ($files as $file)
		{
			$filename = basename($file);
			// Region list get updated individually.
			if ($filename == "regions.php")
			{
				continue;
			}
			unlink($file);
		}
		RuleManager::fetchRules(true);
		MenuManager::fetchMenus(true);
		GroupManager::fetchGroups(true);
		ProfileManager::fetchProfiles(true);
		return true;
	}
}
