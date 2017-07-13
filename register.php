<?php
/*
	Module Register
	Register page for company users.
	Revision 00024
	By James "Carbon" leon Neo
*/
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	ob_start();
	require_once "index.php";
	ob_clean();
	$username = htmlspecialchars(trim($_POST["username"]));
	$password = htmlspecialchars(trim($_POST["password"]));
	$phone = htmlspecialchars(trim($_POST["phone"]));
	$company = htmlspecialchars(trim($_POST["company"]));
	$response = Common\API\EntranceAPI::register($username, $password, $phone, $company);
	echo json_encode($response);
	die;
}
?>
<!doctype html>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>质码产品质量追溯管理平台</title>
		<link rel="stylesheet" href="/resources/css/message.css" />
		<link rel="stylesheet" href="/resources/css/entrance/register.css" />
	</head>
	<body>
	<div class="bg"></div>
	<article>
		<section>
			<h2 class="logotitile"><img src="/resources/images/logo.png">产品质量追溯管理平台</h2>
		</section>

		<section>
			<button><a href="/login.php">登录</a></button>
			<button class="active"><a href="/register.php">注册</a></button>
		</section>

		<section class="sign_up">
			<div>
				<label for="username">
					<img src="/resources/images/user.png" alt="">
				</label>
				<input type="text" name="username" id="username" maxlength="16" data-name="用户名" placeholder="用户名" />
			</div>
			<div>
				<label for="phone">
					<img src="/resources/images/phone.png" alt="">
				</label>
				<input type="text" name="phone" id="phone" data-name="手机号码" placeholder="手机号码" />
			</div>
			<div>
				<label for="password">
					<img src="/resources/images/password.png" alt="">
				</label>
				<input type="password" name="password" id="password" data-name="密码" placeholder="密码" />
			</div>
			<div>
				<label for="reconfirm">
					<img src="/resources/images/password.png" alt="">
				</label>
				<input type="password" name="reconfirm" id="reconfirm" data-name="确认密码" placeholder="确认密码" />
			</div>
			<div>
				<label for="company">
					<img src="/resources/images/company.png" alt="">
				</label>
				<input type="text" name="company" id="company" data-name="企业名称" placeholder="企业名称" />
			</div>
		</section>
		<section>
			<button id="submit" class="submit">立即注册</button>
		</section>
	</article>
	<script type="text/javascript" src="/resources/js/jquery.min.js"></script>
	<script type="text/javascript" src="/resources/js/methods.js"></script>
	<script type="text/javascript" src="/resources/js/getData_test.js"></script>
	<script type="text/javascript" src="/resources/js/message_test.js"></script>
	<script type="text/javascript" src="/resources/js/entrance/register.js"></script>
	</body>
</html>
