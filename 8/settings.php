<?php

// Выключаем отображение ошибок после отладки.
define('DISPLAY_ERRORS', 0);

// По возможности кладём скрипты и включаемые файлы выше
// публично доступной директории из соображений безопасности.

// Папки со скриптами и модулями.
define('INCLUDE_PATH', './scripts' . PATH_SEPARATOR . './modules');

// Храним настройки в массиве чтоб легче было смотреть (print_r),
// хранить (serialize), оверрайдить и не плодить глобалов.
$conf = array(
  'sitename' => 'Demo Framework',
  'theme' => './theme',
  'charset' => 'UTF-8',
  'clean_urls' => TRUE,
  'display_errors' => 1,
  'date_format' => 'Y.m.d',
  'date_format_2' => 'Y.m.d H:i',
  'date_format_3' => 'd.m.Y',
  'basedir' => '/test/',
  'login' => 'u82185',
  'password' => '7586396',
  'db_host' => 'localhost',
  'db_name' => 'u82185',
  'db_user' => 'u82185',
  'db_psw' => '7586396',
  'timeout' => 3600,
  'token_length' => 32
);

// Определения ресурсов для диспатчера.
$urlconf = array(
  '' => array('module' => 'form'),
  '/^form/' => array('module' => 'form'),
  '/^login$/' => array('module' => 'login'),
  '/^admin$/' => array('module' => 'admin', 'auth' => 'auth_admin'),
  '/^admin\/(\d+)$/' => array('module' => 'get_application', 'auth' => 'auth_admin')
);

// Отрубаем кеш.
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
