<?php
function send_slack($channel, $texts) {
    global $ENGINE_CONFIG;

    $blocks = [];
    foreach($texts as $text) {
        $blocks[] = [
            "type" => "section",
            "text" => [
                "type" => "mrkdwn",
                "text" => $text
            ]
        ];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://slack.com/api/chat.postMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "text={$texts[0]}&channel={$channel}&blocks=" . json_encode($blocks, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$ENGINE_CONFIG['SLACK']['TOKEN']}"]);
    $res = json_decode(curl_exec($ch), true);

    // 최대한 모델과 라이브러리는 분리하려 했지만 이건 모델의 도움을 받아야겠음
    SlackLog::push($channel, $blocks);

    return $res['ok'] === 1;
}

function broadcast_success_slack($member_name, $slack_id, $test_name, $point, $rank) {
    global $ENGINE_CONFIG;

    $texts = [];
    $texts[] = "*정답!*" . PHP_EOL . "{$member_name}(<@{$slack_id}>) 님이 `{$test_name}` 문제를 맞추셨습니다!! ({$point}p 획득)";

    $texts[] = format_rank_slack($rank);

    send_slack($ENGINE_CONFIG['SLACK']['CHANNEL'], $texts);
}

function format_rank_slack($rank) {
    $rank_text = '*랭킹 안내*' . PHP_EOL;
    for($i = 0; $i < count($rank); $i++) {
        $rank_text .= ($i + 1) . ". {$rank[$i]['name']} ({$rank[$i]['sum_point']}p)" . PHP_EOL;
    }
    return $rank_text;
}

function send_test_result_slack($channel, $test_name, $result, $result_id, $test_case = null) {
    $texts = [];
    $texts[] = "*채점 결과* (#{$result_id})" . PHP_EOL . "`{$test_name}` {$result}";
    if($test_case !== null) {
        $texts[] = "관련 테스트 케이스 : {$test_case}";
    }

    send_slack($channel, $texts);
}

function send_error_slack($channel, $error) {
    $texts = [];
    $texts[] = "*오류 안내*" . PHP_EOL . $error;

    send_slack($channel, $texts);
}