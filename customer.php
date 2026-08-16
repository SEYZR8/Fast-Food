<?php
session_start();
require_once __DIR__.'/conf.php';
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
$conn->query("CREATE TABLE IF NOT EXISTS customer_profiles(id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id BIGINT NULL,name VARCHAR(190) NOT NULL,phone VARCHAR(60) NOT NULL UNIQUE,email VARCHAR(190) NULL,address TEXT NULL,password_hash VARCHAR(255) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$msg='';
if(isset($_POST['logout'])){unset($_SESSION['customer_id']);header('Location: customer.php');exit;}
if(isset($_POST['register'])){
  $name=trim((string)($_POST['name']??''));
  $phone=preg_replace('/[^0-9+]/','',trim((string)($_POST['phone']??'')));
  $rawEmail=trim((string)($_POST['email']??''));
  $email=$rawEmail===''?null:filter_var($rawEmail,FILTER_VALIDATE_EMAIL);
  $pass=(string)($_POST['password']??'');
  $address=trim((string)($_POST['address']??''));
  if($name===''||!preg_match('/^\+?[0-9]{9,15}$/',$phone)||strlen($pass)<6){
    $msg='Ism, to‘g‘ri telefon va kamida 6 belgili parol kerak.';
  }elseif($rawEmail!==''&&$email===false){
    $msg='Email manzilini to‘g‘ri kiriting.';
  }else{
    try{
      $check=$conn->prepare('SELECT id FROM customer_profiles WHERE phone=? LIMIT 1');
      $check->bind_param('s',$phone);$check->execute();
      if($check->get_result()->fetch_assoc()){
        $msg='Bu telefon raqami allaqachon ro‘yxatdan o‘tgan. Kirish bo‘limidan foydalaning.';
      }else{
        $hash=password_hash($pass,PASSWORD_DEFAULT);
        $st=$conn->prepare('INSERT INTO customer_profiles(name,phone,email,password_hash,address) VALUES(?,?,?,?,?)');
        $emailValue=$email===null?'':$email;
        $st->bind_param('sssss',$name,$phone,$emailValue,$hash,$address);$st->execute();
        session_regenerate_id(true);$_SESSION['customer_id']=$conn->insert_id;header('Location: customer.php');exit;
      }
    }catch(Throwable $e){error_log('Customer registration: '.$e->getMessage());$msg='Ro‘yxatdan o‘tishda xatolik yuz berdi. Qaytadan urinib ko‘ring.';}
  }
}
if(isset($_POST['login'])){
  $login=trim((string)($_POST['login']??''));$pass=(string)($_POST['password']??'');
  try{
    $st=$conn->prepare('SELECT id,password_hash FROM customer_profiles WHERE phone=? OR email=? LIMIT 1');$st->bind_param('ss',$login,$login);$st->execute();$u=$st->get_result()->fetch_assoc();
    if($u&&password_verify($pass,(string)($u['password_hash']??''))){session_regenerate_id(true);$_SESSION['customer_id']=(int)$u['id'];header('Location: customer.php');exit;}
    $msg='Login yoki parol noto‘g‘ri.';
  }catch(Throwable $e){error_log('Customer login: '.$e->getMessage());$msg='Kirishda server xatosi. Qaytadan urinib ko‘ring.';}
}
$uid=(int)($_SESSION['customer_id']??0);$me=null;$orders=[];
if($uid){
  $st=$conn->prepare('SELECT * FROM customer_profiles WHERE id=? LIMIT 1');$st->bind_param('i',$uid);$st->execute();$me=$st->get_result()->fetch_assoc();
  if($me){$st=$conn->prepare('SELECT id,customer_name,phone,address,note,amount,payment_method,status,created_at FROM admin_orders WHERE phone=? ORDER BY created_at DESC LIMIT 50');$st->bind_param('s',$me['phone']);$st->execute();$q=$st->get_result();while($x=$q->fetch_assoc())$orders[]=$x;}
}
?><!doctype html><html lang="uz"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Lavash N1 • Mijoz kabineti</title><style>*{box-sizing:border-box}body{margin:0;font-family:Inter,system-ui,Arial;background:radial-gradient(circle at 15% 10%,#fff0e5,transparent 30%),radial-gradient(circle at 90% 20%,#fff6db,transparent 28%),#f7f8fb;color:#101828}.wrap{max-width:1050px;margin:auto;padding:25px}.hero{padding:30px;border-radius:28px;background:linear-gradient(135deg,#111827,#283548);color:#fff;box-shadow:0 25px 70px #10182825}.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:18px}.card{background:#fff;border:1px solid #eaecf0;border-radius:22px;padding:22px;box-shadow:0 18px 50px #1018280d}input,button{width:100%;padding:12px 14px;margin:7px 0;border-radius:13px;border:1px solid #d0d5dd;font:inherit}button{border:0;background:linear-gradient(135deg,#ff4d00,#ff8a00);color:#fff;font-weight:900;cursor:pointer}.muted{color:#667085}.order{border:1px solid #eaecf0;border-radius:17px;padding:15px;margin:10px 0}.pill{display:inline-block;padding:5px 9px;border-radius:999px;background:#ecfdf3;font-weight:800;font-size:12px}.danger{background:#101828}.link{display:inline-block;color:#ff4d00;font-weight:900;text-decoration:none;margin-top:10px}@media(max-width:750px){.grid{grid-template-columns:1fr}.wrap{padding:13px}}</style></head><body><div class="wrap"><div class="hero"><div style="font-size:42px">🌯</div><h1 style="margin:5px 0">Lavash N1 — Mijoz kabineti</h1><p style="opacity:.8">Buyurtmalaringizni kuzating va keyingi buyurtmani tez bering.</p><a href="index.php" style="color:#fff;font-weight:900">← Saytga qaytish</a></div><?php if($msg):?><div class="card" style="margin-top:18px;color:#b42318"><?=h($msg)?></div><?php endif;?><?php if(!$me):?><div class="grid"><div class="card"><h2>🔐 Kirish</h2><form method="post"><input name="login" placeholder="Telefon yoki email" required><input name="password" type="password" placeholder="Parol" required><button name="login" value="1">Kirish</button></form><p class="muted">Kabinet ochmasdan ham saytdan buyurtma berishingiz mumkin.</p></div><div class="card"><h2>✨ Ro‘yxatdan o‘tish</h2><form method="post"><input name="name" placeholder="Ism familiya" required><input name="phone" placeholder="+998998305603" required><input name="email" type="email" placeholder="Email (ixtiyoriy)"><input name="address" placeholder="Yetkazib berish manzili"><input name="password" type="password" minlength="6" placeholder="Parol (6+ belgi)" required><button name="register" value="1">Kabinet ochish</button></form></div></div><?php else:?><div class="card" style="margin-top:18px"><h2>Salom, <?=h($me['name'])?> 👋</h2><p class="muted">📞 <?=h($me['phone'])?><?=($me['address']??'')!==''?' · 📍 '.h($me['address']):''?></p><form method="post"><button name="logout" value="1" class="danger">Chiqish</button></form><a class="link" href="index.php#ln1-simple-menu">🛒 Yangi buyurtma</a></div><div class="card" style="margin-top:18px"><h2>📦 Buyurtmalarim</h2><?php if(!$orders):?><p class="muted">Hali buyurtma yo‘q.</p><?php endif;?><?php foreach($orders as $o):?><div class="order"><b>#<?=h($o['id'])?></b><span class="pill" style="float:right"><?=h($o['status'])?></span><p>💰 <?=number_format((float)$o['amount'],0,' ',' ')?> so‘m · <?=h(($o['payment_method']??'cash')==='card'?'Karta':'Naqd')?></p><p class="muted">📍 <?=h($o['address'])?><br><?=h($o['note'])?><br><?=h($o['created_at'])?></p></div><?php endforeach;?></div><?php endif;?></div></body></html>