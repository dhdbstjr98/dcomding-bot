# dcomding-bot
GitHub webhook과 Slack을 연동한 코딩테스트 자동 채점 프로그램입니다.

## Usage
1. dcomding-bot.sql 실행
2. 소스 폴더에 config.php를 만들어 다음과 같이 추가한 후 내용 입력
```php
<?php
$ENGINE_CONFIG = [];

$ENGINE_CONFIG['DB'] = [
    'HOST' => '',
    'USER' => '',
    'PASSWORD' => '',
    'DB' => '',
];

$ENGINE_CONFIG['SLACK'] = [
    'TOKEN' => '',
    'CHANNEL' => '',
];

$ENGINE_CONFIG['DEV_REPOSITORY'] = [
    ""
];
```
