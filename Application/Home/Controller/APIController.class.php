<?php
namespace Home\Controller;

use Think\Controller;
use Common\API\EntranceAPI;

class APIController extends Controller
{
	public function repassword($oldPassword, $newPassword)
	{
		if (!isset($_SESSION["user_info"]))
		{
			header("Location: /login.php");
		}
		else
		{
			$user_id = $_SESSION["user_info"]["id"];
			$result = EntranceAPI::repassword($user_id, $oldPassword, $newPassword);
		}
		echo json_encode($result);
	}
}
