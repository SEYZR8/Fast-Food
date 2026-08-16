<?php
include 'function.php';
headers();
banners();
echo '<link rel="stylesheet" href="css/premium.css"><link rel="stylesheet" href="css/lavash-n1.css"><link rel="stylesheet" href="css/cart-checkout.css">';
echo '<section class="ln1-hero3d"><div class="ln1-orb ln1-orb-a"></div><div class="ln1-orb ln1-orb-b"></div><div class="ln1-hero-content"><span class="ln1-pill">🔥 LAVASH N1 • SAMARQAND</span><h1>Issiq. Mazali. <span>Lavash N1.</span></h1><p>Avval mahsulotlarni savatga yig‘ing. Keyin bitta oddiy formada ism, telefon, xabar va manzilni yuborib, hammasini birdan buyurtma qiling.</p><div class="ln1-hero-actions"><a href="#ln1SimpleMenu" class="ln1-main-btn">🌯 Menyuni ko‘rish</a><a href="#ln1SimpleMenu" class="ln1-ghost-btn">🛒 Savatga yig‘ish</a></div><div class="ln1-trust"><span>⚡ Tez dastavka</span><span>💳 Naqd / Karta</span><span>📍 Lokatsiya bir bosishda</span></div></div><div class="ln1-food-3d" aria-hidden="true"><div class="ln1-food-ring"></div><div class="ln1-food-emoji">🌯</div></div></section>';
echo '<section class="ln1-offer-pro"><div><small>BUGUNGI AKSIYA</small><h2>Bir nechta mahsulotni bitta zakazda oling</h2><p>Savatga yig‘ing va oxirida faqat bir marta ma’lumot kiriting.</p></div><a href="#ln1SimpleMenu">Menyuni ochish →</a></section>';
echo '<div id="ln1SimpleMenu" class="ln1-simple-menu"></div>';
search_result();
loadtabel();
dishes();
introduction();
special_menu();
order_contact();
footers();
?>
<script src="cart_checkout.js"></script>
<script>
(function(){
 document.title='Lavash N1 • Samarqand';
 document.querySelectorAll('#adminLink,a[href*="admin"],a[href*="courier"]').forEach(e=>e.remove());
 document.querySelectorAll('.review,.reviews,.testimonial,.testimonials,#review,#reviews,.chat,.chat-box,.chat-widget,.floating-chat,.messages-widget,.msg-modal,.review-section').forEach(e=>e.remove());
 const phone='+998998305603'; const bar=document.createElement('div'); bar.className='ff-float'; bar.innerHTML='<a href="tel:'+phone+'" aria-label="Qo‘ng‘iroq">☎</a><a href="javascript:void(0)" id="ln1CartFloat" aria-label="Savat">🛒</a><a href="customer.php" aria-label="Mijoz kabineti">👤</a>'; document.body.appendChild(bar);
 document.getElementById('ln1CartFloat').onclick=()=>window.LavashCart&&window.LavashCart.open();
 const logo=document.querySelector('header .logo'); if(logo) logo.innerHTML='<i class="fas fa-utensils"></i> Lavash N1';
 const map={'#home':'Bosh sahifa','#dishes':'Menyu','#about':'Biz haqimizda','#menu':'Aksiya','#order':'Dastavka'};
 document.querySelectorAll('header .navbar a').forEach(a=>{const h=a.getAttribute('href');if(map[h])a.textContent=map[h];});
 document.querySelectorAll('.box,.dish,.menu-item,.single-dish,.card,.ff-hero-card,.ln1-product-card,.ln1-food-card').forEach(el=>{el.classList.add('ff-3d');el.addEventListener('pointermove',function(e){if(!matchMedia('(pointer:fine)').matches)return;const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform='perspective(900px) rotateX('+(-y*3)+'deg) rotateY('+(x*4)+'deg) translateY(-5px)';});el.addEventListener('pointerleave',()=>el.style.transform='');});
})();
</script>