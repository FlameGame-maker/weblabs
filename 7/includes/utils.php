<?php

$conf = array(
	'login' => 'u82185',
	'password' => '7586396',
	'timeout' => 3600,
	'token_length' => 32,
	'db_name' => 'u82185',
	'db_user' => 'u82185',
	'db_pass' => '7586396'
);

function conf($key) {
  global $conf;
  return isset($conf[$key]) ? $conf[$key] : FALSE;
}

function generateToken($user_id = null) {
	$token = bin2hex(random_bytes(conf('token_length')));
		
	$expires_at = date('Y-m-d H:i:s', time() + conf('timeout'));
	   
	$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "INSERT INTO xhr_tokens (token, user_id, expires_at) VALUES (:token, :user_id, :expires_at)";
		
	$stmt = $pdo->prepare($sql);
	$stmt->execute([
		':token' => $token,
		':user_id' => $user_id,
		':expires_at' => $expires_at
	]);
		
	return $token;
}

function cleanExpiredTokens() {
	$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "DELETE FROM xhr_tokens WHERE expires_at < NOW()";
	$pdo->exec($sql);
}

function validateToken($token, $user_id = null) {
	cleanExpiredTokens();

	$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM xhr_tokens WHERE token = :token AND expires_at > NOW()";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([':token' => $token ]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if (!$result) return false;
	if ($user_id !== null && $result['user_id'] != $user_id) return false;

	$sql = "DELETE FROM xhr_tokens WHERE token = :token";
	$stmt = $pdo->prepare($sql);
	$pdo->exec($sql);
	$stmt->execute([ ':token' => $token ]);

	return true;
}
