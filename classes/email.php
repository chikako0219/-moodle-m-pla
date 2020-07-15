<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace mod_sharedpanel;

defined('MOODLE_INTERNAL') || die();

class email extends card
{
    protected $moduleinstance;

    private $emailaddr;
    private $emailpassword;

    private $emailport;

    private $cardobj;

    public function __construct($modinstance) {
        $this->moduleinstance = $modinstance;
        $this->emailaddr = $modinstance->emailadr1;
        $this->emailpassword = $modinstance->emailpas1;
        $this->emailport = $modinstance->emailport;
        $this->cardobj = new card($modinstance);

        parent::__construct($modinstance);
    }

    public function is_enabled() {
        if (empty($this->moduleinstance->emailhost) ||
            empty($this->moduleinstance->emailport) ||
            empty($this->emailaddr) ||
            empty($this->emailpassword)) {
            return false;
        }
        return true;
    }

    public function get($date = null) {
        global $DB;

        $cond = [
            'inputsrc' => 'email',
            'sharedpanelid' => $this->moduleinstance->id,
            'hidden' => 0
        ];
        if (!is_null($date)) {
            $cond['timeposted'] = $date;
        }

        return $DB->get_record('sharedpanel_cards', $cond);
    }

    public function is_exists($date = null) {
        global $DB;

        $cond = [
            'inputsrc' => 'email',
            'sharedpanelid' => $this->moduleinstance->id,
            'hidden' => 0
        ];
        if (!is_null($date)) {
            $cond['timeposted'] = $date;
        }

        return $DB->record_exists('sharedpanel_cards', $cond);
    }

    public function import() {
        global $DB, $USER;

        if ($this->moduleinstance->emailisssl === '1') {
            $mailbox = '{' . $this->moduleinstance->emailhost .
                ':' .
                $this->moduleinstance->emailport .
                '/novalidate-cert/imap/ssl}' .
                "INBOX";
        } else {
            $mailbox = '{' . $this->moduleinstance->emailhost .
                ':' .
                $this->moduleinstance->emailport .
                '/novalidate-cert/imap}' .
                "INBOX";
        }

        $mbox = imap_open($mailbox, $this->emailaddr, $this->emailpassword, OP_READONLY);

        if (!$mbox) {
            $this->error->message = imap_last_error();
            return false;
        }
	   $messageids = imap_search($mbox,'ALL',SE_UID);
	
        if (!$messageids) {
            return null;
        }
        $cardids = [];
        foreach ($messageids as $num => $messageid) {
            if ($DB->record_exists('sharedpanel_cards', ['messageid' => $messageid])) {
                continue;
            }

            $num++;
            $head = imap_headerinfo($mbox, $num);
	    $body = imap_fetchbody($mbox, $num, 1, FT_INTERNAL);
            $body = trim($body);
	    //var_dump($body);
	    $structure = imap_fetchstructure($mbox, $num);
            //var_dump($structure);
            $subject = mb_convert_encoding(imap_base64($body), 'utf-8', 'auto');
            $title = trim($head->subject);
	    var_dump($title);		

//attachment処理
$attachments = array();
if(isset($structure->parts) && count($structure->parts)) {

	for($i = 0; $i < count($structure->parts); $i++) {

		$attachments[$i] = array(
			'is_attachment' => false,
			'filename' => '',
			'name' => '',
			'attachment' => ''
		);
		
		if($structure->parts[$i]->ifdparameters) {
			foreach($structure->parts[$i]->dparameters as $object) {
				//var_dump($object);
				
				if(strtolower($object->attribute) == 'filename') {
					$attachments[$i]['is_attachment'] = true;
					$attachments[$i]['filename'] = $object->value;
				}
			}
		}
		
		if($structure->parts[$i]->ifparameters) {
			foreach($structure->parts[$i]->parameters as $object) {
				if(strtolower($object->attribute) == 'name') {
					$attachments[$i]['is_attachment'] = true;
					$attachments[$i]['name'] = $object->value;
				}
			}
		}	
		
		if($attachments[$i]['is_attachment']) {
			$attachments[$i]['attachment'] = imap_fetchbody($mbox, $num, $i+1);
			if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
				//$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
				 $attachments = "<img src='data:image/jpg;base64,".$attachments[$i]['attachment']."' width=200 height=200><br>";
				//var_dump($attachments);
			}
			elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
				$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
			}
		}
	}
}
            
            // If sender is Evernote
            if (strpos($head->from[0]->host, "evernote.com") !== false) {
	      $encodedData = str_replace(' ','+',$body);
	      $body = base64_decode($encodedData);

              $user_info_field_id = $DB->get_record('user_info_field', ['shortname' => 'sharedpanelevernote'])->id;
              $user_info_field_data = $DB->get_records_sql('

                    SELECT *
                      FROM {user_info_data}
                     WHERE fieldid = ?
                      AND ' . $DB->sql_compare_text('data', 255) . ' = ' . $DB->sql_compare_text('?', 255),
              array($user_info_field_id, $head->from[0]->personal));


              foreach ($user_info_field_data as $key => $val){
                $fielduserid = print_r($user_info_field_data[$key]->userid, TRUE);
                //file_put_contents("/var/www/moodledata/nyanko5.txt", $neko, FILE_APPEND);
                $user_info_field_data = $fielduserid;
              }

              $cardid = $this->cardobj->add($body, $head->fromaddress, $attachment, 'evernote', $messageid, strtotime($head->date),$user_info_field_data);

	      continue;
            }

           
	    // If sender is email
            if (strpos($head->from[0]->host, "evernote.com") !== true && strpos($title, "morimori") !== false) {

              $user_info_field_id = $DB->get_record('user_info_field', ['shortname' => 'sharedpanelemail'])->id;
              $user_info_field_data = $DB->get_records_sql('
                    SELECT *
                      FROM {user_info_data}
                     WHERE fieldid = ?
                      AND ' . $DB->sql_compare_text('data', 255) . ' = ' . $DB->sql_compare_text('?', 255),
              array($user_info_field_id, $head->from[0]->personal));

              foreach ($user_info_field_data as $key => $val){
                $fielduserid = print_r($user_info_field_data[$key]->userid, TRUE);
                //file_put_contents("/var/www/moodledata/nyanko5.txt", $neko, FILE_APPEND);
                $user_info_field_data = $fielduserid;
              }


              //$user_info_field_data = $user_info_field_data[3]->userid;

              $cardid = $this->cardobj->add($body, $head->fromaddress, $attachment, 'email', $messageid, strtotime($head->date),$user_info_field_data);

	      continue;
            }


            $cardids[] = $cardid;
            foreach (mod_sharedpanel_get_tags($body) as $tagstr) {
                $tagobj = new tag($this->moduleinstance);
                $tagobj->set($cardid, $tagstr, $USER->id);
            }
        }

        return $cardids;
    }

    public function get_error() {
        return $this->error;
    }
}
