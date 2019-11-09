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

use Abraham\TwitterOAuth\TwitterOAuth;
use WebDriver\Exception;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib/twitteroauth/autoload.php');

class twitter extends card
{
    public function is_enabled()
    {
        $config = get_config('sharedpanel');

        /*
                if (empty($config->TWconsumerKey) ||
                    empty($config->TWconsumerSecret) ||
                    empty($config->TWaccessToken) ||
                    empty($config->TWaccessTokenSecret)) {
                    return false;
                } else {
                    return true;
                }
        */
        return true;
    }

    public function import()
    {
        global $DB;

        $config = get_config('sharedpanel');
        $connection = new TwitterOAuth(
            trim("kjgrOLpTVMdHew6C1yeI3SchG"),
            trim("jKEw6nKwx8MT4aZBaGyYJhjpWw2OdZ8oOicGZ4r3KzXueDA31b"),
            trim("1871610955-xj4zWJNJlK9sNEL1FdkpZXvDtAEDH4w4m0qF0Cv"),
            trim("1srFcqxrst4jER6Vwf7pmmfYpKSQUbTXiKMWOkxrt76iu")
        /*
                    trim($config->TWconsumerKey),
                    trim($config->TWconsumerSecret),
                    trim($config->TWaccessToken),
                    trim($config->TWaccessTokenSecret)
        */
        );

        try {
            $credentials = $connection->get("account/verify_credentials");
        } catch (Exception $e) {
            return false;
        }

        if (property_exists($credentials, 'errors')) {
            $this->error->code = $credentials->errors[0]->code;
            $this->error->message = $credentials->errors[0]->message;

            return false;
        }

        $cond = ["q" => $this->moduleinstance->hashtag1, 'count' => '3', "include_entities" => true];

        $latestcard = self::get_last_card('twitter');
        if ($latestcard) {
            $cond['since_id'] = $latestcard->messageid;
        }
        $tweets = $connection->get("search/tweets", $cond);
        if (property_exists($tweets, 'errors') || !$tweets->statuses) {
            return null;
        }

        $cardobj = new card($this->moduleinstance);

        $cardids = [];
        foreach ($tweets->statuses as $tweet) {
//            var_dump($tweet);
            $content = $tweet->text;
            $content = mod_sharedpanel_utf8mb4_encode_numericentity($content);
            $username = mod_sharedpanel_utf8mb4_encode_numericentity($tweet->user->name);

            $attachment = $tweet->entities->media[0]->media_url;
            $image = file_get_contents($attachment);
            $attachment = "<img src='data:image/jpg;base64," . base64_encode($image) . "'width=200 height=200><br>";


            /* 以下、Pinterestでうまくいったやつ

                        $image_url = $pin->image->original->url;
                        $image = file_get_contents($image_url);
                        $content = "<img src='data:image/jpg;base64,".base64_encode($image)."'width=200 height=200><br>";
            */

            $user_info_field_id = $DB->get_record('user_info_field', ['shortname' => 'sharedpanel_twitter'])->id;
            $user_info_field_data = $DB->get_records_sql('
                    SELECT *
                      FROM {user_info_data}
                     WHERE fieldid = ?
                       AND ' . $DB->sql_compare_text('data', 255) . ' = ' . $DB->sql_compare_text('?', 255),
                array($user_info_field_id, $username));

            if ($user_info_field_data) {
                $cardids[] = $cardobj->add($content, $user_info_field_data, $attachment, 'twitter', $tweet->id, strtotime($tweet->created_at));
            } else {
                $cardids[] = $cardobj->add($content, 0, $attachment, 'twitter', $tweet->id, strtotime($tweet->created_at));
            }
        }

        return $cardids;
    }

    public function get_error()
    {
        return $this->error;
    }
}
