<?php
include 'function.php';
headers();
banners();
search_result();
loadtabel();
dishes();
// Keep the existing ordering flow but remove the old review/chat promotional widgets.
introduction();
special_menu();
order_contact();
footers();
?>
<style>
:root{--ff-orange:#ff5a00;--ff-orange2:#ff8a00;--ff-ink:#111827;--ff-glass:rgba(255,255,255,.78);--ff-shadow:0 24px 70px rgba(17,24,39,.14)}
html{scroll-behavior:smooth}body{background:radial-gradient(circle at 15% 10%,#fff1e8 0,transparent 30%),radial-gradient(circle at 85% 30%,#fff7df 0,transparent 28%),#fff;color:var(--ff-ink);overflow-x:hidden}body:before{content:"";position:fixed;inset:-30%;z-index:-1;pointer-events:none;background:conic-gradient(from 180deg at 50% 50%,rgba(255,90,0,.07),rgba(255,200,80,.06),rgba(255,90,0,.03),rgba(255,90,0,.07));animation:ffspin 22s linear infinite}@keyframes ffspin{to{transform:rotate(360deg)}}
.box,.card,.dish,.menu-item,.single-dish,.modal-content{border-radius:24px!important;box-shadow:var(--ff-shadow)!important;border:1px solid rgba(255,255,255,.8)!important;background:var(--ff-glass)!important;backdrop-filter:blur(14px)}
.btn,button,input[type=submit]{border-radius:14px!important;transition:transform .2s ease,box-shadow .2s ease,filter .2s ease}.btn:hover,button:hover,input[type=submit]:hover{transform:translateY(-2px);filter:saturate(1.08);box-shadow:0 14px 30px rgba(255,90,0,.18)}
body:after{content:"🍔";position:fixed;right:3vw;top:18vh;font-size:clamp(70px,11vw,150px);z-index:-1;filter:drop-shadow(0 30px 24px rgba(0,0,0,.18));animation:fffloat 5s ease-in-out infinite;opacity:.13;pointer-events:none}@keyframes fffloat{0%,100%{transform:translate3d(0,0,0) rotate(-6deg)}50%{transform:translate3d(-12px,-22px,0) rotate(7deg)}}
.review,.reviews,.testimonial,.testimonials,#review,#reviews,.chat,.chat-box,.chat-widget,.floating-chat,.messages-widget,.msg-modal,.review-section{display:none!important}
.ff-premium-bar{position:fixed;left:18px;bottom:18px;z-index:9998;display:flex;gap:10px;align-items:center}.ff-premium-bar a{display:inline-flex;align-items:center;gap:7px;text-decoration:none;padding:12px 16px;border-radius:999px;font-weight:900;color:#fff;background:linear-gradient(135deg,var(--ff-orange),var(--ff-orange2));box-shadow:0 16px 35px rgba(255,90,0,.28)}.ff-premium-bar a.secondary{background:#111827;box-shadow:0 14px 30px rgba(17,24,39,.2)}
.ff-3d{position:relative;transform-style:preserve-3d;transition:transform .35s ease;will-change:transform}.ff-3d:hover{transform:perspective(900px) rotateX(2deg) rotateY(-3deg) translateY(-5px)}
.ff-pay{margin-top:14px;padding:14px;border-radius:18px;background:linear-gradient(135deg,#fff,#fff7ef);border:1px solid #ffe0c2}.ff-pay label{display:inline-flex;gap:8px;align-items:center;margin-right:18px;font-weight:800;cursor:pointer}.ff-pay input{accent-color:var(--ff-orange)}
@media(max-width:700px){.ff-premium-bar{left:10px;right:10px;bottom:10px}.ff-premium-bar a{flex:1;justify-content:center;padding:10px 12px}.ff-premium-bar a.secondary{display:none}}
</style>
<script>
(function(){
 const phone='+998 99 830 56 03',address='Samarqand, Chelak 78 oldi';
 document.querySelectorAll('.footer .box').forEach(function(box){const title=(box.querySelector('h3')||{}).textContent||'';if(title.toLowerCase().includes('contact'))box.innerHTML='<h3>Aloqa</h3><a href="tel:+998998305603">'+phone+'</a><a href="#order">🛵 Dastavka berish</a><a href="#">📍 '+address+'</a>';});
 document.querySelectorAll('.dish,.menu-item,.single-dish,.card,.box').forEach(el=>el.classList.add('ff-3d'));
 const form=document.getElementById('orderForm');
 if(form){const phoneInput=form.querySelector('[name="number"]'),qty=form.querySelector('[name="quantity"]')||form.querySelector('[name="qty"]'),name=form.querySelector('[name="name"]'),addr=form.querySelector('[name="address"]');if(phoneInput){phoneInput.type='tel';phoneInput.inputMode='tel';phoneInput.placeholder=phone;phoneInput.required=true}if(qty){qty.min='1';qty.value=qty.value||'1';qty.required=true}if(name)name.required=true;if(addr){addr.required=true;addr.placeholder='Yetkazib berish manzilingiz'}
 const pay=document.createElement('div');pay.className='ff-pay';pay.innerHTML='<strong>💳 To‘lov usuli</strong><br><label><input type="radio" name="payment_method" value="cash" checked> Naqd</label><label><input type="radio" name="payment_method" value="card"> Karta</label>';form.appendChild(pay);
 form.addEventListener('submit',function(e){if(!name?.value.trim()||!phoneInput?.value.trim()||!addr?.value.trim()||parseInt(qty?.value||0,10)<1){e.preventDefault();const box=document.getElementById('order_msg');if(box)box.innerHTML='<div class="alert alert-warning">Iltimos, ism, telefon, manzil va miqdorni to‘ldiring.</div>';return}const btn=form.querySelector('input[type="submit"]');if(btn){btn.disabled=true;btn.value='Buyurtma yuborilmoqda...'}})}
 const wrap=document.createElement('div');wrap.className='ff-premium-bar';const call=document.createElement('a');call.href='tel:+998998305603';call.textContent='☎ '+phone;const account=document.createElement('a');account.className='secondary';account.href='customer.php';account.textContent='👤 Mijoz kabineti';wrap.append(call,account);document.body.appendChild(wrap);
 if(matchMedia('(pointer:fine)').matches){document.querySelectorAll('.ff-3d').forEach(el=>el.addEventListener('pointermove',e=>{const r=el.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;el.style.transform='perspective(900px) rotateX('+(-y*3)+'deg) rotateY('+(x*4)+'deg) translateY(-3px)'}));}
})();
</script>