<?php
use core\notification;

//importメソッドの呼び出し部分
$pinterestobj = new \mod_sharedpanel\pinterest($sharedpanel);
if ($pinterestobj->is_enabled()) {
    $cardidspinterest = $pinterestobj->import();
    if ($cardidspinterest != false || is_null($cardidspinterest) || is_array($cardidspinterest)) {
        //カードの件数が0であれば，0件と表示
        if (count($cardidspinterest) == 0) {
            //Please change tweets to posts/mail/feeds etc...
            echo \mod_sharedpanel\html_writer::message(notification::INFO,  get_string('import_twitter_no_tweets', 'mod_sharedpanel'));
        } else {
        //新規カードがあれば，読み込み成功と表示
            $str = new \stdClass();
            $str->source = 'pinterest';
            $str->count = count($cardidspinterest);
            echo \mod_sharedpanel\html_writer::message(notification::SUCCESS, get_string('import_success', 'mod_sharedpanel', $str));
        }
    } else {
   	 //それ以外については，エラーを表示
        echo \mod_sharedpanel\html_writer::message(notification::ERROR, get_string('import_failed', 'mod_sharedpanel', 'pinterest'));
        $error = $pinterestobj->get_error();
        debugging($error->code . ":" . $error->message);
    }
} else {
    //認証失敗を表示 
   echo \mod_sharedpanel\html_writer::message(notification::INFO, get_string('import_no_authinfo', 'mod_sharedpanel', 'pinterest'));
}
