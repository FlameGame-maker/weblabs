<?php

header('Content-Type: application/json; charset=utf-8');

$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';

$id = $_GET['id'] ?? 0;
if ($id != 0) {
    $pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $user, $pass);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$sql = "SELECT * FROM application WHERE id = :id";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([ ':id' => $id ]);
	$data = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($data);
}

?>