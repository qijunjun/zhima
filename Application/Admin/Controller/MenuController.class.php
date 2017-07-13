<?php
/*
	Class MenuController
	Controller class to manage menu entries.
	Revision 00011
	By James "Carbon" leon Neo
*/
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\MenuAPI;

class MenuController extends BaseController
{
	protected function _initialize()
	{
		parent::_initialize();
		if (ACTION_NAME != "index")
		{
			header("content-type: application/x-javascript");
		}
	}

	public function fetch()
	{
		$result = MenuAPI::fetchData();
		echo json_encode($result);
	}

	public function add($path, $title, $parentId)
	{
		$icon = $_POST["icon"];
		$response = MenuAPI::addMenu($path, $title, $parentId, $icon);
		echo json_encode($response);
	}

	public function edit($id, $path = null, $title = null, $status = 1)
	{
		$icon = $_POST["icon"];
		$response = MenuAPI::editMenu($id, $path, $title, $status, $icon);
		echo json_encode($response);
	}

	public function remove($id)
	{
		$response = MenuAPI::removeMenu($id);
		echo json_encode($response);
	}
}
