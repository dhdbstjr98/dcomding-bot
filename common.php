<?php
define("_ENGINE_INCLUDED_", true);
require_once($ENGINE_PATH . "/config.php");

set_time_limit(0);

ini_set("display_errors","on");
error_reporting(E_ALL);

$required_files = array();
$dirs = ["models", "lib"];
foreach($dirs as $dir) {
    $files = dir("{$ENGINE_PATH}/{$dir}");
    while ($entry = $files->read()) {
        if (preg_match("/(\.php)$/i", $entry))
            $required_files[] = "{$dir}/{$entry}";
    }
}
if(count($required_files) > 0) {
    foreach($required_files as $file) {
        require_once($file);
    }
}

require_once($ENGINE_PATH . "/lib/DB.php");

function response($success, $data = null) {
    echo json_encode(["success" => $success, "data" => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function die_with_slack($member, $error) {
    send_error_slack($member->slack_id, $error);
    response(false, ["message" => $error]);
}