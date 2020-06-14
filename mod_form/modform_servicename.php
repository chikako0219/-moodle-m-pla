<?php

//タイトル（サービス名）部分
$mform->addElement('header', 'sharedpanelfieldset_servicename', get_string('servicename’, 'mod_sharedpanel'))

//入力部分
$mform->setExpanded('sharedpanelfieldset_servicename');
        $mform->addElement('text', 'newservice_config1', get_string('newservice_config1', 'mod_sharedpanel'));
        $mform->setType('newservice_config1', PARAM_TEXT);
        $mform->addElement('text', 'newservice_config2', get_string('newservice_config2', 'mod_sharedpanel'));
        $mform->setType('newservice_config2', PARAM_TEXT);
