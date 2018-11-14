<?php

foreach ($cards as $card) {
    echo \mod_sharedpanel\html_writer::card($sharedpanel, $context, $card);
}

?>
