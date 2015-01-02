<?php

/**
 * Small toolkit library
 *
 * Currently used for conversion of array and object structures. Could be used to record small an re-usable function
 *
 * @class Toolkit
 * @author   Dev Team <dev@startx.fr>
 * @copyright Copyright (c) 2003-2013 startx.fr
 * @license https://github.com/startxfr/sxapi/blob/master/licence.txt
 */
class Toolkit {

    /**
     * Convert array to object
     */
    static function object2Array($obj) {
        if (is_object($obj)) {
            $obj = get_object_vars($obj);
        }
        if (is_array($obj)) {
            return array_map('Toolkit::object2Array', $obj);
        } else {
            return $obj;
        }
    }

    /**
     * Convert array to object
     */
    static function array2Object($array) {
        if (is_array($array)) {
            return (object) array_map('Toolkit::array2Object', $array);
        } else {
            return $array;
        }
    }

    /**
     * Convert string to array according to a sep
     */
    static function string2Array($string, $sep = ',') {
        if (is_string($string) and strpos($string, $sep) !== false) {
            $ex = @explode($sep, $string);
            if (is_array($ex)) {
                return $ex;
            } else {
                return array($string);
            }
        } elseif (is_array($string)) {
            return $string;
        } elseif (is_null($string)) {
            return array();
        } else {
            return array($string);
        }
    }

    static function &array_merge_recursive_distinct(array &$array1, &$array2 = null) {
        $merged = $array1;
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $merged[$key] = (array_key_exists($key, $merged) and is_array($merged[$key])) ? Toolkit::array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
                } else {
                    $merged[$key] = $val;
                }
            }
        }
        return $merged;
    }

}
