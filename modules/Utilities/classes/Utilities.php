<?php defined('SYSPATH') or die('No direct script access.');

class Utilities 
{
    public static function is_numeric_array($array) 
    {
        if (is_array($array)) {
            foreach ($array as $element) {
                if (!is_numeric($element)) {
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            throw new Exception('Argument must be an array.');
        }
    }
    
    public static function numeric_array_to_sql_in($array)
    {
        $in         = '(';
        $last_key   = count($array) - 1;
        
        foreach ($array as $key => $element) {
            if (is_numeric($element)) {
                if ($key == $last_key) {
                    $in .= $element . ',';
                } else {
                    $in .= $element . ')';
                }
            } else {
                throw new Exception('Array element must be a number.');
            }
        }
        return DB::expr($in);
    }
}
