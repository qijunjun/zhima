<?php
namespace Admin\API;

use Admin\Utility\Reflection;

class ReflectionAPI
{
	public static function getNodes($forced = false)
	{
		$nodes = $forced ? null : S("nodes") ;
		if (!is_array($nodes))
		{
			$nodes = array();
			$modules = Reflection::getModules();
			foreach ($modules as $module)
			{
				$nodes[$module] = array(
					"name" => $module,
					"path" => $module,
				);
				$controllers = Reflection::getControllers($module);
				foreach ($controllers as $controller)
				{
					$controllerPath = $module . "/" . $controller["name"];
					$nodes[$controllerPath] = array(
						"name" => $controller["name"],
						"path" => $controllerPath,
					);
					$actions = Reflection::getActions($controller);
					foreach ($actions as $action)
					{
						$actionPath = $controllerPath . "/" . $action["name"];
						$nodes[$actionPath] = array(
							"name" => $action["name"],
							"path" => $actionPath,
						);
						$i++;
					}
					if (empty($actions))
					{
						unset($nodes[$controllerPath]);
					}
				}
				if (empty($controllers))
				{
					unset($nodes[$module]);
				}
			}
			S("nodes", $nodes, 3600);
		}
		return $nodes;
	}
}
