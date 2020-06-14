<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

        $mform->addElement('header', 'sharedpanelfieldset_line', 'LINE');
        $mform->setExpanded('sharedpanelfieldset_line');

        $mform->addElement('text', 'line_channel_id', 'Channel ID');
        $mform->setType('line_channel_id', PARAM_TEXT);
        $mform->addElement('text', 'line_channel_secret', 'Channel secret key');
        $mform->setType('line_channel_secret', PARAM_TEXT);
        $mform->addElement('text', 'line_channel_access_token', 'Channel access token');
        $mform->setType('line_channel_access_token', PARAM_TEXT);

        if ($instanceid) {
            $_SESSION['sharedpanel_instanceid'] = $instanceid;
            $mform->addElement('html', '<h5>Webhook URL</h5>');
            if ((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] === 'on')) {
                $mform->addElement('html',
                    '<div class="well">' . $CFG->wwwroot . '/mod/sharedpanel/line_webhook.php?id=' . $instanceid . '</div>');
            } else {
                $mform->addElement('html',
                    '<div class="well">' . get_string('form_line_warning_https', 'mod_sharedpanel') . '</div>');
            }
        }

?>

</body>
</html>
