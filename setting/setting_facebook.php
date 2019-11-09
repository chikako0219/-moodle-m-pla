<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php


        $settings->add(new admin_setting_heading('sharedpanel/facebook',
        get_string('facebook', 'sharedpanel'), get_string('facebook', 'sharedpanel')));
   
	    $settings->add(new admin_setting_configtext('sharedpanel/FBappID',
        get_string('FBappID', 'sharedpanel'),
        get_string('FBappID_help', 'sharedpanel'), ''));
    
	    $settings->add(new admin_setting_configtext('sharedpanel/FBsecret',
        get_string('FBsecret', 'sharedpanel'),
        get_string('FBsecret_help', 'sharedpanel'), ''));


?>

</body>
</html>
