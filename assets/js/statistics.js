import SlimSelect from 'slim-select'

document.addEventListener('DOMContentLoaded', function () {
    let container = document.querySelector('.statistics-container');
    if (!container) {
        return;
    }

    let facultySelect = container.querySelector('#faculty-select');
    let universitySelect = container.querySelector('#university-select');
    let start = container.querySelector('[name="date_start"]');
    let end = container.querySelector('[name="date_end"]');
    let stage = container.querySelector('[name="stage"]');
    let flow = container.querySelector('[name="flow"]');

    let facultySlim = new SlimSelect({
        select: facultySelect,
    });
    let universitySlim = new SlimSelect({
        select: universitySelect,
    });

    let tableContainer = container.querySelector('.statistics-table');

    let button = container.querySelector('.statistic-button');
    button.addEventListener('click', async function (){
        let url = button.dataset.url;
        url = url + '?faculty=' + facultySlim.getSelected().join() +
            '&university=' + universitySlim.getSelected().join() +
            '&date_start=' + start.value +
            '&date_end=' + end.value +
            '&stage=' + stage.value +
            '&flow=' + flow.value;

        let res = await fetch(url).then((result) => result.text());

        if (res) {
            tableContainer.innerHTML = res
        }
    });
});