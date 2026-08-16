<?php
include 'function.php';
headers();
banners();
echo '<link rel="stylesheet" href="css/premium.css">';
echo '<section class="ff-hero"><div class="ff-hero-card ff-3d"><span class="ff-kicker">🔥 Samarqand • Tez dastavka</span><h1>Issiq. Tez. Mazali.</h1><p>Sevimli fast food’ingizni bir necha bosishda buyurtma qiling. Mahsulotni tanlang, manzilni kiriting va Naqd yoki Karta orqali to‘lov usulini belgilang.</p><div class="ff-actions"><a class="ff-primary" href="#dishes">🍔 Menyuni ko‘rish</a><a class="ff-secondary" href="#order">🛵 Dastavka berish</a></div></div></section>';
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
 const admin=document.getElementById('adminLink'); if(admin) admin.remove();
 document.querySelectorAll('.review,.reviews,.testimonial,.testimonials,#review,#reviews,.chat,.chat-box,.chat-widget,.floating-chat,.messages-widget,.msg-modal,.review-section').forEach(e=>e.remove());
 const phone='+998998305603'; const bar=document.createElement('div'); bar.className='ff-float';
 bar.innerHTML='<a href="tel:'+phone+'" aria-label="Qo‘ng‘iroq">☎</a><a href="customer.php" aria-label="Mijoz kabineti">👤</a>'; document.body.appendChild(bar);
 document.querySelectorAll('.box,.dish,.menu-item,.single-dish,.card,.ff-hero-card').forEach(el=>{el.classList.add('ff-3d');el.addEventListener('pointermove',function(e){if(!matchMedia('(pointer:fine)').matches)return;const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform='perspective(900px) rotateX('+(-y*3)+'deg) rotateY('+(x*4)+'deg) translateY(-4px)';});el.addEventListener('pointerleave',function(){el.style.transform='';});});
 const form=document.getElementById('orderForm');
 if(form){const payment=form.querySelector('.ff-pay')||(()=>{const p=document.createElement('div');p.className='ff-pay';p.innerHTML='<strong>💳 To‘lov usuli</strong><br><label><input type="radio" name="payment_method" value="cash" checked> Naqd</label> <label><input type="radio" name="payment_method" value="card"> Karta</label>';form.appendChild(p);return p;})();const phoneInput=form.querySelector('[name="number"]');if(phoneInput){phoneInput.type='tel';phoneInput.inputMode='tel';phoneInput.placeholder='+998 99 830 56 03';}const addr=form.querySelector('[name="address"]');if(addr)addr.placeholder='Samarqand, Chelak 78 oldi yoki aniq yetkazish manzili';}
})();
</script>