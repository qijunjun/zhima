<?php
namespace Common\Controller;

use Think\Controller;
use Common\Utility\Auth;

class BaseController extends Controller
{
	protected function _initialize()
	{
		/*
		$path = array(MODULE_NAME, MODULE_NAME . "/" . CONTROLLER_NAME, MODULE_NAME . "/" . CONTROLLER_NAME . "/" . ACTION_NAME);
		if (!Auth::check($path))
		{
			dump(false);
			die;
		}
		*/
		if ($_SERVER["HTTP_REFERER"] == "")
		{
			$this -> redirect("/#" . __INFO__);
		}
	}
}
