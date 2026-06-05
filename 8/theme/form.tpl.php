<!DOCTYPE html>
<html>
<head>
	<title>Лаб. Работа 6</title>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<style>
		body { display: flex; flex-direction: column; align-items: center; font-family: 'Montserrat', sans-serif; margin: 0; width: 100vw; }
		.mobile-nav { position: fixed; bottom: 0; left: 0; width: 100%; z-index: 1000; display: flex; justify-content: space-between; background-color: black; }
		.mobile-nav-bar { position: relative; width: 100vw; margin-bottom: 20px; }
		.mobile-nav-toggle { position: absolute; bottom: 0; right: 0; cursor: pointer; z-index: 1001; }
		.mobile-nav-menu { position: absolute; bottom: 36px; left: 0px; transform-origin: bottom; transform: scaleY(0); background-color: black; width: 100%; opacity: 0; pointer-events: none; }
		.mobile-nav-menu.active {transform: scaleY(1); opacity: 1; pointer-events: auto; }
		.mobile-nav-items { list-style: none; display: flex; flex-direction: column; gap: 10px; }
		.mobile-nav-item { padding: 10px; display: flex; align-items: center; cursor: pointer; color: lightgrey; border-bottom: 1px solid lightgrey; text-decoration: none; font-size: 80%; }
		header { display: flex; justify-content: center; width: 100%; height: auto; padding: 40px 0; position: relative; overflow: hidden; color: white;  }
		.header-video { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; filter: brightness(0.3); }
		.header-container { width: 95%; z-index: 1; }
		.main-nav { display: none; flex-direction: column; gap: 30px; justify-content: space-between; align-items: center; margin: 30px 0px 80px 0px; }
		.logo { height: 40px; width: auto; }
		.nav-menu { display: flex; flex-wrap: wrap; justify-content: center; gap: 15px; list-style: none; margin: 0; padding: 0; }
		.nav-item { font-size: 70%; cursor: pointer; }
		.nav-item .dropdown-menu { display: flex; flex-direction: column; visibility: hidden; position: absolute; background-color: red; z-index: 2; }
		.nav-item:hover .dropdown-menu { visibility: visible; }
		.nav-item .dropdown-menu a { max-width: 100%; color: white; padding: 5px 10px; text-decoration: none; }
		.nav-item .dropdown-menu a:hover { background-color: darkred; }
		.hero-content { display: grid; grid-template-columns: 1fr; gap: 40px; padding-top: 30px; }
		.hero-offer { display: flex; flex-direction: column; gap: 20px; }
		.hero-title { font-size: 36px; font-weight: 600; line-height: 1.2; margin: 0; }
		.hero-subtitle { font-size: 18px;font-weight: 300; line-height: 1.5; margin: 0; }
		.btn-tariffs { display: none; align-self: flex-start; padding: 12px 32px; color: white; background-color: transparent; border: 2px solid red; border-radius: 5px; font-weight: 600; cursor: pointer; }
		.stats-row { display: grid; gap: 15px; width: 100%; list-style: none; padding: 0; margin: 0; list-style: none; grid-template-columns: 1fr 1fr; grid-template-rows: auto auto auto; }
		.stats-row li { width: 90%; padding: 5px; margin-top: 40px; }
		.stats-elem { width: 50%; display: block; border-left: 2px solid red; }
		.stats-elem h3 { font-size: 32px; font-weight: 600; margin: 0 0 10px 0; }
		.stats-elem p { font-size: 14px; font-weight: 400; line-height: 1.4; margin: 0; }
		main { display: flex; flex-direction: column; align-items: center; width: 95%; }
		.gallery-wrapper { display: flex; flex-direction: row; justify-content: center; align-items: center; width: 80%; position: relative; overflow: hidden; margin-bottom: 40px; }
		.gallery-track { display: flex; flex-direction: row; transition: transform 0.3s; width: 100%; margin: 0 10px; }
		.slide { display: flex; justify-content: center; min-width: 100%; }
		.slide-content { display: flex; flex-direction: column; width: 425px; background-color: white; border: 1px solid lightgrey; border-radius: 5px; }
		.slide-content h3 { width: 80%; align-self: center; padding-bottom: 30px;border-bottom: 2px solid lightgrey; font-size: 24px; color: darkorange; }
		.slide-content ul li { margin: 15px; font-size: 16px; }
		.slide-content ul li::marker { color: darkorange; }
		.slide-content button { align-self: center; width: 300px; height: 60px; margin-top: 10px; margin-bottom: 50px; border: 3px solid darkorange; border-radius: 5px; color: darkorange; background-color: white; }
		.slide-content button:hover { border: 3px solid white; color: white; background-color: darkorange; }
		.btn { background: white; width: 30px; height: 30px; border: 1px solid grey; border-radius: 15px; color: grey; background-color: white; cursor: pointer; z-index: 2; }
		.btn:hover { background-color: aliceblue; }
		.btn:disabled { opacity: 0.5; cursor: not-allowed; }
		footer { display: grid; grid-template-columns: 1fr; gap: 20px; background: linear-gradient(black, darkslategrey); }
		.form-wrapper { display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; width: 70%; margin: 0 auto; }
		.form-wrapper form { width: 100%; }
		.form-title { font-size: 25px; text-align: center; }
		.contact-us { color: white; height: 100%; }
		.contact-us h3 { margin: 80px 0px 0px 20px; }
		.contact-us u { margin: 10px 0px 0px 20px; }
		.form-group { margin-bottom: 5px; }
		.form-control { width: 100%; padding: 12px 15px; border: 1px solid grey; border-radius: 8px; background-color: transparent; color: lightgray; }
		.form-control::placeholder { font-weight: 500; color: lightgray; opacity: 1; }
		textarea.form-control { min-height: 80px; resize: none; }
		.checkbox-group { display: flex; flex-direction: row; margin-top: 10px 0px; }
		.checkbox-group input { width: 20px; height: 20px; margin-right: 10px; }
		.checkbox-group label { font-size: 80%; }
		.submit-button { width: 100%; padding: 15px; background-color: cadetblue; color: white; border: none; border-radius: 8px; cursor: pointer; margin-top: 20px; }
		.submit-button:hover { background-color: darkturquoise; }
		.submit-button:disabled { background-color: darkgray; cursor: not-allowed; }
		.submit-button h3 { margin: 0; }
		.message { padding: 15px; border-radius: 8px; margin-top: 20px; display: none; text-align: center; }
		.success { background-color: yellowgreen; border: 1px solid black; }
		.error-field { background-color: firebrick; border: 1px solid black; }
		.error { color: firebrick; }

		@media (min-width: 880px) {
			.mobile-nav { display: none; }
			header { height: 650px; padding: 0; }
			.header-container { width: 70%; }
			.main-nav { display: flex; flex-direction: row; gap: 0; margin-bottom: 60px; }
			.nav-menu { justify-content: space-between; gap: 20px; }
			.hero-content { grid-template-columns: 1fr 1fr; gap: 60px; }
			.gallery-wrapper { width: 40%; }
			footer { grid-template-columns: 1fr 1fr; }
			main { width: 100%; background: linear-gradient(to top right, white, deeppink); background-color:  }
			.stats-elem { width: 33%; }
			.stats-row { grid-template-columns: 1fr 1fr 1fr; grid-template-rows: auto auto; }
			.btn-tariffs { display: block; }
			.hero-title { font-size: 48px; }
		}
	</style>
</head>
<body>
	<div class="mobile-nav">
		<div class="mobile-nav-bar">
			<img class="logo" src="res/Drupal.png" alt="Логотип">
			<div class="mobile-nav-toggle" id="navToggle">
				<img src="res/NavToggle.png" alt="Navigation bar toggle" />
			</div>
			<div class="mobile-nav-menu" id="navMenu">
				<ul class="mobile-nav-items">
					<li class="mobile-nav-item active">
						ПОДДЕРЖКА
					</li>
					<li class="mobile-nav-item">
						АДМИНИСТРИРОВАНИЕ
					</li>
					<li class="mobile-nav-item">
						РЕКЛАМА
					</li>
					<li class="mobile-nav-item">
						О НАС
					</li>
					<li class="mobile-nav-item">
						ПРОЕКТЫ
					</li>
					<li class="mobile-nav-item">
						КОНТАКТЫ
					</li>
				</ul>
			</div>
		</div>
	</div>

	<header>
		<video class="header-video" autoplay muted loop playsinline>
			<source src="res/video.mp4" type="video/mp4">
		</video>

		<div class="header-container">
			<nav class="main-nav">
				<img class="logo" src="res/Drupal.png" alt="Логотип">

				<ul class="nav-menu">
					<li class="nav-item">ПОДДЕРЖКА</li>
					<li class="nav-item">
						АДМИНИСТРИРОВАНИЕ ▾
						<div class="dropdown-menu">
							<a href="#">МИГРАЦИЯ</a>
							<a href="#">БЭКАПЫ</a>
							<a href="#">АУДИТ БЕЗОПАСНОСТИ</a>
							<a href="#">ОПТИМИЗАЦИЯ СКОРОСТИ</a>
							<a href="#">ПЕРЕЕЗД НА HTTPS</a>
						</div>
					</li>
					<li class="nav-item">ПРОДВИЖЕНИЕ</li>
					<li class="nav-item">РЕКЛАМА</li>
					<li class="nav-item">О НАС</li>
					<li class="nav-item">ПРОЕКТЫ</li>
					<li class="nav-item">КОНТАКТЫ</li>
				</ul>
			</nav>

			<div class="hero-content">
				<section class="hero-offer">
					<h1 class="hero-title">Поддержка сайтов на Drupal</h1>
					<p class="hero-subtitle">Сопровождение и поддержка сайтов на CMS Drupal любых версий и запущенности</p>
					<button class="btn-tariffs" id="tarrifsBtn">Тарифы</button>
				</section>

				<section>
					<ul class="stats-row" style="flex-wrap: nowrap;">
						<li class="stats-elem">
							<h3>#1</h3>
							<p>Drupal-разработчик в России по версии Рейтинга Рунета</p>
						</li>
						<li class="stats-elem">
							<h3>3+</h3>
							<p>средний опыт специалистов более 3 лет</p>
						</li>
						<li class="stats-elem">
							<h3>14</h3>
							<p>лет опыта в сфере Drupal</p>
						</li>
						<li class="stats-elem">
							<h3>50+</h3>
							<p>модулей и тем в формате DrupalGive</p>
						</li>
						<li class="stats-elem">
							<h3>90 000+</h3>
							<p>часов поддержки сайтов на Drupal</p>
						</li>
						<li class="stats-elem">
							<h3>300+</h3>
							<p>Проектов на поддержке</p>
						</li>
					</ul>
				</section>
			</div>
		</div>
	</header>

	<main>
		<h1>Тарифы</h1>
		<div class="gallery-wrapper">
			<button class="btn" id="prevBtn"><</button>
			<div style="flex-grow: 1; overflow: hidden;">
				<div class="gallery-track" id="galleryTrack">
					<div class="slide">
						<div class="slide-content">
							<h3>Стартовый</h3>
							<ul>
								<li>Консультации и работы по CEO</li>
								<li>Услуги дизайнера</li>
								<li>Неиспользованные оплаченные часы не переносятся</li>
								<li>Предоплата от 6 000 руюлей в месяц</li>
							</ul>
							<button class="contact-us">СВЯЖИТЕСЬ С НАМИ</button>
						</div>
					</div>
					<div class="slide">
						<div class="slide-content">
							<h3>Бизнес</h3>
							<ul>
								<li>Консультации и работы по CEO</li>
								<li>Услуги дизайнера</li>
								<li>Высокое время реакции - до 2 рабочих дней</li>
								<li>Неиспользованные оплаченные часы не переносятся</li>
								<li>Предоплата от 30 000 руюлей в месяц</li>
							</ul>
							<button class="contact-us">СВЯЖИТЕСЬ С НАМИ</button>
						</div>
					</div>
					<div class="slide">
						<div class="slide-content">
							<h3>VIP</h3>
							<ul>
								<li>Консультации и работы по CEO</li>
								<li>Услуги дизайнера</li>
								<li>Максимальное время реакции - в день обращения</li>
								<li>Неиспользованные оплаченные часы не переносятся</li>
								<li>Предоплата от 270 000 руюлей в месяц</li>
							</ul>
							<button class="contact-us">СВЯЖИТЕСЬ С НАМИ</button>
						</div>
					</div>
				</div>
			</div>
			<button class="btn" id="nextBtn">></button>
		</div>

		<div class="form-title"><?php if (isset($c['messages']['login'])) print $c['messages']['login'] ?></div>
	</main>

	<footer>
		<section class="form-wrapper">
			<div class="contact-us">
				<h1>Оставьте заявку на поддержку сайта</h1>
				<p>
					Срочно нужна поддержка сайта? Ваша команда не успевает справиться самостоятельно или предыдущий подрядчик не справился с работой?
					Тогда вам точно к нам! Просто оставьте заявку и наш менеджер с вами свяжется!
				</p>
				<h3>8 800 222-26-73</h3>
				<u>info@drupal-coder.ru</u>
			</div>
		</section>

		<section>
			<div class="form-wrapper">
				<form action="form" method="POST" id="feedbackForm">
					<input type="hidden" name="token" value="<?php print $c['token'] ?? ''; ?>">
					<div class="form-group">
						<label style="color: lightgray;">
							ФИО:<br/>
							<input type="text" name="fio" <?php if (isset($c['errors']['fio'])) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?> placeholder="Иванов Иван Иванович" value="<?php print $c['values']['fio']; ?>">
							<?php if (isset($c['messages']['fio'])) print $c['messages']['fio'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
							Телефон:<br/>
							<input type="tel" name="phone" <?php if (isset($c['errors']['phone'])) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?> placeholder="+7(9xx)xxx-xx-xx" value="<?php print $c['values']['phone']; ?>">
							<?php if (isset($c['messages']['phone'])) print $c['messages']['phone'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
							Электронная почта:<br/>
							<input type="email" name="email" <?php if (isset($c['errors']['email'])) {print 'class="error-field form-control"';} else {print 'class="form-control';} ?> value="<?php print $c['values']['email']; ?>">
							<?php if (isset($c['messages']['email'])) print $c['messages']['email'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
							Дата рождения:<br/>
							<input type="date" name="birthday" <?php if (isset($c['errors']['birthday'])) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?> value="<?php print $c['values']['birthday']; ?>">
							<?php if (isset($c['messages']['birthday'])) print $c['messages']['birthday'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
							Пол:<br/>
							<input name="sex" type="radio" value="man" <?php if ($c['values']['sex']=="man") print 'checked'; ?>/>Мужской
							<input name="sex" type="radio" value="woman" <?php if ($c['values']['sex']=="woman") print 'checked'; ?>/>Женский
							<?php if (isset($c['messages']['sex'])) print $c['messages']['sex'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
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
							<?php if (isset($c['messages']['langs'])) print $c['messages']['langs'] ?>
						</label>
					</div>
					<div class="form-group">
						<label style="color: lightgray;">
							Биография:
							<textarea name="bio" <?php if (isset($c['errors']['bio'])) {print 'class="error-field form-control"';} else {print 'class="form-control"';} ?>><?php print $c['values']['bio']; ?></textarea>
							<?php if (isset($c['messages']['bio'])) print $c['messages']['bio'] ?>
						</label>
					</div>
					<div class="checkbox-group">
						<input type="checkbox" name="consent">
						<label style="color: lightgray;">
							С контрактом ознакомлен(а)
							<?php if (isset($c['messages']['consent'])) print $c['messages']['consent'] ?>
						</label>
					</div>
					<div class="form-group">
						<button class="submit-button" type="submit"><h3>Сохранить</h3></button>
						<button class="submit-button" id="logout"><h3>Выйти</h3></button>
					</div>
				</form>
			</div>
		</section>
	</footer>

	<script>
		let currentSlide = 0;
		let slidesPerView = 1;
		const $slides = $('.slide');
		const totalSlides = $slides.length;
		const $galleryTrack = $('#galleryTrack');

		function updateGallery() {
			const slideWidth = 100 / slidesPerView;
			$galleryTrack.css('transform', `translateX(${-currentSlide * slideWidth}%)`);

			$('#prevBtn').prop('disabled', currentSlide <= 0);
			$('#nextBtn').prop('disabled', currentSlide >= totalSlides - slidesPerView);
		}

		$(document).ready(function () {
			updateGallery();

			$('#nextBtn').on('click', function () {
				if (currentSlide < totalSlides - slidesPerView) {
					currentSlide++;
					updateGallery();
				}
			});

			$('#prevBtn').on('click', function () {
				if (currentSlide > 0) {
					currentSlide--;
					updateGallery();
				}
			});

			$('.contact-us').on('click', function () {
				feedbackForm.scrollIntoView();
			});

			$('#tarrifsBtn').on('click', function () {
				$('.gallery-wrapper')[0].scrollIntoView();
			});

			const navToggle = document.getElementById('navToggle');
			const navMenu = document.getElementById('navMenu');

			navToggle.addEventListener('click', function () {
				navMenu.classList.toggle('active');
				navToggle.classList.toggle('active');
			});

			document.getElementById('logout').addEventListener('click', function() {
				fetch('form', {
					method: 'POST',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
					body: 'action=clicked'
				})
				.then(response => response.text())
				.then(data => alert(data));
			});

			<?php if ($c['congrats'] !== "") print 'alert("Данные были успешно сохранены");'; ?>
		});
	</script>
</body>
</html>