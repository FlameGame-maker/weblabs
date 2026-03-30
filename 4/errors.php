<?php
enum fioCodes {
	case OK = 0;
	case EMPTY = 1;
	case NOT_LETTER = 2;
	case NOT_ENOUGH = 3;
	case TOO_MUCH = 4;
	case TOO_LONG = 5;
	case INVALID = 6;
}

enum phoneCodes {
	case OK = 0;
	case EMPTY = 1;
	case NOT_DIGIT = 2;
	case WRONG_COUNTRY = 3;
	case TOO_LONG = 4;
	case TOO_SHORT = 5;
	case INVALID = 6;
}

enum emailCodes {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum dateCodes {
	case OK = 0;
	case EMPTY = 1;
	case DONT_EXISTS = 2;
	case TOO_EARLY = 3;
	case TOO_FAR = 4;
	case INVALID = 5;
}

enum sexCodes {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum langsCodes {
	case OK = 0;
	case EMPTY = 1;
	case INVALID = 2;
}

enum bioCodes {
	case OK = 0;
	case EMPTY = 1;
}

enum consentCodes {
	case OK = 0;
	case EMPTY = 1;
}


$fioErrors = array(
	fioCodes::OK => 'ФИО подходит',
	fioCodes::EMPTY => 'Поле обязательно для заполнения',
	fioCodes::NOT_LETTER => 'Допустимы только буквы кириллицы и символ -',
	fioCodes::NOT_ENOUGH => 'Недостаточно компонентов ФИО',
	fioCodes::TOO_MUCH => 'Лишние компоненты в ФИО',
	fioCodes::TOO_LONG => 'Количество данных в поле превышает допустимое',
	fioCodes::INVALID => 'Недействительное значение ФИО'
);

$phoneErrors = array(
	phoneCodes::OK => 'Номер телефона подходит',
	phoneCodes::EMPTY => 'Поле обязательно для заполнения',
	phoneCodes::NOT_DIGIT => 'Допустимы только цифры и символы (,),-',
	phoneCodes::WRONG_COUNTRY => 'Обслуживаются только номера, начинающиеся с +7 (или 8)',
	phoneCodes::TOO_LONG => 'Номер телефона слишком длинный',
	phoneCodes::TOO_SHORT => 'Номер телефона слишком короткий',
	phoneCodes::INVALID => 'Недействительное значение номера телефона'
);

$emailErrors = array(
	emailCodes::OK => 'Email подходит',
	emailCodes::EMPTY => 'Поле обязательно для заполнения',
	emailCodes::INVALID => 'Недействительное значение email'
);

$dateErrors = array(
	dateCodes::OK => 'День рождения подходит',
	dateCodes::EMPTY => 'Поле обязательно для заполнения',
	dateCodes::DONT_EXISTS => 'Такой день не существует',
	dateCodes::TOO_EARLY => 'Слишком давнее значения дня рождения',
	dateCodes::TOO_FAR => 'День рождения должен быть до текущей даты',
	dateCodes::INVALID => 'Недействительное значение дня рождения'
);

$sexErrors = array(
	sexCodes::OK => 'Пол подходит',
	sexCodes::EMPTY => 'Поле обязательно для заполнения',
	sexCodes::INVALID => 'Недействительное значение пола'
);

$langsErrors = array(
	langsCodes::OK => 'Выбранные языки подходят',
	langsCodes::EMPTY => 'Поле обязательно для заполнения',
	langsCodes::INVALID => 'Недействительное значение выбранных языков'
);

$bioErrors = array(
	bioCodes::OK => 'Биография подходит',
	bioCodes::EMPTY => 'Поле обязательно для заполнения',
	bioCodes::INVALID => 'Недействительное значение биографии'
);

$consentErrors = array(
	consentCodes::OK => 'Согласие предоставлено',
	consentCodes::EMPTY => 'Согласие не предоставлено',
	consentCodes::INVALID => 'Недействительное значение согласия'
);

?>