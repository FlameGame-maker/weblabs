<?php

include('db.php');

function login_get($request) {
	if (!empty($_SESSION['login'])) {
		return redirect('form');
	}

	$contents['login_error'] = !empty($_COOKIE['login_error']) ? htmlspecialchars($_COOKIE['login_error']) : '';
	setcookie('login_error', 0, 1);
	
	header('Content-Type: text/html; charset='.conf('charset'));
	return theme('login', $contents);
}

function login_post($request) {
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';

	$app_user = db_row("SELECT id, login, password_hash FROM app_users WHERE login = :login", [':login' => $login]);

	if ($app_user !== false && password_verify($password, $app_user['password_hash'])) {
		if (!$session_started) {
			session_start();
		}
		setcookie('login_error', 0, 1);
		$_SESSION['login'] = $login;
		$_SESSION['uid'] = $user->id;

		return redirect('form');
	} else {
		setcookie('login_error', 1, time() + 24 * 60 * 60);
		return redirect('login');
	}
}