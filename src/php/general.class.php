<?php
class General {
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
    public static function date($date = null) {
        $format = 'Y-m-d';
        return $date ? @date($format, $date) : date($format, time());
    }
    // returns false on failure
    public static function datetime($date = null) {
        $format = 'Y-m-d H:i:s';
        return $date ? @date($format, $date) : date($format, time());
    }
}
?>
