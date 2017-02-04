<?php

namespace Helper;

class Utf8Helper {
    public static function utf8EncodeArray($array) {
        if(!is_array($array)) {
            return $array;
        }
        
        array_walk_recursive($array, function(&$item){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });
 
        return $array;
    }
}

