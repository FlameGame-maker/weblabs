<?php
enum fioCodes: int {
	case OK = 0;
	case EMPTY = 1;
	case NOT_LETTER = 2;
	case NOT_ENOUGH = 3;
	case TOO_MUCH = 4;
	case TOO_LONG = 5;
	case INVALID = 6;
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
	fioCodes::TOO_MUCH->value => 'Лишние компоненты в ФИО',
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

?>