<?php

header('Content-Type: application/json; charset=utf-8');
include('db.php');

function get_application_get($request, $url_param_1) {
	/*if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
		session_unset();
		session_destroy();
		header('WWW-Authenticate: Basic realm="Session Expired"');
		return unauthorized();
	}*/
	//echo implode(" ", $request());
	
	$id = intval($url_param_1);
	if ($id != 0) {
		$params = [':id' => $id];
		$data = db_row("SELECT * FROM application WHERE id = :id", $params);
		if ($data) {
			$langs = db_query("SELECT lang_id FROM application_langs WHERE application_id = :app_id;", [':app_id' => $id]);
			$data['langs'] = array_column($langs, 'lang_id');

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data);
			exit;
		}

		exit;
	}
}

?>
