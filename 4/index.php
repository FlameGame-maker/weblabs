<?php

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
include('errors.php');


$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';


function validateFIO($fio) {
	$fioRegex = '/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u';
	if ($fio === "") return fioCodes::EMPTY->value;
	else if (strlen($fio) > 150) return fioCodes::TOO_LONG->value;
	else if (!preg_match($fioRegex, $fio)) {
		if (preg_match('/[^[\p{L}\-]]/u', $fio)) return fioCodes::NOT_LETTER->value;
		else if (preg_match('/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+$/u', $fio)) return fioCodes::NOT_ENOUGH->value;
		else if (preg_match('/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s([А-ЯЁ][а-яё]+)*/u', $fio)) return fioCodes::TOO_MUCH->value;
		else return fioCodes::INVALID->value;
	}
	return fioCodes::OK->value;
}

function validatePhone($phone) {
	$phoneRegex = '/^(\+7|8)(?:\(9\d{2}\)(?:\d{3}-\d{2}-\d{2}|\d{7})|9\d{9})$/';
	if ($phone === "") {
		return phoneCodes::EMPTY->value;
	} else if (!preg_match($phoneRegex, $phone)) {
		if (preg_match('/[^[0-9\-\(\)]/', $phone)) return phoneCodes::NOT_DIGIT->value;
		else if (!preg_match('/^(+7|8)/', $phone)) return phoneCodes::WRONG_COUNTRY->value;
		else if (preg_match_all('/[0-9]/', $phone) > 12) return phoneCodes::TOO_LONG->value;
		else if (preg_match_all('/[0-9]/', $phone) < 12) return phoneCodes::TOO_SHORT->value;
		else return phoneCodes::INVALID->value;
	}
	return phoneCodes::OK->value;
}

function validateEmail($email) {
	$emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/';
	if (trim($email) === "") {
		return emailCodes::EMPTY->value;
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return emailCodes::INVALID->value;
	}
	return emailCodes::OK->value;
}

function validateDate($date) {
	if ($date === "") return dateCodes::EMPTY->value;
	else if (!preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $date)) return dateCodes::INVALID->value;
	else {
		list($year, $month, $day) = explode("-", $date);
		if (!checkdate($month, $day, $year)) return dateCodes::DONT_EXISTS->value;
		else if (strtotime($date) > time() + 25 * 60 * 60) return dateCodes::TOO_FAR->value;
		else if (strtotime($date) - time() > 100 * 365 * 24 * 60 * 60) return dateCodes::TOO_EARLY->value;
	}
	return dateCodes::OK->value;
}

function validateSex($sex) {
	if (!isset($sex) || $sex === "") return sexCodes::EMPTY->value;
	else if ($sex != "man" && $sex != "woman") return sexCodes::INVALID->value;
	return sexCodes::OK->value;
}

function validateLanguages($languages) {
	if (!isset($languages) || empty($languages)) return langsCodes::EMPTY->value;
	$availableLangs = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
	foreach ($languages as $lang) {
		if (!in_array((int)$lang, $availableLangs)) {
			return langsCodes::INVALID->value;
		}
	}
	return langsCodes::OK->value;
}

function validateBio($bio) {
	if (trim($bio) === "") return bioCodes::EMPTY->value;
	return bioCodes::OK->value;
}

function validateConsent($consent) {
	if (!isset($consent) || empty($consent)) return consentCodes::EMPTY->value;
	return consentCodes::OK->value;
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
  $congrats = "";

  if (!empty($_COOKIE['saved'])) {
    setcookie('saved', '', 100000);
    $congrats = '<dialog closedby="any" id="congrats-dialog" style="order:1;position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);min-height:15vh;min-width:30vw;text-align:center;text-justify:center;border-radius:5px">Данные были успешно сохранены!<button commandfor="congrats-dialog" command="close">Принять</button></dialog>';
  }

  $errors = array();
  $errors['fio'] = !empty($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : "";
  $errors['phone'] = !empty($_COOKIE['phone_error']) ? $_COOKIE['phone_error'] : "";
  $errors['email'] = !empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : "";
  $errors['birthday'] = !empty($_COOKIE['birthday_error']) ? $_COOKIE['birthday_error'] : "";
  $errors['sex'] = !empty($_COOKIE['sex_error']) ? $_COOKIE['sex_error'] : "";
  $errors['langs'] = !empty($_COOKIE['langs_error']) ? $_COOKIE['langs_error'] : "";
  $errors['bio'] = !empty($_COOKIE['bio_error']) ? $_COOKIE['bio_error'] : "";
  $errors['consent'] = !empty($_COOKIE['consent_error']) ? $_COOKIE['consent_error'] : "";

  if ($errors['fio']) {
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
    $messages['fio'] = '<div class="error">'.$fioErrors[$errors['fio']].'</div>';
  }
  if ($errors['phone']) {
    setcookie('phone_error', '', 100000);
    setcookie('phone_value', '', 100000);
    $messages['phone'] = '<div class="error">'.$phoneErrors[$errors['phone']].'</div>';
  }
  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
    $messages['email'] = '<div class="error">'.$emailErrors[$errors['email']].'</div>';
  }
  if ($errors['birthday']) {
    setcookie('birthday_error', '', 100000);
    setcookie('birthday_value', '', 100000);
    $messages['birthday'] = '<div class="error">'.$dateErrors[$errors['birthday']].'</div>';
  }
  if ($errors['sex']) {
    setcookie('sex_error', '', 100000);
    setcookie('sex_value', '', 100000);
    $messages['sex'] = '<div class="error">'.$sexErrors[$errors['sex']].'</div>';
  }
  if ($errors['langs']) {
    setcookie('langs_error', '', 100000);
    setcookie('langs_value', '', 100000);
    $messages['langs'] = '<div class="error">'.$langsErrors[$errors['langs']].'</div>';
  }
  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
    $messages['bio'] = '<div class="error">'.$bioErrors[$errors['bio']].'</div>';
  }
  if ($errors['consent']) {
    setcookie('consent_error', '', 100000);
    $messages['consent'] = '<div class="error">'.$consentErrors[$errors['consent']].'</div>';
  }

  $values = array();
  $values['fio'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['birthday'] = empty($_COOKIE['birthday_value']) ? '' : $_COOKIE['birthday_value'];
  $values['sex'] = empty($_COOKIE['sex_value']) ? '' : $_COOKIE['sex_value'];
  $values['langs'] = empty($_COOKIE['langs_value']) ? array() : json_decode($_COOKIE['langs_value'], true);
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
	if ($validationResult !== langsCodes::OK) {
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
	setcookie('langs_value', json_encode($languages), time() + 30 * 24 * 60 * 60);
	setcookie('bio_value', $bio, time() + 30 * 24 * 60 * 60);

	if ($errors) {
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
		header('Location: index.php');
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