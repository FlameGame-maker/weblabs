<!DOCTYPE html>
<html>
<head>
	<title>Лаб. Работа 6</title>
	<link rel="stylesheet" href="pages/admin_style.css">
</head>
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
			<label class="label-form">Биография:<textarea id="bio" name="bio" <?php if ($errors['bio']) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>></textarea></label>
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
						<td><?= htmlspecialchars($app['langs']) ?></td>
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
				fetch('get_application.php?id=' + id)
				.then(response => {
					if (!response.ok) {
						throw new Error('Ошибка HTTP: ' + response.status);
					}
					return response.json();
				})
				.then(data => {
					console.log('Полученные данные:', data);
					if (data) {
						document.getElementById('editId').value = data.id;
						document.getElementById('fio').value = `${data.surname} ${data.name} ${data.patronymic}`;
						document.getElementById('phone').value = data.phone_number;
						document.getElementById('email').value = data.email;
						document.getElementById('birthday').value = data.birthday;
						document.getElementById('bio').value = data.biography;
						if (data.sex == "man") document.getElementById('man').checked = "checked";
						else document.getElementById('woman').checked = "checked";

						const languages = document.querySelector('select[name="languages[]"]');
						Array.from(languages.options).forEach(option => { option.selected = false; });
						data.langs.forEach(value => {
							const option = languages.querySelector(`option[value="${value}"]`);
							if (option) option.selected = true;
						});
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