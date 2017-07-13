<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 16/5/7
 * Time: 下午3:31
 */
namespace Home\Controller;

use Think\Controller;
use Common\API\RegionAPI;

class UtilityController extends Controller
{
	public function regions()
	{
		$response = RegionAPI::fetchRegions();
		echo json_encode($response);
	}
}
