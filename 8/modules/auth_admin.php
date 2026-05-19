<?php

function auth(&$request, $r) {
  if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
	session_unset();
	session_destroy();
  }
  $users = array(conf('login') => conf('password'));
  if (empty($user) && !empty($_SERVER['PHP_AUTH_USER'])) {
    $user = array(
      'login' => $_SERVER['PHP_AUTH_USER'],
      'pass' => $users[$_SERVER['PHP_AUTH_USER']]
    );
    $request['user'] = $user;
  }
  if (!isset($_SERVER['PHP_AUTH_USER']) || empty($user) || $_SERVER['PHP_AUTH_USER'] != $user['login'] || $_SERVER['PHP_AUTH_PW'] != $user['pass']) {
    unset($user);
    $response = array(
      'headers' => array(sprintf('WWW-Authenticate: Basic realm="%s"', conf('sitename')), 'HTTP/1.0 401 Unauthorized'),
      'entity' => theme('401', $request),
    );
    return $response;
  } else {
    $_SESSION['timeout'] = time() + conf('timeout');
  }
}
