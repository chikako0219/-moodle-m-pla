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
class pinterest extends card
{
    // この外部サービスが有効になっているかの判定
    /*
    public function is_enabled() {
        $config = get_config('sharedpanel');
        if (empty($config->newservice_config1) ||
            empty($config->newservice_config2)) {
            return false;
        } else {
            return true;
        }
        return true; // 有効にする
    }
    */
    public function is_enabled()
    {
        $config = get_config('sharedpanel');
        return true;
    }

    // importメソッド．
    public function import()
    {
        $config = get_config('sharedpanel'); // サイト全体で共通のSharedpanel設定

        // Sharedpanelインスタンスごとの設定
        $config1 = $this->moduleinstance->newservice_config1;
        $config2 = $this->moduleinstance->newservice_config2;

//	$api = 'https://api.pinterest.com/v1/boards/'.$config1.'/pins/?access_token='.$config2.'&fields=id%2Clink%2Cnote%2Curl%2Cimage%2Ccreator%2Ccreated_at';
        $api = 'https://api.pinterest.com/v1/boards/chikakonagaoka0219/testboard/pins/?access_token=AlCi1RiDQFc8fqLZIKBF8WdHsCGMFXASxhX8dlhFdl3MluBm-QtUQDAAAeHqRXbl5IIgexIAAAAA&fields=id%2Clink%2Cnote%2Curl%2Cimage%2Ccreator%2Ccreated_at';

        $json = file_get_contents($api);
        $pdata = json_decode($json);

        $cardobj = new card($this->moduleinstance); // cardクラスで新しいインスタンスを作成
        $cardids = [];
        // 外部サービスから取得したデータを cardクラスのaddメソッドで格納
        foreach ($pdata->data as $pin) {
            $note = mod_sharedpanel_utf8mb4_encode_numericentity($pin->note); //投稿文
            $username = mod_sharedpanel_utf8mb4_encode_numericentity($pin->creator->first_name); //投稿者
            $name_of_service = 'pinterest'; //投稿媒体
            $data_id = $pin->id; //投稿ID
            $unixtime = strtotime($pin->created_at); //投稿日時

            $image_url = $pin->image->original->url;
            $image = file_get_contents($image_url);
            $content = "<img src='data:image/jpg;base64," . base64_encode($image) . "'width=200 height=200><br>";
//            $cardids[] = $cardobj->add($content, $username, $name_of_service, $note, $unixtime);
            $cardids[] = $cardobj->add($note, $username, $content, $name_of_service, $data_id, $unixtime);


        }
        return $cardids;
    }

    // エラー表示．修正不要．
    public function get_error()
    {
        return $this->error;
    }
}
