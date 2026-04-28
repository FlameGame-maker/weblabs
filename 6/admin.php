<?php
header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);

if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_USER'] != 'u82185' || md5($_SERVER['PHP_AUTH_PW']) != md5('7586396')) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Требуется авторизация</h1>');
  exit();
}

include('errors.php');

$dbname = 'u82185';
$user = 'u82185';
$pass = '7586396';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$messages = array();

	$errors = array();
	$errors['fio'] = empty($_COOKIE['fio_error']) ? "" : strip_tags($_COOKIE['fio_error']);
	$errors['phone'] = empty($_COOKIE['phone_error']) ? "" : strip_tags($_COOKIE['phone_error']);
	$errors['email'] = empty($_COOKIE['email_error']) ? "" : strip_tags($_COOKIE['email_error']);
	$errors['birthday'] = empty($_COOKIE['birthday_error']) ? "" : strip_tags($_COOKIE['birthday_error']);
	$errors['sex'] = empty($_COOKIE['sex_error']) ? "" : strip_tags($_COOKIE['sex_error']);
	$errors['langs'] = empty($_COOKIE['langs_error']) ? "" : strip_tags($_COOKIE['langs_error']);
	$errors['bio'] = empty($_COOKIE['bio_error']) ? "" : strip_tags($_COOKIE['bio_error']);
	$errors['consent'] = empty($_COOKIE['consent_error']) ? "" : strip_tags($_COOKIE['consent_error']);

	if ($errors['fio']) {
		setcookie('fio_error', '', 100000);
		setcookie('fio_value', '', 100000);
		$messages['fio'] = '<div class="error">'.$fioErrors[$errors['fio']].'</div>';
	}
	if ($errors['phone']) {
		setcookie('phone_error', '', 100000);
		setcookie('phone_value', '', 100000);
		$messages['phone'] = '<div class="error">'.$phoneErrors[$errors['phone']].'</div>';
	}
	if ($errors['email']) {
		setcookie('email_error', '', 100000);
		setcookie('email_value', '', 100000);
		$messages['email'] = '<div class="error">'.$emailErrors[$errors['email']].'</div>';
	}
	if ($errors['birthday']) {
		setcookie('birthday_error', '', 100000);
		setcookie('birthday_value', '', 100000);
		$messages['birthday'] = '<div class="error">'.$dateErrors[$errors['birthday']].'</div>';
	}
	if ($errors['sex']) {
		setcookie('sex_error', '', 100000);
		setcookie('sex_value', '', 100000);
		$messages['sex'] = '<div class="error">'.$sexErrors[$errors['sex']].'</div>';
	}
	if ($errors['langs']) {
		setcookie('langs_error', '', 100000);
		setcookie('langs_value', '', 100000);
		$messages['langs'] = '<div class="error">'.$langsErrors[$errors['langs']].'</div>';
	}
	if ($errors['bio']) {
		setcookie('bio_error', '', 100000);
		setcookie('bio_value', '', 100000);
		$messages['bio'] = '<div class="error">'.$bioErrors[$errors['bio']].'</div>';
	}
	if ($errors['consent']) {
		setcookie('consent_error', '', 100000);
		$messages['consent'] = '<div class="error">'.$consentErrors[$errors['consent']].'</div>';
	}

	$pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $user, $pass);
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

	?>
		<title>Лаб. Работа 6</title>
		<link rel="stylesheet" href="admin_style.css">
		<body>
			<div class="form-container">
				<div class="title">Вы вошли как администратор </div>

				<h3>Статистика предпочтений языков</h3>
				<?php for ($i = 0; $i < count($names); $i++){
					print $names[$i].": ".$counts[$i]."<br>";
				}?>

				<h3>Редактирование заявки</h3>
				<form action="" method="POST" id="form">
					<label class="label-form">ID:<br><input type="text" id="editId" name="editId" class="input-form"></label>
					<label class="label-form">ФИО:<br><input type="text" id="fio" name="fio" <?php if ($errors['fio']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="Иванов Иван Иванович"></label>
					<?php if (!empty($messages['fio'])) print $messages['fio'] ?>
					<label class="label-form">Телефон:<br><input type="tel" id="phone" name="phone" <?php if ($errors['phone']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="+7(9xx)xxx-xx-xx"></label>
					<?php if (!empty($messages['phone'])) print $messages['phone'] ?>
					<label class="label-form">Электронная почта:<br><input type="email" id="email" name="email" <?php if ($errors['email']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?>></label>
					<?php if (!empty($messages['email'])) print $messages['email'] ?>
					<label class="label-form">Дата рождения:<br><input type="date" id="birthday" name="birthday" <?php if ($errors['birthday']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?>></label>
					<?php if (!empty($messages['birthday'])) print $messages['birthday'] ?>
					<label class="label-form">
						Пол:<br />
						<input name="sex" type="radio" id="man" value="man"/>Мужской
						<input name="sex" type="radio" id="woman" value="woman"/>Женский
					</label>
					<?php if (!empty($messages['sex'])) print $messages['sex'] ?>
					<label class="label-form">
						Любимый язык программирования<br />
						<select name="languages[]" multiple>
							<option value="1">Pascal</option>
							<option value="2">C</option>
							<option value="3">C++</option>
							<option value="4">JavaScript</option>
							<option value="5">PHP</option>
							<option value="6">Python</option>
							<option value="7">Java</option>
							<option value="8">Haskal</option>
							<option value="9">Clojure</option>
							<option value="10">Prolog</option>
							<option value="11">Scala</option>
							<option value="12">Go</option>
						</select>
					</label>
					<?php if (!empty($messages['langs'])) print $messages['langs'] ?>
					<label class="label-form">Биография:<textarea name="bio" <?php if ($errors['bio']) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>></textarea></label>
					<?php if (!empty($messages['bio'])) print $messages['bio'] ?>
					<input type="hidden" name="action" value="update">
					<div>
						<button class="form-btn" type="submit">Обновить запись</button>
					</div>
				</form>

				<h3>Текущие заявки</h3>
				<div style="overflow-x: auto;">
					<table id="dataTable">
						<thead>
							<tr>
								<th>ID</th><th>Фамилия</th><th>Имя</th><th>Отчество</th><th>Телефон</th>
								<th>Email</th><th>Дата рожд.</th><th>Пол</th><th>Биография</th><th>Действия</th>
							</tr>
						</thead>
						<tbody id="tableBody">
							<?php foreach($applications as $app): ?>
							<tr>
								<td><?= $app['id'] ?></td>
								<td><?= htmlspecialchars($app['surname']) ?></td>
								<td><?= htmlspecialchars($app['name']) ?></td>
								<td><?= htmlspecialchars($app['patronymic']) ?></td>
								<td><?= htmlspecialchars($app['phone_number']) ?></td>
								<td><?= htmlspecialchars($app['email']) ?></td>
								<td><?= htmlspecialchars($app['birthday']) ?></td>
								<td><?= htmlspecialchars($app['sex']) ?></td>
								<td><?= htmlspecialchars($app['biography']) ?></td>
								<td class="action-btns">
									<form method="POST" style="display:inline">
										<input type="hidden" name="action" value="delete">
										<input type="hidden" name="id" value="<?= $app['id'] ?>">
										<button class="act-btn" type="submit" onclick="return confirm('Удалить?')">Удалить</button>
									</form>
									<button class="act-btn" id="edit-btn" data-id="<?php print $app['id']; ?>">Редактировать</button>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<script type="text/javascript">
					function loadApplicationToForm(id) {
						fetch('api/get_application.php?id=' + id)
						.then(response => {
							if (!response.ok) {
								throw new Error('Ошибка HTTP: ' + response.status);
							}
							return response.json();
						})
						.then(data => {
							console.log('Полученные данные:', data);
							// Используем полученные данные
							if (data) {
								console.log('ID:', data.id);
								console.log('Другие поля:', data);
								document.getElementById('editId').value = data.id;
								document.getElementById('fio').value = `${data.surname} ${data.name} ${data.patronymic}`;
								document.getElementById('phone').value = data.phone_number;
								document.getElementById('email').value = data.email;
								document.getElementById('birthday').value = data.birthday;
								if (data.sex == "man") document.getElementById('man').checked = "checked";
								else document.getElementById('woman').checked = "checked";
							} else {
								console.log('Запись не найдена');
							}
						})
						.catch(error => {
							console.error('Ошибка:', error);
						});
					}

					document.addEventListener("DOMContentLoaded", (event) => {
						document.querySelectorAll('#edit-btn').forEach(btn => {
							btn.addEventListener('click', (e) => {
								const id = parseInt(btn.getAttribute('data-id'));
								loadApplicationToForm(id);
							});
						});
					});
				</script>
			</div>
		</body>
	<?php
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['action'])) {
        $pdo = new PDO("mysql:host=localhost;dbname=$dbname;charset=utf8", $user, $pass);
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
