<?php
use core\notification;

$twitterobj = new \mod_sharedpanel\twitter($sharedpanel);
if ($twitterobj->is_enabled()) {
    $cardidstwitter = $twitterobj->import();
    if ($cardidstwitter != false || is_null($cardidstwitter) || is_array($cardidstwitter)) {
        if (count($cardidstwitter) == 0) {
            echo \mod_sharedpanel\html_writer::message(notification::INFO, get_string('import_twitter_no_tweets', 'mod_sharedpanel'));
        } else {
            $str = new \stdClass();
            $str->source = 'Twitter';
            $str->count = count($cardidstwitter);
            echo \mod_sharedpanel\html_writer::message(notification::SUCCESS, get_string('import_success', 'mod_sharedpanel', $str));
        }
    } else {
        echo \mod_sharedpanel\html_writer::message(notification::ERROR, get_string('import_failed', 'mod_sharedpanel', 'Twitter'));
        $error = $twitterobj->get_error();
        debugging($error->code . ":" . $error->message);
    }
} else {
    echo \mod_sharedpanel\html_writer::message(notification::INFO, get_string('import_no_authinfo', 'mod_sharedpanel', 'Twitter'));
}
