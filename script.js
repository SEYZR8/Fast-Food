let menu = document.querySelector('#menu-bars');
let navbar = document.querySelector('.navbar');
const header = document.querySelector('header');

if (menu && navbar) {
  menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
  };
}

const searchIcon = document.querySelector('#search-icon');
const searchForm = document.querySelector('#search-form');
const closeBtn = document.querySelector('.close_btn');

if (searchIcon && searchForm) {
  searchIcon.onclick = () => searchForm.classList.toggle('active');
}
if (closeBtn && searchForm) {
  closeBtn.onclick = () => searchForm.classList.remove('active');
}

if (typeof Swiper !== 'undefined') {
  new Swiper('.home-slider', {
    spaceBetween: 30,
    centeredSlides: true,
    autoplay: { delay: 7500, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    loop: true,
  });

  new Swiper('.review-slider', {
    spaceBetween: 20,
    centeredSlides: true,
    autoplay: { delay: 7500, disableOnInteraction: false },
    loop: true,
    breakpoints: {
      0: { slidesPerView: 1 },
      640: { slidesPerView: 2 },
      768: { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  });
}

// The old loader code was commented out, so the overlay stayed forever.
// Always hide it after the page is ready, with a short safety timeout.
function hideLoader() {
  const loader = document.querySelector('.loader-container');
  if (!loader) return;
  loader.classList.add('fade-out');
  window.setTimeout(() => {
    loader.remove();
  }, 700);
}

window.addEventListener('load', hideLoader);
window.setTimeout(hideLoader, 1800);

if (typeof $ !== 'undefined') {
  $('.dropdown').click(function (e) {
    e.preventDefault();
    const dropdownItem = $(this).attr('data-dropdown');
    if (dropdownItem) $(dropdownItem).toggle('active');
    $(this).siblings().children('.caret').toggleClass('rotate-180');
  });

  $('#color-gallery .color-item').click(function (e) {
    e.preventDefault();
    const lst = window.localStorage;
    const hsl = $(this).data('hsl');
    const color = $(this).data('color');
    const colorAlt = $(this).data('color-alt');
    const colorLighter = $(this).data('color-lighter');
    const colorSts = $(this).data('color-sts');
    lst.setItem('hsl', hsl);
    lst.setItem('theme', colorSts);
    theme();
    console.log(`lighter: ${colorLighter}, alt: ${colorAlt}, color: ${color}`);
  });
}

function theme() {
  if (typeof $ === 'undefined') return;
  const lst = window.localStorage;
  const hsl = lst.getItem('hsl');
  const currentTheme = lst.getItem('theme');
  if (hsl) $(':root').css('--hue-color', hsl);
  if (currentTheme === 'dark') {
    $(':root').css('--body-color', 'var(--bs-dark)');
    $(':root').css('--body-color-light', 'var(--bs-gray-dark)');
    $(':root').css('--text-color', 'white');
  } else {
    $(':root').css('--body-color', 'white');
    $(':root').css('--body-color-light', '#eee');
    $(':root').css('--text-color', 'var(--bs-dark)');
  }
}

theme();
