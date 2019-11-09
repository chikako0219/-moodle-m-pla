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

// ネームスペースの規定
namespace mod_sharedpanel;

defined('MOODLE_INTERNAL') || die();
use WebDriver\Exception;

// 外部サービスの接続等に必要なファイルの呼び出し
/* Twitterの例
use Abraham\TwitterOAuth\TwitterOAuth;
require_once(__DIR__ . '/../lib/twitteroauth/autoload.php');
*/

// クラスcardの拡張
class servicename extends card
{
    // この外部サービスが有効になっているかの判定
    public function is_enabled() {
        /* Twitterの例
        $config = get_config('sharedpanel');
        if (empty($config->TWconsumerKey) ||
            empty($config->TWconsumerSecret) ||
            empty($config->TWaccessToken) ||
            empty($config->TWaccessTokenSecret)) {
            return false;
        } else {
            return true;
        }
        */
        return true; // 有効にする
    }
    
    // importメソッド．
    public function import() {
        $config = get_config('sharedpanel'); // サイト全体で共通のSharedpanel設定

	// Sharedpanelインスタンスごとの設定
        $config1 = $this->moduleinstance->newservice_config1;
        $config2 = $this->moduleinstance->newservice_config2;

        /* Twitterの例
        $connection = new TwitterOAuth(
            trim($config->TWconsumerKey),
            trim($config->TWconsumerSecret),
            trim($config->TWaccessToken),
           trim($config->TWaccessTokenSecret)
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
        $cond = ["q" => $this->moduleinstance->hashtag1, 'count'=>'10', "include_entities"=>true];
        $tweets = $connection->get("search/tweets", $cond);
        if (property_exists($tweets, 'errors') || !$tweets->statuses) {
            return null;
        }
	*/

        $cardobj = new card($this->moduleinstance); // cardクラスで新しいインスタンスを作成
        $cardids = [];
        // 外部サービスから取得したデータを cardクラスのaddメソッドで格納
	/* Twitterの例
        foreach ($tweets->statuses as $tweet) {
            $content  = mod_sharedpanel_utf8mb4_encode_numericentity($tweet->text);
            $username = mod_sharedpanel_utf8mb4_encode_numericentity($tweet->user->name);
	    $name_of_service = 'twitter';
	    $data_id = $tweet->id;
	    $unixtime = strtotime($tweet->created_at);
            $cardids[] = $cardobj->add($content, $username, $name_of_service, $data_id, $unixtime);
        }
	*/
        return $cardids;
    }

    // エラー表示．修正不要．
    public function get_error() {
        return $this->error;
    }
}
