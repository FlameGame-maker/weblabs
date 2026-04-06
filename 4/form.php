<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Лаб. Работа 3</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<?php
		if (!empty($messages)) {
		  print('<dialog id="messages">');
		  foreach ($messages as $message) {
			print($message);
		  }
		  print('</dialog>');
		}
	?>

	<div class="form-container">
		<div class="title">Форма</div>
		<form action="index.php" method="POST" id="form">
			<label class="label-form">ФИО:<br><input type="text" class="input-form" <?php if ($errors['fio']) {print 'class="error"';} ?> placeholder="Иванов Иван Иванович" value="<?php print $values['fio']; ?>" name="fio"></label>
			<label class="label-form">Телефон:<br><input type="tel" class="input-form" <?php if ($errors['phone']) {print 'class="error"';} ?> placeholder="+7(9xx)xxx-xx-xx" name="phone"></label>
			<label class="label-form">Электронная почта:<br><input type="email" class="input-form" <?php if ($errors['email']) {print 'class="error"';} ?> name="email"></label>
			<label class="label-form">Дата рождения:<br><input type="date" class="input-form" <?php if ($errors['birthday']) {print 'class="error"';} ?> name="birthday"></label>
			<label class="label-form" >
				//Если значение в куки, соответствующую кнопку сделать checked
				Пол:<br />
				<input name="sex" type="radio" value="man" />Мужской
				<input name="sex" type="radio" value="woman" />Женский
			</label>
			<label class="label-form">
			//Распарсить записанный в куки массив, соотвутствующие значения сделать selected
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
			<label class="label-form">Биография:<textarea class="form-control" name="bio"></textarea></label>
			<label class="label-form"><input type="checkbox" name="consent">С контрактом ознакомлен(а)</label>
			<button class="form-btn" type="submit">Сохранить</button>
		</form>
		
	</div>
</body>
</html>