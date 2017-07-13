<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
	public function index()
	{
		$referer = $_SERVER["HTTP_REFERER"];
		$flag = strripos($referer, "login.php") || strripos($referer, "register.php");
		if (!isset($_SESSION["user_info"]) && !$flag)
		{
			header("Location: /login.php");
		}
		else
		{
			$this -> display();
		}
	}
}

