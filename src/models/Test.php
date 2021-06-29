<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

class Test {
    private $id;
    private $seq;
    private $name;
    private $te_dirname;
    private $hard;
    private $point;
    private $group_id;
    private $group_name;
    private $group_dirname;
    private $group_start;
    private $group_end;
    private $dirname;

    public function __construct($param) {
        if(is_array($param)) {
            $this->id = $param['te_id'];
            $this->seq = $param['te_seq'];
            $this->te_name = $param['te_name'];
            $this->te_dirname = $param['te_dirname'];
            $this->hard = $param['te_hard'];
            $this->point = $param['te_point'];
            $this->group_id = $param['tg_id'];
            $this->group_name = $param['tg_name'];
            $this->group_dirname = $param['tg_dirname'];
            $this->group_start = $param['tg_start'];
            $this->group_end = $param['tg_end'];
            $this->dirname = "{$this->group_dirname}/{$this->te_dirname}";
            $this->name = "{$this->group_name} / {$this->te_name}";
        } else {
            $row = DB::fetch("SELECT * FROM test INNER JOIN test_group USING(tg_id) WHERE te_id = ?", "i", $param);
            $this->__construct($row);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function is_in_time() {
        return  date("Y-m-d H:i:s") < $this->group_end;
    }

    public function is_hidden() {
        return date("Y-m-d H:i:s") < $this->group_start;
    }

    public function save_result($member, $language, $code, $result, $time, $test_case = null, &$is_first) {
        if(!$member) {
            return null;
        }

        DB::query("INSERT test_result SET
                        te_id = ?,
                        mb_id = ?,
                        tr_language = ?,
                        tr_code = ?,
                        tr_result = ?,
                        tr_time = ?,
                        tc_id = ?
        ", "iisssii", $this->id, $member->id, $language, $code, $result, $time, $test_case === null ? null : $test_case->id);

        $result_id = DB::get_inserted_id();

        if($result === TestResult::SUCCESS && !$member->has_point($this)) {
            $member->add_point($this);
            $is_first = true;
        } else {
            $is_first = false;
        }

        return $result_id;
    }

    static public function get_from_dirname($dirname) {
        $dirnames = explode("/", $dirname);
        $row = DB::fetch("SELECT * FROM test INNER JOIN test_group USING(tg_id) WHERE tg_dirname = ? and te_dirname = ?", "ss", $dirnames[0], $dirnames[1]);
        if(!$row) {
            return null;
        } else {
            return new Test($row);
        }
    }
}

class TestResult {
    public const SUCCESS = "success";
    public const FAILED = "failed";
    public const COMPILE_ERROR = "compile_error";
    public const RUNTIME_ERROR = "runtime_error";
    public const TIMEOUT = "timeout";
    public const ARCHIVING = "archiving";
}