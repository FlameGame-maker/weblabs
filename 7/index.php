<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(0);

include('includes/utils.php');
include('includes/validation.php');


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$messages = array();
	$congrats = "";

	if (!empty($_COOKIE['saved'])) {
		setcookie('saved', '', 100000);
		setcookie('login', '', 100000);
		setcookie('password', '', 100000);
		$congrats = '<dialog closedby="any" id="congrats-dialog" style="order:1;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);min-height:15vh;min-width:30vw;text-align:center;text-justify:center;border-radius:5px">Данные были успешно сохранены!<button commandfor="congrats-dialog" command="close">Принять</button></dialog>';

		if (!empty($_COOKIE['password'])) {
			$messages['login'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
			и паролем <strong>%s</strong> для изменения данных.', htmlspecialchars($_COOKIE['login']), htmlspecialchars($_COOKIE['password']));
		}
	}

	if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login']) && !empty($_SESSION['uid'])) {
		if (!isset($_SESSION['timeout']) || $_SESSION['timeout'] < time()) {
			session_unset();
			session_destroy();
			$messages['login'] = 'Ваша сессия окончилась. Требуется повторный <a href="login.php">вход</a>';
			header('Location: index.php');
		}
	}

	$errors = array();
	$fields = ['fio', 'phone', 'email', 'birthday', 'sex', 'langs', 'bio', 'consent'];
	foreach ($fields as $field) {
		$errors[$field] = empty($_COOKIE[$field.'_error']) ? '' : (is_numeric($_COOKIE[$field.'_error']) ? '' : $_COOKIE[$field.'_error']);
		if ($errors[$field]) {
			setcookie($field.'_error', '', 100000);
			setcookie($field.'_value', '', 100000);
			$messages[$field] = '<div class="error">'.$errorMessages[$field][$errors[$field]].'</div>';
		}
	}

	$values = array();
	$values['fio'] = !empty($_COOKIE['fio_value']) ? htmlspecialchars($_COOKIE['fio_value']) : '';
	$values['phone'] = !empty($_COOKIE['phone_value']) ? htmlspecialchars($_COOKIE['phone_value']) : '';
	$values['email'] = !empty($_COOKIE['email_value']) ? htmlspecialchars($_COOKIE['email_value']) : '';
	$values['birthday'] = !empty($_COOKIE['birthday_value']) ? htmlspecialchars($_COOKIE['birthday_value']) : '';
	$values['sex'] = !empty($_COOKIE['sex_value']) ? htmlspecialchars($_COOKIE['sex_value']) : '';
	$values['langs'] = !empty($_COOKIE['langs_value']) ? json_decode($_COOKIE['langs_value'], true) : array();
	$values['bio'] = !empty($_COOKIE['bio_value']) ? htmlspecialchars($_COOKIE['bio_value']) : '';

	if (empty($errors)) {
		if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login']) && !empty($_SESSION['uid'])) {
			$login = $_SESSION['login'];
			$uid = $_SESSION['uid'];

			try {
				$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$sql = "SELECT application_id FROM app_users WHERE login = :login AND id = :id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':login' => $login,
					':id' => $uid
				]);
				$user = $stmt->fetchObject();

				$sql = "SELECT surname, name, patronymic, phone_number, email, birthday, sex, biography FROM application WHERE application_id = :application_id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':application_id' => $user->application_id
				]);
				$application = $stmt->fetchObject();

				$values['fio'] = $application->surname.' '.$application->name.' '.$application->patronymic;
				$values['phone'] = $application->phone_number;
				$values['email'] = $application->email;
				$values['birthday'] = $application->birthday;
				$values['sex'] = $application->sex;
				$values['bio'] = $application->bio;

				$sql = "SELECT lang_id FROM application_langs WHERE application_id = :app_id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':app_id' => $user->application_id
				]);
				$values['langs'] = $stmt->fetchAll();

				printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
			} catch (PDOException $e) {
				setcookie('db_error', "Something went wrong", time() + 24 * 60 * 60);
				exit;
			}
		}

		$token = generateToken($uid);
	} else $token = generateToken();

	include('pages/form.page.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$token = $_POST['token'] ?? '';
	$fio = $_POST['fio'] ?? '';
	$phone = $_POST['phone'] ?? '';
	$email = $_POST['email'] ?? '';
	$birthday = $_POST['birthday'] ?? '';
	$sex = $_POST['sex'] ?? '';
	$languages = $_POST['languages'] ?? [];
	$bio = $_POST['bio'] ?? '';
	$consent = $_POST['consent'] ?? '';

	$inputErrors = false;

	$validationResult = validateFIO($fio);
	if ($validationResult !== fioCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('fio_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validatePhone($phone);
	if ($validationResult !== phoneCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('phone_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateEmail($email);
	if ($validationResult !== emailCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('email_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateDate($birthday);
	if ($validationResult !== dateCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('birthday_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateSex($sex);
	if ($validationResult !== sexCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('sex_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateLanguages($languages);
	if ($validationResult !== langsCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('langs_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateBio($bio);
	if ($validationResult !== bioCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('bio_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateConsent($consent);
	if ($validationResult !== consentCodes::OK->value) {
		 $inputErrors = true;
		 setcookie('consent_error', $validationResult, time() + 24 * 60 * 60);
	}

	setcookie('fio_value', $fio, time() + 30 * 24 * 60 * 60);
	setcookie('phone_value', $phone, time() + 30 * 24 * 60 * 60);
	setcookie('email_value', $email, time() + 30 * 24 * 60 * 60);
	setcookie('birthday_value', $birthday, time() + 30 * 24 * 60 * 60);
	setcookie('sex_value', $sex, time() + 30 * 24 * 60 * 60);
	setcookie('langs_value', json_encode($languages), time() + 30 * 24 * 60 * 60);
	setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);

	if ($inputErrors) {
		header('Location: index.php');
		exit();
	} else {
		 setcookie('fio_error', '', 1);
		 setcookie('phone_error', '', 1);
		 setcookie('email_error', '', 1);
		 setcookie('birthday_error', '', 1);
		 setcookie('sex_error', '', 1);
		 setcookie('lang_error', '', 1);
		 setcookie('bio_error', '', 1);
		 setcookie('consent_error', '', 1);
	}

	try {
		$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (!empty($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
			$login = $_SESSION['login'];
			$uid = $_SESSION['uid'];

			if (validateToken($token, $uid)) {
				$sql = "SELECT login, password_hash, application_id FROM app_users WHERE login = :login AND id = :id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':login' => $login,
					':id' => $uid
				]);
				$user = $stmt->fetchObject();

				$pdo->beginTransaction();

				$sql = "UPDATE application SET surname = :surname, name = :name, patronymic = :patronymic, phone_number = :phone,
					email = :email, birthday = :birthday, sex = :sex, biography = :bio WHERE id = :application_id";
			
				$parts = explode(' ', $fio);
				$fioParts = [
					'surname' => $parts[0] ?? '',
					'name' => $parts[1] ?? '',
					'patronymic' => $parts[2] ?? ''
				];

				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':application_id' => $user->application_id,
					':surname' => $fioParts['surname'],
					':name' => $fioParts['name'],
					':patronymic' => $fioParts['patronymic'],
					':phone' => $phone,
					':email' => $email,
					':birthday' => $birthday,
					':sex' => $sex,
					':bio' => $bio
				]);
				
				$sql = "DELETE FROM application_langs WHERE application_id = :application_id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':application_id' => $user->application_id
				]);

				$sqlLang = "INSERT INTO application_langs (application_id, lang_id) VALUES (:application_id, :lang_id)";
				$stmtLang = $pdo->prepare($sqlLang);
				foreach ($languages as $langId) {
					$stmtLang->execute([
						':application_id' => $applicationId,
						':lang_id' => (int)$langId
					]);
				}
			}
		} else {
			if (validateToken($token)) {
				$pdo->beginTransaction();

				$sql = "INSERT INTO application (surname, name, patronymic, phone_number, email, birthday, sex, biography) 
					VALUES (:surname, :name, :patronymic, :phone, :email, :birthday, :sex, :bio)";
		
				$parts = explode(' ', $fio);
				$fioParts = [
					'surname' => $parts[0] ?? '',
					'name' => $parts[1] ?? '',
					'patronymic' => $parts[2] ?? ''
				];
				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':surname' => $fioParts['surname'],
					':name' => $fioParts['name'],
					':patronymic' => $fioParts['patronymic'],
					':phone' => $phone,
					':email' => $email,
					':birthday' => $birthday,
					':sex' => $sex,
					':bio' => $bio
				]);
		
				$applicationId = $pdo->lastInsertId();

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

				$sql = "INSERT INTO app_users (login, password_hash, application_id) 
					VALUES (:login, :password_hash, :application_id)";

				$stmt = $pdo->prepare($sql);
				$stmt->execute([
					':login' => $login,
					':password_hash' => password_hash($password, PASSWORD_DEFAULT),
					':application_id' => $applicationId
				]);

				$sqlLang = "INSERT INTO application_langs (application_id, lang_id) VALUES (:application_id, :lang_id)";
				$stmtLang = $pdo->prepare($sqlLang);
		
				foreach ($languages as $langId) {
					$stmtLang->execute([
						':application_id' => $applicationId,
						':lang_id' => (int)$langId
					]);
				}

				$pdo->commit();
			}
		}
		
		setcookie('saved', '1', time() + 24 * 60 * 60);
		header('Location: index.php');
	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		
		$inputErrors = true;
		setcookie('db_error', "Something went wrong", time() + 24 * 60 * 60);
		exit;
	}
}
?>