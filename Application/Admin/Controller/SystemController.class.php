<?php
/*
	Class SystemController
	Controller class to manage system configurations.
	Revision 00003
	By James "Carbon" leon Neo
*/
namespace Admin\Controller;

use Common\Controller\BaseController;
use Admin\API\SystemAPI;

class SystemController extends BaseController
{
	public function clearTemp()
	{
		$response = SystemAPI::clearTemp();
		echo json_encode($response);
	}

	public function updateCache()
	{
		$response = SystemAPI::updateCache();
		echo json_encode($response);
	}
}
