<?php

header('Content-Type: text/html; charset=UTF-8');

include('includes/utils.php');

$session_started = false;

if (session_start()) {
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
	

	<?php
}

else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';

	$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
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
