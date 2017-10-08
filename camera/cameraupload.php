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
//
require_once('../../../config.php');

$id = required_param('id', PARAM_INT);
$sharedpanelid = optional_param('n', 0, PARAM_INT);

$cameracomment = required_param('cameracomment', PARAM_TEXT);
$cameracomment = htmlspecialchars($cameracomment, ENT_QUOTES);

$name = required_param('name', PARAM_TEXT);
$name = htmlspecialchars($name, ENT_QUOTES);

header("Refresh: 3; URL=" . $CFG->wwwroot . "/mod/sharedpanel/view.php?id=" . $id);
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<?php
if ($CFG->dbtype == "mysqli") {
    $dbtype = "mysql";
} else {
    $dbtype = $CFG->dbtype;
}
$db = new PDO($dbtype . ':dbname=' . $CFG->dbname . ';host=' . $CFG->dbhost, $CFG->dbuser, $CFG->dbpass);
$db->query("SET NAMES utf8");
$cardid = mod_sharedpanel_get_sharedpanel_cardid();

$sql2 = "insert into " . $CFG->prefix . "sharedpanel_cards " . "(id, sharedpanelid, userid, rating, content, comment, hidden, timeposted, timecreated, timemodified, inputsrc, messageid, sender, positionx, positiony)" . " values (?" . str_repeat(", ?", 14) . ")";
$stmt = $db->prepare($sql2);
$time = time();
$z = 0;
$s = "";
$inputsrc = "camera";
if (is_uploaded_file($_FILES["capture"]["tmp_name"])) {
    $ret1 = "";
    if ($cameracomment) {
        $ret1 .= $cameracomment . "<br/><br/>";
    }
    $ret1 .= "<img src='data:image/gif;base64,";
    $ret1 .= rotatecompress_img($_FILES["capture"]["tmp_name"], 600);
    $ret1 .= "' width=85%><br/>";
    $stmt->bindParam(1, $cardid);
    $stmt->bindParam(2, $sharedpanelid); // sharedpanelid
    $stmt->bindParam(3, $z); // userid
    $stmt->bindParam(4, $z); // rating
    $stmt->bindParam(5, $cardid); // content
    $stmt->bindParam(5, $ret1); // content
    $stmt->bindParam(6, $s); // comment
    $stmt->bindParam(7, $z); // hidden
    $stmt->bindParam(8, $time); // post
    $stmt->bindParam(9, $time); // create
    $stmt->bindParam(10, $time); // modify
    $stmt->bindParam(11, $inputsrc); // inputsrc
    $stmt->bindParam(12, $z); // messageid
    $stmt->bindParam(13, $name); // sender
    $stmt->bindParam(14, $z); // positionx
    $stmt->bindParam(15, $z); // positiony
    $db->beginTransaction();
    $stmt->execute();
    $db->commit();
    echo "アップロードに成功しました<br />";
} else {
    if ($cameracomment != "") {
        $ret1 = $cameracomment;
        $stmt->bindParam(1, $cardid);
        $stmt->bindParam(2, $sharedpanelid); // sharedpanelid
        $stmt->bindParam(3, $z); // userid
        $stmt->bindParam(4, $z); // rating
        $stmt->bindParam(5, $cardid); // content
        $stmt->bindParam(5, $ret1); // content
        $stmt->bindParam(6, $s); // comment
        $stmt->bindParam(7, $z); // hidden
        $stmt->bindParam(8, $time); // post
        $stmt->bindParam(9, $time); // create
        $stmt->bindParam(10, $time); // modify
        $stmt->bindParam(11, $inputsrc); // inputsrc
        $stmt->bindParam(12, $z); // messageid
        $stmt->bindParam(13, $name); // sender
        $stmt->bindParam(14, $z); // positionx
        $stmt->bindParam(15, $z); // positiony
        $db->beginTransaction();
        $stmt->execute();
        $db->commit();
        echo "メッセージを登録しました<br />";
    }
}
echo "<br/><a href='../view.php?id=$id'>カード一覧を表示する</a>";
?>

</body>
</html>

<?php
function rotatecompress_img($imgname, $width) {
    $imagea = imagecreatefromjpeg($imgname);
    $exif_data = exif_read_data($imgname);
    if (isset($exif_data['Orientation']) && $exif_data['Orientation'] == 6) {
        $imagea = imagerotate($imagea, 270, 0);
    }
    $imagea = imagescale($imagea, $width, -1); // proportionally compress image with $width
    $jpegfile = tempnam("/tmp", "email-jpg-");
    imagejpeg($imagea, $jpegfile);
    imagedestroy($imagea);
    $attached = base64_encode(file_get_contents($jpegfile));
    unlink($jpegfile);
    return $attached;
}

?>
