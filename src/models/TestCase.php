<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

class TestCase {
    private $id;
    private $test;
    private $seq;
    private $name;
    private $input;
    private $output;

    public function __construct($param) {
        if(is_array($param)) {
            $this->id = $param['tc_id'];
            $this->seq = $param['tc_seq'];
            $this->name = $param['tc_name'] ?? "Testcase #{$this->seq}";
            $this->input = $param['tc_input'];
            $this->output = $param['tc_output'];

            if(isset($param['test']))
                $this->test = $param['test'];
            else
                $this->test = new Test($param['te_id']);
        } else {
            $row = DB::fetch("SELECT * FROM test_case WHERE tc_id = ?", "i", $param);
            $this->__construct($row);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    static public function get_from_test($test) {
        $cases = DB::fetch_all("SELECT * FROM test_case WHERE te_id = ? ORDER BY tc_seq ASC", "i", $test->id);
        $ret = [];
        foreach($cases as $case) {
            $case['test'] = $test;
            $ret[] = new TestCase($case);
        }
        return $ret;
    }
}