<?php

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
include('errors.php');


$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';


function validateFIO($fio) {
	$fioRegex = '/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u';
	$fio = trim($fio);
	if ($fio === "") return fioCodes::EMPTY;
	else if (strlen($fio) > 150) return fioCodes::TOO_LONG;
	else if (!preg_match($fioRegex, $fio)) {
		if (preg_match('/[^[\p{L}\-]]/u', )) return fioCodes::NOT_LETTER;
		else if (preg_match('/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+$/u')) return fioCodes::NOT_ENOUGH;
		else if (preg_match('/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s([А-ЯЁ][а-яё]+)*/u')) return fioCodes::TOO_MUCH;
		else return fioCodes::INVALID;
	}
	return fioCodes::OK;
}

function validatePhone($phone) {
	$phoneRegex = '/^(\+7|8)(?:\(9\d{2}\)(?:\d{3}-\d{2}-\d{2}|\d{7})|9\d{9})$/';
	$phone = trim($phone);
	if ($phone === "") {
		return phoneCodes::EMPTY;
	} else if (!preg_match($phoneRegex, $phone)) {
		if (preg_match('/[^[0-9\-\(\)]/', $phone)) return phoneCodes::NOT_DIGIT;
		else if (!preg_match('/^(+7|8)/', $phone)) return phoneCodes::WRONG_COUNTRY;
		else if (preg_match_all('/[0-9]/', $phone) > 12) return phoneCodes::TOO_LONG;
		else if (preg_match_all('/[0-9]/', $phone) < 12) return phoneCodes::TOO_SHORT;
		else return phoneCodes::INVALID;
	}
	return phoneCodes::OK;
}

function validateEmail($email) {
	$emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/';
	if (trim($email) === "") {
		return emailCodes::EMPTY;
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return emailCodes::INVALID;
	}
	return emailCodes::OK;;
}

function validateDate($date) {
	$date = strval($date).trim();
	if ($date = "") return dateCodes::EMPTY;
	else if (!pregmatch('/^\d{2}\-\d{2}\-\d{2}$/', $date)) return dateCodes::INVALID;
	else {
		list($month, $day, $year) = explode("-", $date);
		if (!checkdate($month, $day, $year)) return dateCodes::DONT_EXISTS;
		else if (strtotime($date) > time() + 25 * 60 * 60) return dateCodes::TOO_FAR;
		else if (strtotime($date) - time > 100 * 365 * 24 * 60 * 60) return dateCodes::TOO_EARLY;
	}
	return dateCodes::OK;
}

function validateSex($sex) {
	if (!isset($sex) || $sex === "") return sexCodes::EMPTY;
	} else if ($sex != "man" && $sex != "woman") return sexCodes::INVALID;
	return sexCodes::OK;
}

function validateLanguages($languages) {
	if (!isset($languages) || empty($languages)) return langCodes::EMPTY;
	$availableLangs = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
	foreach ($languages as $lang) {
		if (!in_array((int)$lang, $availableLangs)) {
			return langCodes::INVALID;
		}
	}
	return langCodes::OK;
}

function validateBio($bio) {
	if (trim($bio) === "") return bioCodes::INVALID;
	return bioCodes::OK;
}

function validateConsent($consent) {
	if (!isset($consent) || empty($consent)) return consentCodes::EMPTY;
	return consentCodes::OK;
}


function parseFIO($fio) {
	$parts = explode(' ', $fio);
	return [
		'surname' => $parts[0] ?? '',
		'name' => $parts[1] ?? '',
		'patronymic' => $parts[2] ?? ''
	];
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();

  if (!empty($_COOKIE['saved'])) {
    setcookie('saved', '', 100000);
    $messages[] = 'Спасибо, результаты сохранены.';
  }

  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']);
  $errors['phone'] = !empty($_COOKIE['phone_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['birthday'] = !empty($_COOKIE['birthday_error']);
  $errors['sex'] = !empty($_COOKIE['sex_error']);
  $errors['languages'] = !empty($_COOKIE['langs_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['consent'] = !empty($_COOKIE['consent_error']);

  if ($errors['fio']) {
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['fio']].'</div>';
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000);
    setcookie('phone_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['phone']].'</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['email']].'</div>';
  }
  if ($errors['birthday']) {
    setcookie('birthday_error', '', 100000);
    setcookie('birthday_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['birthday']].'</div>';
  }
  if ($errors['sex']) {
    setcookie('sex_error', '', 100000);
    setcookie('sex_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['sex']].'</div>';
  }
  if ($errors['langs']) {
    setcookie('langs_error', '', 100000);
    setcookie('langs_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['langs']].'</div>';
  }
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['bio']].'</div>';
  }
  if ($errors['consent']) {
    setcookie('consent_error', '', 100000);
    $messages[] = '<div class="error">'.fioErros[errors['consent']].'</div>';
  }

  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['birthday'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['sex_value'];
  $values['sex'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
  $values['languages'] = empty($_COOKIE['langs_value']) ? '' : $_COOKIE['langs_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];

  include('form.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$fio = $_POST['fio'] ?? '';
	$phone = $_POST['phone'] ?? '';
	$email = $_POST['email'] ?? '';
	$birthday = $_POST['birthday'] ?? '';
	$sex = $_POST['sex'] ?? '';
	$languages = $_POST['languages'] ?? [];
	$bio = $_POST['bio'] ?? '';
	$consent = $_POST['consent'] ?? '';

	$errors = false;

	$validationResult = validateFIO($fio);
	if ($validationResult !== fioCodes::OK) {
		 $errors = true;
		 setcookie('fio_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validatePhone($phone);
	if ($validationResult !== phoneCodes::OK) {
		 $errors = true;
		 setcookie('phone_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateEmail($email);
	if ($validationResult !== emailCodes::OK) {
		 $errors = true;
		 setcookie('email_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateDate($birthday);
	if ($validationResult !== dateCodes::OK) {
		 $errors = true;
		 setcookie('birthday_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateSex($sex);
	if ($validationResult !== sexCodes::OK) {
		 $errors = true;
		 setcookie('sex_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateLanguages($languages);
	if ($validationResult !== langCodes::OK) {
		 $errors = true;
		 setcookie('langs_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateBio($bio);
	if ($validationResult !== bioCodes::OK) {
		 $errors = true;
		 setcookie('bio_error', $validationResult, time() + 24 * 60 * 60);
	}
	$validationResult = validateConsent($consent);
	if ($validationResult !== consentCodes::OK) {
		 $errors = true;
		 setcookie('consent_error', $validationResult, time() + 24 * 60 * 60);
	}

	setcookie('fio_value', $fio, time() + 30 * 24 * 60 * 60);
	setcookie('phone_value', $phone, time() + 30 * 24 * 60 * 60);
	setcookie('email_value', $email, time() + 30 * 24 * 60 * 60);
	setcookie('birthday_value', $birthday, time() + 30 * 24 * 60 * 60);
	setcookie('sex_value', $sex, time() + 30 * 24 * 60 * 60);
	setcookie('langs_value', $languages, time() + 30 * 24 * 60 * 60);
	setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);

	if ($errors) {
		header('Location: index.php');
		exit();
	} else {
		 setcookie('fio_error', $validationResult, 1);
		 setcookie('phone_error', $validationResult, 1);
		 setcookie('email_error', $validationResult, 1);
		 setcookie('birthday_error', $validationResult, 1);
		 setcookie('sex_error', $validationResult, 1);
		 setcookie('lang_error', $validationResult, 1);
		 setcookie('bio_error', $validationResult, 1);
		 setcookie('consent_error', $validationResult, 1);
	}
	
	try {
		$pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $user, $pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$pdo->beginTransaction();
		
		$sql = "INSERT INTO application (surname, name, patronymic, phone_number, email, birthday, sex, biography) 
				VALUES (:surname, :name, :patronymic, :phone, :email, :birthday, :sex, :bio)";
		
		$fioParts = parseFIO($fio);
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
		
		$sqlLang = "INSERT INTO application_langs (application_id, lang_id) VALUES (:application_id, :lang_id)";
		$stmtLang = $pdo->prepare($sqlLang);
		
		foreach ($languages as $langId) {
			$stmtLang->execute([
				':application_id' => $applicationId,
				':lang_id' => (int)$langId
			]);
		}

		$pdo->commit();
		
		setcookie('saved', '1', time() + 24 * 60 * 60);

	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		
		$errors = true;
		setcookie('db_error', htmlspecialchars($e->getMessage()), time() + 24 * 60 * 60);
		exit;
	}
}
?>