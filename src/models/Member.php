<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

class Member {
    private $id;
    private $name;
    private $slack_id;
    private $github_id;
    private $is_hidden;

    public function __construct($param) {
        if(is_array($param)) {
            $this->id = $param['mb_id'];
            $this->name = $param['mb_name'];
            $this->slack_id = $param['mb_slack_id'];
            $this->github_id = $param['mb_github_id'];
            $this->is_hidden = $param['mb_is_hidden'];
        } else {
            $row = DB::fetch("SELECT * FROM member WHERE mb_id = ?", "i", $param);
            $this->__construct($row);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function has_point($test) {
        if(!$test)
            return null;
        
        $point = DB::fetch("SELECT mp_id FROM member_point WHERE mb_id = ? and te_id = ?", "ii", $this->id, $test->id);
        return isset($point['mp_id']);
    }

    public function add_point($test) {
        if(!$test)
            return null;
        
        DB::query("INSERT INTO member_point (mb_id, te_id) VALUES (?,?)", "ii", $this->id, $test->id);
    }

    static public function get_from_github_id($github_id) {
        $row = DB::fetch("SELECT * FROM member WHERE mb_github_id = ?", "s", $github_id);
        if(!$row) {
            return null;
        } else {
            return new Member($row);
        }
    }

    static public function get_rank_with_name() {
        return DB::fetch_all("SELECT
                        mb_name AS name,
                        SUM(point) AS sum_point
                    FROM (
                        SELECT
                            mb_id,
                            mb_name,
                            SUM(te_point) AS point
                        FROM member_point
                        INNER JOIN member USING (mb_id)
                        INNER JOIN test USING (te_id)
                        WHERE mb_is_hidden = 0
                        GROUP BY mb_id
                        UNION (
                            SELECT
                                mb_id,
                                mb_name,
                                0 AS point
                            FROM member
                            WHERE mb_is_hidden = 0
                        )
                    ) t
                    GROUP BY mb_name
                    ORDER BY sum_point DESC
        ");
    }

    static public function get_weekrank_with_name() {
        return DB::fetch_all("SELECT
                        mb_name AS name,
                        SUM(point) AS sum_point
                    FROM (
                        SELECT
                            mb_id,
                            mb_name,
                            SUM(te_point) AS point
                        FROM member_point
                        INNER JOIN member USING (mb_id)
                        INNER JOIN test USING (te_id)
						INNER JOIN test_group USING (tg_id)
                        WHERE mb_is_hidden = 0 and tg_start <= now() and tg_end > now()
                        GROUP BY mb_id
                        UNION (
                            SELECT
                                mb_id,
                                mb_name,
                                0 AS point
                            FROM member
                            WHERE mb_is_hidden = 0
                        )
                    ) t
                    GROUP BY mb_name
                    ORDER BY sum_point DESC
        ");
    }
}