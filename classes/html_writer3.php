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

use core\notification;

class html_writer3 extends \html_writer
{
    /**
     * Return message box
     *
     * @param string $type
     * @param string $content
     * @return string
     */
    public static function message($type, $content) {
        switch ($type) {
            case notification::SUCCESS:
                return \html_writer::div($content, 'alert alert-success');
            case notification::ERROR:
                return \html_writer::div($content, 'alert alert-error');
            case notification::WARNING:
                return \html_writer::div($content, 'alert');
            case notification::INFO:
                return \html_writer::div($content, 'alert alert-info');
        }
    }

    /**
     * Return user icon and link
     *
     * @param $userid
     * @return string
     * @throws \dml_exception
     * @throws \moodle_exception
     */

    public static function card($instance, $context, $card) {
        global $USER, $OUTPUT;

        $html = \html_writer::start_div('card span1 col-md-3', ['id' => 'card' . $card->id]);

        if (has_capability('moodle/course:manageactivities', $context)) {
            $dellink = new \moodle_url('deletecard.php', ['id' => $context->instanceid, 'c' => $card->id, 'sesskey' => sesskey()]);
            $icon = $OUTPUT->action_icon($dellink, new \pix_icon('t/delete', ''));
            $html .= self::span($icon, 'card-icon-del');
        }
		
		//コンテンツ，投稿日時，送信者を$htmlへ挿入します
	$html .= self::start_tag('table',array('class' => 'sharedpanel_table1'));

	$html .= self::start_tag('tr',array("border" => "2"));

	$html .= self::start_tag('td',array("border" => "2","width" => '100px'));
        $html .= self::span($card->sender);
        $html .= self::end_tag('td');

	$html .= self::start_tag('td',array("border" => "2","width" => '150px'));
        $html .= self::span(" from " . $card->inputsrc);
        $html .= self::end_tag('td');

	$html .= self::start_tag('td',array("border" => "2","width" => '200px'));
        $html .= self::span(userdate($card->timeposted));
        $html .= self::end_tag('td');

	$html .= self::start_tag('td',array("border" => "2","width" => '200px'));
        $html .= self::span($card->attachment_filename);
        $html .= self::end_tag('td');

	$html .= self::start_tag('td',array("border" => "2","width" => '300px'));
        $html .= self::span($card->content);
        $html .= self::end_tag('td');

	$html .=self::end_tag('tr');
	
	/*
        $html .= self::span($card->content);
        $html .= self::span(
            '<br/><br/>' . userdate($card->timeposted) . "<br/>" . $card->sender . "<br/> from " . $card->inputsrc);
        */

/*
        // If attachment exists.
        if (!is_null($card->attachment_filename) && !empty($card->attachment_filename)) {
            $fs = get_file_storage();
            $file = $fs->get_file(
                $context->id,
                'mod_sharedpanel',
                'attachment',
                $card->id,
                '/',
                $card->attachment_filename
            );


            // 画像があった場合の処理
            if ($file->get_mimetype() === 'image/png' ||
                $file->get_mimetype() === 'image/jpeg' ||
                $file->get_mimetype() === 'image/gif') {
                $url = \moodle_url::make_pluginfile_url(
                    $context->id,
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename()
                );
                $html .= self::span(
                    self::empty_tag('img', ['src' => $url->out(), 'class' => 'card-body-attachment'])
                );
            } else {
                $url = \moodle_url::make_pluginfile_url(
                    $context->id,
                    $file->get_component(),
                    $file->get_filearea(),
                    $file->get_itemid(),
                    $file->get_filepath(),
                    $file->get_filename(),
                    true
                );
                $html .= self::span(self::link($url, get_string('download'), ['class' => 'btn btn-success']));
            }
        }
*/
        $html .= self::end_div();
	$html .= self::end_tag('table');
        return $html;
    }
}
