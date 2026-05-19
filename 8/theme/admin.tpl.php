<!DOCTYPE html>
<html>
<head>
	<title>Лаб. Работа 6</title>
	<style>
		* { box-sizing: border-box; margin: 0; padding: 0; }
		body { background-color: black; min-height: 100vh; background-color: darkslateblue; }
		.title { font-size: 25px; text-align: center; }
		h3 { border-bottom: 2px solid grey; margin-bottom: 10px; padding-bottom: 10px; }
		.form-container { width: 60%; min-width: 300px; height: auto; min-height: 400px; background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; justify-content: flex-start; margin: 50px auto; }
		form { width: 100%; height: auto; min-height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
		.label-form { width: 100%; margin-bottom: 5px; font-weight: bold; }
		.input-form { width: 100%; height: 2.5rem; border-radius: 20px; padding: 0 15px; border: 1px solid darkslateblue; font-size: 16px; }
		.error-field { border: 2px solid red; color: red; }
		.error { color: red; margin-right: auto; margin-bottom: 10px; }
		textarea { width: 100%; min-height: 100px; padding: 10px; border-radius: 10px; border: 1px solid darkslateblue; resize: vertical; font-size: 16px; }
		.form-btn { align-self: center; width: 40%; min-width: 120px; min-height: 45px; border-radius: 25px; background-color: darkslateblue; color: white; border: none; font-size: 16px; cursor: pointer; }
		.act-btn { align-self: center; min-width: 100%; min-height: 20px; margin: 3px 0; border-radius: 5px; background-color: darkslateblue; color: white; border: none; font-size: 16px; cursor: pointer; }
		@media (max-width: 768px) {
			.popup { width: 95%; padding: 15px; max-height: 85vh; }
			.form-btn { width: 45%; min-width: 100px; height: 40px; font-size: 14px; }
			#call-popup { padding: 12px 25px; font-size: 16px; }
			.input-form { height: 2.2rem; font-size: 14px; }
			textarea { font-size: 14px; }
		}
	</style>
</head>
<body>
	<div class="form-container">
		<div class="title">Вы вошли как администратор </div>

		<h3>Статистика предпочтений языков</h3>
		<?php for ($i = 0; $i < count($c['names']); $i++){
			print $c['names'][$i].": ".$c['counts'][$i]."<br>";
		}?>

		<h3>Редактирование заявки</h3>
		<form action="admin" method="POST" id="form">
			<input type="hidden" name="method" value="put">
			<input type="hidden" name="token" value="<?php print $c['token']; ?>">
			<label class="label-form">ID:<br><input type="text" id="editId" name="id" class="input-form"></label>
			<label class="label-form">ФИО:<br><input type="text" id="fio" name="fio" <?php if ($c['errors']['fio']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="Иванов Иван Иванович"></label>
			<?php if (!empty($c['messages']['fio'])) print $c['messages']['fio'] ?>
			<label class="label-form">Телефон:<br><input type="tel" id="phone" name="phone" <?php if ($c['errors']['phone']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="+7(9xx)xxx-xx-xx"></label>
			<?php if (!empty($c['messages']['phone'])) print $c['messages']['phone'] ?>
			<label class="label-form">Электронная почта:<br><input type="email" id="email" name="email" <?php if ($c['errors']['email']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?>></label>
			<?php if (!empty($c['messages']['email'])) print $c['messages']['email'] ?>
			<label class="label-form">Дата рождения:<br><input type="date" id="birthday" name="birthday" <?php if ($c['errors']['birthday']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?>></label>
			<?php if (!empty($c['messages']['birthday'])) print $c['messages']['birthday'] ?>
			<label class="label-form">
				Пол:<br />
				<input name="sex" type="radio" id="man" value="man"/>Мужской
				<input name="sex" type="radio" id="woman" value="woman"/>Женский
			</label>
			<?php if (!empty($c['messages']['sex'])) print $c['messages']['sex'] ?>
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
			<?php if (!empty($c['messages']['langs'])) print $c['messages']['langs'] ?>
			<label class="label-form">Биография:<textarea id="bio" name="bio" <?php if ($c['errors']['bio']) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>></textarea></label>
			<?php if (!empty($c['messages']['bio'])) print $c['messages']['bio'] ?>
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
						<th>Email</th><th>Дата рожд.</th><th>Пол</th><th>Любимые языки</th><th>Биография</th><th>Действия</th>
					</tr>
				</thead>
				<tbody id="tableBody">
					<?php foreach($c['applications'] as $app): ?>
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
							<form action="admin" method="POST" style="display:inline">
								<input type="hidden" name="method" value="delete">
								<input type="hidden" name="token" value="<?php print $c['token']; ?>">
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
				const baseUrl = '<?= conf("basedir") ?>';
				fetch(baseUrl + 'admin/' + id)
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