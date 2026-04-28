<?php

header('Content-Type: text/html; charset=UTF-8');

$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';

$session_started = false;

if (session_start() && ) {
	if ($_COOKIE[session_name()]) {
		$session_started = true;
		if (!empty($_SESSION['login'])) {
			header('Location: index.php');
			exit();
		}
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$login_error = !empty($_COOKIE['login_error']) ? $_COOKIE['login_error'] : '';
	setcookie('login_error', 0, 1);
	?>
	<style>
		body {
			background-color: black;
			min-height: 100vh;
			background-color: darkslateblue;
		}

		.form-container {
			position: absolute;
			width: 50%;
			min-width: 300px;
			height: auto;
			min-height: 250px;
			max-height: 90vh;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			background-color: white;
			padding: 20px;
			border-radius: 5px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			overflow-y: auto;
		}

		form {
			width: 100%;
			height: auto;
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: flex-start;
		}

		.label-form {
			width: 100%;
			margin-bottom: 5px;
			font-weight: bold;
		}

		.input-form {
			width: 100%;
			height: 2.5rem;
			border-radius: 20px;
			padding: 0 15px;
			border: 1px solid darkslateblue;
			font-size: 16px;
		}

		.form-btn {
			align-self: center;
			width: 40%;
			min-width: 120px;
			min-height: 45px;
			border-radius: 25px;
			background-color: darkslateblue;
			color: white;
			border: none;
			font-size: 16px;
			cursor: pointer;
		}
	</style>

	<body>
		<div class="form-container">
			<form action="" method="POST">
				<?php if ($login_error) print '<label class="label-form">Неверные логин или пароль.</label>' ?>
				<label class="label-form">Логин:<br><input type="text" name="login" class="input-form"></label>
				<label class="label-form">Пароль:<br><input type="text" name="password" class="input-form"></label>
				<button class="form-btn" type="submit">Войти</button>
			</form>
		</div>
	</body>

	<?php
}

else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';

	$pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = "SELECT id, login, password_hash FROM app_users WHERE login = :login";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([
		':login' => $login
	]);
	$app_user = $stmt->fetchObject();

	if ($app_user !== false && password_verify($password, $app_user->password_hash)) {
		if (!$session_started) {
			session_start();
		}
		setcookie('login_error', 0, 1);
		$_SESSION['login'] = $login;
		$_SESSION['uid'] = $user->id;

		header('Location: index.php');
	} else {
		setcookie('login_error', 1, time() + 24 * 60 * 60);
		header('Location: login.php');
	}
}
