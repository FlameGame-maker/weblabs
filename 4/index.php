<?php
error_reporting(E_ALL);
$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';

include('errors.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$errors = [];
	
	$fio = $_POST['fio'] ?? '';
	$phone = $_POST['phone'] ?? '';
	$email = $_POST['email'] ?? '';
	$birthday = $_POST['birthday'] ?? '';
	$sex = $_POST['sex'] ?? '';
	$languages = $_POST['languages'] ?? [];
	$bio = $_POST['bio'] ?? '';
	$consent = $_POST['consent'] ?? '';

	$validationResult = validateFIO($fio);
	if ($validationResult !== true) $errors[] = $validationResult;
	$validationResult = validatePhone($phone);
	if ($validationResult !== true) $errors[] = $validationResult;
	$validationResult = validateEmail($email);
	if ($validationResult !== true) $errors[] = $validationResult;
	if (empty($birthday)) $errors[] = "Поле 'Дата рождения' обязательно для заполнения";
	$validationResult = validateSex($sex);
	if ($validationResult !== true) $errors[] = $validationResult;
	$validationResult = validateLanguages($languages);
	if ($validationResult !== true) $errors[] = $validationResult;
	$validationResult = validateBio($bio);
	if ($validationResult !== true) $errors[] = $validationResult;
	$validationResult = validateConsent($consent);
	if ($validationResult !== true) $errors[] = $validationResult;

	if (!empty($errors)) {
		header('Location: index.php');
		exit();
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
		
		echo "<h3>Данные успешно сохранены.</h3>";
		
	} catch (PDOException $e) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		
		echo "<h3>Ошибка базы данных:</h3>";
		echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
		exit;
	}
} else {
	header('Location: index.html');
	exit;
}
?>