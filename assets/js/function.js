document.addEventListener('DOMContentLoaded', function () {
    initAjaxForms();
    exelFormAlert();
});

function initAjaxForms() {
    const forms = document.querySelectorAll(".ajax-form");

    forms.forEach((form) => {
        form.addEventListener("submit", function (event) {
            event.preventDefault(); // Останавливаем стандартное поведение отправки формы

            // Собираем данные формы
            const formData = new FormData(form);

            // Отправляем данные с помощью fetch
            fetch(form.action, {
                method: form.method || "POST", // Метод запроса
                body: formData, // Данные формы
            })
            .then((response) => {
                if (response.ok) {
                    return response.json(); // Предполагаем, что ответ в формате JSON
                }
                throw new Error("Ошибка сети");
            })
            .then((data) => {
                if (data.result === 'success') {
                    form.dispatchEvent(new CustomEvent('success_send'));
                } else {
                    form.dispatchEvent(new CustomEvent('error_send'));
                }

                form.reset();
            })
            .catch((error) => {
                // Обработка ошибок
                console.error("Ошибка отправки:", error);
                alert('В результате обработки возникла ошибка!');
            });
        });
    });
}

function exelFormAlert() {
    let form = document.querySelector('.exel-form');

    if (form) {
        form.addEventListener('submit', function () {
            alert('Началась обработка данных. Пожайлуйста не закрывайте страницу!');
        });
        form.addEventListener('success_send', function () {
            alert('Обработка файла завершилась успешно!');
        });
        form.addEventListener('error_send', function () {
            alert('В результате обработки возникла ошибка!');
        });
    }
}