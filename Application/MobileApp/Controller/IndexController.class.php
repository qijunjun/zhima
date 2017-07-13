<?php
namespace MobileApp\Controller;

use Think\Controller;

class IndexController extends Controller
{
	public function index()
	{
		if (!$_COOKIE["Very_First_Open"])
		{
			$this -> redirect("splash");
		}
		else
		{
			$this -> display();
		}
	}

	public function splash()
	{
		setcookie("Very_First_Open", 1, 32503651199, "/");
		$this -> display();
	}
}