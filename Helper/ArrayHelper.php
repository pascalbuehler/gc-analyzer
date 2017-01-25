<?php
namespace Helper;

class ArrayHelper {
    public static function insertNestedValue($array, $keys, $value) {
        $a = &$array;
        while(count($keys)>0) {
            $k = array_shift($keys);
            if(!is_array($a)) {
                $a = array();
            }
            $a = &$a[$k];
        }
        $a = $value;

        return $array;
    }

    public static function getNestedValue($array, $keys) {
        $found = true;
        $a = &$array;
        while(count($keys)>0) {
            $k = array_shift($keys);
            if(!isset($a[$k])) {
                $found = false;
                break;
            }
            $a = $a[$k];
        }

        return $found ? $a : null;
    }
}