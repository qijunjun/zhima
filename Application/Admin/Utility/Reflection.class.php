<?php
/*
	Module Reflection
	Common functions for frameworks reflection based on ThinkPHP
	Revision 00025
	By James "Carbon" leon Neo
*/
namespace Admin\Utility;

class Reflection
{
	public static function getModules()
	{
		$modules = array();
		$i = 0;
		$RuntimePath = pathinfo(RUNTIME_PATH, PATHINFO_DIRNAME) . "/" . pathinfo(RUNTIME_PATH, PATHINFO_FILENAME);
		$CommonPath = pathinfo(COMMON_PATH, PATHINFO_DIRNAME) . "/" . pathinfo(COMMON_PATH, PATHINFO_FILENAME);
		$HTMLPath = pathinfo(HTML_PATH, PATHINFO_DIRNAME) . "/" . pathinfo(HTML_PATH, PATHINFO_FILENAME);
		$AddonPath = pathinfo(ADDON_PATH, PATHINFO_DIRNAME) . "/" . pathinfo(ADDON_PATH, PATHINFO_FILENAME);
		$folders = glob(APP_PATH . "*", GLOB_ONLYDIR);
		foreach ($folders as $folder)
		{
			if ($folder != $RuntimePath && $folder != $CommonPath && $folder != $HTMLPath && $folder != $AddonPath)
			{
				$modules[$i++] = basename($folder);
			}
		}
		return $modules;
	}

	public static function getControllers($module)
	{
		$controllers = array();
		$i = 0;
		$files = glob(APP_PATH . $module . "/Controller/*.class.php");
		foreach ($files as $file)
		{
			$name = basename($file, "Controller.class.php");
			if ($names[$name])
			{
				unset($names[$name]);
			}
			$path = $module . "\\Controller\\" . basename($file, ".class.php");
			$controllers[$i++] = array(
				"name"   => $name,
				"module" => $module,
				"path"   => $path
			);
		}
		return $controllers;
	}

	public static function getActions($controller)
	{
		$actions = array();
		$names = array();
		$i = 0;
		$files = glob(APP_PATH . $controller["module"] . "/View/" . $controller["name"] . "/*.html");
		foreach ($files as $file)
		{
			$name = basename($file, ".html");
			$names[$name] = $name;
		}
		try
		{
			$reflector = new \ReflectionClass($controller["path"]);
		}
		catch (\Exception $exception)
		{
			return;
		}
		$methods = $reflector -> getMethods();
		foreach ($methods as $method)
		{
			if ($method -> class == $controller["path"] && $method -> name != "__construct" && $method -> name != "__destruct")
			{
				$name = $method -> name;
				if (!$method -> isPublic() || $method -> isStatic() || $method -> isAbstract())
				{
					unset($names[$name]);
					continue;
				}
				$actions[$i++] = array(
					"name"       => $name,
					"module"     => $controller["module"],
					"controller" => $controller["name"]
				);
			}
		}
		foreach ($names as $name)
		{
			$actions[$i++] = array(
				"name"   => $name,
				"module"     => $controller["module"],
				"controller" => $controller["name"]
			);
		}
		return $actions;
	}
}
