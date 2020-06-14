<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

        $mform->addElement('header', 'sharedpanelfieldset_evernote', 'Evernote');
        $mform->setExpanded('sharedpanelfieldset_evernote');

        $mform->addElement('text', 'emailadr2', get_string('form_emailadr2', 'mod_sharedpanel'));
        $mform->setType('emailadr2', PARAM_TEXT);
        $mform->addElement('passwordunmask', 'emailpas2', get_string('form_emailpas2', 'mod_sharedpanel'));
        $mform->setType('emailpas2', PARAM_RAW);
        $mform->addElement('text', 'emailkey2', get_string('form_emailkey2', 'mod_sharedpanel'));
        $mform->setType('emailkey2', PARAM_TEXT);

?>

</body>
</html>
