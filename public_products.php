<?php
ob_start();
ini_set('display_errors','0');
header('Content-Type: application/json; charset=utf-8');
function out($d,$c=200){while(ob_get_level()>0)ob_end_clean();http_response_code($c);echo json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
try{
    require __DIR__.'/conf.php';
    $table='product';
    $check=$conn->query("SHOW TABLES LIKE 'product'");
    if(!$check || $check->num_rows===0){
        $check2=$conn->query("SHOW TABLES LIKE 'products'");
        if($check2 && $check2->num_rows>0)$table='products';
    }
    $sql="SELECT id,p_title,p_subtitle,p_desc,p_prize,p_image,cat_id,scat_id,status FROM `$table` WHERE status IS NULL OR status IN('show','active','1') ORDER BY id DESC";
    $q=$conn->query($sql);
    $rows=[];
    while($r=$q->fetch_assoc()){
        $rows[]=[
            'id'=>(int)$r['id'],
            'name'=>(string)($r['p_title']??'Taom'),
            'subtitle'=>(string)($r['p_subtitle']??''),
            'desc'=>(string)($r['p_desc']??''),
            'price'=>(float)($r['p_prize']??0),
            'image'=>(string)($r['p_image']??'dish-1.png'),
            'cat_id'=>(string)($r['cat_id']??''),
            'scat_id'=>(string)($r['scat_id']??'')
        ];
    }
    out(['ok'=>true,'data'=>$rows]);
}catch(Throwable $e){error_log('public_products: '.$e->getMessage());out(['ok'=>false,'message'=>'Menyuni yuklashda xato.'],500);}
