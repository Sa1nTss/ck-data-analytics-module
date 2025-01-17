import Cookies from "js-cookie";

document.addEventListener("DOMContentLoaded", function (event) {
    // скрытие боковой панели ↓
    const hideBtn = document.querySelector('.hide-btn');
    const asidePanel = document.querySelector('.aside-panel');
    const pageWrapper = document.querySelector('.page-wrapper');
    const navLinkWords = document.querySelectorAll('.aside-panel .nav-link-title');
    let asideHiddenValue = Cookies.get('aside_hidden');


    // переводим строку из куки в булевое значение
    if (asideHiddenValue === 'true') {
        asideHiddenValue = true;
    } else if (asideHiddenValue === 'false') {
        asideHiddenValue = false;
    } else {
        Cookies.set('aside_hidden', 'false', {expires: 7});
        asideHiddenValue = false;
    }

    // if(asideClosedProgram && asideOpenProgram){
    //     hideBtn.addEventListener('click', ()=>{
    //         asideClosedProgram.classList.toggle('d-none')
    //         asideOpenProgram.classList.toggle('d-none')
    //     })
    // }
    if (hideBtn && asidePanel && pageWrapper && navLinkWords) {
        // скрываем панель сразу, если кука true
        if (asideHiddenValue === true) {
            navLinkWords.forEach(navLinkWord => {
                navLinkWord.classList.add('nav-link--hidden');
            })
            asidePanel.classList.add('aside--hidden');
            hideBtn.classList.add('hide-btn--hidden');
            pageWrapper.classList.add('page-wrapper--full');
        }

        hideBtn.addEventListener('click', function () {
            navLinkWords.forEach(navLinkWord => {
                navLinkWord.classList.toggle('nav-link--hidden');
            })
            asidePanel.classList.toggle('aside--hidden');
            hideBtn.classList.toggle('hide-btn--hidden');
            pageWrapper.classList.toggle('page-wrapper--full');
            asideHiddenValue = !asideHiddenValue;
            Cookies.set('aside_hidden', asideHiddenValue, {expires: 7})
        })
    }
    // скрытие боковой панели  ↑
    const obj = document.querySelectorAll('#navbar-menu > ul > li > div > div > div > a.dropdown-item');
    if (obj) {
        obj.forEach((element) => {
            let parent = element.parentElement.parentElement.parentElement.parentElement;
            if (element.className.includes('active')) {
                let link = parent.firstElementChild;
                let drop = parent.lastElementChild;
                link.setAttribute('aria-expanded', 'true');
                link.classList.add('show');
                drop.setAttribute('data-bs-popper', 'none');
                drop.classList.add('show');
            }
        });
    }

})
