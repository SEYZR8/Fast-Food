(function(){
 const CART_KEY='lavash_n1_cart_v1';
 const menu=[
  {name:'Lavash N1 Classic',price:32000,emoji:'🌯'},
  {name:'N1 Burger',price:35000,emoji:'🍔'},
  {name:'N1 Combo',price:49000,emoji:'🍟'},
  {name:'Chicken Lavash',price:36000,emoji:'🌯'},
  {name:'Cola 1L',price:12000,emoji:'🥤'},
  {name:'Kartoshka',price:15000,emoji:'🍟'}
 ];
 let cart=JSON.parse(localStorage.getItem(CART_KEY)||'[]');
 const save=()=>localStorage.setItem(CART_KEY,JSON.stringify(cart));
 const money=n=>Number(n).toLocaleString('uz-UZ')+' so‘m';
 const esc=s=>String(s).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
 function render(){
  const root=document.getElementById('ln1SimpleMenu'); if(!root)return;
  root.innerHTML='<div class="ln1-menu-head"><div><span class="ln1-badge">🍽️ Tez tanlang</span><h2>Bugungi menyu</h2><p>Mahsulotni tanlang → savatga yig‘ing → bir marta buyurtma bering.</p></div><button class="ln1-cart-btn" id="ln1OpenCart">🛒 Savat <b id="ln1Count">0</b></button></div><div class="ln1-menu-grid">'+menu.map((p,i)=>'<article class="ln1-food-card"><div class="ln1-food-emoji">'+p.emoji+'</div><div class="ln1-food-body"><h3>'+esc(p.name)+'</h3><strong>'+money(p.price)+'</strong><button class="ln1-add" data-i="'+i+'">＋ Savatga</button></div></article>').join('')+'</div>';
  root.querySelectorAll('.ln1-add').forEach(b=>b.onclick=()=>{const p=menu[+b.dataset.i];const x=cart.find(v=>v.name===p.name);x?x.qty++:cart.push({...p,qty:1});save();updateCount();openCart();});
  root.querySelector('#ln1OpenCart').onclick=openCart; updateCount();
 }
 function updateCount(){const c=cart.reduce((s,x)=>s+x.qty,0),e=document.getElementById('ln1Count');if(e)e.textContent=c;}
 function ensureModal(){if(document.getElementById('ln1CartModal'))return;const d=document.createElement('div');d.id='ln1CartModal';d.innerHTML='<div class="ln1-modal-bg"></div><div class="ln1-modal"><button class="ln1-close" id="ln1Close">×</button><h2>🛒 Buyurtmangiz</h2><div id="ln1CartItems"></div><div class="ln1-total" id="ln1Total"></div><form id="ln1Checkout"><div class="ln1-form-grid"><input name="name" placeholder="Ismingiz" required><input name="phone" type="tel" inputmode="tel" placeholder="+998 99 123 45 67" required></div><textarea name="message" placeholder="Xabar yoki qo‘shimcha izoh (ixtiyoriy)"></textarea><div class="ln1-loc-row"><button type="button" id="ln1Locate">📍 Lokatsiyamni olish</button><span id="ln1LocText">Lokatsiya olinmagan</span></div><input type="hidden" name="lat"><input type="hidden" name="lng"><textarea name="address" placeholder="Yetkazib berish manzili" required></textarea><div class="ln1-pay"><b>To‘lov</b><label><input type="radio" name="payment_method" value="cash" checked> Naqd</label><label><input type="radio" name="payment_method" value="card"> Karta</label></div><button class="ln1-submit" type="submit">🚀 Hammasini birdan zakaz qilish</button><div id="ln1Status"></div></form></div>';
 document.body.appendChild(d);document.getElementById('ln1Close').onclick=closeCart;d.querySelector('.ln1-modal-bg').onclick=closeCart;
 document.getElementById('ln1Locate').onclick=()=>{if(!navigator.geolocation){document.getElementById('ln1LocText').textContent='Telefon lokatsiyani qo‘llamaydi';return}document.getElementById('ln1LocText').textContent='Aniqlanmoqda...';navigator.geolocation.getCurrentPosition(pos=>{const a=pos.coords.latitude,b=pos.coords.longitude;d.querySelector('[name=lat]').value=a;d.querySelector('[name=lng]').value=b;document.getElementById('ln1LocText').innerHTML='✅ Lokatsiya olindi';},()=>document.getElementById('ln1LocText').textContent='Lokatsiya olishga ruxsat berilmadi',{enableHighAccuracy:true,timeout:10000});};
 d.querySelector('#ln1Checkout').onsubmit=async e=>{e.preventDefault();if(!cart.length)return;const f=new FormData(e.target);f.append('items',JSON.stringify(cart));const s=document.getElementById('ln1Status'),btn=e.target.querySelector('.ln1-submit');btn.disabled=true;btn.textContent='Yuborilmoqda...';s.textContent='';try{const r=await fetch('CartOrder.php',{method:'POST',body:f});const data=await r.json();if(data.ok){s.innerHTML='<div class="ln1-success">✅ Buyurtma qabul qilindi! №'+esc(data.order_id)+'<br>Admin panelga tushdi.</div>';cart=[];save();updateCount();setTimeout(closeCart,2600);}else{s.textContent=data.message||'Xatolik';}}catch(err){s.textContent='Server bilan ulanishda xatolik';}btn.disabled=false;btn.textContent='🚀 Hammasini birdan zakaz qilish';};}
 function renderCart(){const c=document.getElementById('ln1CartItems');if(!c)return;if(!cart.length){c.innerHTML='<div class="ln1-empty">Savat bo‘sh. Avval menyudan mahsulot qo‘shing.</div>';document.getElementById('ln1Total').textContent='';return}c.innerHTML=cart.map((x,i)=>'<div class="ln1-cart-row"><div><b>'+esc(x.emoji+' '+x.name)+'</b><div class="ln1-row-price">'+money(x.price)+' × '+x.qty+'</div></div><div class="ln1-qty"><button data-a="dec" data-i="'+i+'">−</button><b>'+x.qty+'</b><button data-a="inc" data-i="'+i+'">＋</button><button data-a="del" data-i="'+i+'">🗑️</button></div></div>').join('');c.querySelectorAll('button').forEach(b=>b.onclick=()=>{const i=+b.dataset.i,a=b.dataset.a;if(a==='inc')cart[i].qty++;if(a==='dec'){cart[i].qty--;if(cart[i].qty<=0)cart.splice(i,1)}if(a==='del')cart.splice(i,1);save();renderCart();updateCount();});document.getElementById('ln1Total').textContent='Jami: '+money(cart.reduce((s,x)=>s+x.price*x.qty,0));}
 function openCart(){ensureModal();document.getElementById('ln1CartModal').classList.add('open');renderCart();}
 function closeCart(){const d=document.getElementById('ln1CartModal');if(d)d.classList.remove('open');}
 window.LavashCart={open:openCart};
 render();
})();