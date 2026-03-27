<?php
error_reporting(E_ALL);
$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';

function validateFIO($fio) {
	$fioRegex = '/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u';
	if (trim($fio) === "") {
		return "Поле 'ФИО' обязательно для заполнения";
	} else if (strlen($fio) > 150) {
		return "Длина ФИО не должна превышать 150 символов";
	} else if (!preg_match($fioRegex, $fio)) {
		return "Ошибка формата ФИО";
	}
	return true;
}

function validatePhone($phone) {
	$phoneRegex = '/^(\+7|8)(?:\(9\d{2}\)(?:\d{3}-\d{2}-\d{2}|\d{7})|9\d{9})$/';
	if (trim($phone) === "") {
		return "Поле 'Номер телефона' обязательно для заполнения";
	} else if (!preg_match($phoneRegex, $phone)) {
		return "Ошибка формата номера телефона";
	}
	return true;
}

function validateEmail($email) {
	$emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]+$/';
	if (trim($email) === "") {
		return "Поле 'e-mail' обязательно для заполнения";
	} else if (!preg_match($emailRegex, $email)) {
		return "Ошибка формата электронной почты";
	}
	return true;
}

function validateSex($sex) {
	if (!isset($sex) || $sex === "") {
		return "Поле 'Пол' обязательно для заполнения";
	} else if ($sex != "man" && $sex != "woman") {
		return "Недопустимое значение поля 'Пол'";
	}
	return true;
}

function validateLanguages($languages) {
	if (!isset($languages) || empty($languages)) {
		return "Необходимо выбрать хотя бы один язык программирования";
	}
	$availableLangs = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
	foreach ($languages as $lang) {
		if (!in_array((int)$lang, $availableLangs)) {
			return "Недействительное значение языка программирования";
		}
	}
	return true;
}

function validateBio($bio) {
	if (trim($bio) === "") {
		return "Поле 'Биография' обязательно для заполнения";
	}
	return true;
}

function validateConsent($consent) {
	if (!isset($consent) || empty($consent)) {
		return "Для подачи формы необходимо подтвердить ознакомление";
	}
	return true;
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
		echo "<h3>Ошибки валидации:</h3>";
		echo "<ul>";
		foreach ($errors as $error) {
			echo "<li>" . htmlspecialchars($error) . "</li>";
		}
		echo "</ul>";
		exit;
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