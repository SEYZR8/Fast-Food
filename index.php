<?php
include 'function.php';
headers();
banners();
search_result();
loadtabel();
dishes();
msgModals();
introduction();
special_menu();
review();
order_contact();
footers();
?>
<script>
(function(){
  const phone='+998 99 830 56 03', address='Samarqand, Chelak 78 oldi';
  document.querySelectorAll('.footer .box').forEach(function(box){const title=(box.querySelector('h3')||{}).textContent||'';if(title.toLowerCase().includes('contact'))box.innerHTML='<h3>Aloqa</h3><a href="tel:+998998305603">'+phone+'</a><a href="#order">🛵 Dastavka berish</a><a href="#">📍 '+address+'</a>';});
  const form=document.getElementById('orderForm');
  if(form){const phoneInput=form.querySelector('[name="number"]'),qty=form.querySelector('[name="quantity"]'),name=form.querySelector('[name="name"]'),addr=form.querySelector('[name="address"]');if(phoneInput){phoneInput.type='tel';phoneInput.inputMode='tel';phoneInput.placeholder=phone;phoneInput.required=true;}if(qty){qty.min='1';qty.value=qty.value||'1';qty.required=true;}if(name)name.required=true;if(addr){addr.required=true;addr.placeholder='Yetkazib berish manzilingiz';}form.addEventListener('submit',function(e){if(!name.value.trim()||!phoneInput.value.trim()||!addr.value.trim()||parseInt(qty.value||0,10)<1){e.preventDefault();const box=document.getElementById('order_msg');if(box)box.innerHTML='<div class="alert alert-warning">Iltimos, ism, telefon, manzil va miqdorni to‘ldiring.</div>';return;}const btn=form.querySelector('input[type="submit"]');if(btn){btn.disabled=true;btn.value='Yuborilmoqda...';}});}
  const wrap=document.createElement('div');wrap.style.cssText='position:fixed;right:16px;bottom:16px;z-index:9999;display:flex;gap:8px;flex-direction:column;align-items:flex-end';
  const call=document.createElement('a');call.href='tel:+998998305603';call.textContent='☎ '+phone;call.style.cssText='background:#ff5a00;color:#fff;padding:12px 16px;border-radius:999px;text-decoration:none;font-weight:800;box-shadow:0 8px 25px #0003';
  const adm=document.createElement('a');adm.href='admin_pro.php';adm.textContent='⚙ Admin';adm.style.cssText='background:#111827;color:#fff;padding:8px 12px;border-radius:999px;text-decoration:none;font-size:12px;font-weight:800;box-shadow:0 6px 18px #0002';wrap.append(call,adm);document.body.appendChild(wrap);
})();
</script>