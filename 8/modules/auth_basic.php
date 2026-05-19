<?php

include('tokens.php');

function auth(&$request, $r) {
  if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
	session_unset();
	session_destroy();
  }
  if (empty($user) && !empty($_SERVER['PHP_AUTH_USER'])) {
    $entry = db_query('SELECT id, login, password_hash FROM app_users WHERE login = :login', $_SERVER['PHP_AUTH_USER']);
    if ($entry == false) $user = array();
    else $user = array('uid' => $entry['id'], 'login' => $entry['login'], 'pass_hash' => $entry['password_hash']);
    $request['user'] = $user;
  }
  if (!isset($_SERVER['PHP_AUTH_USER']) || empty($user) || $_SERVER['PHP_AUTH_USER'] != $user['login'] || !password_verify($_SERVER['PHP_AUTH_PW'], $user['pass_hash'])) {
    unset($user);
    $response = array(
      'headers' => array(sprintf('WWW-Authenticate: Basic realm="%s"', conf('sitename')), 'HTTP/1.0 401 Unauthorized'),
      'entity' => theme('401', $request),
    );
    return $response;
  } else {
    $_SESSION['uid'] = $user['uid'];
    $_SESSION['timeout'] = time() + conf('timeout');
    generateToken($user['uid']);
  }
}
