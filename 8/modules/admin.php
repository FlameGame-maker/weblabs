<?php

include('validation.php');
include('db.php');
include('tokens.php');

function admin_get($request) {
	if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
		session_unset();
		session_destroy();
		header('WWW-Authenticate: Basic realm="Session Expired"');
		return unauthorized();
	}

	$contents = array();
	$contents['messages'] = array();
	$contents['errors'] = array();
	$fields = ['fio', 'phone', 'email', 'birthday', 'sex', 'langs', 'bio', 'consent'];
	foreach ($fields as $field) {
		$contents['errors'][$field] = empty($_COOKIE[$field.'_error']) ? '' : (is_numeric($_COOKIE[$field.'_error']) ? '' : $_COOKIE[$field.'_error']);
		if ($contents['errors'][$field]) {
			setcookie($field.'_error', '', 100000);
			setcookie($field.'_value', '', 100000);
			$contents['messages'][$field] = '<div class="error">'.getErrorMessage($field, $contents['errors'][$field]).'</div>';
		}
	}

	$stats = db_query("SELECT langs.name, COUNT(*) as count FROM application app INNER JOIN application_langs applangs ON app.id = applangs.application_id INNER JOIN langs ON langs.id = applangs.lang_id GROUP BY langs.id, langs.name;");
	$contents['names'] = array_column($stats, 'name');
	$contents['counts'] = array_column($stats, 'count');

	$contents['applications'] = db_query("SELECT * FROM application;");

	foreach($contents['applications'] as &$app){
		$params = [':app_id' => $app['id']];
		$langs = db_query("SELECT langs.name FROM langs JOIN application_langs ON langs.id = application_langs.lang_id WHERE application_id = :app_id;", $params);
		$app['langs'] = implode(' ', array_column($langs, 'name'));
	}
	unset($app);

	$contents['token'] = generateToken();

	header('Content-Type: text/html; charset='.conf('charset'));
	return theme('admin', $contents);
}

function admin_put($request){
	if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
		session_unset();
		session_destroy();
		header('WWW-Authenticate: Basic realm="Session Expired"');
		return unauthorized();
	}
	
	$token = $_POST['token'] ?? '';
	if (validateToken($token)) {
		$result = db_row("SELECT id FROM application WHERE id = :id", [':id' => intval($_POST['id'])]);

		if($result !== false) {
			$fio = $_POST['fio'] ?? '';
			$phone = $_POST['phone'] ?? '';
			$email = $_POST['email'] ?? '';
			$birthday = $_POST['birthday'] ?? '';
			$sex = $_POST['sex'] ?? '';
			$languages = $_POST['languages'] ?? [];
			$bio = $_POST['bio'] ?? '';

			$inputErrors = validateAll($fio, $phone, $email, $birthday, $sex, $languages, $bio, true);
			if (!$inputErrors) {
				$parts = explode(' ', $fio);
				$fioParts = [
					'surname' => $parts[0] ?? '',
					'name' => $parts[1] ?? '',
					'patronymic' => $parts[2] ?? ''
				];

				$params = [
					':application_id' => $_POST['id'],
					':surname' => $fioParts['surname'],
					':name' => $fioParts['name'],
					':patronymic' => $fioParts['patronymic'],
					':phone' => $phone,
					':email' => $email,
					':birthday' => $birthday,
					':sex' => $sex,
					':bio' => $bio
				];

				db_command("UPDATE application SET surname = :surname, name = :name, patronymic = :patronymic, phone_number = :phone,
					email = :email, birthday = :birthday, sex = :sex, biography = :bio WHERE id = :application_id", $params);
			}
		}
	} else return access_denied();

	return redirect('admin');
}

function admin_delete($request) {
	if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
		session_unset();
		session_destroy();
		header('WWW-Authenticate: Basic realm="Session Expired"');
		return unauthorized();
	}

	$token = $_POST['token'] ?? '';
	if (validateToken($token)) {
		$result = db_row("SELECT id FROM application WHERE id = :id", [':id' => intval($_POST['id'])]);

		if($result !== false) {
			db_command("DELETE FROM application WHERE id = :id", [':id' => $result['id']]);
		}
	}

	return redirect('admin');
}
?>
