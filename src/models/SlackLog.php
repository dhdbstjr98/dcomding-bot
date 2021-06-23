<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

class SlackLog {
    private $id;
    private $channel;
    private $blocks;
    private $created_at;

    public function __construct($param) {
        if(is_array($param)) {
            $this->id = $param['sl_id'];
            $this->channel = $param['sl_channel'];
            $this->blocks = $param['sl_blocks'];
            $this->created_at = $param['sl_created_at'];
        } else {
            $row = DB::fetch("SELECT * FROM slack_log WHERE sl_id = ?", "i", $param);
            $this->__construct($row);
        }
    }

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    static public function push($channel, $blocks) {
        $blocks = json_encode($blocks, JSON_UNESCAPED_UNICODE);
        DB::query("INSERT slack_log SET sl_channel = ?, sl_blocks = ?", "ss", $channel, $blocks);
        return DB::get_inserted_id();
    }
}