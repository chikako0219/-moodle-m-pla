<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

namespace mod_sharedpanel;

use core\notification;
use Facebook\Exceptions\FacebookResponseException;


require_once(dirname(dirname(dirname(__FILE__))) . '/../config.php');
require_once(dirname(__FILE__) . '/../lib.php');
require_once(dirname(__FILE__) . '/../locallib.php');


global $DB, $PAGE, $OUTPUT;

$id = optional_param('id', 0, PARAM_INT);
if ($id) {
    $cm = get_coursemodule_from_id('sharedpanel', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

    $sharedpanel = $DB->get_record('sharedpanel', array('id' => $cm->instance), '*', MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

	
$cardidstwitter = null;

// Twitter.
$twitterobj = new twitter($sharedpanel);
if ($twitterobj->is_enabled()) {
    $cardidstwitter = $twitterobj->import();
    if ($cardidstwitter != false || is_null($cardidstwitter) || is_array($cardidstwitter)) {
        if (count($cardidstwitter) == 0) {
            echo html_writer::message(notification::INFO, get_string('import_twitter_no_tweets', 'mod_sharedpanel'));
        } else {
            $str = new \stdClass();
            $str->source = 'Twitter';
            $str->count = count($cardidstwitter);
            echo html_writer::message(notification::SUCCESS, get_string('import_success', 'mod_sharedpanel', $str));
        }
    } else {
        echo html_writer::message(notification::ERROR, get_string('import_failed', 'mod_sharedpanel', 'Twitter'));
        $error = $twitterobj->get_error();
        debugging($error->code . ":" . $error->message);
    }
} else {
    echo html_writer::message(notification::INFO, get_string('import_no_authinfo', 'mod_sharedpanel', 'Twitter'));
}

?>

</body>
</html>
