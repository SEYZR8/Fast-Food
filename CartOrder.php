<?php
session_start();
require_once __DIR__.'/database/conf.php';
header('Content-Type: application/json; charset=utf-8');
function out($d,$c=200){http_response_code($c);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
try{
$name=trim((string)($_POST['name']??''));$phone=preg_replace('/[^0-9+]/','',trim((string)($_POST['phone']??'')));$message=trim((string)($_POST['message']??''));$address=trim((string)($_POST['address']??''));$lat=(float)($_POST['lat']??0);$lng=(float)($_POST['lng']??0);$payment=in_array($_POST['payment_method']??'cash',['cash','card'],true)?$_POST['payment_method']:'cash';$items=json_decode((string)($_POST['items']??'[]'),true);
if($name===''||!preg_match('/^\+?[0-9]{9,15}$/',$phone)||!is_array($items)||!count($items))out(['ok'=>false,'message'=>'Ism, telefon va savatni tekshiring.'],422);
if($address==='')$address=$lat&&$lng?('Lokatsiya: '.$lat.', '.$lng):'';if($address==='')out(['ok'=>false,'message'=>'Yetkazib berish manzilini kiriting yoki lokatsiya yuboring.'],422);
$conn->query("CREATE TABLE IF NOT EXISTS admin_orders(id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,source_table VARCHAR(128),source_key VARCHAR(191),customer_name VARCHAR(190) NOT NULL DEFAULT '',phone VARCHAR(60) NOT NULL DEFAULT '',address TEXT,note TEXT,amount DECIMAL(12,2) NOT NULL DEFAULT 0,payment_method VARCHAR(20) NOT NULL DEFAULT 'cash',lat DECIMAL(10,7) NULL,lng DECIMAL(10,7) NULL,status VARCHAR(40) NOT NULL DEFAULT 'new',courier_id BIGINT UNSIGNED NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,UNIQUE KEY source_ref(source_table,source_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
foreach(['lat DECIMAL(10,7) NULL','lng DECIMAL(10,7) NULL'] as $col){try{$conn->query('ALTER TABLE admin_orders ADD COLUMN '.$col);}catch(Throwable $e){}}
$total=0;$lines=[];
foreach($items as $it){$title=trim((string)($it['name']??''));$qty=max(1,(int)($it['qty']??1));$price=(float)($it['price']??0);if($title===''||$price<0)continue;$like='%'.$title.'%';$st=$conn->prepare('SELECT P_id,p_title,p_prize FROM product WHERE p_title LIKE ? LIMIT 1');$st->bind_param('s',$like);$st->execute();$p=$st->get_result()->fetch_assoc();$finalPrice=$p?(float)$p['p_prize']:$price;$finalTitle=$p?$p['p_title']:$title;$total+=$finalPrice*$qty;$lines[]=$finalTitle.' × '.$qty.' (' . number_format($finalPrice,0,'.',' ') . ' so‘m)';}
if(!$lines||$total<=0)out(['ok'=>false,'message'=>'Savatdagi mahsulotlarni tekshiring.'],422);
$invId='cart-'.strtoupper(bin2hex(random_bytes(5)));$note=implode('; ',$lines).($message!==''?' | Xabar: '.$message:'');$st=$conn->prepare("INSERT INTO admin_orders(source_table,source_key,customer_name,phone,address,note,amount,payment_method,lat,lng,status) VALUES('cart',?,?,?,?,?,?,?,?,?,'new')");$st->bind_param('ssssdddsii',$invId,$name,$phone,$address,$note,$total,$payment,$lat,$lng);
// The numeric bind types above may vary by PHP version; use a safer fallback.
if(!$st->execute()){
 $st=$conn->prepare("INSERT INTO admin_orders(source_table,source_key,customer_name,phone,address,note,amount,payment_method,lat,lng,status) VALUES('cart',?,?,?,?,?,?,?,?,'new')");
 $st->bind_param('sssssdssdd',$invId,$name,$phone,$address,$note,$total,$payment,$lat,$lng);$st->execute();
}
out(['ok'=>true,'order_id'=>$conn->insert_id,'total'=>$total,'payment_method'=>$payment]);
}catch(Throwable $e){error_log('CartOrder: '.$e->getMessage());out(['ok'=>false,'message'=>'Server xatosi.'],500);}