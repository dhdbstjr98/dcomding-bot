<?php
const LANGUAGES = ["cpp", "c", "js", "py"];

function git_pull($github_id, $is_dev = false) {
    global $ENGINE_PATH;

    $code_dir = $is_dev ? "code_dev" : "code";
    exec("cd {$ENGINE_PATH}/../{$code_dir}/{$github_id} && git pull origin {$github_id}");
}

function isolate_code($dirname, $github_id, $ext, $is_dev = false) {
    global $ENGINE_PATH;

    $code_dir = $is_dev ? "code_dev" : "code";
    $file = file_get_contents("{$ENGINE_PATH}/../{$code_dir}/{$github_id}/{$dirname}/{$github_id}.{$ext}");
    $hash = substr(sha1($dirname . $github_id . $ext . time() . rand(0, 10000)), 0, 8);

    $working_path = "{$ENGINE_PATH}/../working/{$hash}";
    mkdir($working_path);
    file_put_contents($working_path . "/{$github_id}.{$ext}", $file);

    return $hash;
}

function compile_code($hash, $github_id, $ext) {
    global $ENGINE_PATH;

    $working_path = "{$ENGINE_PATH}/../working/{$hash}";

    switch($ext) {
        case "c":
            exec("gcc {$working_path}/{$github_id}.c -o {$working_path}/{$github_id} 1>&1 2>&2", $rows, $return);
            break;
        case "cpp":
            exec("gcc {$working_path}/{$github_id}.cpp -o {$working_path}/{$github_id} -lstdc++ -ldl -lm 1>&1 2>&2", $rows, $return);
            break;
        case "py":
        case "js":
            return true;
    }

    var_dump($return);

    return $return === 0;
}

function run_code($hash, $github_id, $ext, $input, &$err_no, &$time) {
    global $ENGINE_PATH;

    $working_path = "{$ENGINE_PATH}/../working/{$hash}";
    file_put_contents($working_path . "/input.txt", str_replace("\r", "", $input));

    $command = null;
    switch($ext) {
        case "py":
            $command = "python3 {$working_path}/{$github_id}.py";
            break;
        case "js":
            $command = "node {$working_path}/{$github_id}.js";
            break;
        case "c":
        case "cpp":
            $command = "{$working_path}/{$github_id}";
            break;
    }

    if($command === null) {
        return null;
    }

    $start_time = get_microtime();
    exec("timeout 3 {$command} < {$working_path}/input.txt 1>&1 2>&2", $rows, $return);
    $time = (int)((get_microtime() - $start_time) * 1000);

    if($return > 0) {
        $err_no = $return;
        return null;
    }

    $output = "";
    foreach($rows as $row) {
        $output .= trim($row) . PHP_EOL;
    }
    
    return rtrim($output);
}

function get_code($hash, $github_id, $ext) {
    global $ENGINE_PATH;

    return file_get_contents("{$ENGINE_PATH}/../working/{$hash}/{$github_id}.{$ext}");
}

function remove_code($hash) {
    global $ENGINE_PATH;

    exec("rm -rf {$ENGINE_PATH}/../working/{$hash}");
}

function get_microtime() {
	$time = explode(' ', microtime());
	return (float)$time[0] + (float)$time[1];
}
