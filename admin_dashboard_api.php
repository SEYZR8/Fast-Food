<?php
session_start(); require_once __DIR__.'/conf.php'; header('Content-Type: application/json; charset=utf-8');
if(empty($_SESSION['pro_admin'])){http_response_code(401);echo json_encode(['ok'=>false,'message'=>'Unauthorized']);exit;}
function out($d,$c=200){http_response_code($c);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function cycleStart(){ $t=time(); $d=strtotime(date('Y-m-d').' 06:00:00'); return $t >= $d ? date('Y-m-d H:i:s',$d) : date('Y-m-d H:i:s',strtotime('-1 day',$d)); }
try{
$conn->query("CREATE TABLE IF NOT EXISTS admin_orders(id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,source_table VARCHAR(128),source_key VARCHAR(191),customer_name VARCHAR(190) NOT NULL DEFAULT '',phone VARCHAR(60) NOT NULL DEFAULT '',address TEXT,note TEXT,amount DECIMAL(12,2) NOT NULL DEFAULT 0,payment_method VARCHAR(20) NOT NULL DEFAULT 'cash',status VARCHAR(40) NOT NULL DEFAULT 'new',courier_id BIGINT UNSIGNED NULL,lat DECIMAL(10,7) NULL,lng DECIMAL(10,7) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
foreach(['payment_method VARCHAR(20) NOT NULL DEFAULT \'cash\'','lat DECIMAL(10,7) NULL','lng DECIMAL(10,7) NULL'] as $c){try{$conn->query('ALTER TABLE admin_orders ADD COLUMN '.$c);}catch(Throwable $e){}}
$action=$_REQUEST['action']??'';$start=cycleStart();$end=date('Y-m-d H:i:s',strtotime($start.' +18 hours'));
if($action==='dashboard'){
$st=$conn->prepare("SELECT COUNT(*) orders,COALESCE(SUM(CASE WHEN status<>'cancelled' THEN amount ELSE 0 END),0) sales,COALESCE(SUM(CASE WHEN status='completed' THEN amount ELSE 0 END),0) completed_sales FROM admin_orders WHERE created_at>=? AND created_at<?");$st->bind_param('ss',$start,$end);$st->execute();$m=$st->get_result()->fetch_assoc();
$n=(int)$conn->query("SELECT COUNT(*) c FROM admin_orders WHERE status='new' AND created_at>=CURDATE()+INTERVAL 6 HOUR AND created_at<CURDATE()+INTERVAL 1 DAY + INTERVAL 6 HOUR")->fetch_assoc()['c'];
$online=(int)$conn->query("SELECT COUNT(*) c FROM couriers WHERE status='online' AND last_seen>=NOW()-INTERVAL 2 MINUTE")->fetch_assoc()['c'];
out(['ok'=>true,'data'=>['orders'=>(int)$m['orders'],'sales'=>(float)$m['sales'],'completed_sales'=>(float)$m['completed_sales'],'new'=>$n,'online_couriers'=>$online,'cycle_start'=>$start,'cycle_end'=>$end,'cycle_label'=>date('d.m.Y H:i',strtotime($start)).' — '.date('d.m.Y H:i',strtotime($end))]]);
}
if($action==='history'){
$from=$_GET['from']??'';$to=$_GET['to']??'';$status=$_GET['status']??'';$w=[];$p=[];$types='';
if($from!==''){$w[]='o.created_at>=?';$p[]=$from;$types.='s';}if($to!==''){$w[]='o.created_at<?';$p[]=$to;$types.='s';}if($status!==''){$w[]='o.status=?';$p[]=$status;$types.='s';}
$sql='SELECT o.*,c.name courier_name,c.phone courier_phone FROM admin_orders o LEFT JOIN couriers c ON c.id=o.courier_id'.($w?' WHERE '.implode(' AND ',$w):'').' ORDER BY o.created_at DESC LIMIT 1000';$st=$conn->prepare($sql);if($p)$st->bind_param($types,...$p);$st->execute();$q=$st->get_result();$rows=[];while($x=$q->fetch_assoc())$rows[]=$x;out(['ok'=>true,'data'=>$rows]);
}
if($action==='assign'){$oid=(int)($_POST['order_id']??0);$cid=(int)($_POST['courier_id']??0);if(!$oid||!$cid)out(['ok'=>false,'message'=>'Buyurtma va kuryer kerak'],422);$st=$conn->prepare('UPDATE admin_orders SET courier_id=?,status=IF(status="new","confirmed",status) WHERE id=?');$st->bind_param('ii',$cid,$oid);$st->execute();out(['ok'=>true]);}
if($action==='status'){$oid=(int)($_POST['order_id']??0);$s=(string)($_POST['status']??'new');if(!$oid||!in_array($s,['new','confirmed','preparing','delivering','completed','cancelled'],true))out(['ok'=>false],422);$st=$conn->prepare('UPDATE admin_orders SET status=? WHERE id=?');$st->bind_param('si',$s,$oid);$st->execute();out(['ok'=>true]);}
out(['ok'=>false,'message'=>'Unknown action'],404);
}catch(Throwable $e){error_log('Admin dashboard API: '.$e->getMessage());out(['ok'=>false,'message'=>'Server xatosi'],500);}
?>