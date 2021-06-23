<?php
if(!defined("_ENGINE_INCLUDED_") || !_ENGINE_INCLUDED_) {
    exit;
}

class DB {
    static private $connect;
    
    static public function initialize() {
        global $ENGINE_CONFIG;

        self::$connect = mysqli_connect($ENGINE_CONFIG['DB']['HOST'], $ENGINE_CONFIG['DB']['USER'], $ENGINE_CONFIG['DB']['PASSWORD']);
        mysqli_select_db(self::$connect, $ENGINE_CONFIG['DB']['DB']);
        self::query("SET CHARSET UTF8");
    }

    static public function query($query) {
        if(func_num_args() > 1) {
            $prepared = self::prepare($query);
            $args = func_get_args();
            $args[0] = $prepared;
            call_user_func_array("self::bind", $args);
            self::execute($prepared);
            return $prepared;
        } else {
            return mysqli_query(self::$connect, $query);
        }
    }
    
    static public function fetch($param) {
        if(func_num_args() > 1) {
            $param = call_user_func_array("self::query", func_get_args());
        }
        
        if($param instanceof mysqli_stmt) {
            $res = mysqli_stmt_get_result($param);
        } else {
            $res = self::query($param);
        }

        return mysqli_fetch_assoc($res);
    }
    
    static public function fetch_all($param) {
        if(func_num_args() > 1) {
            $param = call_user_func_array("self::query", func_get_args());
        }

        if($param instanceof mysqli_stmt) {
            DB::execute($param);
            $res = mysqli_stmt_get_result($param);
        } else {
            $res = self::query($param);
        }

        $ret = [];
        while(($row = mysqli_fetch_assoc($res)) !== null) {
            $ret[] = $row;
        }
        return $ret;
    }

    static public function prepare($query) {
        return mysqli_prepare(self::$connect, $query);
    }

    static public function bind() {
        $args = func_get_args();
        foreach($args as $key => &$val) {
            $args[$key] = &$val;
        }
        return call_user_func_array("mysqli_stmt_bind_param", $args);
    }

    static public function execute($prepared) {
        return mysqli_stmt_execute($prepared);
    }

    static public function get_inserted_id() {
        return mysqli_insert_id(self::$connect);
    }
}

DB::initialize();