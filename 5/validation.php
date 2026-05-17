<?php
enum fioCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case NOT_LETTER = 2;
	case NOT_ENOUGH = 3;
	case TOO_LONG = 4;
	case INVALID = 5;
}

enum phoneCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case NOT_DIGIT = 2;
	case WRONG_COUNTRY = 3;
	case TOO_LONG = 4;
	case TOO_SHORT = 5;
	case INVALID = 6;
}

enum emailCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum dateCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case DONT_EXISTS = 2;
	case TOO_EARLY = 3;
	case TOO_FAR = 4;
	case INVALID = 5;
}

enum sexCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum langsCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum bioCodes: int {
	case OK = 0;
	case EMPTY = 1;
}

enum consentCodes: int {
	case OK = 0;
	case EMPTY = 1;
}


$fioErrors = array(
	fioCodes::OK->value => 'ФИО подходит',
	fioCodes::EMPTY->value => 'Поле обязательно для заполнения',
	fioCodes::NOT_LETTER->value => 'Допустимы только буквы кириллицы и символ -',
	fioCodes::NOT_ENOUGH->value => 'Недостаточно компонентов ФИО',
	fioCodes::TOO_LONG->value => 'Количество данных в поле превышает допустимое',
	fioCodes::INVALID->value => 'Недействительное значение ФИО'
);

$phoneErrors = array(
	phoneCodes::OK->value => 'Номер телефона подходит',
	phoneCodes::EMPTY->value => 'Поле обязательно для заполнения',
	phoneCodes::NOT_DIGIT->value => 'Допустимы только цифры и символы (,),-',
	phoneCodes::WRONG_COUNTRY->value => 'Обслуживаются только номера, начинающиеся с +7 (или 8)',
	phoneCodes::TOO_LONG->value => 'Номер телефона слишком длинный',
	phoneCodes::TOO_SHORT->value => 'Номер телефона слишком короткий',
	phoneCodes::INVALID->value => 'Недействительное значение номера телефона'
);

$emailErrors = array(
	emailCodes::OK->value => 'Email подходит',
	emailCodes::EMPTY->value => 'Поле обязательно для заполнения',
	emailCodes::INVALID->value => 'Недействительное значение email'
);

$dateErrors = array(
	dateCodes::OK->value => 'День рождения подходит',
	dateCodes::EMPTY->value => 'Поле обязательно для заполнения',
	dateCodes::DONT_EXISTS->value => 'Такой день не существует',
	dateCodes::TOO_EARLY->value => 'Слишком давнее значения дня рождения',
	dateCodes::TOO_FAR->value => 'День рождения должен быть до текущей даты',
	dateCodes::INVALID->value => 'Недействительное значение дня рождения'
);

$sexErrors = array(
	sexCodes::OK->value => 'Пол подходит',
	sexCodes::EMPTY->value => 'Поле обязательно для заполнения',
	sexCodes::INVALID->value => 'Недействительное значение пола'
);

$langsErrors = array(
	langsCodes::OK->value => 'Выбранные языки подходят',
	langsCodes::EMPTY->value => 'Поле обязательно для заполнения',
	langsCodes::INVALID->value => 'Недействительное значение выбранных языков'
);

$bioErrors = array(
	bioCodes::OK->value => 'Биография подходит',
	bioCodes::EMPTY->value => 'Поле обязательно для заполнения',
);

$consentErrors = array(
	consentCodes::OK->value => 'Согласие предоставлено',
	consentCodes::EMPTY->value => 'Согласие не предоставлено',
);


function validateFIO($fio) {
	$fioRegex = '/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u';
	if ($fio === "") return fioCodes::EMPTY->value;
	else if (strlen($fio) > 150) return fioCodes::TOO_LONG->value;
	else if (!preg_match($fioRegex, $fio)) {
		if (preg_match('/^[А-ЯЁ][а-яё]+(?:-[А-ЯЁ][а-яё]+)*\s[А-ЯЁ][а-яё]+$/u', $fio)) return fioCodes::NOT_ENOUGH->value;
		else if (preg_match('/[^[\p{L}\-]]/u', $fio)) return fioCodes::NOT_LETTER->value;
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
		else if (!preg_match('/^((\+7)|8)/', $phone)) return phoneCodes::WRONG_COUNTRY->value;
		else if (preg_match_all('/[0-9]/', $phone) > 11) return phoneCodes::TOO_LONG->value;
		else if (preg_match_all('/[0-9]/', $phone) < 11) return phoneCodes::TOO_SHORT->value;
		else return phoneCodes::INVALID->value;
	}
	return phoneCodes::OK->value;
}

function validateEmail($email) {
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
		else if (time() - strtotime($date) > 100 * 365 * 24 * 60 * 60) return dateCodes::TOO_EARLY->value;
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


?>