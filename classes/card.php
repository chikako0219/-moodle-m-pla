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

/**
 * Classes for card
 *
 * @package    mod_sharedpanel
 * @copyright  2016 NAGAOKA Chikako, KITA Toshihiro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

namespace mod_sharedpanel;

defined('MOODLE_INTERNAL') || die();

//require_once($CFG->dirroot . '/config.php');


/**
 * Card controller class
 *
 * @package mod_sharedpanel
 */
class card
{

    protected $moduleinstance;
    protected $error;

    /**
     * card constructor.
     * @param $modinstance
     */
    public function __construct($modinstance)
    {
        $this->moduleinstance = $modinstance;
        $this->error = new \stdClass();
        $this->error->code = 0;
        $this->error->message = "";
    }

    /**
     * get card by cardid
     *
     * @param $cardid
     * @return mixed
     * @throws \dml_exception
     */
    public function get($cardid)
    {
        global $DB;
        return $DB->get_record('sharedpanel_cards', ['id' => $cardid]);
    }

    /**
     * get cards
     *
     * @param string $order
     * @return array
     * @throws \dml_exception
     */
    public function gets($order = 'like')
    {
        global $DB, $USER;

        $sql = "
	SELECT *, (SELECT COUNT(*) FROM {sharedpanel_card_likes} l
	WHERE l.userid = :userid AND l.ltype = :ltype AND l.cardid = c.id) likes
	FROM {sharedpanel_cards} c WHERE c.hidden = 0 AND c.sharedpanelid = :moduleinstanceid";

        $ltype = 0;
        if ($order === 'like') {
            $ltype = 0;
            $sql .= " ORDER BY likes DESC, c.timecreated DESC, c.gravity ASC";
        } else if ($order === 'newest') {
            $sql .= " ORDER BY c.timecreated DESC, c.gravity ASC";
        } else if ($order === 'important') {
            $ltype = 1;
            $sql .= " ORDER BY likes DESC, c.timecreated DESC, c.gravity ASC";
        }

        return $DB->get_records_sql($sql, ['userid' => $USER->id, 'ltype' => $ltype, 'moduleinstanceid' => $this->moduleinstance->id]);
    }

    public function get_last_card($inputsrc)
    {
        global $DB;
        $cards = $DB->get_records('sharedpanel_cards',
            ['sharedpanelid' => $this->moduleinstance->id, 'inputsrc' => $inputsrc],
            'id DESC'
        );

        return $cards ? current($cards) : false;
    }

    public static function get_tags($cardid)
    {
        global $DB;
        return $DB->get_records('sharedpanel_card_tags', ['cardid' => $cardid]);
    }

    public function add($content, $sender, $attachment = '', $inputsrc = 'moodle', $messageid = "", $timeupdated = "", $userid = "")
    {

	//require_once('../../../config.php');

        global $DB, $USER, $PAGE;

	var_dump($timeupdated);

        $data = new \stdClass;
        $data->sharedpanelid = $this->moduleinstance->id;

	//mail("t-kita@kumamoto-u.ac.jp","test1","OK?");

        if (empty($userid)) {
            $userid = $USER->id;
        }

        $instance = $DB->get_record('sharedpanel', ['id' => $this->moduleinstance->id], '*', MUST_EXIST);


	//get context
        $sharedpanel = $DB->get_record('sharedpanel', ['id' => $this->moduleinstance->id], '*', MUST_EXIST);
        $course = $DB->get_record('course', ['id' => $sharedpanel->course], '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('sharedpanel', $sharedpanel->id, $course->id, false, MUST_EXIST);

        $context = \context_module::instance($cm->id);

        $data->userid = $userid;

        if (empty($timeupdated)) {
            $data->timeposted = time();
        } elseif ($inputsrc == "line") {
	    $timeupdated1 = new \DateTime($timeupdated);
            $data->timeposted = (int)$timeupdated1->format('U');
        } else {
            $data->timeposted = $timeupdated;
        }

        $data->timecreated = time();
        $data->timemodified = time();

        $data->sender = $sender;
        $data->messageid = $messageid;
        $data->content = $content;
        $data->hidden = 0;
        $data->inputsrc = $inputsrc;
//        $data->attachment_filename = '';
        $data->attachment_filename = $attachment;
        $cards = self::gets();
        if (!$cards) {
            $data->gravity = 0;
        } else {
            $card = end($cards);

            $data->gravity = $card->gravity + 1;
        }

        switch ($inputsrc) {
            /* change "servicename" to your servicename
            case "servicename":
                require_once(dirname(__FILE__)."/event/card_created_servicename.php"); //EVERNOTEのクラスを読み込む
                $event = event\card_created_evernote::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'userid' => $userid,
                    'other' => [
                        'source' => "evernote",
                        'username' => $sender,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
	    */
            case "moodle":
                $event = event\card_created_moodle::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'other' => [
                        'source' => "moodle",
                        'username' => $sender,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            case "facebook":
                $event = event\card_created_facebook::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'other' => [
                        'source' => "facebook",
                        'username' => $sender,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            case "twitter":
                $event = event\card_created_twitter::create([
                   'objectid' => $PAGE->cm->instance,
                   'context' => $PAGE->context,
		   'userid' => $userid,
		   'other' => [
                        'source' => "twitter",
                        'username' => $sender,	
                        'moodleuserid' => $userid,
			'timeposted' => $timeupdated,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            case "email":
                require_once(dirname(__FILE__)."/event/card_created_email.php"); //EMAILのクラスを読み込む
                $event = event\card_created_email::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'userid' => $userid,
                    'other' => [
                        'source' => "email",
                        'username' => $sender,
                        'timeposted' => $timeupdated,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            case "evernote":
                require_once(dirname(__FILE__)."/event/card_created_evernote.php"); //EVERNOTEのクラスを読み込む
                $event = event\card_created_evernote::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'userid' => $userid,
                    'other' => [
                        'source' => "evernote",
                        'username' => $sender,
                        'timeposted' => $timeupdated,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            case "line":
		require_once(dirname(__FILE__)."/event/card_created_line.php"); //LINEのクラスを読み込む
                $event = event\card_created_line::create([
                    'objectid' => $this->moduleinstance->id,                                    			
                    'context' => $context,
		    'userid' => $userid,
                    'other' => [
                        'source' => "line",
                        'username' => $sender,
                        'timeposted' => $data->timeposted,
                        'moodleuserid' => $userid,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
            default:
                $event = event\card_created_moodle::create([
                    'objectid' => $PAGE->cm->instance,
                    'context' => $PAGE->context,
                    'other' => [
                        'username' => $sender,
                        'content' => strip_tags($content)
                    ]
                ]);
                break;
        }


          if ($inputsrc == "line") {

          $event->add_record_snapshot('course', $course);
          //$event->add_record_snapshot("sharedpanel", $data);
          $event->add_record_snapshot("sharedpanel", $this->moduleinstance);
          $event->trigger();


	  $nyanko1 = print_r($course, TRUE);
          $nyanko2 = print_r($this->moduleinstance, TRUE);

	  //mail("t-kita@kumamoto-u.ac.jp","test1","OK?");
          //file_put_contents("/var/www/moodledata/nyanko1.txt", $nyanko1, FILE_APPEND);
          //file_put_contents("/var/www/moodledata/nyanko3.txt", $nyanko2, FILE_APPEND);


          } else {
/*
          $event->add_record_snapshot('course', $PAGE->course);
          $event->add_record_snapshot($PAGE->cm->modname, $instance);
*/
	  print_r("twitter");
	  var_dump($PAGE->course);
          print_r("LINE");
          var_dump($course);
  
          print_r("twitter");
          var_dump($instance);
          print_r("LINE2");
          var_dump($this->moduleinstance);

          $event->trigger();
          }

	//error_log("afterrecord");

	//error_log($data->timeposted);
        //error_log($data->sender);
        //error_log($data->messageid);
        //error_log($data->content);
        //error_log($data->hidden);
        //error_log($data->inputsrc);
        //error_log($data->timemodified);
        //error_log($data->attachment_filename);


        return $DB->insert_record('sharedpanel_cards', $data);
	//error_log("finish");

    }

    public
    function add_attachment($context, $cardid, $content, $filename)
    {
        global $DB;

        $fs = get_file_storage();

        $fileinfo = [
            'contextid' => $context->id,
            'component' => 'mod_sharedpanel',
            'filearea' => 'attachment',
            'itemid' => $cardid,
            'filepath' => '/',
            'filename' => $filename
        ];
        $fs->create_file_from_string($fileinfo, $content);

        $card = self::get($cardid);
        $card->attachment_filename = $filename;

        return $DB->update_record('sharedpanel_cards', $card);
    }

    public
    function add_attachment_by_pathname($context, $cardid, $filepath, $filename)
    {
        global $DB;

        $fs = get_file_storage();

        $fileinfo = [
            'contextid' => $context->id,
            'component' => 'mod_sharedpanel',
            'filearea' => 'attachment',
            'itemid' => $cardid,
            'filepath' => '/',
            'filename' => $filename
        ];
        $fs->create_file_from_pathname($fileinfo, $filepath);

        $card = self::get($cardid);
        $card->attachment_filename = $filename;

        return $DB->update_record('sharedpanel_cards', $card);
    }

    public
    function update($cardid, $content)
    {
        global $DB;

        $data = new \stdClass();
        $data->id = $cardid;
        $data->content = $content;

        return $DB->update_record('sharedpanel_cards', $data);
    }

    public
    function delete($cardid)
    {
        global $DB;

        $card = self::get($cardid);
        $card->hidden = 1;

        return $DB->update_record('sharedpanel_cards', $card);
    }

    public
    function switch_hide_card($cardid)
    {
        global $DB;

        $card = $DB->get_record('sharedpanel_cards', ['id' => $cardid]);

        if ($card->hidden == 1) {
            $card->hidden = 0;
        } else {
            $card->hidden = 1;
        }

        return $DB->update_record('sharedpanel_cards', $card);
    }
}
