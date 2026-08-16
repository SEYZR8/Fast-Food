<?php
include 'function.php';
headers();
banners();
echo '<link rel="stylesheet" href="css/premium.css"><link rel="stylesheet" href="css/lavash-n1.css"><link rel="stylesheet" href="css/cart-checkout.css">';
echo '<section class="ln1-hero3d"><div class="ln1-orb ln1-orb-a"></div><div class="ln1-orb ln1-orb-b"></div><div class="ln1-hero-content"><span class="ln1-pill">🔥 LAVASH N1 • SAMARQAND</span><h1>Issiq. Mazali. <span>Lavash N1.</span></h1><p>Yangi tayyorlanadi, tez yetkaziladi. Bir bosishda buyurtma bering.</p><div class="ln1-hero-actions"><a href="#ln1-menu" class="ln1-main-btn">🌯 Menyuni ko‘rish</a><a href="#ln1-simple-menu" class="ln1-ghost-btn">🛒 Savatga yig‘ish</a></div><div class="ln1-trust"><span>⚡ Tez dastavka</span><span>💳 Naqd / Karta</span><span>📍 Samarqand</span></div></div><div class="ln1-food-3d" aria-hidden="true"><div class="ln1-food-ring"></div><div class="ln1-food-emoji">🌯</div></div></section>';
echo '<section class="ln1-offer-pro"><div><small>BUGUNGI AKSIYA</small><h2>Lavash + kartoshka + ichimlik</h2><p>Birga buyurtma qiling — tez va issiq dastavka.</p></div><a href="#ln1-simple-menu">Aksiyani ko‘rish →</a></section>';
echo '<section class="ln1-menu-pro" id="ln1-menu"><div class="ln1-section-head"><div><span>🌯 LAVASH N1 MENYU</span><h2>Bugun sinab ko‘ring</h2><p>Mahsulotni savatga yig‘ing va hammasini bir martada zakaz qiling.</p></div></div></section>';
echo '<section class="ln1-simple-menu" id="ln1-simple-menu"><div id="ln1SimpleMenu"></div></section>';
search_result();
loadtabel();
dishes();
introduction();
special_menu();
order_contact();
footers();
?>
<script>
(function(){
 document.title='Lavash N1 • Samarqand';
 document.querySelectorAll('#adminLink,a[href*="admin"],a[href*="courier"]').forEach(e=>e.remove());
 document.querySelectorAll('.review,.reviews,.testimonial,.testimonials,#review,#reviews,.chat,.chat-box,.chat-widget,.floating-chat,.messages-widget,.msg-modal,.review-section').forEach(e=>e.remove());
 const phone='+998998305603'; const bar=document.createElement('div'); bar.className='ff-float'; bar.innerHTML='<a href="tel:'+phone+'" aria-label="Qo‘ng‘iroq">☎</a><a href="customer.php" aria-label="Mijoz kabineti">👤</a>'; document.body.appendChild(bar);
 const logo=document.querySelector('header .logo'); if(logo) logo.innerHTML='<i class="fas fa-utensils"></i> Lavash N1';
 const map={'#home':'Bosh sahifa','#dishes':'Menyu','#about':'Biz haqimizda','#menu':'Aksiya','#order':'Dastavka'};
 document.querySelectorAll('header .navbar a').forEach(a=>{if(map[a.getAttribute('href')])a.textContent=map[a.getAttribute('href')];});
 const form=document.getElementById('orderForm'); if(form){if(!form.querySelector('.ff-pay')){const p=document.createElement('div');p.className='ff-pay';p.innerHTML='<strong>💳 To‘lov usuli</strong><br><label><input type="radio" name="payment_method" value="cash" checked> Naqd</label> <label><input type="radio" name="payment_method" value="card"> Karta</label>';form.appendChild(p);}const n=form.querySelector('[name="number"]');if(n){n.type='tel';n.inputMode='tel';n.placeholder='+998 99 830 56 03';}const a=form.querySelector('[name="address"]');if(a)a.placeholder='Samarqand, Chelak 78-Maktab oldi yoki aniq manzil';}
 document.querySelectorAll('.box,.dish,.menu-item,.single-dish,.card,.ff-hero-card').forEach(el=>{el.classList.add('ff-3d');el.addEventListener('pointermove',function(e){if(!matchMedia('(pointer:fine)').matches)return;const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform='perspective(900px) rotateX('+(-y*3)+'deg) rotateY('+(x*4)+'deg) translateY(-5px)';});el.addEventListener('pointerleave',()=>el.style.transform='');});
 const boxes=document.querySelectorAll('.footer .box');
 if(boxes.length>=1){boxes[0].innerHTML='<h3>Manzil</h3><a href="#order">Samarqand Chelak 78-Maktab oldi</a><a href="https://maps.google.com/?q=Samarqand+Chelak+78-Maktab+oldi" target="_blank" rel="noopener">📍 Xarita</a>';}
 if(boxes.length>=2){boxes[1].innerHTML='<h3>Tezkor havolalar</h3><a href="#home">Bosh sahifa</a><a href="#ln1-simple-menu">Taomlar</a><a href="#about">Biz haqimizda</a><a href="#menu">Menyu</a><a href="#review">Fikrlar</a><a href="#order">Buyurtma</a>';}
 if(boxes.length>=3){boxes[2].innerHTML='<h3>Aloqa ma\'lumotlari</h3><a href="tel:+998998305603">+998998305603</a><a href="tel:+998998305603">+998998305603</a><a href="mailto:pubggangster71@gmail.com">pubggangster71@gmail.com</a><a href="#order">Dastavka berish</a><a href="https://maps.google.com/?q=Samarqand+Chelak+78-Maktab+oldi" target="_blank" rel="noopener">📍 Manzilni ochish</a>';}
})();
</script>
<script src="cart_checkout.js"></script>
