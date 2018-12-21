<?php
require_once 'config.php';
define('API_TOKEN', '565331056:AAEEjBrvkwSbtw5AdIcQYEXui3zxiCtM_14');
////////////////////////// Functions /////////////////////////
function bot($data){
    return json_decode(file_get_contents("https://api.telegram.org/bot".API_TOKEN."/".$data),true);
}
function message($chat_id,$msg,$markup=null){
    if($markup!=null)
    {
        bot("sendMessage?chat_id=".$chat_id."&text=".$msg."&reply_markup=".$markup);
    }
    else
    {
        bot("sendMessage?chat_id=".$chat_id."&text=".$msg);
    }
}

function forwardMessage($user_id,$message_id,$from_chat_id){
    bot("forwardMessage?chat_id=".$user_id."&from_chat_id=".$from_chat_id."&message_id=".$message_id);
}

function editMessage($chat_id,$message_id,$msg){
        bot("editMessageText?chat_id=".$chat_id."&message_id=".$message_id."&text=".$msg);
}

function deleteMessage($chat_id,$message_id){
    bot("deleteMessage?chat_id=".$chat_id."&message_id=".$message_id);
}

function photo($chat_id,$photo_link,$caption=null)
{
    bot("sendPhoto?chat_id=".$chat_id."&photo=".$photo_link."&caption=".$caption);
}
function video($chat_id,$video_link,$caption=null)
{
    bot("sendVideo?chat_id=".$chat_id."&video=".$video_link."&caption=".$caption);
}

function send_file($chat_id,$file_id,$caption=null)
{
    bot("sendDocument?chat_id=".$chat_id."&document=".$file_id."&caption=".$caption);
}

function action($chat_id,$action)
{
    bot("sendChatAction?chat_id=".$chat_id."&action=".$action);
}

function answer_query($query_id,$text,$show_alert=false)
{
    bot("answerCallbackQuery?callback_query_id=".$query_id."&text=".$text."&show_alert=".$show_alert);
}

function getStep($user_id){
    global $db;
    $query="select step from users WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res['step'];
}

function setStep($user_id,$step){
    global $db;
    $query="update users set step='".$step."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function addDownload($user_id){
    global $db;
    $query="update users set downloaded_music=downloaded_music+1 WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function getDownload($user_id){
    global $db;
    $query="select downloaded_music from users WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res['downloaded_music'];
}

function addSent($user_id){
    global $db;
    $query="update users set sent_music=sent_music+1 WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function getSent($user_id){
    global $db;
    $query="select sent_music from users WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res['sent_music'];
}

function addMusic($file_id,$title,$performer,$duration,$file_size,$mime_type){
    global $db;
    $query="insert into music(file_id,performer,title,duration,file_size,mime_type) VALUES('$file_id','$performer','$title','$duration','$file_size','$mime_type')";
    $res=mysqli_query($db, $query);
    return $res;
}

function addDownloades($id,$file_id,$title,$performer,$duration,$file_size,$user_id){
    global $db;
    $query="insert into downloads(file_id,performer,title,duration,file_size,music_id,user_id) VALUES('$file_id','$performer','$title','$duration','$file_size','$id','$user_id')";
    $res=mysqli_query($db, $query);
    return $res;
}

function isDownloaded($user_id,$id){
    global $db;
    $query="select * from downloads WHERE user_id=".$user_id." and music_id=".$id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return (isset($res['music_id']))?true:false;
}

function addMessage($sender_id,$receiver_id,$message_text){
    global $db;
    $query="insert into sent_messages(sender_id,receiver_id,message) VALUES('$sender_id','$receiver_id','".$message_text."')";
    $res=mysqli_query($db, $query);
    return mysqli_insert_id($db);
}

function addBlock($blocker_user,$blocked_user){
    global $db;
    $query="insert into blocked_users(blocker_user,blocked_user) VALUES('$blocker_user','$blocked_user')";
    $res=mysqli_query($db, $query);
    return mysqli_insert_id($db);
}
function unblock($blocker_user,$blocked_user){
    global $db;
    $query="delete from blocked_users WHERE blocker_user='$blocker_user' and blocked_user='$blocked_user'";
    $res=mysqli_query($db, $query);
    return $res;
}

function setPayTime($user_id,$timestamp){
    global $db;
    $query="update users set pay_time='".$timestamp."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function setReceiver($user_id,$receiver_id){
    global $db;
    $query="update users set receiver_id='".$receiver_id."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function setMessage_tmp($user_id,$message){
    global $db;
    $query="update users set message_tmp='".$message."' WHERE user_id='$user_id'";
    $res=mysqli_query($db, $query);
    return $res;
}

function setSearch($user_id,$search_string){
    global $db;
    $query="update users set last_search='".$search_string."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function getSearch($user_id){
    global $db;
    $query="select last_search from users WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res['last_search'];
}

function getUserByUsername($username){
    global $db;
    $query="select * from users WHERE username='".$username."'";
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res;
}

function setAnswerID($user_id,$answer_to){
    global $db;
    $query="update users set answer_to='".$answer_to."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function setOnOff($user_id,$bool){
    global $db;
    $query="update users set on_off=".$bool." WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function setShownName($user_id,$name){
    global $db;
    $query="update users set shown_name='".$name."' WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function setPrivacy($user_id,$bool){
    global $db;
    $query="update users set privacy=".$bool." WHERE user_id=".$user_id;
    $res=mysqli_query($db, $query);
    return $res;
}

function isBlockedByUser($receiver_id,$sender_id){
    global $db;
    $query="select * from blocked_users WHERE blocker_user='$receiver_id' and blocked_user='$sender_id'";
    $res=mysqli_query($db, $query);
    $res=mysqli_num_rows($res);
    return ($res>0)?true:false;
}

function inline_btn($i){
    $ar=array();
    $button=array();
    for($c=0;$c<count($i);$c=$c+2)
    {
        $button[$c/2 % 2]=array("text"=>urlencode($i[$c]),"callback_data"=>$i[$c+1]);
        if($c/2 % 2){
            array_push($ar,array($button[0],$button[1]));
            $button=array();
        }elseif(count($i)-$c<=2){
            array_push($ar,array($button[0]));
            $button=array();
        }
    }
    return "&reply_markup=".json_encode(array("inline_keyboard"=>$ar));
}

function getMusic($id){
    global $db;
    $query="select * from music WHERE id=".$id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res;
}

function isMember($user_id,$chat_id){
    $status=bot("getChatMember?chat_id=".$chat_id."&user_id=".$user_id);
    return $status['result']['status'];
}

function getMessage($message_id){
    global $db;
    $query="select * from sent_messages WHERE id=".$message_id;
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res;
}

function getMusicCount(){
    global $db;
    $query="select * from music";
    $res=mysqli_query($db, $query);
    $res=mysqli_num_rows($res);
    return $res;
}

function getMemberCount(){
    global $db;
    $query="select * from users";
    $res=mysqli_query($db, $query);
    $res=mysqli_num_rows($res);
    return $res;
}

function getUser($user_id){
    global $db;
    $query="select * from users WHERE user_id='$user_id' OR hash_id='$user_id'";
    $res=mysqli_query($db, $query);
    $res=mysqli_fetch_assoc($res);
    return $res;
}

function deleteBlock($user_id){
    global $db;
    $query="delete from blocked_users where blocker_user='$user_id'";
    $res=mysqli_query($db, $query);
    return $res;
}


function mainMenu(){
    $markup=array('keyboard'=>array(array('📩 می‌خوام پیام ناشناس دریافت کنم'),array('🔸 دریافت عضویت VIP'),array('📤 ارسال مستقیم ناشناس','📬 پیام‌های دریافت شده'),array('⚙️ تنظیمات','🤔 راهنما')),'resize_keyboard'=>true);
    return json_encode($markup);
}

function settingMenu(){
    $markup=array('keyboard'=>array(array('نام نمایشی','آزادسازی بلاک شده ها'),array('حریم شخصی','قطع سرویس'),array('منوی اصلی','اطلاعات حساب')),'resize_keyboard'=>true);
    return json_encode($markup);
}

function sendMenu(){
    $markup=array('keyboard'=>array(array('لغو و رفتن به منوی اصلی')),'resize_keyboard'=>true);
    return json_encode($markup);
}

function nameMenu(){
    $markup=array('keyboard'=>array(array('لغو و بازگشت به منوی تنظیمات')),'resize_keyboard'=>true);
    return json_encode($markup);
}

function acceptMenu(){
    $markup=array('keyboard'=>array(array('❌ لغوش کن ❌','✅ حله بفرست ✅')),'resize_keyboard'=>true);
    return json_encode($markup);
}

function privacyMenu($status){
    if($status){
        $markup=array('keyboard'=>array(array("دریافت فقط از طریق لینک"),array('بازگشت به منوی تنظیمات')),'resize_keyboard'=>true);
    }else{
        $markup=array('keyboard'=>array(array("دریافت آزاد از همه"),array('بازگشت به منوی تنظیمات')),'resize_keyboard'=>true);
    }
    return json_encode($markup);
}
