<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

    $settings->add(new admin_setting_heading('sharedpanel/twitter',
        get_string('twitter', 'sharedpanel'), get_string('twitter', 'sharedpanel')));
    $settings->add(new admin_setting_configtext('sharedpanel/TWconsumerKey',
        get_string('TWconsumerKey', 'sharedpanel'),
        get_string('TWconsumerKey_help', 'sharedpanel'), ''));

    $settings->add(new admin_setting_configtext('sharedpanel/TWconsumerSecret',
        get_string('TWconsumerSecret', 'sharedpanel'),
        get_string('TWconsumerSecret_help', 'sharedpanel'), ''));

    $settings->add(new admin_setting_configtext('sharedpanel/TWaccessToken',
        get_string('TWaccessToken', 'sharedpanel'),
        get_string('TWaccessToken_help', 'sharedpanel'), ''));

    $settings->add(new admin_setting_configtext('sharedpanel/TWaccessTokenSecret',
        get_string('TWaccessTokenSecret', 'sharedpanel'),
        get_string('TWaccessTokenSecret_help', 'sharedpanel'), ''));

?>

</body>
</html>
