<?php
class General {
    public static function getDepth() {
        $depth = './';
        $count = substr_count($_SERVER['PHP_SELF'], '/');
        if ($count > 1) {
            $depth = str_repeat('../', $count - 1);
        }
        return $depth;
    }
    public static function output($string) {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }
    public static function outputArray($data) {
        foreach ($data as $key => $value) {
            $data[$key] = self::output($value);
        }
        return $data;
    }
    // returns false on failure
    public static function getDate($date = null) {
        $format = 'Y-m-d';
        return $date ? @date($format, $date) : date($format, time());
    }
    // returns false on failure
    public static function getDatetime($date = null) {
        $format = 'Y-m-d H:i:s';
        return $date ? @date($format, $date) : date($format, time());
    }
}
?>
