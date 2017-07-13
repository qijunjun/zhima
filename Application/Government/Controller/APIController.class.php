<?php
namespace Government\Controller;

use Think\Controller;
use Government\API\RegionAPI;

class APIController extends Controller
{
	public function region()
	{
		$result = RegionAPI::regionInfo();
		echo json_encode($result);
	}
}
