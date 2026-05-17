<?php

$conf = array(
	'login' => 'u82185',
	'password' => '7586396',
	'db_name' => 'u82185',
	'db_user' => 'u82185',
	'db_pass' => '7586396'
);

function conf($key) {
  global $conf;
  return isset($conf[$key]) ? $conf[$key] : FALSE;
}
