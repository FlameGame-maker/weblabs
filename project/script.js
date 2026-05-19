let currentSlide = 0;
let slidesPerView = 1;
const $slides = $('.slide');
const totalSlides = $slides.length;
const $galleryTrack = $('#galleryTrack');

const STORAGE_KEY = 'feedbackFormData';
const FORM_SUBMIT_URL = 'https://api.slapform.com/gameflametest@gmail.com';
let isPopupOpen = false;


function updateGallery() {
	const slideWidth = 100 / slidesPerView;
	$galleryTrack.css('transform', `translateX(${-currentSlide * slideWidth}%)`);

	$('#prevBtn').prop('disabled', currentSlide <= 0);
	$('#nextBtn').prop('disabled', currentSlide >= totalSlides - slidesPerView);
}


function saveFormData() {
	const formData = {
		fullName: document.getElementById('fullName').value,
		email: document.getElementById('email').value,
		phone: document.getElementById('phone').value,
		organization: document.getElementById('organization').value,
		message: document.getElementById('message').value,
		privacyPolicy: document.getElementById('privacyPolicy').checked
	};

	localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
}

function loadFormData() {
	const savedData = localStorage.getItem(STORAGE_KEY);

	if (savedData) {
		try {
			const formData = JSON.parse(savedData);

			document.getElementById('fullName').value = formData.fullName || '';
			document.getElementById('email').value = formData.email || '';
			document.getElementById('phone').value = formData.phone || '';
			document.getElementById('organization').value = formData.organization || '';
			document.getElementById('message').value = formData.message || '';
			document.getElementById('privacyPolicy').checked = formData.privacyPolicy || false;
		} catch (e) {
			console.error('Ошибка при загрузке данных из LocalStorage:', e);
		}
	}
}

function clearFormData() {
	localStorage.removeItem(STORAGE_KEY);

	document.getElementById('fullName').value = '';
	document.getElementById('email').value = '';
	document.getElementById('phone').value = '';
	document.getElementById('organization').value = '';
	document.getElementById('message').value = '';
	document.getElementById('privacyPolicy').checked = false;
}

function submitForm() {
	const feedbackForm = document.getElementById('feedbackForm');
	if (!feedbackForm.checkValidity()) {
		feedbackForm.reportValidity();
		return;
	}

	const formData = {
		fullName: document.getElementById('fullName').value,
		email: document.getElementById('email').value,
		phone: document.getElementById('phone').value,
		organization: document.getElementById('organization').value,
		message: document.getElementById('message').value,
		privacyPolicy: document.getElementById('privacyPolicy').checked
	};

	const submitButton = document.getElementById('submitButton');
	submitButton.disabled = true;
	submitButton.innerHTML = 'Отправка...';

	const successMessage = document.getElementById('successMessage');
	const errorMessage = document.getElementById('errorMessage');
	successMessage.style.display = 'none';
	errorMessage.style.display = 'none';

	// Отправляем данные на сервер
	fetch(FORM_SUBMIT_URL, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
		},
		body: JSON.stringify(formData)
	})
		.then(response => {
			if (response.ok) {
				return response.json();
			} else {
				throw new Error('Ошибка сети');
			}
		})
		.then(data => {
			successMessage.style.display = 'block';
			errorMessage.style.display = 'none';

			clearFormData();
			closeForm();
		})
		.catch(error => {
			// Показываем сообщение об ошибке
			successMessage.style.display = 'none';
			errorMessage.style.display = 'block';

			console.error('Ошибка при отправке формы:', error);
		})
		.finally(() => {
			submitButton.disabled = false;
			submitButton.innerHTML = 'Отправить сообщение';
		});
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


	const feedbackForm = document.getElementById('feedbackForm');
	feedbackForm.addEventListener('submit', function (e) {
		e.preventDefault();
		submitForm();
	});
	feedbackForm.addEventListener('input', function () {
		saveFormData();
	});

	$('.contact-us').on('click', function () {
		feedbackForm.scrollIntoView();
	});

	$('#tarrifsBtn').on('click', function () {
		$('.gallery-wrapper')[0].scrollIntoView();
	});

	loadFormData();

	const navToggle = document.getElementById('navToggle');
	const navMenu = document.getElementById('navMenu');

	navToggle.addEventListener('click', function () {
		navMenu.classList.toggle('active');
		navToggle.classList.toggle('active');
	});
});