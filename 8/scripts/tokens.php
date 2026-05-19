<?php

include_once('db.php');

function generateToken($user_id = null) {
	$token = bin2hex(random_bytes(conf('token_length')));
		
	$expires_at = date('Y-m-d H:i:s', time() + conf('timeout'));

	if (isset($user_id)) db_command("INSERT INTO xhr_tokens (token, user_id, expires_at) VALUES (:token, :user_id, :expires_at)", [':token' => $token, ':user_id' => intval($user_id), ':expires_at' => $expires_at]);
	else db_command("INSERT INTO xhr_tokens (token, user_id, expires_at) VALUES (:token, NULL, :expires_at)", [':token' => $token, ':expires_at' => $expires_at]);
	
	return $token;
}

function cleanExpiredTokens() {
	db_command("DELETE FROM xhr_tokens WHERE expires_at < NOW()");
}

function validateToken($token, $user_id = null) {
	cleanExpiredTokens();

	$result = db_row("SELECT * FROM xhr_tokens WHERE token = :token AND expires_at > NOW()", [ ':token' => $token ]);
	
	if (!$result) return false;
	if ($user_id !== null && $result['user_id'] != $user_id) return false;

	db_command("DELETE FROM xhr_tokens WHERE token = :token", [ ':token' => $token ]);

	return true;
}
?>