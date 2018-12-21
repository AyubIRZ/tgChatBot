<?php
include_once("functions.php");
$api = 'test';
$amount =$_GET['amount'];
$redirect = 'http://3963ac07.ngrok.io/telegram-bot-course/47/verify.php?id='.$_GET['id'];
$factorNumber = 123;
$result = send($api,$amount,$redirect,$factorNumber);
$result = json_decode($result);
if($result->status) {
    $go = "https://pay.ir/payment/gateway/$result->transId";
    header("Location: $go");
} else {
    echo $result->errorMessage;
}