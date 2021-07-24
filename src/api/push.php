<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

function handle_fatal_error() {
    global $member;
    $error = error_get_last();
    if($error['type'] == 1) {
        if(!empty($member)) {
            die_with_slack($member, "채점 중 에러가 발생했습니다.\n\n무한루프 또는 메모리 초과일 경우도 여기에 해당합니다.");
        }
    }
}

register_shutdown_function("handle_fatal_error");

if(!isset($_POST['payload'])) {
    response(false, ["message" => "payload를 입력해주세요."]);
}

$payload = json_decode($_POST['payload'], true);

if(!$payload || !is_array($payload)) {
    response(false, ["message" => "비정상적인 payload"]);
}

$is_dev = in_array($payload['repository']['full_name'], $ENGINE_CONFIG['DEV_REPOSITORY']);
$userid = $payload['pusher']['name'];
$member = Member::get_from_github_id($userid);
if(!$member) {
    response(false, ["message" => "알 수 없는 유저"]);
}

if($payload['ref'] !== "refs/heads/{$member->github_id}") {
    if($payload['ref'] === "refs/heads/master")
        exit;
    die_with_slack($member, "본인 브랜치에 푸시하지 않아 무시됩니다.");
}

$file = null;
$added_count = count($payload['head_commit']['added']);
$modified_count = count($payload['head_commit']['modified']);
if($added_count === 1) {
    $file = $payload['head_commit']['added'][0];
} else if($modified_count === 1) {
    $file = $payload['head_commit']['modified'][0];
} else {
    die_with_slack($member, "추가/수정된 파일이 하나가 아닙니다.");
}

$dirs = explode("/", $file);
$ext = null;
if(preg_match("/^{$member->github_id}\.(\w+)$/", $dirs[2], $match)) {
    if(!in_array($match[1], LANGUAGES)) {
        die_with_slack($member, "지원하지 않는 언어입니다.");
    }
    $ext = $match[1];
} else {
    die_with_slack($member, "올바르지 않은 파일명입니다.");
}

$dirname = "{$dirs[0]}/{$dirs[1]}";
$test = Test::get_from_dirname($dirname);

if(!$test) {
    die_with_slack($member, "알 수 없는 테스트입니다.");
}

if(!$test->is_in_time()) {
    die_with_slack($member, "제한 시간 초과된 테스트입니다.");
}


git_pull($member->github_id, $is_dev);
$hash = isolate_code($test->dirname, $member->github_id, $ext, $is_dev);
$result = TestResult::SUCCESS;
$result_test_case = null;
$max_time = 0;

if(compile_code($hash, $member->github_id, $ext)) {
    $test_cases = TestCase::get_from_test($test);
    foreach($test_cases as $test_case) {
        $output = run_code($hash, $member->github_id, $ext, $test_case->input, $err_no, $time);

        if($output === null) {
            if($err_no === 124)
                $result = TestResult::TIMEOUT;
            else
                $result = TestResult::RUNTIME_ERROR;
            $result_test_case = $test_case;
            $max_time = $time;
            break;
        }

        if(str_replace("\r\n", "\n", $output) != str_replace("\r\n", "\n", $test_case->output)) {
            $result = TestResult::FAILED;
            $result_test_case = $test_case;
            $max_time = $time;
            break;
        }

        if($time > $max_time)
            $max_time = $time;
    }
} else {
    $result = TestResult::COMPILE_ERROR;
}

$code = get_code($hash, $member->github_id, $ext);
remove_code($hash);

$result_id = $test->save_result($member, $ext, $code, $result, $max_time, $result_test_case, $is_first);

if(!$result_id) {
    die_with_slack($member, "채점 결과 저장에 오류가 발생하였습니다.");
}

if($is_first && !$test->is_hidden() && !$member->is_hidden) {
    broadcast_success_slack($member->name, $member->slack_id, $test->name, $ext, $test->point, Member::get_rank_with_name());
}

send_test_result_slack($member->slack_id, $test->name, $ext, $result, $max_time, $result_id, $result_test_case === null ? null : $result_test_case->name);

response(true, ["result_id" => $result_id]);
