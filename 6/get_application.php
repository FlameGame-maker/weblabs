<?php

header('Content-Type: application/json; charset=utf-8');

include('includes/utils.php');

$id = $_GET['id'] ?? 0;
if ($id != 0) {
    $pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM application WHERE id = :id";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([ ':id' => $id ]);
	$data = $stmt->fetch(PDO::FETCH_ASSOC);

	$sql = "SELECT lang_id FROM application_langs WHERE application_id = :app_id;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([
		'app_id' => $id
	]);
	$langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
	$data['langs'] = $langs;

    header('Content-Type: application/json');
    echo json_encode($data);
}

?>