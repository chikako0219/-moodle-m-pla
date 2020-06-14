<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once(__DIR__ . "../../lib/Facebook/autoload.php");

        // Facebook.
        $fb = new \Facebook\Facebook([
            'app_id' => $config->FBappID,
            'app_secret' => $config->FBsecret
        ]);

        $mform->addElement('header', 'sharedpanelfieldset_facebook', 'Facebook');
        $mform->setExpanded('sharedpanelfieldset_facebook');

        $mform->addElement('text', 'fbgroup1', get_string('form_fbgroup1', 'mod_sharedpanel'));
        $mform->setType('fbgroup1', PARAM_TEXT);

        $mform->addElement('html', '<h5>Facebook User Access Token</h5>');

        if ($instance) {
            $mform->addElement('html',
                '<div class="well">' . get_string('facebook_get_user_access_token_msg', 'mod_sharedpanel') . '</div>');

            if ($instance->fbuseraccesstoken) {
                $mform->addElement('html',
                    '<div class="well">' . get_string('facebook_get_user_access_token_ok', 'mod_sharedpanel') . '</div>');
            } else {
                $mform->addElement('html',
                    '<div class="well">' . get_string('facebook_get_user_access_token_notyet', 'mod_sharedpanel') . '</div>');
            }
            $callback = new moodle_url($CFG->wwwroot . '/mod/sharedpanel/facebook_login.php');
            $helper = $fb->getRedirectLoginHelper();
            $url = new moodle_url($helper->getLoginUrl($callback->out(true), ['user_managed_groups']));
            $action = new \popup_action("click", $url, ["width" => "600px"]);
            $mform->addElement('html',
                $OUTPUT->action_link($url->out(),
                    get_string('facebook_get_user_access_token', 'mod_sharedpanel'),
                    $action,
                    ["class" => "btn btn-success"])
            );
        } else {
            $mform->addElement('html',
                '<div class="well">' . get_string('facebook_get_user_access_token_msg_reload', 'mod_sharedpanel') . '</div>');
        }

?>

</body>
</html>
