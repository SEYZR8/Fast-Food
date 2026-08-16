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
  const phone = '+998 99 830 56 03';
  const address = 'Samarqand, Chelak 78 oldi';
  document.querySelectorAll('.footer .box').forEach(function(box){
    const title = (box.querySelector('h3') || {}).textContent || '';
    if(title.toLowerCase().includes('contact')){
      box.innerHTML = '<h3>Aloqa</h3><a href="tel:+998998305603">'+phone+'</a><a href="#order">Buyurtma berish</a><a href="#">'+address+'</a>';
    }
  });
  const orderForm = document.getElementById('orderForm');
  if(orderForm){
    const phoneInput = orderForm.querySelector('[name="number"]');
    const qtyInput = orderForm.querySelector('[name="quantity"]');
    const nameInput = orderForm.querySelector('[name="name"]');
    const addressInput = orderForm.querySelector('[name="address"]');
    if(phoneInput){ phoneInput.type='tel'; phoneInput.inputMode='tel'; phoneInput.placeholder=phone; phoneInput.required=true; }
    if(qtyInput){ qtyInput.min='1'; qtyInput.value=qtyInput.value || '1'; qtyInput.required=true; }
    if(nameInput) nameInput.required=true;
    if(addressInput){ addressInput.required=true; addressInput.placeholder='Yetkazib berish manzilingiz'; }
    orderForm.addEventListener('submit', function(e){
      if(!nameInput.value.trim() || !phoneInput.value.trim() || !addressInput.value.trim() || parseInt(qtyInput.value||0,10)<1){
        e.preventDefault();
        const box=document.getElementById('order_msg');
        if(box) box.innerHTML='<div class="alert alert-warning">Iltimos, ism, telefon, manzil va miqdorni to‘ldiring.</div>';
        return;
      }
      const btn=orderForm.querySelector('input[type="submit"]');
      if(btn){btn.disabled=true;btn.value='Yuborilmoqda...';}
    });
  }
  const admin=document.getElementById('adminLink');
  if(admin) admin.textContent='Admin panel';
  const floating=document.createElement('a');
  floating.href='tel:+998998305603';
  floating.textContent='☎ Buyurtma: '+phone;
  floating.style.cssText='position:fixed;right:16px;bottom:16px;z-index:9999;background:#ff2b8a;color:#fff;padding:12px 16px;border-radius:999px;text-decoration:none;font-weight:700;box-shadow:0 8px 25px #0003';
  document.body.appendChild(floating);
})();
</script>