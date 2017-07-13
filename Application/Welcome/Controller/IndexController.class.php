<?php
namespace Welcome\Controller;

use Common\Controller\BaseController;
use Common\Utility\Auth;

class IndexController extends BaseController
{
	public function welcome()
	{
		if ($_SESSION["user_info"]["groups"][0] == 4)
		{
			header("Location: /Government/Company");
			return;
		}
		if (!Auth::check("Unauthenticated"))
		{
			$this -> display("welcome");
		}
		else
		{
			$this -> display("registerSuccess");
		}
	}
}
