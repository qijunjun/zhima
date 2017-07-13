<?php
/*
	Module Login
	Login page for platform users.
	Revision 00004
	By James "Carbon" leon Neo
*/
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
	ob_start();
	require_once 'index.php';
	ob_clean();
	$username = htmlspecialchars(trim($_POST["username"]));
	$password = htmlspecialchars(trim($_POST["password"]));
	$response = Common\API\EntranceAPI::login($username, $password);
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
		<link rel="stylesheet" href="/resources/css/entrance/login.css" />
	</head>
	<body>
	<div class="bg"></div>
	<article>
		<section>
			<h2 class="logotitile"><img src="/resources/images/logo.png">产品质量追溯管理平台</h2>
		</section>
		<section>
			<button class="active"><a href="/login.php">登录</a></button>
			<button><a href="/register.php">注册</a></button>
		</section>
		<section class="login">
			<div>
				<label for="username">
					<img src="/resources/images/user.png" alt="">
				</label>
				<input type="text" name="username" id="username" data-name="用户名" placeholder="用户名" />
			</div>
			<div>
				<label for="password">
					<img src="/resources/images/password.png" alt="">
				</label>
				<input type="password" name="password" id="password" data-name="密码" placeholder="密码" />
			</div>
			<!--<div>
				<label for="code">
					<img src="/resources/images/code.png" alt="">
				</label>
				<input type="text" name="code" id="code" data-name="验证码" placeholder="验证码" />
				<button>获取验证码</button>
			</div>-->
		</section>
		<section>
			<button id="submit" class="submit">立即登录</button>
		</section>
	</article>
	<script type="text/javascript" src="/resources/js/jquery.min.js"></script>
	<script type="text/javascript" src="/resources/js/getData.js"></script>
	<script type="text/javascript" src="/resources/js/methods.js"></script>
	<script type="text/javascript" src="/resources/js/message.js"></script>
	<script type="text/javascript" src="/resources/js/entrance/login.js"></script>
	</body>
</html>
