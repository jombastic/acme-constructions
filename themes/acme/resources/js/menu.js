const menuButton = document.querySelector('#menuButton')
const menuList = document.querySelector('#menuList')

function toggleMenu() {
    menuList.classList.toggle('nav-menu--is-open')
}

menuButton.addEventListener('click', toggleMenu)
