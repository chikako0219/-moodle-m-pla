<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
</head>

<body>

<?php

        $mform->addElement('header', 'sharedpanelfieldset_twitter', 'Twitter');
        $mform->setExpanded('sharedpanelfieldset_twitter');
        $mform->addElement('text', 'hashtag1', get_string('form_import_tweet_hashtag', 'mod_sharedpanel'));
        $mform->setType('hashtag1', PARAM_TEXT);

?>

</body>
</html>
