<?php
session_start();
require_once __DIR__ . '/conf.php';
header('Content-Type: application/json; charset=utf-8');

// Admin credentials are configurable in Render. Defaults keep the panel usable on a fresh install.
$adminUser = getenv('ADMIN_USER') ?: 'Daler';
$adminPass = getenv('ADMIN_PASSWORD') ?: 'Lavash';
$action = $_REQUEST['action'] ?? '';

function out($data, int $code=200){ http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); exit; }
function admin_ok(){ return !empty($_SESSION['pro_admin']) && $_SESSION['pro_admin'] === true; }
function ident($s){ return preg_match('/^[A-Za-z0-9_]+$/',$s) ? $s : ''; }

if ($action === 'login') {
    $u = trim((string)($_POST['username'] ?? ''));
    $p = (string)($_POST['password'] ?? '');
    if (hash_equals($adminUser,$u) && hash_equals($adminPass,$p)) {
        session_regenerate_id(true); $_SESSION['pro_admin']=true; $_SESSION['pro_admin_name']=$u;
        out(['ok'=>true]);
    }
    out(['ok'=>false,'message'=>'Login yoki parol noto‘g‘ri'],401);
}
if ($action === 'logout') { $_SESSION=[]; session_destroy(); out(['ok'=>true]); }
if (!admin_ok()) out(['ok'=>false,'message'=>'Unauthorized'],401);

try {
    $conn->query("CREATE TABLE IF NOT EXISTS admin_settings (setting_key VARCHAR(80) PRIMARY KEY, setting_value TEXT NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->query("CREATE TABLE IF NOT EXISTS admin_orders (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, source_table VARCHAR(128) NULL, source_key VARCHAR(191) NULL, customer_name VARCHAR(190) NOT NULL DEFAULT '', phone VARCHAR(60) NOT NULL DEFAULT '', address TEXT NULL, note TEXT NULL, amount DECIMAL(12,2) NOT NULL DEFAULT 0, status VARCHAR(40) NOT NULL DEFAULT 'new', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY source_ref(source_table,source_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $conn->query("CREATE TABLE IF NOT EXISTS admin_reviews (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, customer_name VARCHAR(190) NOT NULL DEFAULT '', message TEXT NOT NULL, rating TINYINT UNSIGNED NOT NULL DEFAULT 5, status VARCHAR(20) NOT NULL DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    if ($action === 'summary') {
        $r=[]; foreach(['new','confirmed','preparing','delivering','completed','cancelled'] as $s){$q=$conn->query("SELECT COUNT(*) c FROM admin_orders WHERE status='".$conn->real_escape_string($s)."'");$r[$s]=(int)$q->fetch_assoc()['c'];}
        $r['orders']=(int)$conn->query("SELECT COUNT(*) c FROM admin_orders")->fetch_assoc()['c'];
        $r['revenue']=(float)$conn->query("SELECT COALESCE(SUM(amount),0) v FROM admin_orders WHERE status<>'cancelled'")->fetch_assoc()['v'];
        $r['today']=(int)$conn->query("SELECT COUNT(*) c FROM admin_orders WHERE DATE(created_at)=CURDATE()")->fetch_assoc()['c'];
        out(['ok'=>true,'data'=>$r]);
    }
    if ($action === 'orders') {
        $status=$_GET['status']??''; $limit=min(100,max(10,(int)($_GET['limit']??50)));
        $where=$status!=='' ? " WHERE status='".$conn->real_escape_string($status)."'" : '';
        $q=$conn->query("SELECT * FROM admin_orders{$where} ORDER BY created_at DESC LIMIT {$limit}"); $rows=[]; while($x=$q->fetch_assoc())$rows[]=$x; out(['ok'=>true,'data'=>$rows]);
    }
    if ($action === 'status') {
        $id=(int)($_POST['id']??0); $status=trim((string)($_POST['status']??'new'));
        $allowed=['new','confirmed','preparing','delivering','completed','cancelled']; if(!$id||!in_array($status,$allowed,true))out(['ok'=>false],422);
        $st=$conn->prepare('UPDATE admin_orders SET status=? WHERE id=?');$st->bind_param('si',$status,$id);$st->execute();out(['ok'=>true]);
    }
    if ($action === 'settings') {
        if($_SERVER['REQUEST_METHOD']==='POST'){
            foreach(['phone','address','store_name','delivery_note','currency'] as $k){ if(isset($_POST[$k])){ $v=trim((string)$_POST[$k]);$st=$conn->prepare('INSERT INTO admin_settings(setting_key,setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');$st->bind_param('ss',$k,$v);$st->execute(); } }
        }
        $q=$conn->query('SELECT setting_key,setting_value FROM admin_settings');$d=['phone'=>'+998 99 830 56 03','address'=>'Samarqand, Chelak 78 oldi','store_name'=>'Fast Food','delivery_note'=>'Tez va issiq dastavka','currency'=>'so‘m'];while($x=$q->fetch_assoc())$d[$x['setting_key']]=$x['setting_value'];out(['ok'=>true,'data'=>$d]);
    }
    if ($action === 'tables') {
        $q=$conn->query('SHOW TABLES');$tables=[];while($x=$q->fetch_row())$tables[]=$x[0];out(['ok'=>true,'data'=>$tables]);
    }
    out(['ok'=>false,'message'=>'Unknown action'],404);
} catch(Throwable $e){ error_log('Admin API: '.$e->getMessage()); out(['ok'=>false,'message'=>'Server xatosi'],500); }
