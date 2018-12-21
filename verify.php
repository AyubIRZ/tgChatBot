<?php
include_once("functions.php");
include_once("function.php");
date_default_timezone_set("Asia/Tehran");
$pay_time_days=90*24*60*60;
global $db;
$api = 'test';
$transId = $_POST['transId'];
$result = verify($api,$transId);
$result = json_decode($result,true);
if($result['status']==1){
    setPayTime($_GET['id'],time()+$pay_time_days);
    $expiry_time=date("Y-m-d H:i",time()+$pay_time_days);
    $msg=urlencode("✅ عضویت VIP با موفقیت برای شما کاربر گرامی فعال گردید. ✅

عضویت شما تا تاریخ $expiry_time اعتبار دارد.");
    message($_GET['id'], $msg);
    header("Location: https://t.me/harfe_nashenas_7learn_bot");
}else{
    $msg=urlencode("❗️ متاسفانه پرداخت شما ناموفق بود و عضویت VIP برای شما فعال نشده است.");
    message($_GET['id'], $msg);
}