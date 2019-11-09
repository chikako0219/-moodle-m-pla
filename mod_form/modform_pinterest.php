<?php

//タイトル（サービス名）部分
	//$mform->addElement('header', 'sharedpanelfieldset_pinterest', get_string('pinterest’, 'mod_sharedpanel'));
	$mform->addElement('header', 'sharedpanelfieldset_pinterest', 'Pinterest');


//入力部分
	$mform->setExpanded('sharedpanelfieldset_pinterest');
        $mform->addElement('text', 'newservice_config1', get_string('newservice_config1', 'mod_sharedpanel'));
        $mform->setType('newservice_config1', PARAM_TEXT);
        $mform->addElement('text', 'newservice_config2', get_string('newservice_config2', 'mod_sharedpanel'));
        $mform->setType('newservice_config2', PARAM_TEXT);
