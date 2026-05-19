<!DOCTYPE html>
<html>
<head>
	<title>Лаб. Работа 6</title>
	<style>
		* { box-sizing: border-box; margin: 0; padding: 0; }
		body { background-color: black; min-height: 100vh; background-color: darkslateblue; }
		.title { font-size: 25px; text-align: center; }
		.form-container { position: absolute; width: 60%; min-width: 300px; height: auto; min-height: 400px; max-height: 90vh; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; justify-content: flex-start; overflow-y: auto; }
		form { width: 100%; height: auto; min-height: 300px; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
		.label-form { width: 100%; margin-bottom: 5px; font-weight: bold; }
		.input-form { width: 100%; height: 2.5rem; border-radius: 20px; padding: 0 15px; border: 1px solid darkslateblue; font-size: 16px; }
		.error-field { border: 2px solid red; color: red; }
		.error { color: red; margin-right: auto;margin-bottom: 10px; }
		textarea { width: 100%; min-height: 100px; padding: 10px; border-radius: 10px; border: 1px solid darkslateblue; resize: vertical; font-size: 16px; }
		.form-btn { align-self: center; width: 40%; min-width: 120px; min-height: 45px; border-radius: 25px; background-color: darkslateblue; color: white; border: none; font-size: 16px; cursor: pointer; }
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
		<div class="title"><?php if (isset($c['messages']['login'])) print $c['messages']['login'] ?></div>
		<div class="title">Форма</div>
		<form action="form" method="POST" id="form">
			<input type="hidden" name="token" value="<?php print $c['token'] ?? ''; ?>">
			<label class="label-form">ФИО:<br><input type="text" name="fio" <?php if (isset($c['errors']['fio'])) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="Иванов Иван Иванович" value="<?php print $c['values']['fio']; ?>"></label>
			<?php if (isset($c['messages']['fio'])) print $c['messages']['fio'] ?>
			<label class="label-form">Телефон:<br><input type="tel" name="phone" <?php if (isset($c['errors']['phone'])) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="+7(9xx)xxx-xx-xx" value="<?php print $c['values']['phone']; ?>"></label>
			<?php if (isset($c['messages']['phone'])) print $c['messages']['phone'] ?>
			<label class="label-form">Электронная почта:<br><input type="email" name="email" <?php if (isset($c['errors']['email'])) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> value="<?php print $c['values']['email']; ?>"></label>
			<?php if (isset($c['messages']['email'])) print $c['messages']['email'] ?>
			<label class="label-form">Дата рождения:<br><input type="date" name="birthday" <?php if (isset($c['errors']['birthday'])) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> value="<?php print $c['values']['birthday']; ?>"></label>
			<?php if (isset($c['messages']['birthday'])) print $c['messages']['birthday'] ?>
			<label class="label-form">
				Пол:<br />
				<input name="sex" type="radio" value="man" <?php if ($c['values']['sex']=="man") print 'checked'; ?>/>Мужской
				<input name="sex" type="radio" value="woman" <?php if ($c['values']['sex']=="woman") print 'checked'; ?>/>Женский
			</label>
			<?php if (isset($c['messages']['sex'])) print $c['messages']['sex'] ?>
			<label class="label-form">
				Любимый язык программирования<br />
				<select name="languages[]" multiple>
					<option value="1" <?php if (in_array(1, $c['values']['langs'])) print 'selected' ?> >Pascal</option>
					<option value="2" <?php if (in_array(2, $c['values']['langs'])) print 'selected' ?> >C</option>
					<option value="3" <?php if (in_array(3, $c['values']['langs'])) print 'selected' ?> >C++</option>
					<option value="4" <?php if (in_array(4, $c['values']['langs'])) print 'selected' ?> >JavaScript</option>
					<option value="5" <?php if (in_array(5, $c['values']['langs'])) print 'selected' ?> >PHP</option>
					<option value="6" <?php if (in_array(6, $c['values']['langs'])) print 'selected' ?> >Python</option>
					<option value="7" <?php if (in_array(7, $c['values']['langs'])) print 'selected' ?> >Java</option>
					<option value="8" <?php if (in_array(8, $c['values']['langs'])) print 'selected' ?> >Haskal</option>
					<option value="9" <?php if (in_array(9, $c['values']['langs'])) print 'selected' ?> >Clojure</option>
					<option value="10" <?php if (in_array(10, $c['values']['langs'])) print 'selected' ?> >Prolog</option>
					<option value="11" <?php if (in_array(11, $c['values']['langs'])) print 'selected' ?> >Scala</option>
					<option value="12" <?php if (in_array(12, $c['values']['langs'])) print 'selected' ?> >Go</option>
				</select>
			</label>
			<?php if (isset($c['messages']['langs'])) print $c['messages']['langs'] ?>
			<label class="label-form">Биография:<textarea name="bio" <?php if (isset($c['errors']['bio'])) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>><?php print $c['values']['bio']; ?></textarea></label>
			<?php if (isset($c['messages']['bio'])) print $c['messages']['bio'] ?>
			<label class="label-form"><input type="checkbox" name="consent">С контрактом ознакомлен(а)</label>
			<?php if (isset($c['messages']['consent'])) print $c['messages']['consent'] ?>
			<div>
				<button class="form-btn" type="submit">Сохранить</button>
				<button class="form-btn" id="logout">Выйти</button>
			</div>
		</form>
		<script>
			document.getElementById('logout').addEventListener('click', function() {
				fetch('index.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: 'action=clicked'
				})
				.then(response => response.text())
				.then(data => alert(data));
			});

			<?php if ($c['congrats'] !== "") print 'alert("Данные были успешно сохранены");'; ?>
		</script>
	</div>
</body>
</html>