<?php
require_once("./_common.php");

if(!isset($_GET['api'])) {
    response(false, ["message" => "API를 입력해주세요."]);
}

$api_path = $ENGINE_PATH . "/api/{$_GET['api']}.php";

if(!file_exists($api_path)) {
    response(false, ["message" => "존재하지 않는 API"]);
}

require($api_path);