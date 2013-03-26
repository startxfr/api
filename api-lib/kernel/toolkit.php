<?php

class Toolkit {

    /**
     * Convert array to object
     */
    static function object2Array($obj) {
        if (is_object($obj))
            $obj = get_object_vars($obj);
        if (is_array($obj))
            return array_map('Toolkit::object2Array', $obj);
        else
            return $obj;
    }

    /**
     * Convert array to object
     */
    static function array2Object($array) {
        if (is_array($array))
            return (object) array_map('Toolkit::array2Object', $array);
        else
            return $array;
    }

    static function &array_merge_recursive_distinct(array &$array1, &$array2 = null) {
        $merged = $array1;
        if (is_array($array2))
            foreach ($array2 as $key => $val)
                if (is_array($array2[$key]))
                    $merged[$key] = is_array($merged[$key]) ? Toolkit::array_merge_recursive_distinct($merged[$key], $array2[$key]) : $array2[$key];
                else
                    $merged[$key] = $val;
        return $merged;
    }

}

?>