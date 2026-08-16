(() => {
  'use strict';

  document.documentElement.lang = 'uz';

  const replacements = new Map([
    ['resto.', 'Fast Food'],
    ['home', 'Bosh sahifa'],
    ['dishes', 'Taomlar'],
    ['about', 'Biz haqimizda'],
    ['menu', 'Menyu'],
    ['review', 'Fikrlar'],
    ['order', 'Buyurtma'],
    ['Admin Panel', 'Admin panel'],
    ['sign in', 'Kirish'],
    ['sign up', "Ro'yxatdan o'tish"],
    ['logout', 'Chiqish'],
    ['profile', 'Profil'],
    ['settings', 'Sozlamalar'],
    ['search', 'Qidirish'],
    ['search...', 'Qidirish...'],
    ['your search data', 'Qidiruv natijalari'],
    ['search dishes', 'Taomlarni qidirish'],
    ['our dishes', 'Bizning taomlarimiz'],
    ['popular dishes', 'Mashhur taomlar'],
    ['about us', 'Biz haqimizda'],
    ['why choose us?', 'Nega bizni tanlash kerak?'],
    ['best food in the country', 'Eng mazali taomlar'],
    ['free delivery', 'Bepul yetkazib berish'],
    ['easy payments', "Qulay to'lov"],
    ['24/7 service', '24/7 xizmat'],
    ['learn more', 'Batafsil'],
    ['our menu', 'Bizning menyu'],
    ['today`s speciality', 'Bugungi maxsus taomlar'],
    ['today’s speciality', 'Bugungi maxsus taomlar'],
    ['customer review', 'Mijozlar fikri'],
    ['what they say', 'Ular nima deydi'],
    ['order now', 'Hozir buyurtma bering'],
    ['fast and free', 'Tez va qulay'],
    ['your name', 'Ismingiz'],
    ['your number', 'Telefon raqamingiz'],
    ['your order', 'Buyurtmangiz'],
    ['additional food', "Qo'shimcha taom"],
    ['how musch', 'Miqdori'],
    ['date and time', 'Sana va vaqt'],
    ['your address', 'Manzilingiz'],
    ['your message', 'Xabaringiz'],
    ['enter your name', 'Ismingizni kiriting'],
    ['enter your number', 'Telefon raqamingizni kiriting'],
    ['enter food name', 'Taom nomini kiriting'],
    ['extra with food', "Qo'shimcha ma'lumot"],
    ['how many orders', 'Buyurtmalar soni'],
    ['enter your address', 'Manzilingizni kiriting'],
    ['enter your message', 'Xabaringizni kiriting'],
    ['buying', 'Sotib olish'],
    ['grand total', 'Jami summa'],
    ['image', 'Rasm'],
    ['title', 'Nomi'],
    ['qty', 'Soni'],
    ['prize', 'Narxi'],
    ['total prize', 'Jami narx'],
    ['delete all', "Hammasini o'chirish"],
    ['locations', 'Manzillar'],
    ['quick links', "Tezkor havolalar"],
    ['contact info', "Aloqa ma'lumotlari"],
    ['india', 'Hindiston'],
    ['japan', 'Yaponiya'],
    ['russia', 'Rossiya'],
    ['france', 'Fransiya'],
    ['please wait....', 'Iltimos, kuting...'],
    ['please select some cart', 'Savatchaga mahsulot tanlang'],
    ['please login first', 'Avval tizimga kiring'],
    ['thanks for shopping', 'Xaridingiz uchun rahmat!'],
    ['serve is slow', 'Server javob berishi sekinlashdi'],
    ['sell', 'Sotuv'],
    ['stock', 'Ombor'],
    ['cash in', 'Kirim'],
    ['cash out', 'Chiqim'],
    ['refund', 'Qaytarish'],
    ['return (refund)', 'Qaytarish'],
    ['debit (cash in)', 'Kirim'],
    ['credit (cash out)', 'Chiqim'],
    ['find', 'Topish'],
    ['submit', 'Saqlash'],
    ['date', 'Sana'],
    ['status', 'Holat'],
    ['action', 'Amal'],
    ['invoices', 'Hisob-fakturalar'],
    ['invoice', 'Hisob-faktura'],
    ['description', 'Tavsif'],
    ['desc', 'Tavsif'],
    ['product', 'Mahsulot'],
    ['products', 'Mahsulotlar'],
    ['category', 'Kategoriya'],
    ['categories', 'Kategoriyalar'],
    ['users', 'Foydalanuvchilar'],
    ['user', 'Foydalanuvchi'],
    ['dashboard', 'Boshqaruv paneli'],
    ['add', "Qo'shish"],
    ['update', 'Yangilash'],
    ['edit', 'Tahrirlash'],
    ['delete', "O'chirish"],
    ['save', 'Saqlash'],
    ['close', 'Yopish'],
    ['cancel', 'Bekor qilish'],
    ['show', "Ko'rsatish"],
    ['hide', 'Yashirish'],
    ['pending', 'Kutilmoqda'],
    ['approved', 'Tasdiqlangan'],
    ['completed', 'Yakunlangan'],
    ['direct order', 'To‘g‘ridan-to‘g‘ri buyurtma']
  ]);

  const normalize = (value) => value.replace(/\s+/g, ' ').trim();

  function translateText(text) {
    const clean = normalize(text);
    if (!clean) return null;
    const exact = replacements.get(clean.toLowerCase());
    return exact || null;
  }

  function translate(root = document.body) {
    if (!root) return;
    const walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT);
    const nodes = [];
    let node;
    while ((node = walker.nextNode())) nodes.push(node);

    nodes.forEach((textNode) => {
      const parent = textNode.parentElement;
      if (!parent || /^(SCRIPT|STYLE|NOSCRIPT|TEXTAREA)$/i.test(parent.tagName)) return;
      const translated = translateText(textNode.nodeValue);
      if (translated) textNode.nodeValue = translated;
    });

    root.querySelectorAll('input[placeholder], textarea[placeholder], button, input[type="submit"], input[type="button"]').forEach((el) => {
      const value = el.getAttribute('placeholder') || el.value || el.textContent;
      const translated = translateText(value || '');
      if (!translated) return;
      if (el.hasAttribute('placeholder')) el.setAttribute('placeholder', translated);
      if ('value' in el && el.value) el.value = translated;
      if (!el.hasAttribute('placeholder') && !('value' in el)) el.textContent = translated;
    });

    // Display all product/cart prices in Uzbek so'm.
    root.querySelectorAll('.price, #crt_amt, #crt_tax, #crt_total').forEach((el) => {
      if (el.dataset.uzbekMoney === '1') return;
      const value = normalize(el.textContent || '');
      if (/^-?[\d\s,.]+$/.test(value)) {
        el.textContent = `${value} so'm`;
        el.dataset.uzbekMoney = '1';
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    translate();
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node.nodeType === Node.ELEMENT_NODE) translate(node);
        });
      });
    });
    observer.observe(document.body, { childList: true, subtree: true });
  });
})();
