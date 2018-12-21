<?php
require_once 'function.php';
global $db;
date_default_timezone_set("Asia/Tehran");
$input=file_get_contents("php://input");
file_put_contents('result.txt', $input.PHP_EOL.PHP_EOL,FILE_APPEND);
$update=json_decode($input,true);
$api_url="https://api.telegram.org/bot".API_TOKEN."/";
///////////////////////////// Admin info /////////////////////////////
$channel_id="@nashenas_7learn";
$bot_username="file_shop_7learn_bot";
$admin_user_id=252519699;
$limit_musics=10;
$pay_time_days=90*24*60*60;
$bot_directory="http://a7e45de0.ngrok.io/telegram-bot-course/project2/";
/////////////////////////////////////////////////////////////////////
if(array_key_exists('message', $update)){
    $user_id=$update['message']['from']['id'];
    $chat_id=$update['message']['chat']['id'];
    $message_id=$update['message']['message_id'];
    $username=(array_key_exists('username',$update['message']['from']))?$update['message']['from']['username']:null;
    $last_name=(array_key_exists('last_name',$update['message']['from']))?$update['message']['from']['last_name']:null;
    $first_name=$update['message']['from']['first_name'];
    $text=$update['message']['text'];
    $audio=(array_key_exists('audio',$update['message']))?$update['message']['audio']['file_id']:null;
    $caption=$update['message']['caption'];
}elseif (array_key_exists('callback_query', $update)){
    $callback_id=$update['callback_query']['id'];
    $user_id=$update['callback_query']['from']['id'];
    $chat_id=$update['callback_query']['message']['chat']['id'];
    $message_id=$update['callback_query']['message']['message_id'];
    $username=(array_key_exists('username',$update['callback_query']['from']))?$update['callback_query']['from']['username']:null;
    $first_name=$update['callback_query']['from']['first_name'];
    $last_name=(array_key_exists('last_name',$update['callback_query']['from']))?$update['callback_query']['from']['last_name']:null;
    $text=$update['callback_query']['data'];
}

///////////////////////////////////////////////////////////////
if(strpos($text, '/start')!==false){
    if($text=='/start'){
        action($chat_id,'typing');
        $query="select * from users WHERE user_id=".$user_id;
        $res=mysqli_query($db, $query);
        $num=mysqli_num_rows($res);
        if($num==0){
            $hash_id=md5($user_id);
            $query="insert into users(user_id,hash_id,first_name,last_name,username,step,shown_name) VALUES( '$user_id' ,'$hash_id','$first_name','$last_name','$username','home','$first_name')";
            $res=mysqli_query($db, $query);
        }
        $msg=urlencode("چه کاری برات انجام بدم؟");
        message($chat_id, $msg,mainMenu());
        setStep($user_id, 'home');
        setReceiver($user_id, null);
        setMessage_tmp($user_id, null);
        setAnswerID($user_id,null );
        setOnOff($user_id,1 );
    }else{
        setOnOff($user_id,1 );
        setReceiver($user_id, null);
        setMessage_tmp($user_id, null);
        setAnswerID($user_id,null );
        $hash_receiver_id=substr($text,7);
        $query="select * from users WHERE user_id=".$user_id;
        $res=mysqli_query($db, $query);
        $num=mysqli_num_rows($res);
        if($num==0){
            $hash_id=md5($user_id);
            $query="insert into users(user_id,hash_id,first_name,last_name,username,shown_name) VALUES( '$user_id' ,'$hash_id','$first_name','$last_name','$username','$first_name')";
            $res=mysqli_query($db, $query);
        }
        if($hash_receiver_id!=md5($user_id)){
            $user_data=getUser($hash_receiver_id);
            $shown_name=$user_data['shown_name'];
            if($user_data['on_off']){
                $msg=urlencode("در حال ارسال پیام ناشناس به $shown_name هستی. می‌تونی انتقاد یا هر حرفی که تو دلت هست رو بفرستی چون پیامت به صورت کاملا ناشناس ارسال می‌شه.

منتظر گرفتن متن پیام ازت هستیم... ضمنا اگه عضو VIP باشی می‌تونی به صورت مستقیم و بدون لینک به کاربران پیام ناشناس ارسال کنی.");
                message($chat_id, $msg,sendMenu());
                setStep($user_id, 'send_message');
                setReceiver($user_id,$hash_receiver_id);
            }else{
                $msg=urlencode("متاسفانه کاربر مورد نظر سرویس خود را قطع نموده و شما امکان ارسال پیام به این کاربر را ندارید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                message($chat_id, $msg,mainMenu());
                setStep($user_id, 'home');
                setReceiver($user_id, null);
                setMessage_tmp($user_id, null);
                setAnswerID($user_id,null );
            }

        }else{
            setOnOff($user_id, 1);
            setReceiver($user_id, null);
            setMessage_tmp($user_id, null);
            setAnswerID($user_id,null );
            $msg=urlencode("اینکه ادم گاهی با خودش حرف بزنه خوبه، ولی اینجا نمی‌تونی به خودت پیام ناشناس بفرستی.

چه کاری برات انجام بدم؟");
            message($chat_id, $msg,mainMenu());
        }
    }
}else {
    $step = getStep($user_id);
    switch ($step) {
        case 'home': {
            switch ($text) {
                case '📩 می‌خوام پیام ناشناس دریافت کنم': {
                    action($chat_id,'typing');
                    $hash=md5($user_id);
                    $shown_name=getUser($user_id)['shown_name'];
                    $msg=urlencode("سلام $shown_name هستم 😉
لینک زیر رو لمس کن و هر انتقادی که نسبت به من داری یا حرفی که تو دلت هست رو با خیال راحت بنویس و بفرست. بدون اینکه از اسمت باخبر بشم پیامت به من می‌رسه. خودتم می‌تونی امتحان کنی و از همه بخوای راحت و ناشناس بهت پیام بفرستن، حرفای خیلی جالبی می‌شنوی:

👇👇👇
https://t.me/".$bot_username."?start=".getUser($user_id)['hash_id']);
                    message($chat_id, $msg);
                    $msg=urlencode("☝️ پیام بالا رو به دوستات و گروه‌هایی که می‌شناسی فوروارد کن یا لینک داخلش رو تو شبکه‌های اجتماعی بذار تا بقیه بتونن بهت پیام ناشناس بفرستن. پیام‌ها از طریق همین برنامه بهت می‌رسه.

- برای تغییر اسم نمایشیت در ربات می‌تونی به قسمت تنظیمات و دکمه ی \"نام نمایشی\" بری.");
                    message($chat_id, $msg);
                }
                    break;

                case '🔸 دریافت عضویت VIP': {
                    action($chat_id,'typing');
                    $pay_time=getUser($user_id)['pay_time'];
                    if($pay_time==null or $pay_time<time()){
                        include_once("functions.php");
                        $api = 'test';
                        $amount =5000;
                        $redirect = $bot_directory.'verify.php?id='.$user_id;
                        $factorNumber = time();
                        $result = send($api,$amount,$redirect,$factorNumber);
                        $result = json_decode($result);
                        if($result->status) {
                            $go = "https://pay.ir/payment/gateway/$result->transId";
                            $msg=urlencode("نیاز به فعال کردن عضویت VIP داری!

با دریافت عضویت VIP:

🔸به فرستنده پیام‌های ناشناسی که برات میاد به صورت ناشناس یا با معرفی خودت جواب می‌فرستی

🔸بدون نیاز به عضویت در هیچ کانالی در همه ساعات از برنامه استفاده  می‌کنی

🔸به دوستانت که کاربر برنامه هستند (بیشتر از ۱۰ میلیون نفر) بدون داشتن لینک اختصاصیشون پیام ناشناس می‌فرستی

🔸علاوه بر متن، تصویر، ویدیو، موزیک، وویس یا گیف به صورت ناشناس می‌فرستی

🔸از تیم برنامه حمایت می‌کنی که برنامه‌های بهتری بسازن

امکانات خاص برنامه روز به روز بیشتر و جالب‌تر میشن. پس همین الان لینک زیر رو لمس کن و با فقط ۵ تومن برای خودت سه ماه (90 روز) کامل عضویت نامحدود VIP فعال کن و لذت ببر:

".$go);
                            message($chat_id, $msg);
                        } else {
                            $msg=$result->errorMessage;
                            message($admin_user_id, urlencode($msg));
                        }

                    }else{

                        $expiry_time=date("Y-m-d H:i",$pay_time);
                        $msg=urlencode("✅ اعتبار شما هنوز به پایان نرسیده و شما نیاز به فعالسازی اشتراک VIP ندارید.".PHP_EOL.PHP_EOL."تاریخ پایان اشتراک VIP شما: $expiry_time");
                        message($chat_id, $msg);
                    }
                }
                    break;

                case '📤 ارسال مستقیم ناشناس': {
                    $pay_time=getUser($user_id)['pay_time'];
                    if($pay_time==null or $pay_time<time()){
                        include_once("functions.php");
                        $api = 'test';
                        $amount =5000;
                        $redirect = $bot_directory.'verify.php?id='.$user_id;
                        $factorNumber = time();
                        $result = send($api,$amount,$redirect,$factorNumber);
                        $result = json_decode($result);
                        if($result->status) {
                            $go = "https://pay.ir/payment/gateway/$result->transId";
                            $msg=urlencode("نیاز به فعال کردن عضویت VIP داری!

با دریافت عضویت VIP:

🔸به فرستنده پیام‌های ناشناسی که برات میاد به صورت ناشناس یا با معرفی خودت جواب می‌فرستی

🔸بدون نیاز به عضویت در هیچ کانالی در همه ساعات از برنامه استفاده  می‌کنی

🔸به دوستانت که کاربر برنامه هستند (بیشتر از ۱۰ میلیون نفر) بدون داشتن لینک اختصاصیشون پیام ناشناس می‌فرستی

🔸علاوه بر متن، تصویر، ویدیو، موزیک، وویس یا گیف به صورت ناشناس می‌فرستی

🔸از تیم برنامه حمایت می‌کنی که برنامه‌های بهتری بسازن

امکانات خاص برنامه روز به روز بیشتر و جالب‌تر میشن. پس همین الان لینک زیر رو لمس کن و با فقط ۵ تومن برای خودت سه ماه (90 روز) کامل عضویت نامحدود VIP فعال کن و لذت ببر:

".$go);
                            message($chat_id, $msg);
                        } else {
                            $msg=$result->errorMessage;
                            message($admin_user_id, urlencode($msg));
                        }
                    }else{
                        $msg=urlencode("برای ارسال مستقیم پیام ناشناس لطفا

1️⃣ یه پیام متنی از کسی که می‌خوای بهش پیام ناشناس بفرستی رو الان به این بات فوروارد کن
یا
2️⃣ آی‌دی تلگرام کسی که می‌خوای بهش پیام ناشناس بفرستی رو به این شکل بفرست: girandeh@

تا ببینیم ایشون عضو برنامه هست یا نه!");
                        message($chat_id, $msg,sendMenu());
                        setStep($user_id, 'direct_message');
                    }
                }
                    break;

                case '📬 پیام‌های دریافت شده': {
                    $query="select * from sent_messages WHERE receiver_id='$user_id'";
                    $res=mysqli_query($db, $query);
                    $num=mysqli_num_rows($res);
                    if($num>0){
                        $result="📪 پیام‌های متنی دریافت شده".PHP_EOL.PHP_EOL;
                        $cnt=($num>=$limit_musics)?$limit_musics:$num;
                        for ($i=1;$i<=$cnt;$i++){
                            $fetch=mysqli_fetch_assoc($res);
                            $messageID=$fetch['id'];
                            $message=urldecode($fetch['message']);
                            $result.=((strlen($message)>150)?substr($message,0,145).". . .":$message).PHP_EOL."نمایش جزئیات 👈 /detail_".$messageID.PHP_EOL."------------------------".PHP_EOL;
                        }
                        if($num>$limit_musics){
                            $result.="🔍 $num پیام پیدا شد 🔍";
                            message($chat_id, urlencode($result).inline_btn(array('صفحه ی بعد','next_'.$limit_musics)));
                        }else{
                            $result.="🔍 $num پیام پیدا شد 🔍";
                            message($chat_id, urlencode($result));
                        }

                    }else{
                        $msg="در حال حاضر پیامی نداری. چطوره با زدن دستور /link لینک خودت رو بگیری و به دوستات و گروه‌ها بفرستی تا بتونند بهت پیام ناشناس بفرستند؟";
                        message($chat_id, urlencode($msg));
                    }
                }
                    break;

                case strpos($text, 'next_')!==false: {
                    $data=explode('_',$text );
                    $last_id=$data[1];
                    $query="select * from sent_messages WHERE receiver_id='$user_id'";
                    $res=mysqli_query($db, $query);
                    $num=mysqli_num_rows($res);
                    $records=array();
                    while ($fetch=mysqli_fetch_assoc($res)){
                        $records[]=$fetch;
                    }
                    if($last_id+$limit_musics<$num){
                        $endponit=$last_id+$limit_musics;
                    }else{
                        $endponit=$num;
                    }
                    $result="👇 پیام های بعدی 👇".PHP_EOL.PHP_EOL;
                    $cnt=($num>=$limit_musics)?$limit_musics:$num;
                    for ($i=$last_id;$i<$endponit;$i++){
                        $messageID=$records[$i]['id'];
                        $message=urldecode($records[$i]['message']);
                        $result.=((strlen($message)>150)?substr($message,0,145).". . .":$message).PHP_EOL."نمایش جزئیات 👈 /detail_".$messageID.PHP_EOL."------------------------".PHP_EOL;
                    }
                    if($num>$last_id+$limit_musics){
                        $result.="🔍 $num پیام پیدا شد 🔍";
                        message($chat_id, urlencode($result).inline_btn(array('صفحه ی بعد','next_'.$endponit,'صفحه ی قبل','prev_'.$endponit)));
                    }else{
                        $result.="🔍 $num پیام پیدا شد 🔍";
                        message($chat_id, urlencode($result).inline_btn(array('صفحه ی قبل','prev_'.$endponit)));
                    }

                }break;

                case strpos($text, 'prev_')!==false: {
                    $data=explode('_',$text );
                    $last_id=$data[1];
                    $query="select * from sent_messages WHERE receiver_id='$user_id'";
                    $res=mysqli_query($db, $query);
                    $num=mysqli_num_rows($res);
                    $records=array();
                    while ($fetch=mysqli_fetch_assoc($res)){
                        $records[]=$fetch;
                    }
                    if($last_id%$limit_musics==0){
                        $endponit=$last_id-$limit_musics;
                    }else{
                        $last_id=$last_id-($last_id%$limit_musics);
                        $endponit=$last_id;
                    }
                    $result="👇 پیام های بعدی👇".PHP_EOL.PHP_EOL;
                    $cnt=($num>=$limit_musics)?$limit_musics:$num;
                    for ($i=$endponit-$limit_musics;$i<=$endponit;$i++){
                        $messageID=$records[$i]['id'];
                        $message=urldecode($records[$i]['message']);
                        $result.=((strlen($message)>140)?substr($message,0,140).". . .":$message).PHP_EOL."نمایش جزئیات 👈 /detail_".$messageID.PHP_EOL."------------------------".PHP_EOL;
                    }
                    if($num>$last_id and $endponit-$limit_musics>0){
                        $result.="🔍 $num پیام پیدا شد 🔍";
                        message($chat_id, urlencode($result).inline_btn(array('صفحه ی بعد','next_'.$endponit,'صفحه ی قبل','prev_'.$endponit)));
                    }else{
                        $result.="🔍 $num پیام پیدا شد 🔍";
                        message($chat_id, urlencode($result).inline_btn(array('صفحه ی بعد','next_'.$endponit)));
                    }

                }break;

                case strpos($text,'/detail_'):{
                    $msg_id=explode('_',$text )[1];
                    $msg_info=getMessage($msg_id);
                    $sender_id=$msg_info['sender_id'];
                    $message=urldecode($msg_info['message']);
                    $receiver_id=$msg_info['receiver_id'];
                    if($receiver_id==$user_id){
                        $msg=urlencode("#پیام_ناشناس".PHP_EOL.$message.PHP_EOL.PHP_EOL."🤖 @".$bot_username);
                        Message($chat_id, $msg.inline_btn(array('🚫 بلاک','/blc_'.$msg_id,'↗️ ارسال جواب','/ans_'.$msg_id)));
                    }else{
                        $msg=urlencode("شما اجازه ی این کار را ندارید!");
                        message($chat_id, $msg);
                    }
                }break;

                case '⚙️ تنظیمات': {
                    $msg=urlencode("کنترل حساب:");
                    message($chat_id, $msg,settingMenu());
                    setStep($user_id, 'setting');
                }
                    break;

                case '🤔 راهنما': {
                    $msg=urlencode("من اینجام که کمکت کنم! برای دریافت راهنمایی در مورد هر موضوع، کافیه دستور آبی رنگی که مقابل اون سوال هست رو لمس کنی:

🔹این برنامه چیه اصلا؟ به چه درد می‌خوره؟ /faq1

🔹چطوری پیام ناشناس دریافت کنم؟ /faq2

🔹لینک اختصاصیم رو می‌ذارم اینستاگرام اما کار نمیکنه /faq3

🔹پیام‌های ناشناس من کجا به دستم می‌رسه؟ /faq4

🔹چطوری یه نفر رو آنبلاک کنم؟ /faq8

🔹چطور می‌تونم پیام ناشناس بفرستم؟ /faq5

🔹چطور می‌تونم پیام‌های دریافتی رو یکجا ببینم و به هر کدوم که می‌خوام جواب بدم؟  /faq6

🔹این فعال کردن نسخه VIP یعنی چی؟ آیا باید اپلیکیشن دانلود کنم؟  /faq7

🔹من می‌خوام بدونم چه کسی بهم پیام فرستاده /faq12

🔹قوانین استفاده از این سرویس چیه؟ /faq13");
                    message($chat_id, $msg);
                }
                    break;

                case strpos($text,'/seeMessage_')!==false:{
                    $msg_id=explode('_',$text )[1];
                    $msg_info=getMessage($msg_id);
                    $sender_id=$msg_info['sender_id'];
                    $message=urldecode($msg_info['message']);
                    $receiver_id=$msg_info['receiver_id'];
                    if($receiver_id==$user_id){
                        $msg=urlencode("#پیام_ناشناس".PHP_EOL.$message.PHP_EOL.PHP_EOL."🤖 @".$bot_username);
                        editMessage($chat_id,$message_id, $msg.inline_btn(array('🚫 بلاک','/blc_'.$msg_id,'↗️ ارسال جواب','/ans_'.$msg_id)));
                        $msg=urlencode($message.PHP_EOL.PHP_EOL."-------------".PHP_EOL."☝️ این پیامت رو دید!".PHP_EOL.PHP_EOL."🤖 @".$bot_username);
                        message($sender_id, $msg);
                    }else{
                        $msg=urlencode("شما اجازه ی این کار را ندارید!");
                        message($chat_id, $msg);
                    }
                }break;

                case strpos($text,'/blc_')!==false:{
                    $msg_id=explode('_',$text )[1];
                    $msg_info=getMessage($msg_id);
                    $sender_id=$msg_info['sender_id'];
                    $receiver_id=$msg_info['receiver_id'];
                    if($receiver_id==$user_id){
                        $msg=urlencode("⚠️ آیا از بلاک کردن این کاربر اطمینان دارید؟");
                        message($chat_id, $msg.inline_btn(array('❌ خیر ❌','/declineBlock','✅ بله ✅','/acceptBlock_'.$sender_id)));
                    }else{
                        $msg=urlencode("شما اجازه ی این کار را ندارید!");
                        message($chat_id, $msg);
                    }
                }break;

                case '/declineBlock':{
                    deleteMessage($chat_id, $message_id);
                    $msg=urlencode("♻️ شما از بلاک کردن این کاربر منصرف شدید!");
                    answer_query($callback_id, $msg,true);
                }break;

                case strpos($text,'/acceptBlock_')!==false:{
                    $blocked_user_id=explode('_',$text )[1];
                    addBlock($user_id,$blocked_user_id);
                    deleteMessage($chat_id, $message_id);
                    $msg=urlencode("✅ کاربر مورد نظر با موفقیت بلاک شد و شما دیگر پیامی از این کاربر دریافت نمیکنید!");
                    answer_query($callback_id, $msg,true);
                }break;

                case strpos($text,'/ans_')!==false:{
                    $msg_id=explode('_', $text)[1];
                    $pay_time=getUser($user_id)['pay_time'];
                    if($pay_time==null or $pay_time<time()){
                        include_once("functions.php");
                        $api = 'test';
                        $amount =5000;
                        $redirect = $bot_directory.'verify.php?id='.$user_id;
                        $factorNumber = time();
                        $result = send($api,$amount,$redirect,$factorNumber);
                        $result = json_decode($result);
                        if($result->status) {
                            $go = "https://pay.ir/payment/gateway/$result->transId";
                            $msg=urlencode("نیاز به فعال کردن عضویت VIP داری!

با دریافت عضویت VIP:

🔸به فرستنده پیام‌های ناشناسی که برات میاد به صورت ناشناس یا با معرفی خودت جواب می‌فرستی

🔸بدون نیاز به عضویت در هیچ کانالی در همه ساعات از برنامه استفاده  می‌کنی

🔸به دوستانت که کاربر برنامه هستند (بیشتر از ۱۰ میلیون نفر) بدون داشتن لینک اختصاصیشون پیام ناشناس می‌فرستی

🔸علاوه بر متن، تصویر، ویدیو، موزیک، وویس یا گیف به صورت ناشناس می‌فرستی

🔸از تیم برنامه حمایت می‌کنی که برنامه‌های بهتری بسازن

امکانات خاص برنامه روز به روز بیشتر و جالب‌تر میشن. پس همین الان لینک زیر رو لمس کن و با فقط ۵ تومن برای خودت سه ماه (90 روز) کامل عضویت نامحدود VIP فعال کن و لذت ببر:

".$go);
                            message($chat_id, $msg);
                        } else {
                            $msg=$result->errorMessage;
                            message($admin_user_id, urlencode($msg));
                        }
                    }else{
                        $sender_id=getMessage($msg_id)['sender_id'];
                        setAnswerID($user_id,$sender_id);
                        $msg=urlencode("🔸 در حال ارسال پاسخ به این کاربر هستی. 

یک پیام رو بفرست که برای کاربر ارسال کننده به عنوان پاسخ فرستاده بشه! ");
                        message($chat_id, $msg,sendMenu());
                        setStep($user_id, 'answer_to_user');
                    }
                }break;

                case '/admin': {
                    if($user_id==$admin_user_id){
                        $markup=array('keyboard'=>array(array('آمار کاربران','تعداد موزیک')),'resize_keyboard'=>true);
                        $markup=json_encode($markup);
                        message($user_id, urlencode('حالت ادمین فعال شد!'),$markup);
                        setStep($user_id, 'admin');
                    }else{
                        message($chat_id, 'دستور یافت نشد!');
                    }
                }
                    break;
            }
        }
            break;

        case 'direct_message':{
            if($text=="لغو و رفتن به منوی اصلی"){
                $msg=urlencode("چه کاری برات انجام بدم؟");
                message($chat_id, $msg,mainMenu());
                setStep($user_id, 'home');
            }else{
                if($update['message']['entities'][0]['type']=='mention'){
                    $user=substr($text,1 );
                    $info=getUserByUsername($user);
                    setReceiver($user_id, $info['hash_id']);
                    if($info['user_id']!=$user_id){
                        $hash_receiver_id=$info['user_id'];
                        $user_data=getUser($hash_receiver_id);
                        $shown_name=$user_data['shown_name'];
                        message($chat_id, urlencode($user_data['on_off']));
                        if($user_data['on_off']==1 and $user_data['privacy']==1){
                            $msg=urlencode("در حال ارسال پیام ناشناس به $shown_name هستی. می‌تونی انتقاد یا هر حرفی که تو دلت هست رو بفرستی چون پیامت به صورت کاملا ناشناس ارسال می‌شه.

منتظر گرفتن متن پیام ازت هستیم... ضمنا اگه عضو VIP باشی می‌تونی به صورت مستقیم و بدون لینک به کاربران پیام ناشناس ارسال کنی.");
                            message($chat_id, $msg,sendMenu());
                            setStep($user_id, 'send_message');
                            setReceiver($user_id,$hash_receiver_id);
                        }else{
                            $msg=urlencode("متاسفانه کاربر مورد نظر سرویس خود را قطع نموده و شما امکان ارسال پیام به این کاربر را ندارید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                            message($chat_id, $msg,mainMenu());
                            setStep($user_id, 'home');
                            setReceiver($user_id, null);
                            setMessage_tmp($user_id, null);
                        }

                    }else{
                        setReceiver($user_id, null);
                        setMessage_tmp($user_id, null);
                        setAnswerID($user_id,null );
                        $msg=urlencode("اینکه ادم گاهی با خودش حرف بزنه خوبه، ولی اینجا نمی‌تونی به خودت پیام ناشناس بفرستی.

چه کاری برات انجام بدم؟");
                        message($chat_id, $msg,mainMenu());
                    }
                }
            }
        }break;

        case 'send_message':{
            switch ($text){
                case 'لغو و رفتن به منوی اصلی':{
                    $msg=urlencode("چه کاری برات انجام بدم؟");
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setReceiver($user_id, null);
                }break;
                case '❌ لغوش کن ❌':{
                    $msg=urlencode("🔸 شما از ارسال پیام به کاربر مورد نظر منصرف شدید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setMessage_tmp($user_id, null);
                    setReceiver($user_id, null);
                }break;

                case '✅ حله بفرست ✅':{
                    $user_data=getUser($user_id);
                    $message=urldecode($user_data['message_tmp']);
                    $receiver_id=getUser($user_data['receiver_id'])['user_id'];
                    if(!isBlockedByUser($receiver_id, $user_id)){
                        $msg_id=addMessage($user_id, $receiver_id,urlencode($message));
                        $msg=urlencode("پیام ناشناس جدید داری!");
                        message($receiver_id, $msg.inline_btn(array('👈 ببینم چیه','/seeMessage_'.$msg_id)));
                        $msg=urlencode("✅ پیام ناشناس با موفقیت ارسال شد!

هنگامی که کاربر مورد نظر شما پیام را ببیند به شما اطلاع داده میشود.".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    }else{
                        $msg=urlencode("🚫 متاسفانه شما توسط کاربر مورد نظر بلاک شده اید و امکان ارسال پیام به این کاربر را ندارید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    }
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setMessage_tmp($user_id, null);
                    setReceiver($user_id, null);
                }break;

                default:{
                    if(getUser($user_id)['message_tmp']==null){
                        $msg_txt=$text;
                        setMessage_tmp($user_id,urlencode($msg_txt));
                        $msg=urlencode($text.PHP_EOL."---------------".PHP_EOL."☝️ پیام تا اینجا ثبت شد.

همین رو بفرستیم یا لفوش کنیم؟");
                        message($chat_id, $msg,acceptMenu());
                    }

                }
            }
        }
            break;

        case 'answer_to_user':{
            switch ($text){
                case 'لغو و رفتن به منوی اصلی':{
                    $msg=urlencode("چه کاری برات انجام بدم؟");
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setAnswerID($user_id, null);
                }break;
                case '❌ لغوش کن ❌':{
                    $msg=urlencode("🔸 شما از ارسال پاسخ به کاربر مورد نظر منصرف شدید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setMessage_tmp($user_id, null);
                    setAnswerID($user_id, null);
                }break;

                case '✅ حله بفرست ✅':{
                    $user_data=getUser($user_id);
                    $message=urldecode($user_data['message_tmp']);
                    $receiver_id=$user_data['answer_to'];
                    if(!isBlockedByUser($receiver_id, $user_id)){
                        $msg_id=addMessage($user_id, $receiver_id,urlencode($message));
                        $msg=urlencode("پاسخ جدید داری!");
                        message($receiver_id, $msg.inline_btn(array('👈 ببینم چیه','/seeMessage_'.$msg_id)));
                        $msg=urlencode("✅ پاسخ به کاربر با موفقیت ارسال شد!

هنگامی که کاربر مورد نظر شما پاسخ را ببیند به شما اطلاع داده میشود.".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    }else{
                        $msg=urlencode("🚫 متاسفانه شما توسط کاربر مورد نظر بلاک شده اید و امکان ارسال پاسخ به این کاربر را ندارید!".PHP_EOL.PHP_EOL."چه کاری برات انجام بدم؟");
                    }
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                    setMessage_tmp($user_id, null);
                    setReceiver($user_id, null);
                }break;

                default:{
                    if(getUser($user_id)['message_tmp']==null){
                        $msg_txt=$text;
                        setMessage_tmp($user_id,urlencode($msg_txt));
                        $msg=urlencode($text.PHP_EOL."---------------".PHP_EOL."☝️ پیام تا اینجا ثبت شد.

همین پاسخ رو به کاربر بفرستیم یا لفوش کنیم؟");
                        message($chat_id, $msg,acceptMenu());
                    }

                }
            }
        }
            break;


        case 'setting':{
            switch ($text){
                case 'نام نمایشی':{
                    $shown_name=getUser($user_id)['shown_name'];
                    $msg=urlencode("الان زمانی که کسی بخواد بهت پیام ناشناس بده با نام « $shown_name » نمایش داده می‌شی. می‌تونی در جواب همین پیام، نام نمایشی دیگه‌ای برای خودت انتخاب کنی:");
                    message($chat_id, $msg,nameMenu());
                    setStep($user_id, 'setting_shownName');
                }break;

                case 'آزادسازی بلاک شده ها':{
                    $msg=urlencode("با لمس دستور /ublAllConfirm همه کسایی که تا الان بلاک کردی می‌تونن دوباره بهت پیام ناشناس بدن.");
                    message($chat_id, $msg);
                }break;

                case 'حریم شخصی':{
                    $status=getUser($user_id)['privacy'];
                    if($status){
                        $msg=urlencode("همیشه می‌تونی تنظیمات حریم شخصیت رو کنترل کنی و تعیین کنی آیا فقط کسایی که لینک اختصاصیت رو بهشون فرستادی بتونند بهت پیام ناشناس بفرستن، یا همه افرادی که تلگرام دارن.

الان همه می‌تونند بهت پیام ناشناس بفرستند. با انتخاب دکمه «دریافت فقط از طریق لینک» می‌تونی محدودش کنی به کسایی که لینک اختصاصیت رو دارند یا بهشون فرستادی.");
                        
                    }else{
                        $msg=urlencode("همیشه می‌تونی تنظیمات حریم شخصیت رو کنترل کنی و تعیین کنی آیا فقط کسایی که لینک اختصاصیت رو بهشون فرستادی بتونند بهت پیام ناشناس بفرستن، یا همه افرادی که تلگرام دارن.

الان فقط کسایی که لینک اختصاصیت رو دارند می‌تونند بهت پیام ناشناس بفرستن. با انتخاب دکمه «دریافت آزاد از همه» می‌تونی اجازه بدی هر کسی که تلگرام داره بهت پیام ناشناس بفرسته.");
                    }
                    message($chat_id, $msg,privacyMenu($status));
                    setStep($user_id, 'setting_privacy');
                }break;

                case 'قطع سرویس':{
                    setOnOff($user_id,0 );
                    $msg=urlencode("ناراحتیم که رفتی اما دیگه پیامی دریافت نمی‌کنی. ممنون که با ما بودی. برای شروع مجدد می‌تونی /start بزنی.");
                    message($chat_id, $msg);
                }break;

                case 'منوی اصلی':{
                    $msg=urlencode("چه کاری برات انجام بدم؟");
                    message($chat_id, $msg,mainMenu());
                    setStep($user_id, 'home');
                }break;

                case 'اطلاعات حساب':{
                    $pay_time=getUser($user_id)['pay_time'];
                    if($pay_time==null or $pay_time<time()){
                        $status="وضعیت عضویت VIP: غیر فعال";
                    }else{
                        $status="وضعیت عضویت VIP: فعال تا ".date("Y-m-d H:i",$pay_time);
                    }
                    message($chat_id, urlencode($status));
                }break;

                case '/ublAllConfirm':{
                    $status=deleteBlock($user_id);
                    $msg=urlencode("حله! آزادسازی همه بلاک‌ شده‌ها با موفقیت انجام شد.");
                    message($chat_id, $msg);
                }
            }
        }
            break;

        case strpos($step, 'setting_'):{
            switch ($step){
                case 'setting_shownName':{
                    if($text=="لغو و بازگشت به منوی تنظیمات"){
                        $msg=urlencode("کنترل حساب:");
                        message($chat_id, $msg,settingMenu());
                        setStep($user_id, 'setting');
                    }else{
                        setShownName($user_id, $text);
                        $msg=urlencode("✅ نام نمایشی شما با موفقیت به « ".$text." » تغییر کرد!".PHP_EOL.PHP_EOL."کنترل حساب:");
                        message($chat_id, $msg,settingMenu());
                        setStep($user_id, 'setting');
                    }
                }break;

                case 'setting_privacy':{
                    if($text=="بازگشت به منوی تنظیمات"){
                        $msg=urlencode("کنترل حساب:");
                        message($chat_id, $msg,settingMenu());
                        setStep($user_id, 'setting');
                    }elseif($text=="دریافت فقط از طریق لینک"){
                        setPrivacy($user_id, 0);
                        $msg=urlencode("حله! الان فقط افرادی که لینک اختصاصیت رو داشته باشند می‌تونند بهت پیام ناشناس بفرستند. همیشه می‌تونی این تنظیمات رو با استفاده از کلید \"حریم شخصی\" تغییر بدی.");
                        message($chat_id, $msg,settingMenu());
                        setStep($user_id, 'setting');
                    }elseif ($text=="دریافت آزاد از همه"){
                        setPrivacy($user_id, 1);
                        $msg=urlencode("حله! الان همه تلگرامی‌ها می‌تونند بهت پیام ناشناس بفرستند. همیشه می‌تونی این تنظیمات رو با استفاده از کلید \"حریم شخصی\" تغییر بدی.");
                        message($chat_id, $msg,settingMenu());
                        setStep($user_id, 'setting');
                    }
                }break;

                case 'setting_shownName':{

                }break;

                case 'setting_shownName':{

                }break;
            }
        }
            break;

        case 'admin':{
            if($user_id==$admin_user_id){
                if($audio!=null){
                    $file_id=$update['message']['audio']['file_id'];
                    $duration=$update['message']['audio']['duration'];
                    $title=$update['message']['audio']['title'];
                    $performer=$update['message']['audio']['performer'];
                    $file_size=$update['message']['audio']['file_size'];
                    $mime_type=$update['message']['audio']['mime_type'];
                    $add=addMusic($file_id, $title, $performer, $duration, $file_size, $mime_type);
                    if($add==true){
                        message($admin_user_id, '✅فایل موزیک با موفقیت اضافه شد✅');
                    }
                }
                switch ($text){
                    case 'آمار کاربران': {
                        action($chat_id, 'typing');
                        $count=getMemberCount();
                        $msg=urlencode("تعداد کاربران ربات شما: ".$count);
                        message($chat_id, $msg);
                    }break;

                    case 'تعداد موزیک': {
                        action($chat_id, 'typing');
                        $count=getMusicCount();
                        $msg=urlencode("تعداد فایل های موزیک شما: ".$count);
                        message($chat_id, $msg);
                    }break;
                }
            }
        }
    }
}