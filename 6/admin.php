<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);

include('includes/utils.php');

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != conf('login') || md5($_SERVER['PHP_AUTH_PW']) != md5(conf('password'))) {
	header('HTTP/1.1 401 Unanthorized');
	header('WWW-Authenticate: Basic realm="My site"');
	print('<h1>401 Требуется авторизация</h1>');
	exit();
}

include('includes/validation.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$messages = array();
	$errors = array();
	$fields = ['fio', 'phone', 'email', 'birthday', 'sex', 'langs', 'bio', 'consent'];
	foreach ($fields as $field) {
		$errors[$field] = empty($_COOKIE[$field.'_error']) ? '' : strip_tags($_COOKIE[$field.'_error']);
		if ($errors[$field]) {
			setcookie($field.'_error', '', 100000);
			setcookie($field.'_value', '', 100000);
			$messages[$field] = '<div class="error">'.$errorMessages[$field][$errors[$field]].'</div>';
		}
	}

	$pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = "SELECT langs.name, COUNT(*) as count FROM application app INNER JOIN application_langs applangs ON app.id = applangs.application_id INNER JOIN langs ON langs.id = applangs.lang_id GROUP BY langs.id, langs.name;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([]);
	$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$names = array_column($stats, 'name');
	$counts = array_column($stats, 'count');

	$sql = "SELECT * FROM application;";
	$stmt = $pdo->prepare($sql);
	$stmt->execute([]);
	$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach($applications as &$app){
		$sql = "SELECT langs.name FROM langs JOIN application_langs ON langs.id = application_langs.lang_id WHERE application_id = :app_id;";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([
			'app_id' => $app['id']
		]);
		$app['langs'] = implode("\n", $stmt->fetchAll(PDO::FETCH_COLUMN));
	}

	include('pages/admin.page.php');
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        $pdo = new PDO('mysql:host=localhost;dbname='.conf('db_name').';charset=utf8', conf('db_user'), conf('db_pass'));
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		switch($_POST['action']) {
            case 'update':
				$sql = "SELECT id FROM application WHERE id = :id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([ ':id' => $_POST['editId'] ]);
				$result = $stmt->fetchObject();

				if($result !== false) {
					$fio = $_POST['fio'] ?? '';
					$phone = $_POST['phone'] ?? '';
					$email = $_POST['email'] ?? '';
					$birthday = $_POST['birthday'] ?? '';
					$sex = $_POST['sex'] ?? '';
					$languages = $_POST['languages'] ?? [];
					$bio = $_POST['bio'] ?? '';
					$consent = $_POST['consent'] ?? '';

					$fioParts = explode(' ', $fio);

					$sql = "UPDATE application SET surname = :surname, name = :name, patronymic = :patronymic, phone_number = :phone,
					email = :email, birthday = :birthday, sex = :sex, biography = :bio WHERE id = :application_id";
					$stmt = $pdo->prepare($sql);
					$stmt->execute([
						':application_id' => $_POST['id'],
						':surname' => $parts[0] ?? '',
						':name' => $parts[1] ?? '',
						':patronymic' => $parts[2] ?? '',
						':phone' => $phone,
						':email' => $email,
						':birthday' => $birthday,
						':sex' => $sex,
						':bio' => $bio
					]);
				}

                header('Location: ' . $_SERVER['PHP_SELF']);
                break;
            case 'delete':
				$sql = "SELECT id FROM application WHERE id = :id";
				$stmt = $pdo->prepare($sql);
				$stmt->execute([ ':id' => $_POST['id'] ]);
				$result = $stmt->fetchObject();
				
				if($result !== false) {
					$sql = "DELETE FROM application WHERE id = :id";
					$stmt = $pdo->prepare($sql);
					$stmt->execute([ ':id' => $result->id ]);
				}
				header('Location: '.$_SERVER['PHP_SELF']);
                break;
        }
        exit;
    }
}
?>
