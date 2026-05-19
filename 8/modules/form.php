<?php

include('validation.php');
include('tokens.php');

function form_get($request) {
	$contents = array();
	$contents['messages'] = array();
	$contents['congrats'] = "";

	if (!empty($_COOKIE['saved'])) {
		setcookie('saved', '', 1);
		setcookie('login', '', 1);
		setcookie('password', '', 1);
		$contents['congrats'] = '<dialog closedby="any" id="congrats-dialog" style="order:1;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);min-height:15vh;min-width:30vw;text-align:center;text-justify:center;border-radius:5px">Данные были успешно сохранены!<button commandfor="congrats-dialog" command="close">Принять</button></dialog>';

		if (!empty($_COOKIE['password'])) {
			$contents['messages']['login'] = 'Вы можете <a href="login">войти</a> с логином <strong>'.htmlspecialchars($_COOKIE['login']).'</strong> и паролем <strong>'.htmlspecialchars($_COOKIE['password']).'</strong> для изменения данных.';
		}
	}

	if (!empty($_SESSION['login']) && !empty($_SESSION['uid'])) {
		if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
			session_unset();
			session_destroy();
			$contents['messages']['login'] = 'Ваша сессия окончилась. Требуется повторный <a href="login.php">вход</a>';
			return redirect('form');
		}
	}

	$contents['errors'] = array();
	$fields = ['fio', 'phone', 'email', 'birthday', 'sex', 'langs', 'bio', 'consent'];
	foreach ($fields as $field) {
		if (!empty($_COOKIE[$field.'_error']) && is_numeric($_COOKIE[$field.'_error'])) {
			$contents['errors'][$field] = $_COOKIE[$field.'_error'];
			setcookie($field.'_error', '', 1);
			setcookie($field.'_value', '', 1);
			$contents['messages'][$field] = '<div class="error">'.getErrorMessage($field, $contents['errors'][$field]).'</div>';
		}
	}

	$contents['values'] = array();
	$contents['values']['fio'] = !empty($_COOKIE['fio_value']) ? htmlspecialchars($_COOKIE['fio_value']) : '';
	$contents['values']['phone'] = !empty($_COOKIE['phone_value']) ? htmlspecialchars($_COOKIE['phone_value']) : '';
	$contents['values']['email'] = !empty($_COOKIE['email_value']) ? htmlspecialchars($_COOKIE['email_value']) : '';
	$contents['values']['birthday'] = !empty($_COOKIE['birthday_value']) ? htmlspecialchars($_COOKIE['birthday_value']) : '';
	$contents['values']['sex'] = !empty($_COOKIE['sex_value']) ? htmlspecialchars($_COOKIE['sex_value']) : '';
	$contents['values']['langs'] = !empty($_COOKIE['langs_value']) ? json_decode($_COOKIE['langs_value'], true) : array();
	$contents['values']['bio'] = !empty($_COOKIE['bio_value']) ? htmlspecialchars($_COOKIE['bio_value']) : '';

	if (empty($contents['errors'])) {
		if (!empty($_SESSION['login']) && !empty($_SESSION['uid'])) {
			$login = $_SESSION['login'];
			$uid = $_SESSION['uid'];

			$user = db_row("SELECT application_id FROM app_users WHERE login = :login AND id = :id", [':login' => $login, ':id' => $uid]);
			if ($user !== false) {
				$application = db_row("SELECT surname, name, patronymic, phone_number, email, birthday, sex, biography FROM application WHERE application_id = :application_id", [':application_id' => $user->application_id]);

				$contents['values']['fio'] = $application['surname'].' '.$application['name'].' '.$application['patronymic'];
				$contents['values']['phone'] = $application['phone_number'];
				$contents['values']['email'] = $application['email'];
				$contents['values']['birthday'] = $application['birthday'];
				$contents['values']['sex'] = $application['sex'];
				$contents['values']['bio'] = $application['biography'];
				$params = [':app_id' => $user['application_id']];
				$contents['values']['langs'] = db_query("SELECT lang_id FROM application_langs WHERE application_id = :app_id", $params);
			
				//printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
			}

			$contents['token'] = generateToken($uid);
		} else $contents['token'] = generateToken();
	}

	header('Content-Type: text/html; charset=' . conf('charset'));
	return theme('form', $contents);
}

function form_post($request) {
	/*if (isset($_POST['action'])) {
		session_destroy();
		return redirect('form');
	}*/

	$token = $_POST['token'] ?? '';
	$fio = $_POST['fio'] ?? '';
	$phone = $_POST['phone'] ?? '';
	$email = $_POST['email'] ?? '';
	$birthday = $_POST['birthday'] ?? '';
	$sex = $_POST['sex'] ?? '';
	$languages = $_POST['languages'] ?? [];
	$bio = $_POST['bio'] ?? '';
	$consent = $_POST['consent'] ?? '';

	$inputErrors = validateAll($fio, $phone, $email, $birthday, $sex, $languages, $bio, $consent);

	setcookie('fio_value', $fio, time() + 30 * 24 * 60 * 60);
	setcookie('phone_value', $phone, time() + 30 * 24 * 60 * 60);
	setcookie('email_value', $email, time() + 30 * 24 * 60 * 60);
	setcookie('birthday_value', $birthday, time() + 30 * 24 * 60 * 60);
	setcookie('sex_value', $sex, time() + 30 * 24 * 60 * 60);
	setcookie('langs_value', json_encode($languages), time() + 30 * 24 * 60 * 60);
	setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);

	if ($inputErrors) {
		return redirect('form');
	}

	setcookie('fio_error', '', 1);
	setcookie('phone_error', '', 1);
	setcookie('email_error', '', 1);
	setcookie('birthday_error', '', 1);
	setcookie('sex_error', '', 1);
	setcookie('lang_error', '', 1);
	setcookie('bio_error', '', 1);
	setcookie('consent_error', '', 1);

	if (!empty($_SESSION['login'])) {
		$login = $_SESSION['login'];
		$uid = $_SESSION['uid'];

		if (validateToken($token, $uid)) {
			$user = db_row("SELECT login, password_hash, application_id FROM app_users WHERE login = :login AND id = :id", [':login' => $login, ':id' => $uid]);
			
			$parts = explode(' ', $fio);
			$fioParts = [
				'surname' => $parts[0] ?? '',
				'name' => $parts[1] ?? '',
				'patronymic' => $parts[2] ?? ''
			];

			$params = [
				':application_id' => $user['application_id'],
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

			db_command("DELETE FROM application_langs WHERE application_id = :application_id", [':application_id' => $user['application_id']]);
			foreach ($languages as $lang_id) {
				db_command("INSERT INTO application_langs (application_id, lang_id) VALUES (:application_id, :lang_id)", [':application_id' => $user['application_id'], ':lang_id' => (int)$lang_id]);
			}
		}
	} else {
		if (validateToken($token)) {
			$parts = explode(' ', $fio);
			$fioParts = [
				'surname' => $parts[0] ?? '',
				'name' => $parts[1] ?? '',
				'patronymic' => $parts[2] ?? ''
			];

			$params = [
				':surname' => $fioParts['surname'],
				':name' => $fioParts['name'],
				':patronymic' => $fioParts['patronymic'],
				':phone' => $phone,
				':email' => $email,
				':birthday' => $birthday,
				':sex' => $sex,
				':bio' => $bio
			];
			db_command("INSERT INTO application (surname, name, patronymic, phone_number, email, birthday, sex, biography) 
				VALUES (:surname, :name, :patronymic, :phone, :email, :birthday, :sex, :bio)", $params);
		
			$applicationId = db_insert_id();

			$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
			$charactersLength = strlen($characters);
			$password = '';
			$login = '';
			for ($i = 0; $i < 8; $i++) {
				$login .= $characters[random_int(0, $charactersLength - 1)];
				$password .= $characters[random_int(0, $charactersLength - 1)];
			}
		
			setcookie('login', $login, time() + 24 * 60 * 60);
			setcookie('password', $password, time() + 24 * 60 * 60);

			$params = [':login' => $login, ':password_hash' => password_hash($password, PASSWORD_DEFAULT), ':application_id' => $applicationId];
			db_command("INSERT INTO app_users (login, password_hash, application_id) VALUES (:login, :password_hash, :application_id)", $params);

			foreach ($languages as $lang_id) {
				db_command("INSERT INTO application_langs (application_id, lang_id) VALUES (:application_id, :lang_id)", [':application_id' => $applicationId, ':lang_id' => (int)$lang_id]);
			}
		}
	}
		
	setcookie('saved', '1', time() + 24 * 60 * 60);
	return redirect('form');
}

?>