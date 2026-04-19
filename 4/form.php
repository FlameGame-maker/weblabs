<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Лаб. Работа 4</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<?php require_once("index.php"); ?>
	<div class="form-container">
		<div class="title">Форма</div>
		<form action="index.php" method="POST" id="form">
			<label class="label-form">ФИО:<br><input type="text" name="fio" <?php if ($errors['fio']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="Иванов Иван Иванович" value="<?php print $values['fio']; ?>"></label>
			<?php if (!empty($messages['fio'])) print $messages['fio'] ?>
			<label class="label-form">Телефон:<br><input type="tel" name="phone" <?php if ($errors['phone']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> placeholder="+7(9xx)xxx-xx-xx" value="<?php print $values['phone']; ?>"></label>
			<?php if (!empty($messages['phone'])) print $messages['phone'] ?>
			<label class="label-form">Электронная почта:<br><input type="email" name="email" <?php if ($errors['email']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> value="<?php print $values['email']; ?>"></label>
			<?php if (!empty($messages['email'])) print $messages['email'] ?>
			<label class="label-form">Дата рождения:<br><input type="date" name="birthday" <?php if ($errors['birthday']) {print 'class="error-field input-form"';} else {print 'class="input-form"';} ?> value="<?php print $values['birthday']; ?>"></label>
			<?php if (!empty($messages['birthday'])) print $messages['birthday'] ?>
			<label class="label-form">
				Пол:<br />
				<input name="sex" type="radio" value="man" <?php if ($values['sex']=="man") print 'checked'; ?>/>Мужской
				<input name="sex" type="radio" value="woman" <?php if ($values['sex']=="woman") print 'checked'; ?>/>Женский
			</label>
			<?php if (!empty($messages['sex'])) print $messages['sex'] ?>
			<label class="label-form">
				Любимый язык программирования<br />
				<select name="languages[]" multiple>
					<option value="1" <?php if (in_array(1, $values['langs'])) print 'selected' ?> >Pascal</option>
					<option value="2" <?php if (in_array(2, $values['langs'])) print 'selected' ?> >C</option>
					<option value="3" <?php if (in_array(3, $values['langs'])) print 'selected' ?> >C++</option>
					<option value="4" <?php if (in_array(4, $values['langs'])) print 'selected' ?> >JavaScript</option>
					<option value="5" <?php if (in_array(5, $values['langs'])) print 'selected' ?> >PHP</option>
					<option value="6" <?php if (in_array(6, $values['langs'])) print 'selected' ?> >Python</option>
					<option value="7" <?php if (in_array(7, $values['langs'])) print 'selected' ?> >Java</option>
					<option value="8" <?php if (in_array(8, $values['langs'])) print 'selected' ?> >Haskal</option>
					<option value="9" <?php if (in_array(9, $values['langs'])) print 'selected' ?> >Clojure</option>
					<option value="10" <?php if (in_array(10, $values['langs'])) print 'selected' ?> >Prolog</option>
					<option value="11" <?php if (in_array(11, $values['langs'])) print 'selected' ?> >Scala</option>
					<option value="12" <?php if (in_array(12, $values['langs'])) print 'selected' ?> >Go</option>
				</select>
			</label>
			<?php if (!empty($messages['langs'])) print $messages['langs'] ?>
			<label class="label-form">Биография:<textarea name="bio" <?php if ($errors['bio']) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>><?php print $values['bio']; ?></textarea></label>
			<?php if (!empty($messages['bio'])) print $messages['bio'] ?>
			<label class="label-form"><input type="checkbox" name="consent">С контрактом ознакомлен(а)</label>
			<?php if (!empty($messages['consent'])) print $messages['consent'] ?>
			<button class="form-btn" type="submit">Сохранить</button>
		</form>
		<script>
			<?php if ($congrats !== "") print 'alert("Данные были успешно сохранены");'; ?>
		</script>
	</div>
</body>
</html>