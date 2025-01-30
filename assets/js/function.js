document.addEventListener('DOMContentLoaded', function () {
    initAjaxForms();
    exelFormAlert();
    setPaginationListeners();
    initTooltips();
    setSortListeners();
    rowsClicker();
    setOnPageListener();
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

    if (form && !form.classList.contains('no-alerts')) {
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

function setPaginationListeners() {
    let paginationLinks = document.querySelectorAll(".page-link");

    if (paginationLinks) {
        paginationLinks.forEach((paginationLink) => {
            paginationLink.addEventListener("click", paginationClicker);
        });
    }
}

function paginationClicker(event) {
    event.preventDefault();
    event.stopPropagation();
    event.cancelBubble = true;
    let paginationLinkData = this.getAttribute("href");
    let tableUrl = '';
    if (paginationLinkData.includes("ajax=true")) {
        tableUrl = paginationLinkData;
    } else {
        tableUrl = `${paginationLinkData}&ajax=true`;
    }
    getTableData(tableUrl).then(res => {
        // getDataFilter();
    });
}

async function getTableData(tableUrl, table = null, selectRowId = null) {
    let result = await fetch(tableUrl).then((result) => result);
    let data = await result.text();
    const ajaxTable = document.querySelector(".ajax-table");

    let selectedBefore = null;
    if (table) {
        selectedBefore = table.querySelector('.user-table-row.is-selected');
        let tableSortIcons = table.querySelectorAll(".sort-icon"),
            tablePaginationLinks = table.querySelectorAll(".page-link");

        clearOldListeners(tableSortIcons, tablePaginationLinks, true);
        table.innerHTML = data;
    }

    // иначе если верхняя
    else if (ajaxTable) {
        selectedBefore = ajaxTable.querySelector('.user-table-row.is-selected');
        clearOldListeners();
        ajaxTable.innerHTML = data;
    }

    setPaginationListeners();
    setSortListeners();


    let url;
    try {
        url = new URL(tableUrl);
    } catch (e) {
        url = new URL(`${location.protocol}//${location.host}${tableUrl}`);
    }
    url.searchParams.delete('ajax');
    history.pushState("", "", url);

    document.dispatchEvent(new CustomEvent('getTableData_loaded', {
        detail: {
            table: ajaxTable,
            selectedBefore: selectedBefore,
            selectRowId: selectRowId,
        }
    }));
}

function clearOldListeners(tableSortIcons, tablePaginationLinks, skipAllRows) {
    let sortIcons = document.querySelectorAll(".sort-icon");
    let paginationLinks = document.querySelectorAll(".page-link");
    if (tableSortIcons || sortIcons) {
        (tableSortIcons && tableSortIcons.length ? tableSortIcons : sortIcons).forEach((sortIcon) => {
            sortIcon.removeEventListener("click", sortClicker);
        });
    }
    if (tablePaginationLinks || paginationLinks) {
        (tablePaginationLinks && tablePaginationLinks.length ? tablePaginationLinks : paginationLinks).forEach((paginationLink) => {
            paginationLink.removeEventListener("click", paginationClicker);
        });
    }
}

function initTooltips(stepClassSelector = null) {
    let tooltipTriggerList;
    if (stepClassSelector !== null) {
        tooltipTriggerList = [].slice.call(document.querySelectorAll(`${stepClassSelector} [data-bs-toggle="tooltip"]`));
    } else {
        tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    }
    if (tooltipTriggerList !== null && tooltipTriggerList !== undefined) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl);
        });
    }
}

function sortClicker(event) {
    event.preventDefault();
    let hrefData = this.getAttribute("href"),
        hrefDataArr = hrefData.split("&"),
        tableUrl = '',
        table = this.closest('#step_data');
    if (hrefDataArr.includes("ajax=true")) {
        tableUrl = hrefData;
    } else {
        tableUrl = `${hrefData}&ajax=true`;
    }
    getTableData(tableUrl, table).then(r => {
        // getDataFilter();
    });
}

function setSortListeners() {
    let sortIcons = document.querySelectorAll(".sort-icon");
    if (sortIcons) {
        sortIcons.forEach((sortIcon) => {
            sortIcon.addEventListener("click", sortClicker);
        });
    }
}

function rowsClicker() {
    let table = document.querySelector('.users-table');
    if (!table) {
        return;
    }

    table.addEventListener('click', function (evt) {
        if (evt.target.closest('.user-table-row')) {
            let currentSelectedRow = table.querySelector('.user-table-row.is-selected');
            currentSelectedRow.classList.remove('is-selected');

            let el = evt.target.closest('.user-table-row');
            if (!el.classList.contains('is-selected')) {
                el.classList.add('is-selected')
            }
        }
    });
}

function setOnPageListener() {
    let selectOnPage = document.querySelector("#on_page_selector");
    let table = document.querySelector('.users-table');

    if (!table) {
        return;
    }

    if (selectOnPage !== null) {
        let sectionName = document.querySelector("#on_page_selector").getAttribute("data-path");

        let dataType = document.querySelector("#on_page_selector").getAttribute("data-type");
        if (table) {
            table.getData = async () => {
                return getTableData(getTableUrl(selectOnPage.dataset.path), null);
            };
        }
    }

    let getTableUrl = function (sectionName, cleanParams = false) {
        let urlParams = cleanParams ? new URLSearchParams()
            : new URLSearchParams(window.location.search);
        let tableUrl = '';
        urlParams.set('ajax', true);
        if (sectionName !== null) {
            tableUrl = `${location.protocol}//${location.host}${sectionName}?${urlParams.toString()}`;
        }
        return tableUrl;
    };

    selectOnPage.addEventListener("change", function () {
        let selectValue = selectOnPage.value;

        let sectionName = document.querySelector("#on_page_selector").getAttribute("data-path");
        let dataSearch = document.querySelector("#on_page_selector").getAttribute("data-search");
        let dataExtendedSearch = document.querySelector("#on_page_selector").getAttribute("data-extended-search");

        let dataType = document.querySelector("#on_page_selector").getAttribute("data-type");

        let params = new URLSearchParams(window.location.search);
        params.set('on_page', selectValue);
        params.set('ajax', true);
        // удаляем значение пагинации, чтобы не получилось так, что выбрана страница которой не существует при текущем on_page
        params.delete('page');
        //let tableUrl = `${location.protocol}//${location.host}${sectionName}?on_page=${selectValue}&ajax=true`;
        if (dataSearch !== null) {
            //tableUrl += '&search=' + dataSearch;
            params.set('search', dataSearch);
        }
        if (dataExtendedSearch !== null) {
            //tableUrl += '&' + dataExtendedSearch;
            params.set('', dataExtendedSearch);
        }
        if (dataType !== null) {
            //tableUrl += '&type=' + dataType;
            params.set('type', dataType);
        }

        let tableUrl = `${location.protocol}//${location.host}${sectionName}?${params.toString()}`;
        getTableData(tableUrl).then(r => {
            // getDataFilter();
        });
    });
}