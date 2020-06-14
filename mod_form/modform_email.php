<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

        $mform->addElement('header', 'sharedpanelfieldset_email', 'Email');
        $mform->setExpanded('sharedpanelfieldset_email');

        $mform->addElement('text', 'emailadr1', get_string('form_emailadr1', 'mod_sharedpanel'));
        $mform->setType('emailadr1', PARAM_TEXT);
        $mform->addElement('text', 'emailkey1', get_string('form_emailkey1', 'mod_sharedpanel'));
        $mform->setType('emailkey1', PARAM_TEXT);
        $mform->addElement('text', 'emailhost', get_string('form_emailhost', 'mod_sharedpanel'));
        $mform->setType('emailhost', PARAM_TEXT);
        $mform->addElement('text', 'emailport', get_string('form_emailport', 'mod_sharedpanel'));
        $mform->setType('emailport', PARAM_INT);
        $mform->addElement('advcheckbox', 'emailisssl', get_string('form_emailisssl', 'mod_sharedpanel'));
        $mform->addElement('passwordunmask', 'emailpas1', get_string('password', 'core'));
        $mform->setType('emailpas1', PARAM_TEXT);

?>

</body>
</html>
