import SlimSelect from 'slim-select'

document.addEventListener('DOMContentLoaded', function () {
   let container = document.querySelector('.analytics-container');

   if (!container) {
       return;
   }

    controlListeners(container);
});

function controlListeners(container) {
    let students = container.querySelector('#student-select');

    let slim = new SlimSelect({
        select: students,
        events: {
            afterChange: getData
        }
    });

    async function getData() {
        let tableContainer = container.querySelector('.analytics-table');
        let url = '/analytics/table_data';
        url += '?student='+slim.getSelected()[0];

        let res = await fetch(url).then((result) => result.text());

        if (res) {
            tableContainer.innerHTML = res
        }
    }
}